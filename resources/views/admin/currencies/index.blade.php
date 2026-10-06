@extends('layouts.admin')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; flex-wrap:wrap; gap:1rem;">
    <div>
        <div style="display:inline-flex; align-items:center; gap:8px; background:rgba(250, 180, 0, 0.12); border:1px solid rgba(250, 180, 0, 0.35); border-radius:999px; padding:4px 14px; font-size:0.75rem; font-weight:800; color:#fbbf24; margin-bottom:0.5rem;">
            <span>⚡ ALGERIAN P2P PARALLEL MARKET</span>
            <span>•</span>
            <span style="color:#00ff88;">Binance P2P & Bybit Live Engine</span>
        </div>
        <h1 style="font-size:1.75rem; font-weight:900; color:#ffffff; margin:0 0 0.35rem;">
            💱 إدارة العملات وأسعار الصرف (السوق الموازي الجزائري)
        </h1>
        <p style="color:#94a3b8; font-size:0.875rem; margin:0;">
            الأسعار مربوطة آلياً بسعر السوق السوداء والموازي في الجزائر (Binance P2P, Bybit, والسكوار) مع اعتماد الدينار الجزائري كعملة أساسية 100%.
        </p>
    </div>

    <!-- Live Sync Button -->
    <form action="<?= url('admin/currencies/sync-p2p') ?>" method="POST" style="margin:0;">
        <?= csrf_field() ?>
        <button type="submit" class="btn" style="background:linear-gradient(135deg, #f59e0b, #d97706); color:#000; font-weight:900; font-size:0.95rem; border-radius:10px; padding:0.75rem 1.4rem; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:10px; box-shadow:0 6px 20px rgba(245, 158, 11, 0.3); transition:all 0.2s ease;">
            <span>🔄</span>
            <span>تحديث أسعار الصرف فورياً من Binance P2P و Bybit</span>
        </button>
    </form>
</div>

<!-- Flash Notifications -->
<?php if ($msg = session()->flash('success')): ?>
    <div style="background:rgba(16, 185, 129, 0.15); color:#34d399; padding:1rem 1.25rem; border-radius:10px; border:1px solid rgba(16, 185, 129, 0.3); margin-bottom:1.75rem; font-weight:700; display:flex; align-items:center; gap:10px;">
        <span style="font-size:1.25rem;">✅</span>
        <span><?= $msg ?></span>
    </div>
<?php endif; ?>

<?php if ($msg = session()->flash('error')): ?>
    <div style="background:rgba(239, 68, 68, 0.15); color:#f87171; padding:1rem 1.25rem; border-radius:10px; border:1px solid rgba(239, 68, 68, 0.3); margin-bottom:1.75rem; font-weight:700; display:flex; align-items:center; gap:10px;">
        <span style="font-size:1.25rem;">⚠️</span>
        <span><?= $msg ?></span>
    </div>
<?php endif; ?>

<!-- P2P Live Telemetry Grid -->
<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:1.25rem; margin-bottom:2rem;">
    <!-- Binance P2P Card -->
    <div class="admin-card" style="margin:0; border:1px solid rgba(245, 158, 11, 0.3); background:rgba(245, 158, 11, 0.04);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
            <span style="font-weight:800; font-size:0.85rem; color:#f59e0b; display:flex; align-items:center; gap:6px;">
                <span>🟡</span> Binance P2P (BaridiMob)
            </span>
            <span style="font-size:0.7rem; background:rgba(245, 158, 11, 0.15); color:#f59e0b; padding:2px 8px; border-radius:4px; font-weight:700;">LIVE</span>
        </div>
        <div style="font-size:1.75rem; font-weight:900; color:#ffffff; font-family:monospace;">
            <?= $p2pRates['binance_usdt'] ?> <span style="font-size:0.95rem; color:#94a3b8; font-weight:700;">د.ج</span>
        </div>
        <div style="font-size:0.75rem; color:#94a3b8; margin-top:4px;">1 USDT = <?= $p2pRates['binance_usdt'] ?> دينار جزائري</div>
    </div>

    <!-- Bybit P2P Card -->
    <div class="admin-card" style="margin:0; border:1px solid rgba(234, 88, 12, 0.3); background:rgba(234, 88, 12, 0.04);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
            <span style="font-weight:800; font-size:0.85rem; color:#ea580c; display:flex; align-items:center; gap:6px;">
                <span>🟠</span> Bybit P2P Algeria
            </span>
            <span style="font-size:0.7rem; background:rgba(234, 88, 12, 0.15); color:#ea580c; padding:2px 8px; border-radius:4px; font-weight:700;">LIVE</span>
        </div>
        <div style="font-size:1.75rem; font-weight:900; color:#ffffff; font-family:monospace;">
            <?= $p2pRates['bybit_usdt'] ?> <span style="font-size:0.95rem; color:#94a3b8; font-weight:700;">د.ج</span>
        </div>
        <div style="font-size:0.75rem; color:#94a3b8; margin-top:4px;">1 USDT = <?= $p2pRates['bybit_usdt'] ?> دينار جزائري</div>
    </div>

    <!-- Square Port-Saïd Euro Card -->
    <div class="admin-card" style="margin:0; border:1px solid rgba(0, 210, 255, 0.3); background:rgba(0, 210, 255, 0.04);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
            <span style="font-weight:800; font-size:0.85rem; color:#00d2ff; display:flex; align-items:center; gap:6px;">
                <span>💶</span> يورو السكوار (Square EUR)
            </span>
            <span style="font-size:0.7rem; background:rgba(0, 210, 255, 0.15); color:#00d2ff; padding:2px 8px; border-radius:4px; font-weight:700;">PARALLEL</span>
        </div>
        <div style="font-size:1.75rem; font-weight:900; color:#ffffff; font-family:monospace;">
            <?= $p2pRates['eur_dzd'] ?> <span style="font-size:0.95rem; color:#94a3b8; font-weight:700;">د.ج</span>
        </div>
        <div style="font-size:0.75rem; color:#94a3b8; margin-top:4px;">1 EUR = <?= $p2pRates['eur_dzd'] ?> دينار جزائري</div>
    </div>

    <!-- Status Card -->
    <div class="admin-card" style="margin:0; border:1px solid rgba(0, 255, 136, 0.3); background:rgba(0, 255, 136, 0.04);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
            <span style="font-weight:800; font-size:0.85rem; color:#00ff88; display:flex; align-items:center; gap:6px;">
                <span>🛡️</span> حالة المحرك
            </span>
            <span style="font-size:0.7rem; background:rgba(0, 255, 136, 0.15); color:#00ff88; padding:2px 8px; border-radius:4px; font-weight:700;">ACTIVE</span>
        </div>
        <div style="font-size:1.05rem; font-weight:800; color:#ffffff; line-height:1.4;">
            متزامن مع السوق 🇩🇿
        </div>
        <div style="font-size:0.75rem; color:#94a3b8; margin-top:4px;">
            آخر فحص: <span style="color:#00ff88;"><?= $p2pRates['last_synced'] ?></span>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns: 1fr 340px; gap:2rem; align-items:start;">
    <!-- Currencies List -->
    <div class="admin-card">
        <div class="admin-card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h3 class="admin-card-title" style="margin:0;">📋 جدول العملات ومعدلات الصرف الفعالة بالمتجر</h3>
            <span style="font-size:0.8rem; color:#94a3b8;">العملة الأساسية: <strong>دينار جزائري (DZD)</strong></span>
        </div>
        <div class="admin-card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>الرمز</th>
                            <th>الاسم بالعربية</th>
                            <th>الرمز المختصر</th>
                            <th>القيمة بالدينار الجزائري</th>
                            <th>معامل الصرف (Exchange Rate)</th>
                            <th>الحالة</th>
                            <th style="text-align:center;">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($currencies as $curr): ?>
                            <?php 
                                $rateInDzd = ($curr->exchange_rate > 0 && $curr->code !== 'DZD') ? round(1.0 / $curr->exchange_rate, 2) : 1.0;
                            ?>
                            <tr>
                                <td style="font-weight:900; color:var(--admin-primary); font-size:1.05rem; font-family:monospace;">
                                    <?= $curr->code ?>
                                    <?php if ($curr->code === 'DZD'): ?>
                                        <span style="font-size:0.75rem;">🇩🇿</span>
                                    <?php elseif ($curr->code === 'USDT'): ?>
                                        <span style="font-size:0.75rem;">🪙</span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-weight:700;"><?= $curr->name_ar ?></td>
                                <td style="font-weight:800; font-family:monospace;"><?= $curr->symbol_ar ?></td>
                                <td>
                                    <?php if ($curr->code === 'DZD'): ?>
                                        <span style="color:#00ff88; font-weight:800;">1 د.ج (الأساس)</span>
                                    <?php else: ?>
                                        <div style="font-weight:900; color:#fbbf24; font-family:monospace; font-size:1.05rem;">
                                            1 <?= $curr->code ?> = <?= number_format($rateInDzd, 2) ?> د.ج
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="font-family:monospace; color:#cbd5e1;"><?= $curr->exchange_rate ?></td>
                                <td>
                                    <span class="status-pill <?= $curr->is_default ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' ?>" style="font-weight:800;">
                                        <?= $curr->is_default ? 'الافتراضية 🇩🇿' : 'نشط بالسوق' ?>
                                    </span>
                                </td>
                                <td style="text-align:center;">
                                    <?php if ($curr->code !== 'DZD'): ?>
                                        <div style="display:flex; gap:6px; justify-content:center;">
                                            <button type="button" class="btn btn-sm" onclick="openEditModal(<?= htmlspecialchars(json_encode([
                                                'id' => $curr->id,
                                                'code' => $curr->code,
                                                'name_ar' => $curr->name_ar,
                                                'name_en' => $curr->name_en,
                                                'symbol_ar' => $curr->symbol_ar,
                                                'symbol_en' => $curr->symbol_en,
                                                'rate_in_dzd' => $rateInDzd,
                                                'status' => $curr->status
                                            ])) ?>)" style="background:rgba(0, 210, 255, 0.15); border:1px solid rgba(0, 210, 255, 0.3); color:#00d2ff; padding:3px 10px; border-radius:6px; font-weight:700; cursor:pointer;">
                                                ✏️ تعديل
                                            </button>
                                            <a href="<?= url("admin/currencies/{$curr->id}/delete", ['_token' => csrf_token()]) ?>" onclick="return confirm('هل أنت متأكد من حذف هذه العملة؟')" class="btn btn-sm" style="background:rgba(239, 68, 68, 0.15); border:1px solid rgba(239, 68, 68, 0.3); color:#f87171; padding:3px 10px; border-radius:6px; font-weight:700; text-decoration:none;">
                                                🗑️
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span style="color:#64748b; font-size:0.75rem;">العملة الأم</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Currency Form -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">➕ إضافة عملة جديدة</h3>
        </div>
        <div class="admin-card-body">
            <form action="<?= url('admin/currencies/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label class="form-label">رمز العملة (ISO Code) *</label>
                    <input type="text" name="code" placeholder="GBP" class="form-control" style="text-transform:uppercase; font-weight:800; font-family:monospace;" required>
                </div>
                <div class="form-group">
                    <label class="form-label">الاسم بالعربية *</label>
                    <input type="text" name="name_ar" placeholder="جنيه إسترليني" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">الاسم بالإنجليزية *</label>
                    <input type="text" name="name_en" placeholder="British Pound" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">قيمة العملة الواحدة بالدينار الجزائري (د.ج) *</label>
                    <input type="number" step="0.01" name="rate_in_dzd" placeholder="مثال: 310.50" class="form-control" style="font-weight:700; color:#fbbf24; font-family:monospace;" required>
                    <small style="color:#94a3b8; font-size:0.75rem; margin-top:4px; display:block;">
                        سيتم حساب معامل الصرف التلقائي نسبة للدينار الجزائري.
                    </small>
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="font-weight:800; margin-top:1rem;">
                    💾 حفظ العملة
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Edit Currency Modal Dialog -->
<div id="currency-modal-backdrop" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.8); backdrop-filter:blur(8px); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#0f172a; border:1px solid rgba(0, 210, 255, 0.4); border-radius:1rem; padding:2rem; max-width:480px; width:90%; box-shadow:0 25px 60px rgba(0,0,0,0.9);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:1rem;">
            <h3 style="font-size:1.25rem; font-weight:800; color:#fff; margin:0;" id="edit-modal-title">
                ✏️ تعديل سعر صرف العملة
            </h3>
            <button type="button" onclick="closeEditModal()" style="background:none; border:none; color:#94a3b8; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>

        <form id="edit-currency-form" action="" method="POST">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom:1rem;">
                <label class="form-label">الاسم بالعربية *</label>
                <input type="text" name="name_ar" id="edit-name-ar" class="form-control" required style="background:#1e293b; color:#fff;">
            </div>
            <div class="form-group" style="margin-bottom:1rem;">
                <label class="form-label">الاسم بالإنجليزية *</label>
                <input type="text" name="name_en" id="edit-name-en" class="form-control" required style="background:#1e293b; color:#fff;">
            </div>
            <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label" style="color:#fbbf24; font-weight:800;">
                    سعر 1 وحدة بالدينار الجزائري (د.ج) في السوق الموازي *
                </label>
                <input type="number" step="0.01" name="rate_in_dzd" id="edit-rate-in-dzd" class="form-control" required style="background:#1e293b; color:#fbbf24; font-size:1.2rem; font-weight:900; font-family:monospace;">
                <small style="color:#94a3b8; font-size:0.75rem; margin-top:4px; display:block;">
                    مثال: 255.00 د.ج لكل 1 دولار أو USDT
                </small>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" onclick="closeEditModal()" class="btn btn-outline" style="color:#94a3b8; border-color:rgba(255,255,255,0.2);">
                    إلغاء
                </button>
                <button type="submit" class="btn btn-primary" style="font-weight:800;">
                    💾 حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(curr) {
    document.getElementById('edit-modal-title').innerText = '✏️ تعديل عملة ' + curr.code;
    document.getElementById('edit-name-ar').value = curr.name_ar;
    document.getElementById('edit-name-en').value = curr.name_en;
    document.getElementById('edit-rate-in-dzd').value = curr.rate_in_dzd;
    document.getElementById('edit-currency-form').action = '/admin/currencies/' + curr.id + '/update';
    
    const modal = document.getElementById('currency-modal-backdrop');
    modal.style.display = 'flex';
}

function closeEditModal() {
    const modal = document.getElementById('currency-modal-backdrop');
    modal.style.display = 'none';
}
</script>
@endsection
