<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller; // ✅ REQUIRED
use App\Models\Account;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_sales')->only('index', 'show');
        $this->middleware('permission:create_sales')->only('create', 'store');
        $this->middleware('permission:edit_sales')->only('edit', 'update');
        $this->middleware('permission:delete_sales')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = Sale::with('seller')->latest();

        if (auth()->user()->isSeller()) {
            $query->where('seller_id', auth()->id());
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('sale_number', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sales = $query->paginate(15)->withQueryString();
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->with('category')->get();
        return view('sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'payment_method'     => 'required|in:cash,card,mobile_money,credit',
            'customer_name'      => 'nullable|string|max:255',
            'customer_phone'     => 'nullable|string|max:20',
            'discount'           => 'nullable|numeric|min:0',
            'notes'              => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount  = 0;
            $totalCost    = 0; // sum of cost prices of all items sold
            $totalRevenue = 0; // sum of selling prices
            $saleItems    = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock_quantity < $item['quantity']) {
                    return back()->with('error',
                        "Insufficient stock for '{$product->name}'. Available: {$product->stock_quantity}");
                }

                $subtotal      = $product->price * $item['quantity'];
                $itemCost      = $product->cost_price * $item['quantity'];
                $totalAmount  += $subtotal;
                $totalCost    += $itemCost;
                $totalRevenue += $subtotal;

                $saleItems[] = [
                    'product'    => $product,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal'   => $subtotal,
                    'cost'       => $itemCost,
                ];
            }

            $discount  = $request->discount ?? 0;
            $netAmount = $totalAmount - $discount;

            // Profit = total selling price - total cost price - discount
            $profit = $netAmount - $totalCost;

            // Create the sale record
            $sale = Sale::create([
                'sale_number'    => Sale::generateSaleNumber(),
                'total_amount'   => $totalAmount,
                'discount'       => $discount,
                'tax'            => 0,
                'net_amount'     => $netAmount,
                'payment_method' => $request->payment_method,
                'status'         => 'completed',
                'customer_name'  => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'notes'          => $request->notes,
                'seller_id'      => auth()->id(),
            ]);

            // Create sale items and deduct stock
            foreach ($saleItems as $itemData) {
                $sale->items()->create([
                    'product_id' => $itemData['product']->id,
                    'quantity'   => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'subtotal'   => $itemData['subtotal'],
                ]);
                $itemData['product']->decrement('stock_quantity', $itemData['quantity']);
            }

            // ── AUTO RECORD REVENUE IN ACCOUNTING ─────────────────────
            // Find the seller's account (or shop owner's account)
            $accountUserId = auth()->id();
            $account = Account::firstOrCreate(
                ['user_id' => $accountUserId],
                ['opening_balance' => 0, 'current_balance' => 0]
            );

            // Only record if profit > 0
            if ($profit > 0) {
                $newBalance = (float) $account->opening_balance
                    - $account->totalInventoryCost()
                    + $account->totalRevenue()
                    + $profit;

                // Build description
                $itemNames = collect($saleItems)
                    ->map(fn($i) => $i['product']->name . ' x' . $i['quantity'])
                    ->join(', ');

                Transaction::create([
                    'user_id'          => $accountUserId,
                    'sale_id'          => $sale->id,
                    'type'             => 'sale_revenue',
                    'amount'           => $profit,
                    'balance_after'    => $newBalance,
                    'description'      => "Sale #{$sale->sale_number} — {$itemNames} (Cost: " . number_format($totalCost) . " | Sold: " . number_format($netAmount) . " | Profit: " . number_format($profit) . ")",
                    'transaction_date' => today()->toDateString(),
                ]);

                $account->update(['current_balance' => $newBalance]);
            }
            // ── END AUTO REVENUE ───────────────────────────────────────

            DB::commit();
            return redirect()->route('sales.show', $sale)
                ->with('success', "Sale #{$sale->sale_number} recorded! Profit: " . number_format($profit) . " RWF added to accounting.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error recording sale: ' . $e->getMessage());
        }
    }

    public function show(Sale $sale)
    {
        $this->authorizeSaleView($sale);
        $sale->load(['items.product.category', 'seller']);
        return view('sales.show', compact('sale'));
    }

    public function receipt(Sale $sale)
    {
        $this->authorizeSaleView($sale);
        $sale->load(['items.product.category', 'seller']);
        return view('sales.receipt', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        return view('sales.edit', compact('sale'));
    }

    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'status' => 'required|in:completed,pending,cancelled,refunded',
            'notes'  => 'nullable|string',
        ]);

        $sale->update($request->only('status', 'notes'));

        return redirect()->route('sales.show', $sale)
            ->with('success', "Sale #{$sale->sale_number} updated.");
    }

    public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            // Restore stock
            foreach ($sale->items as $item) {
                $item->product->increment('stock_quantity', $item->quantity);
            }

            // Remove the auto-recorded revenue transaction for this sale
            Transaction::where('sale_id', $sale->id)->delete();

            // Recalculate account balance
            $account = Account::where('user_id', $sale->seller_id)->first();
            if ($account) {
                $account->update(['current_balance' => $account->calculatedBalance()]);
            }

            $sale->items()->delete();
            $sale->delete();
        });

        return redirect()->route('sales.index')
            ->with('success', "Sale deleted, stock restored and accounting updated.");
    }

    private function authorizeSaleView(Sale $sale): void
    {
        if (auth()->user()->isSeller() && $sale->seller_id !== auth()->id()) {
            abort(403);
        }
    }
}
