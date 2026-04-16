@extends('layouts.app')
@section('title','New Sale')
@section('breadcrumb','Sales / <span>New Sale</span>')

@push('styles')
<style>
    .pos-grid { display: grid; grid-template-columns: 1fr 380px; gap: 20px; }
    @media (max-width: 1024px) { .pos-grid { grid-template-columns: 1fr; } }
    .product-search-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 12px; max-height: 480px; overflow-y: auto; padding: 4px; }
    .product-card {
        background: var(--card-bg); border: 1px solid var(--card-border); border-radius: var(--radius-sm);
        padding: 14px; cursor: pointer; transition: all 0.2s; user-select: none;
    }
    .product-card:hover { border-color: var(--accent); background: var(--accent-soft); }
    .product-card.out-of-stock { opacity: 0.5; cursor: not-allowed; }
    .product-card .p-name { font-size: 13px; font-weight: 600; margin-bottom: 4px; }
    .product-card .p-cat  { font-size: 11px; color: var(--text-muted); margin-bottom: 6px; }
    .product-card .p-price { font-size: 14px; font-weight: 800; color: var(--accent); }
    .product-card .p-stock { font-size: 11px; margin-top: 4px; }

    .cart-item { display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
    .cart-item:last-child { border-bottom: none; }
    .qty-btn { width: 28px; height: 28px; border-radius: 6px; border: 1px solid var(--card-border); background: rgba(255,255,255,0.05); color: var(--text-primary); cursor: pointer; font-size: 15px; display: flex; align-items: center; justify-content: center; }
    .qty-btn:hover { background: var(--accent); border-color: var(--accent); }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1><i class="bi bi-plus-circle-fill me-2" style="color:var(--accent)"></i>New Sale</h1>
    <div class="page-actions">
        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

<form method="POST" action="{{ route('sales.store') }}" id="saleForm">
@csrf
<div class="pos-grid">
    <!-- Left: Product picker -->
    <div>
        <div class="card mb-3">
            <div class="card-body py-3">
                <input type="text" id="productSearch" class="form-control" placeholder="Search products by name or category…">
            </div>
        </div>
        <div class="product-search-grid" id="productGrid">
            @foreach($products as $product)
            <div class="product-card {{ $product->isOutOfStock() ? 'out-of-stock' : '' }}"
                 data-id="{{ $product->id }}"
                 data-name="{{ $product->name }}"
                 data-price="{{ $product->price }}"
                 data-stock="{{ $product->stock_quantity }}"
                 data-cat="{{ $product->category->name }}"
                 onclick="addToCart(this)">
                <div class="p-name">{{ $product->name }}</div>
                <div class="p-cat">{{ $product->category->name }}</div>
                <div class="p-price">{{ number_format($product->price) }} RWF</div>
                <div class="p-stock {{ $product->isLowStock() ? 'text-warning' : '' }}" style="color:var(--text-muted)">
                    Stock: {{ $product->stock_quantity }}
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Right: Cart & payment -->
    <div>
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-cart-fill me-2"></i>Cart
                <span id="cartCount" class="badge ms-1" style="background:var(--accent-soft);color:var(--accent)">0</span>
            </div>
            <div class="card-body p-0" style="min-height:120px">
                <div id="cartItems" style="padding:12px">
                    <div id="emptyCart" style="text-align:center;padding:24px;color:var(--text-muted);font-size:13px">
                        <i class="bi bi-cart" style="font-size:2rem;display:block;margin-bottom:8px"></i>Add products from the left
                    </div>
                </div>
            </div>
            <div class="card-body" style="border-top:1px solid var(--card-border)">
                <div class="d-flex justify-content-between mb-1" style="font-size:13px">
                    <span style="color:var(--text-muted)">Subtotal</span>
                    <span id="subtotalDisplay">0 RWF</span>
                </div>
                <div class="d-flex justify-content-between mb-2" style="font-size:13px">
                    <span style="color:var(--text-muted)">Discount</span>
                    <span style="color:var(--danger)" id="discountDisplay">0 RWF</span>
                </div>
                <div class="d-flex justify-content-between" style="font-size:18px;font-weight:800">
                    <span>Total</span>
                    <span style="color:var(--success)" id="totalDisplay">0 RWF</span>
                </div>
            </div>
        </div>

        <!-- Customer & payment info -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Customer Name</label>
                        <input type="text" name="customer_name" class="form-control" placeholder="Optional">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Customer Phone</label>
                        <input type="text" name="customer_phone" class="form-control" placeholder="Optional">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Payment Method *</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="credit">Credit</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Discount (RWF)</label>
                        <input type="number" name="discount" id="discountInput" class="form-control" min="0" value="0" oninput="updateTotals()">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-3" id="submitBtn" disabled>
            <i class="bi bi-check-circle-fill me-2"></i>Complete Sale
        </button>
        <!-- Hidden cart items will be appended here -->
        <div id="hiddenInputs"></div>
    </div>
</div>
</form>
@endsection

@push('scripts')
<script>
let cart = {};

function addToCart(el) {
    if (el.classList.contains('out-of-stock')) return;
    const id    = el.dataset.id;
    const stock = parseInt(el.dataset.stock);
    if (!cart[id]) {
        cart[id] = { id, name: el.dataset.name, price: parseFloat(el.dataset.price), qty: 0, stock };
    }
    if (cart[id].qty >= stock) {
        alert('Cannot exceed available stock (' + stock + ')');
        return;
    }
    cart[id].qty++;
    renderCart();
}

function removeItem(id) {
    delete cart[id];
    renderCart();
}

function changeQty(id, delta) {
    if (!cart[id]) return;
    cart[id].qty += delta;
    if (cart[id].qty <= 0) delete cart[id];
    else if (cart[id].qty > cart[id].stock) { cart[id].qty = cart[id].stock; alert('Max stock reached.'); }
    renderCart();
}

function renderCart() {
    const container  = document.getElementById('cartItems');
    const empty      = document.getElementById('emptyCart');
    const hiddenDiv  = document.getElementById('hiddenInputs');
    const submitBtn  = document.getElementById('submitBtn');
    const ids        = Object.keys(cart);

    document.getElementById('cartCount').textContent = ids.length;
    hiddenDiv.innerHTML = '';

    if (ids.length === 0) {
        container.innerHTML = '';
        container.appendChild(empty);
        submitBtn.disabled = true;
        updateTotals();
        return;
    }

    empty.style.display = 'none';
    container.innerHTML = '';
    ids.forEach((id, i) => {
        const item = cart[id];
        const div  = document.createElement('div');
        div.className = 'cart-item';
        div.innerHTML = `
            <div style="flex:1;min-width:0">
                <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${item.name}</div>
                <div style="font-size:12px;color:var(--text-muted)">${item.price.toLocaleString()} × ${item.qty}</div>
            </div>
            <div style="display:flex;align-items:center;gap:6px">
                <button type="button" class="qty-btn" onclick="changeQty('${id}',-1)">−</button>
                <span style="font-size:13px;font-weight:700;min-width:20px;text-align:center">${item.qty}</span>
                <button type="button" class="qty-btn" onclick="changeQty('${id}',1)">+</button>
                <button type="button" class="qty-btn" style="color:var(--danger);border-color:rgba(239,68,68,0.3)" onclick="removeItem('${id}')"><i class="bi bi-x"></i></button>
            </div>
        `;
        container.appendChild(div);

        // Hidden inputs for form submission
        const inp1 = document.createElement('input');
        inp1.type = 'hidden'; inp1.name = `items[${i}][product_id]`; inp1.value = item.id;
        const inp2 = document.createElement('input');
        inp2.type = 'hidden'; inp2.name = `items[${i}][quantity]`; inp2.value = item.qty;
        hiddenDiv.appendChild(inp1);
        hiddenDiv.appendChild(inp2);
    });

    submitBtn.disabled = false;
    updateTotals();
}

function updateTotals() {
    let subtotal = Object.values(cart).reduce((s, i) => s + i.price * i.qty, 0);
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    const total = Math.max(0, subtotal - discount);
    document.getElementById('subtotalDisplay').textContent = subtotal.toLocaleString() + ' RWF';
    document.getElementById('discountDisplay').textContent = discount.toLocaleString() + ' RWF';
    document.getElementById('totalDisplay').textContent   = total.toLocaleString() + ' RWF';
}

// Product search filter
document.getElementById('productSearch').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
        const match = card.dataset.name.toLowerCase().includes(q) || card.dataset.cat.toLowerCase().includes(q);
        card.style.display = match ? '' : 'none';
    });
});

// Confirm before submit
document.getElementById('saleForm').addEventListener('submit', function(e) {
    const total = Object.values(cart).reduce((s,i) => s + i.price * i.qty, 0);
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    const net = Math.max(0, total - discount);
    if (!confirm(`Complete sale for ${net.toLocaleString()} RWF?`)) e.preventDefault();
});
</script>
@endpush
