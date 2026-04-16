<?php

namespace App\Http\Controllers;

use App\Models\{Category, Product};
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'creator'])->latest();

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('stock')) {
            if ($request->stock === 'low') {
                $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
            } elseif ($request->stock === 'out') {
                $query->where('stock_quantity', 0);
            }
        }

        $products   = $query->paginate(15)->withQueryString();
        $categories = Category::where('is_active', true)->get();
        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'description'         => 'nullable|string',
            'price'               => 'required|numeric|min:0',
            'cost_price'          => 'nullable|numeric|min:0',
            'stock_quantity'      => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'category_id'         => 'required|exists:categories,id',
            'is_active'           => 'boolean',
        ]);

        $validated['sku']        = Product::generateSku();
        $validated['created_by'] = auth()->id();
        $validated['is_active']  = $request->boolean('is_active', true);

        $product = Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', "Product '{$product->name}' created successfully.");
    }

    public function show(Product $product)
    {
        $product->load(['category', 'saleItems.sale.seller']);
        $totalSold = $product->saleItems->sum('quantity');
        $revenue   = $product->saleItems->sum('subtotal');
        return view('products.show', compact('product', 'totalSold', 'revenue'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'description'         => 'nullable|string',
            'price'               => 'required|numeric|min:0',
            'cost_price'          => 'nullable|numeric|min:0',
            'stock_quantity'      => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'category_id'         => 'required|exists:categories,id',
            'is_active'           => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', "Product '{$product->name}' updated successfully.");
    }

    public function destroy(Product $product)
    {
        if ($product->saleItems()->exists()) {
            return back()->with('error', 'Cannot delete product with existing sale records.');
        }
        $product->delete();
        return redirect()->route('products.index')
            ->with('success', "Product '{$product->name}' deleted.");
    }
}
