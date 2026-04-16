<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    public function index(Request $request)
    {
        $user    = auth()->user();
        $account = Account::firstOrCreate(
            ['user_id' => $user->id],
            ['opening_balance' => 0, 'current_balance' => 0]
        );

        // Sync: Balance = Capital + Revenue (profit from sales)
        $correct = $account->calculatedBalance();
        if ((float)$account->current_balance !== $correct) {
            $account->update(['current_balance' => $correct]);
            $account->current_balance = $correct;
        }

        // Period filter
        $period   = $request->get('period', 'all');
        $dateFrom = null;
        $dateTo   = Carbon::today();

        switch ($period) {
            case 'today':  $dateFrom = Carbon::today(); break;
            case 'week':   $dateFrom = Carbon::now()->startOfWeek(); break;
            case 'month':  $dateFrom = Carbon::now()->startOfMonth(); break;
            case 'year':   $dateFrom = Carbon::now()->startOfYear(); break;
            case 'custom':
                $dateFrom = $request->filled('date_from') ? Carbon::parse($request->date_from) : Carbon::now()->startOfMonth();
                $dateTo   = $request->filled('date_to')   ? Carbon::parse($request->date_to)   : Carbon::today();
                break;
        }

        $txQuery = Transaction::with(['product', 'sale'])
            ->where('user_id', $user->id)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc');

        if ($dateFrom) {
            $txQuery->whereBetween('transaction_date', [
                $dateFrom->toDateString(), $dateTo->toDateString()
            ]);
        }

        $transactions = $txQuery->paginate(15)->withQueryString();

        $sQ = Transaction::where('user_id', $user->id);
        if ($dateFrom) {
            $sQ->whereBetween('transaction_date', [
                $dateFrom->toDateString(), $dateTo->toDateString()
            ]);
        }

        $periodInventory = (float)(clone $sQ)->where('type', 'inventory_purchase')->sum('amount');
        $periodRevenue   = (float)(clone $sQ)->where('type', 'sale_revenue')->sum('amount');
        $periodProfit    = $periodRevenue;

        $chartData = $this->getMonthlyChartData($user->id);

        return view('accounting.index', compact(
            'account', 'transactions', 'period',
            'periodInventory', 'periodRevenue', 'periodProfit',
            'chartData', 'dateFrom', 'dateTo'
        ));
    }

    public function setOpeningBalance(Request $request)
    {
        $request->validate([
            'opening_balance' => 'required|numeric|min:0',
            'notes'           => 'nullable|string',
        ]);

        $user    = auth()->user();
        $account = Account::firstOrCreate(['user_id' => $user->id]);
        $amount  = (float) $request->opening_balance;

        Transaction::where('user_id', $user->id)
            ->where('type', 'opening_balance')
            ->delete();

        Transaction::create([
            'user_id'          => $user->id,
            'type'             => 'opening_balance',
            'amount'           => $amount,
            'balance_after'    => $amount + $account->totalRevenue(),
            'description'      => 'Opening capital',
            'transaction_date' => $request->get('date', today()->toDateString()),
        ]);

        $newBalance = $amount + $account->totalRevenue();

        $account->update([
            'opening_balance' => $amount,
            'notes'           => $request->notes,
            'current_balance' => $newBalance,
        ]);

        return redirect()->route('accounting.index')
            ->with('success', 'Opening capital set to ' . number_format($amount) . ' RWF.');
    }

    // Record stock purchase — tracked in inventory only (does NOT reduce balance)
    public function recordInventory(Request $request)
    {
        $request->validate([
            'product_id'  => 'required|exists:products,id',
            'cost_price'  => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'date'        => 'required|date',
        ]);

        $user    = auth()->user();
        $account = Account::firstOrCreate(['user_id' => $user->id]);
        $cost    = (float) $request->cost_price;
        $product = Product::findOrFail($request->product_id);

        // Balance does NOT change when buying stock
        // Balance = Capital + Revenue (profit from sales only)
        $currentBalance = $account->calculatedBalance();

        Transaction::create([
            'user_id'          => $user->id,
            'product_id'       => $product->id,
            'type'             => 'inventory_purchase',
            'amount'           => $cost,
            'balance_after'    => $currentBalance,
            'description'      => $request->description ?? "Stock purchase: {$product->name}",
            'transaction_date' => $request->date,
        ]);

        return redirect()->route('accounting.index')
            ->with('success', "Stock purchase of " . number_format($cost) . " RWF recorded in inventory.");
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) abort(403);

        $transaction->delete();

        $account = Account::where('user_id', auth()->id())->first();
        if ($account) {
            $account->update(['current_balance' => $account->calculatedBalance()]);
        }

        return redirect()->route('accounting.index')
            ->with('success', 'Transaction deleted and balance recalculated.');
    }

    public function report(Request $request)
    {
        $user = auth()->user();
        $year = $request->get('year', now()->year);

        $monthly = [];
        for ($m = 1; $m <= 12; $m++) {
            $inv    = (float) Transaction::where('user_id', $user->id)
                ->where('type', 'inventory_purchase')
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $m)
                ->sum('amount');

            $profit = (float) Transaction::where('user_id', $user->id)
                ->where('type', 'sale_revenue')
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $m)
                ->sum('amount');

            $monthly[] = [
                'month'     => date('F', mktime(0, 0, 0, $m, 1)),
                'month_num' => $m,
                'inventory' => $inv,
                'revenue'   => $profit,
                'profit'    => $profit,
                'is_profit' => $profit >= 0,
            ];
        }

        $annual = [
            'inventory' => collect($monthly)->sum('inventory'),
            'revenue'   => collect($monthly)->sum('revenue'),
            'profit'    => collect($monthly)->sum('profit'),
            'is_profit' => collect($monthly)->sum('profit') >= 0,
        ];

        // Fix: pass as $availableYears to match the view variable name
        $availableYears = Transaction::where('user_id', $user->id)
            ->selectRaw('YEAR(transaction_date) as year')
            ->groupBy('year')
            ->orderByDesc('year')
            ->pluck('year');

        if ($availableYears->isEmpty()) {
            $availableYears = collect([now()->year]);
        }

        return view('accounting.report', compact(
            'monthly', 'annual', 'year', 'availableYears'
        ));
    }

    private function getMonthlyChartData(int $userId): array
    {
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $inv    = (float) Transaction::where('user_id', $userId)
                ->where('type', 'inventory_purchase')
                ->whereYear('transaction_date', now()->year)
                ->whereMonth('transaction_date', $m)
                ->sum('amount');

            $profit = (float) Transaction::where('user_id', $userId)
                ->where('type', 'sale_revenue')
                ->whereYear('transaction_date', now()->year)
                ->whereMonth('transaction_date', $m)
                ->sum('amount');

            $data[] = [
                'month'     => date('M', mktime(0, 0, 0, $m, 1)),
                'inventory' => $inv,
                'revenue'   => $profit,
                'profit'    => $profit,
            ];
        }
        return $data;
    }
}
