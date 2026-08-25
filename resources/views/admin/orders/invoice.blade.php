<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة ضريبية - <?= $order->order_number ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; margin: 0; padding: 40px; color: #1e293b; font-size: 14px; }
        .invoice-box { max-width: 800px; margin: auto; border: 1px solid #e2e8f0; padding: 30px; border-radius: 8px; }
        .header-table { width: 100%; margin-bottom: 30px; border-bottom: 2px solid #2563eb; padding-bottom: 20px; }
        .details-grid { display: flex; justify-content: space-between; margin-bottom: 30px; background: #f8fafc; padding: 15px; border-radius: 6px; }
        table.items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table.items-table th { background: #f1f5f9; padding: 10px; border: 1px solid #cbd5e1; text-align: right; }
        table.items-table td { padding: 10px; border: 1px solid #cbd5e1; }
        .totals-table { width: 300px; margin-right: auto; border-collapse: collapse; }
        .totals-table td { padding: 6px 10px; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            .invoice-box { border: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="max-width:800px; margin:0 auto 20px; text-align:left;">
        <button onclick="window.print();" style="background:#2563eb; color:#fff; padding:10px 20px; border:none; border-radius:6px; font-weight:bold; cursor:pointer; font-family:inherit;">🖨️ طباعة الفاتورة الآن</button>
    </div>

    <div class="invoice-box">
        <table class="header-table">
            <tr>
                <td>
                    <h1 style="margin:0; font-size:24px; color:#2563eb;">⚡ <?= app_setting('site_name', 'NovaStore') ?></h1>
                    <p style="margin:4px 0 0; color:#64748b;">فاتورة ضريبية رسمية مبسطة</p>
                    <p style="margin:4px 0 0; color:#64748b; font-size:12px;">الرقم الضريبي: 300000000000003</p>
                </td>
                <td style="text-align:left;">
                    <h3 style="margin:0; font-size:18px;">رقم الطلب: <?= $order->order_number ?></h3>
                    <p style="margin:4px 0 0; color:#64748b;">التاريخ: <?= date('Y-m-d H:i', strtotime($order->created_at)) ?></p>
                    <p style="margin:4px 0 0; color:#64748b;">الحالة: <?= strtoupper($order->status) ?></p>
                </td>
            </tr>
        </table>

        <div class="details-grid">
            <div>
                <strong>بيانات المشتري:</strong><br>
                الاسم: <?= htmlspecialchars($order->customer_name) ?><br>
                الهاتف: <?= htmlspecialchars($order->customer_phone) ?><br>
                البريد: <?= htmlspecialchars($order->customer_email) ?>
            </div>
            <div>
                <strong>عنوان وطريقة التوصيل:</strong><br>
                <?= htmlspecialchars($order->shipping_address) ?><br>
                <?= htmlspecialchars($order->shipping_city) ?>, <?= htmlspecialchars($order->shipping_country) ?><br>
                الشحن: <?= htmlspecialchars($order->shipping_method) ?>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>المنتج</th>
                    <th>الكمية</th>
                    <th>سعر الوحدة</th>
                    <th>الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($items as $item): ?>
                    <tr>
                        <td style="text-align:center;"><?= $i++ ?></td>
                        <td>
                            <strong><?= htmlspecialchars($item->product_name) ?></strong><br>
                            <span style="font-size:11px; color:#64748b;">SKU: <?= $item->sku ?></span>
                        </td>
                        <td style="text-align:center;"><?= $item->quantity ?></td>
                        <td style="text-align:left;"><?= format_price($item->unit_price, $order->currency_code) ?></td>
                        <td style="text-align:left; font-weight:bold;"><?= format_price($item->subtotal, $order->currency_code) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td>المجموع الفرعي:</td>
                <td style="text-align:left; font-weight:bold;"><?= format_price($order->subtotal, $order->currency_code) ?></td>
            </tr>
            <?php if ($order->discount_amount > 0): ?>
                <tr style="color:green;">
                    <td>الخصم:</td>
                    <td style="text-align:left; font-weight:bold;">-<?= format_price($order->discount_amount, $order->currency_code) ?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <td>ضريبة القيمة المضافة (15%):</td>
                <td style="text-align:left;"><?= format_price($order->tax_amount, $order->currency_code) ?></td>
            </tr>
            <tr>
                <td>رسوم الشحن:</td>
                <td style="text-align:left;"><?= format_price($order->shipping_fee, $order->currency_code) ?></td>
            </tr>
            <tr style="border-top:2px solid #2563eb; font-size:16px;">
                <td><strong>الإجمالي النهائي:</strong></td>
                <td style="text-align:left; font-weight:bold; color:#2563eb;"><?= format_price($order->total_amount, $order->currency_code) ?></td>
            </tr>
        </table>

        <div style="margin-top:50px; text-align:center; color:#94a3b8; font-size:12px; border-top:1px solid #e2e8f0; padding-top:15px;">
            شكراً لثقتكم بنا! في حال وجود أي استفسار يرجى التواصل مع الدعم الفني: <?= app_setting('contact_phone', '+966 50 123 4567') ?>
        </div>
    </div>
</body>
</html>
