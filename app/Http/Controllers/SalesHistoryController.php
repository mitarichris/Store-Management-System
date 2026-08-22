<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user       = auth()->user();
        $viewMode   = $request->get('view', 'daily'); // daily | monthly | annual
        $dateStr    = $request->get('date', today()->toDateString());
        $month      = $request->get('month', now()->month);
        $year       = $request->get('year', now()->year);
        $sellerId   = $request->get('seller_id');

        // ── Base query ─────────────────────────────────────────────────────
        $query = Sale::with(['seller', 'items.product'])
            ->orderBy('created_at', 'desc');

        // Sellers only see their own sales
        if ($user->isSeller()) {
            $query->where('seller_id', $user->id);
        } elseif ($sellerId) {
            $query->where('seller_id', $sellerId);
        }

        // ── Filter by period ───────────────────────────────────────────────
        switch ($viewMode) {
            case 'daily':
                $date  = Carbon::parse($dateStr);
                $query->whereDate('created_at', $date);
                break;
            case 'monthly':
                $query->whereYear('created_at', $year)
                      ->whereMonth('created_at', $month);
                break;
            case 'annual':
                $query->whereYear('created_at', $year);
                break;
        }

        $sales = $query->paginate(20)->withQueryString();

        // ── Summary for the current filter ─────────────────────────────────
        $summaryQuery = Sale::query();
        if ($user->isSeller()) {
            $summaryQuery->where('seller_id', $user->id);
        } elseif ($sellerId) {
            $summaryQuery->where('seller_id', $sellerId);
        }

        switch ($viewMode) {
            case 'daily':
                $summaryQuery->whereDate('created_at', $date ?? today());
                break;
            case 'monthly':
                $summaryQuery->whereYear('created_at', $year)->whereMonth('created_at', $month);
                break;
            case 'annual':
                $summaryQuery->whereYear('created_at', $year);
                break;
        }

        $summary = [
            'total_sales'    => (clone $summaryQuery)->count(),
            'total_revenue'  => (clone $summaryQuery)->where('status', 'completed')->sum('net_amount'),
            'total_discount' => (clone $summaryQuery)->sum('discount'),
            'completed'      => (clone $summaryQuery)->where('status', 'completed')->count(),
            'cancelled'      => (clone $summaryQuery)->where('status', 'cancelled')->count(),
            'pending'        => (clone $summaryQuery)->where('status', 'pending')->count(),
        ];

        // ── Calendar heatmap — days with sales in current month ───────────
        $calendarYear  = $viewMode === 'daily' ? Carbon::parse($dateStr)->year  : (int)$year;
        $calendarMonth = $viewMode === 'daily' ? Carbon::parse($dateStr)->month : (int)$month;

        $calQuery = Sale::selectRaw('DATE(created_at) as sale_date, COUNT(*) as count, SUM(net_amount) as revenue')
            ->whereYear('created_at', $calendarYear)
            ->whereMonth('created_at', $calendarMonth)
            ->groupBy('sale_date');

        if ($user->isSeller()) {
            $calQuery->where('seller_id', $user->id);
        }

        $calendarData = $calQuery->get()->keyBy('sale_date');

        // ── Available sellers for filter (admin/owner only) ────────────────
        $sellers = [];
        if (!$user->isSeller()) {
            $sellers = User::where('role', 'seller')->get();
        }

        // ── Available years ────────────────────────────────────────────────
        $availableYears = Sale::selectRaw("strftime('%Y', created_at) as year")
            ->groupBy('year')->orderByDesc('year')->pluck('year');

        if ($availableYears->isEmpty()) {
            $availableYears = collect([now()->year]);
        }

        return view('history.index', compact(
            'sales', 'summary', 'viewMode', 'dateStr', 'month', 'year',
            'sellers', 'sellerId', 'calendarData', 'calendarYear', 'calendarMonth',
            'availableYears'
        ));
    }

    // Quick view for a specific date (used by calendar clicks)
    public function byDate(Request $request, string $date)
    {
        return redirect()->route('history.index', [
            'view' => 'daily',
            'date' => $date,
        ]);
    }
}
