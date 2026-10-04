<!DOCTYPE html>
<html lang="<?= current_locale() ?>" dir="<?= is_rtl() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('auth.admin_login') ?> | <?= app_setting('site_name', 'Alpha Gaming Shop') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('assets/css/admin.css') ?>">
    <?php if (is_rtl()): ?>
    <link rel="stylesheet" href="<?= asset('assets/css/rtl.css') ?>">
    <?php endif; ?>
    <style>
        :root {
            --color-admin-primary: #00d2ff;
            --color-admin-accent: #00ff88;
            --color-admin-gold: #ffaa00;
        }
        body {
            background-color: #050811;
            background-image: 
                radial-gradient(circle at 50% 10%, rgba(0, 210, 255, 0.12) 0%, transparent 50%),
                radial-gradient(circle at 10% 90%, rgba(0, 255, 136, 0.08) 0%, transparent 45%),
                linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 30px 30px, 30px 30px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            font-family: 'Cairo', sans-serif;
            color: #f8fafc;
            margin: 0;
            box-sizing: border-box;
        }
        .admin-vault-card {
            background: rgba(13, 20, 36, 0.92);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(0, 210, 255, 0.35);
            border-radius: 1.5rem;
            padding: 3rem 2.5rem;
            max-width: 460px;
            width: 100%;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.9), 0 0 40px rgba(0, 210, 255, 0.18);
            position: relative;
            overflow: hidden;
        }
        .admin-vault-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #00d2ff, #00ff88, #ffaa00);
        }
        .telemetry-tag {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            color: #00ff88;
            background: rgba(0, 255, 136, 0.08);
            border: 1px solid rgba(0, 255, 136, 0.25);
            border-radius: 6px;
            padding: 3px 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 1.5rem;
        }
        .pulse-live {
            width: 8px;
            height: 8px;
            background: #00ff88;
            border-radius: 50%;
            box-shadow: 0 0 10px #00ff88;
            animation: pulseAnim 1.4s infinite;
        }
        @keyframes pulseAnim {
            0%, 100% { transform: scale(0.9); opacity: 0.6; }
            50% { transform: scale(1.3); opacity: 1; }
        }
        .admin-input-wrap {
            position: relative;
            margin-bottom: 1.25rem;
        }
        .admin-input-wrap .admin-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            right: 14px;
            color: #64748b;
            font-size: 1.1rem;
            pointer-events: none;
            transition: color 0.2s ease;
        }
        html[dir="rtl"] .admin-input-wrap .admin-icon {
            right: 14px;
            left: auto;
        }
        html[dir="ltr"] .admin-input-wrap .admin-icon {
            left: 14px;
            right: auto;
        }
        .admin-input-field {
            width: 100%;
            box-sizing: border-box;
            padding: 0.85rem 2.8rem;
            background: #111a2e;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            color: #ffffff;
            font-family: inherit;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.25s ease;
        }
        .admin-input-field:focus {
            border-color: #00d2ff;
            background: #15223c;
            box-shadow: 0 0 20px rgba(0, 210, 255, 0.25);
        }
        .admin-eye-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 14px;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 1.1rem;
            padding: 4px;
        }
        html[dir="rtl"] .admin-eye-btn {
            left: 14px;
            right: auto;
        }
        html[dir="ltr"] .admin-eye-btn {
            right: 14px;
            left: auto;
        }
        .btn-admin-submit {
            background: linear-gradient(135deg, #00d2ff 0%, #0077ff 100%);
            color: #050811;
            border: none;
            padding: 0.95rem;
            border-radius: 10px;
            font-weight: 900;
            font-size: 1.05rem;
            width: 100%;
            cursor: pointer;
            font-family: inherit;
            box-shadow: 0 6px 20px rgba(0, 210, 255, 0.35);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-admin-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 210, 255, 0.55);
        }
        .quick-admin-fill-btn {
            background: rgba(0, 210, 255, 0.1);
            border: 1px dashed rgba(0, 210, 255, 0.4);
            border-radius: 8px;
            padding: 0.65rem 1rem;
            color: #00d2ff;
            font-family: inherit;
            font-size: 0.825rem;
            font-weight: 700;
            width: 100%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 1.5rem;
            transition: all 0.2s ease;
        }
        .quick-admin-fill-btn:hover {
            background: rgba(0, 210, 255, 0.2);
            border-color: #00d2ff;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="admin-vault-card">
        <!-- Telemetry Header -->
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div class="telemetry-tag">
                <span class="pulse-live"></span>
                <span>SYSTEM: ALPHA VAULT ONLINE</span>
            </div>
            <span style="font-family:'JetBrains Mono', monospace; font-size:0.72rem; color:#64748b;">v4.5 SECURE</span>
        </div>

        <!-- Center Brand Emblem -->
        <div style="text-align:center; margin-bottom:1.75rem;">
            <div style="display:inline-flex; align-items:center; justify-content:center; width:68px; height:68px; background:linear-gradient(135deg, rgba(0, 210, 255, 0.2), rgba(0, 255, 136, 0.2)); border:1px solid rgba(0, 210, 255, 0.4); color:#00d2ff; font-size:2.2rem; border-radius:18px; margin-bottom:0.75rem; box-shadow:0 0 25px rgba(0, 210, 255, 0.25);">
                🛡️
            </div>
            <h1 style="font-size:1.75rem; font-weight:900; color:#ffffff; margin:0 0 0.35rem; letter-spacing:-0.5px;">
                <?= __('auth.admin_login') ?>
            </h1>
            <p style="color:#94a3b8; font-size:0.875rem; margin:0;">
                بوابة الإدارة المركزية لمتجر <?= app_setting('site_name', 'Alpha Gaming Shop') ?> 🇩🇿
            </p>
        </div>

        <?php if ($msg = session()->flash('error')): ?>
            <div style="background:rgba(239, 68, 68, 0.15); color:#f87171; padding:0.85rem 1rem; border-radius:8px; font-size:0.875rem; margin-bottom:1.5rem; border:1px solid rgba(239, 68, 68, 0.3); display:flex; align-items:center; gap:8px;">
                <span>⚠️</span> <span><?= $msg ?></span>
            </div>
        <?php endif; ?>

        <?php if ($msg = session()->flash('info')): ?>
            <div style="background:rgba(0, 210, 255, 0.12); color:#38bdf8; padding:0.85rem 1rem; border-radius:8px; font-size:0.875rem; margin-bottom:1.5rem; border:1px solid rgba(0, 210, 255, 0.3); display:flex; align-items:center; gap:8px;">
                <span>ℹ️</span> <span><?= $msg ?></span>
            </div>
        <?php endif; ?>

        <?php if ($msg = session()->flash('success')): ?>
            <div style="background:rgba(0, 255, 136, 0.12); color:#4ade80; padding:0.85rem 1rem; border-radius:8px; font-size:0.875rem; margin-bottom:1.5rem; border:1px solid rgba(0, 255, 136, 0.3); display:flex; align-items:center; gap:8px;">
                <span>✅</span> <span><?= $msg ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= url('admin/login') ?>" method="POST" onsubmit="playAdminSound()">
            <?= csrf_field() ?>
            <div class="admin-input-wrap">
                <label style="display:block; font-size:0.85rem; font-weight:700; color:#cbd5e1; margin-bottom:0.4rem;"><?= __('auth.email') ?> *</label>
                <div style="position:relative;">
                    <span class="admin-icon">✉️</span>
                    <input type="email" id="admin-email" name="email" value="<?= htmlspecialchars(old('email')) ?>" required class="admin-input-field" placeholder="admin@store.com" autofocus>
                </div>
            </div>

            <div class="admin-input-wrap" style="margin-bottom:1.25rem;">
                <label style="display:block; font-size:0.85rem; font-weight:700; color:#cbd5e1; margin-bottom:0.4rem;"><?= __('auth.password') ?> *</label>
                <div style="position:relative;">
                    <span class="admin-icon">🔒</span>
                    <input type="password" id="admin-password" name="password" value="" required class="admin-input-field" placeholder="••••••••">
                    <button type="button" class="admin-eye-btn" onclick="toggleAdminPass(this)">👁️</button>
                </div>
            </div>

            <!-- Anti-Bot CAPTCHA Challenge -->
            <?= \App\Services\CaptchaService::renderHtml() ?>

            <!-- Rate Limit & 2FA Notice -->
            <div style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 8px; padding: 0.6rem 0.85rem; font-size: 0.75rem; color: #94a3b8; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 8px;">
                <span style="color: #00d2ff;">🛡️</span>
                <span>محمية بدرع فوري ضد هجمات القوة العمياء (5 محاولات كحد أقصى).</span>
            </div>

            <button type="submit" class="btn-admin-submit">
                دخول لوحة التحكم المركزية 🚀
            </button>
        </form>

        <div style="text-align:center; margin-top:2rem; padding-top:1.25rem; border-top:1px solid rgba(255,255,255,0.08);">
            <a href="<?= url('/') ?>" style="color:#94a3b8; font-size:0.875rem; text-decoration:none; font-weight:700; transition:color 0.2s ease;" onmouseover="this.style.color='#00d2ff'" onmouseout="this.style.color='#94a3b8'">
                &larr; <?= is_rtl() ? 'العودة لواجهة المتجر الرئيسية' : 'Return to Storefront' ?>
            </a>
        </div>
    </div>

    <script>

        function toggleAdminPass(btn) {
            const pass = document.getElementById('admin-password');
            if (pass.type === 'password') {
                pass.type = 'text';
                btn.innerText = '👁️‍🗨️';
            } else {
                pass.type = 'password';
                btn.innerText = '👁️';
            }
        }

        function playAdminSound() {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                const now = ctx.currentTime;
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.type = 'sine';
                osc.frequency.setValueAtTime(600, now);
                osc.frequency.exponentialRampToValueAtTime(1200, now + 0.1);
                gain.gain.setValueAtTime(0.08, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.12);
                osc.start(now);
                osc.stop(now + 0.12);
            } catch(e) {}
        }
    </script>
</body>
</html>
