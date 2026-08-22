<?php

namespace App\Http\Controllers;

use App\Models\{Category, Product, Sale, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $stats = [];

        if ($user->isSuperAdmin()) {
            $stats = $this->superAdminStats();
        } elseif ($user->isShopOwner()) {
            $stats = $this->shopOwnerStats();
        } else {
            $stats = $this->sellerStats($user);
        }

        return view('dashboard', compact('stats'));
    }

    private function superAdminStats(): array
    {
        return [
            'total_users'      => User::count(),
            'total_products'   => Product::count(),
            'total_categories' => Category::count(),
            'total_sales'      => Sale::count(),
            'total_revenue'    => Sale::where('status', 'completed')->sum('net_amount'),
            'low_stock'        => Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count(),
            'recent_sales'     => Sale::with('seller')->latest()->take(8)->get(),
            'monthly_revenue'  => $this->monthlyRevenue(),
            'top_products'     => $this->topProducts(),
            'sellers'          => User::where('role', 'seller')->withCount('sales')->get(),
        ];
    }

    private function shopOwnerStats(): array
    {
        return [
            'total_products'   => Product::count(),
            'total_categories' => Category::count(),
            'total_sales'      => Sale::count(),
            'total_revenue'    => Sale::where('status', 'completed')->sum('net_amount'),
            'low_stock'        => Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count(),
            'recent_sales'     => Sale::with('seller')->latest()->take(8)->get(),
            'monthly_revenue'  => $this->monthlyRevenue(),
            'top_products'     => $this->topProducts(),
        ];
    }

    private function sellerStats(User $user): array
    {
        return [
            'my_sales_count'   => $user->sales()->count(),
            'my_revenue'       => $user->sales()->where('status', 'completed')->sum('net_amount'),
            'my_recent_sales'  => $user->sales()->with('items.product')->latest()->take(6)->get(),
            'total_products'   => Product::where('is_active', true)->count(),
            'low_stock'        => Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count(),
        ];
    }

    private function monthlyRevenue(): array
    {
        $isSqlite = DB::getDriverName() === 'sqlite';
        $monthExpr = $isSqlite ? "strftime('%m', created_at)" : "DATE_FORMAT(created_at, '%m')";
        $yearExpr  = $isSqlite ? "strftime('%Y', created_at)" : "DATE_FORMAT(created_at, '%Y')";

        return Sale::where('status', 'completed')
            ->selectRaw("$monthExpr as month, $yearExpr as year, SUM(net_amount) as total")
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->take(12)->get()
            ->map(fn($r) => [
                'label' => date('M Y', mktime(0, 0, 0, $r->month, 1, $r->year)),
                'total' => $r->total,
            ])->toArray();
    }

    private function topProducts(): \Illuminate\Support\Collection
    {
        return DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->selectRaw('products.name, SUM(sale_items.quantity) as total_sold, SUM(sale_items.subtotal) as revenue')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->take(5)->get();
    }
}
