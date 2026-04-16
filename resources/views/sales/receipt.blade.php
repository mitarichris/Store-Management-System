<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt — {{ $sale->sale_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 40px 20px;
        }

        /* Action buttons — hidden when printing */
        .actions {
            display: flex; gap: 10px; margin-bottom: 28px;
        }
        .btn {
            padding: 10px 24px; border-radius: 8px; font-size: 14px;
            font-weight: 600; font-family: inherit; cursor: pointer;
            border: none; transition: all 0.2s;
        }
        .btn-print {
            background: #6c63ff; color: #fff;
        }
        .btn-print:hover { background: #5a52e0; }
        .btn-back {
            background: #fff; color: #374151;
            border: 1px solid #e5e7eb;
        }
        .btn-back:hover { background: #f9fafb; }
        .btn-back a { color: inherit; text-decoration: none; }

        /* Receipt paper */
        .receipt {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 40px rgba(0,0,0,0.12);
            overflow: hidden;
            position: relative;
        }

        /* Header */
        .receipt-header {
            background: linear-gradient(135deg, #1a1d28 0%, #13161f 100%);
            color: #fff;
            padding: 32px 28px 24px;
            text-align: center;
            position: relative;
        }
        .shop-icon {
            width: 52px; height: 52px;
            background: #6c63ff;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; font-weight: 800; color: #fff;
            margin: 0 auto 14px;
        }
        .shop-name {
            font-size: 22px; font-weight: 800; letter-spacing: -0.5px;
            margin-bottom: 4px;
        }
        .shop-tagline {
            font-size: 12px; color: rgba(255,255,255,0.5);
            margin-bottom: 20px;
        }
        .sale-number {
            display: inline-block;
            background: rgba(108,99,255,0.2);
            border: 1px solid rgba(108,99,255,0.4);
            border-radius: 20px;
            padding: 5px 16px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px; font-weight: 600;
            color: #a5b4fc;
        }

        /* Zigzag edge */
        .zigzag {
            height: 16px;
            background: linear-gradient(-45deg, #fff 25%, transparent 25%) 0 0,
                        linear-gradient( 45deg, #fff 25%, transparent 25%) 0 0,
                        linear-gradient(-45deg, transparent 75%, #fff 75%),
                        linear-gradient( 45deg, transparent 75%, #fff 75%);
            background-size: 16px 16px;
            background-color: #1a1d28;
        }

        /* Body */
        .receipt-body { padding: 24px 28px; }

        /* Info row */
        .info-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 16px; margin-bottom: 24px;
        }
        .info-item {}
        .info-label {
            font-size: 10px; font-weight: 700; letter-spacing: 0.8px;
            text-transform: uppercase; color: #9ca3af; margin-bottom: 3px;
        }
        .info-value {
            font-size: 13px; font-weight: 600; color: #111827;
        }

        /* Divider */
        .divider {
            border: none;
            border-top: 1px dashed #e5e7eb;
            margin: 20px 0;
        }

        /* Items table */
        .items-header {
            display: grid; grid-template-columns: 1fr auto auto;
            gap: 8px; padding-bottom: 8px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 10px; font-weight: 700; letter-spacing: 0.8px;
            text-transform: uppercase; color: #9ca3af;
        }
        .item-row {
            display: grid; grid-template-columns: 1fr auto auto;
            gap: 8px; padding: 10px 0;
            border-bottom: 1px solid #f9fafb;
            align-items: center;
        }
        .item-row:last-child { border-bottom: none; }
        .item-name { font-size: 13px; font-weight: 600; color: #111827; }
        .item-cat  { font-size: 11px; color: #9ca3af; margin-top: 2px; }
        .item-qty  { font-size: 12px; color: #6b7280; text-align: center; }
        .item-price { font-size: 13px; font-weight: 700; color: #111827; text-align: right; }

        /* Totals */
        .totals { margin-top: 16px; }
        .total-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 5px 0; font-size: 13px;
        }
        .total-row .label { color: #6b7280; }
        .total-row .value { font-weight: 600; color: #111827; }
        .total-final {
            display: flex; justify-content: space-between; align-items: center;
            padding: 14px 16px; margin-top: 12px;
            background: #f9fafb; border-radius: 10px;
            font-size: 15px;
        }
        .total-final .label { font-weight: 700; color: #111827; }
        .total-final .value { font-size: 18px; font-weight: 800; color: #6c63ff; }

        /* Payment badge */
        .payment-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: #ede9fe; color: #5b21b6;
            border-radius: 20px; padding: 5px 14px;
            font-size: 12px; font-weight: 700;
            margin-top: 14px;
        }

        /* Zigzag bottom */
        .zigzag-bottom {
            height: 16px;
            background: linear-gradient(135deg, #f0f2f5 25%, transparent 25%) 0 0,
                        linear-gradient(225deg, #f0f2f5 25%, transparent 25%) 0 0,
                        linear-gradient(315deg, #f0f2f5 25%, transparent 25%),
                        linear-gradient( 45deg, #f0f2f5 25%, transparent 25%);
            background-size: 16px 16px;
            background-color: #fff;
        }

        /* Footer */
        .receipt-footer {
            padding: 20px 28px 28px;
            text-align: center;
        }
        .seller-line {
            font-size: 12px; color: #6b7280; margin-bottom: 16px;
        }
        .seller-line strong { color: #374151; }
        .thank-you {
            font-size: 18px; font-weight: 800; color: #111827;
            letter-spacing: -0.3px; margin-bottom: 6px;
        }
        .thank-you-sub {
            font-size: 12px; color: #9ca3af; line-height: 1.6;
        }
        .receipt-dots {
            display: flex; justify-content: center; gap: 4px; margin-top: 20px;
        }
        .receipt-dots span {
            width: 6px; height: 6px; border-radius: 50%;
            background: #e5e7eb;
        }
        .receipt-dots span:nth-child(3) { background: #6c63ff; }

        /* Status badge */
        .status-badge {
            display: inline-block; padding: 3px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-completed { background: #dcfce7; color: #16a34a; }
        .status-pending   { background: #fef9c3; color: #ca8a04; }
        .status-cancelled { background: #fee2e2; color: #dc2626; }
        .status-refunded  { background: #f3f4f6; color: #6b7280; }

        /* Print styles */
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .actions { display: none !important; }
            .receipt {
                box-shadow: none;
                border-radius: 0;
                max-width: 100%;
            }
            .receipt-header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .zigzag, .zigzag-bottom {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<!-- Action buttons -->
<div class="actions">
    <button class="btn btn-back">
        <a href="{{ route('sales.show', $sale) }}">← Back to Sale</a>
    </button>
    <button class="btn btn-print" onclick="window.print()">
        🖨 Print Receipt
    </button>
</div>

<!-- Receipt -->
<div class="receipt">

    <!-- Header -->
    <div class="receipt-header">
        <div class="shop-icon">S</div>
        <div class="shop-name">StoreHub</div>
        <div class="shop-tagline">Your trusted shopping destination</div>
        <div class="sale-number">{{ $sale->sale_number }}</div>
    </div>

    <!-- Zigzag top -->
    <div class="zigzag"></div>

    <!-- Body -->
    <div class="receipt-body">

        <!-- Sale info grid -->
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Date</div>
                <div class="info-value">{{ $sale->created_at->format('d M Y') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Time</div>
                <div class="info-value">{{ $sale->created_at->format('H:i') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Customer Name</div>
                <div class="info-value">{{ $sale->customer_name ?? 'Walk-in Customer' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Phone</div>
                <div class="info-value">{{ $sale->customer_phone ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="status-badge status-{{ $sale->status }}">
                        {{ ucfirst($sale->status) }}
                    </span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Served by</div>
                <div class="info-value">{{ $sale->seller->name }}</div>
            </div>
        </div>

        <hr class="divider">

        <!-- Items -->
        <div class="items-header">
            <span>Item</span>
            <span style="text-align:center">Qty</span>
            <span style="text-align:right">Amount</span>
        </div>

        @foreach($sale->items as $item)
        <div class="item-row">
            <div>
                <div class="item-name">{{ $item->product->name }}</div>
                <div class="item-cat">
                    {{ $item->product->category->name }} &middot;
                    {{ number_format($item->unit_price) }} RWF each
                </div>
            </div>
            <div class="item-qty">× {{ $item->quantity }}</div>
            <div class="item-price">{{ number_format($item->subtotal) }} RWF</div>
        </div>
        @endforeach

        <!-- Totals -->
        <div class="totals">
            <div class="total-row">
                <span class="label">Subtotal</span>
                <span class="value">{{ number_format($sale->total_amount) }} RWF</span>
            </div>
            @if($sale->discount > 0)
            <div class="total-row">
                <span class="label">Discount</span>
                <span class="value" style="color:#dc2626">− {{ number_format($sale->discount) }} RWF</span>
            </div>
            @endif
            @if($sale->tax > 0)
            <div class="total-row">
                <span class="label">Tax</span>
                <span class="value">+ {{ number_format($sale->tax) }} RWF</span>
            </div>
            @endif
            <div class="total-final">
                <span class="label">TOTAL PAID</span>
                <span class="value">{{ number_format($sale->net_amount) }} RWF</span>
            </div>
        </div>

        <!-- Payment method -->
        <div style="text-align:center">
            <span class="payment-badge">
                💳 {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}
            </span>
        </div>

        @if($sale->notes)
        <hr class="divider">
        <div style="font-size:12px;color:#6b7280;line-height:1.6">
            <strong style="color:#374151">Note:</strong> {{ $sale->notes }}
        </div>
        @endif

    </div>

    <!-- Bottom zigzag -->
    <div class="zigzag-bottom"></div>

    <!-- Footer -->
    <div class="receipt-footer">
        <div class="seller-line">
            Served by: <strong>{{ $sale->seller->name }}</strong>
        </div>
        <div class="thank-you">Thank You! 🎉</div>
        <div class="thank-you-sub">
            Thank you for shopping with us.<br>
            We appreciate your business and look<br>
            forward to serving you again!
        </div>
        <div class="receipt-dots">
            <span></span><span></span><span></span><span></span><span></span>
        </div>
    </div>

</div>

</body>
</html>
