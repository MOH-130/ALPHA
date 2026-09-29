@extends('layouts.storefront')

@section('content')
<div class="container" style="padding-top: 3rem; padding-bottom: 4rem;">
    <div style="max-width: 520px; margin: 0 auto;">
        <div class="auth-glass-card" style="background: rgba(13, 20, 36, 0.95); backdrop-filter: blur(25px); border: 1px solid rgba(0, 210, 255, 0.4); border-radius: 1.5rem; padding: 2.75rem 2.25rem; box-shadow: 0 25px 60px rgba(0,0,0,0.85), 0 0 35px rgba(0, 210, 255, 0.2); position: relative; overflow: hidden;">
            <!-- Top Cyber Glow Line -->
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #00d2ff, #00ff88, #ffaa00);"></div>

            <!-- Shield Icon & Title -->
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="display: inline-flex; align-items: center; justify-content: center; width: 72px; height: 72px; background: linear-gradient(135deg, rgba(0, 210, 255, 0.15), rgba(0, 255, 136, 0.15)); border: 1px solid rgba(0, 255, 136, 0.4); border-radius: 20px; font-size: 2.3rem; margin-bottom: 1rem; box-shadow: 0 0 25px rgba(0, 255, 136, 0.25);">
                    🛡️
                </div>
                <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(0, 255, 136, 0.1); border: 1px solid rgba(0, 255, 136, 0.3); border-radius: 999px; padding: 4px 14px; font-size: 0.78rem; font-weight: 800; color: #00ff88; margin-bottom: 0.75rem;">
                    <span>⚡ ALPHA 2FA ENCRYPTION</span>
                </div>
                <h1 style="font-size: 1.85rem; font-weight: 900; color: #ffffff; margin: 0 0 0.5rem;">
                    التحقق الأمني المزدوج (OTP)
                </h1>
                <p style="color: #94a3b8; font-size: 0.92rem; line-height: 1.6; margin: 0 0 10px;">
                    أدخل رمز الأمان المكون من <strong style="color: #00ff88;">6 أرقام</strong> المرسل إلى:
                    <br>
                    <span style="font-family: monospace; font-size: 1.05rem; font-weight: 700; color: #00d2ff; background: rgba(0, 210, 255, 0.1); padding: 2px 10px; border-radius: 6px; display: inline-block; margin-top: 4px;">
                        <?= htmlspecialchars($maskedEmail) ?>
                    </span>
                </p>

                <div style="margin-top: 8px;">
                    <a href="https://mail.google.com" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 6px; background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.15); color: #ffffff; border-radius: 8px; padding: 5px 12px; font-size: 0.8rem; text-decoration: none; transition: all 0.2s ease;">
                        <span>📬</span>
                        <span>فتح بريد Gmail للتحقق من الكود</span>
                    </a>
                </div>

                <div style="font-size: 0.775rem; color: #64748b; margin-top: 8px;">
                    💡 تلميح: تفقد صندوق الوارد أو مجلد الرسائل غير المرغوب فيها (Spam / Junk).
                </div>
            </div>

            <!-- Debug / Demo Helper Notice (for smooth local testing) -->
            <?php if (!empty($debugOtp)): ?>
            <div style="background: rgba(0, 255, 136, 0.12); border: 1px dashed #00ff88; border-radius: 10px; padding: 0.75rem 1rem; margin-bottom: 1.5rem; text-align: center;">
                <span style="font-size: 0.8rem; color: #94a3b8;">رمز التحقق للأغراض التجريبية (Local Sandbox):</span>
                <div style="font-family: monospace; font-size: 1.5rem; font-weight: 900; color: #00ff88; letter-spacing: 4px; margin-top: 2px;">
                    <?= $debugOtp ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- OTP Form -->
            <form action="<?= url('auth/verify-otp') ?>" method="POST" id="otp-form">
                <?= csrf_field() ?>
                <input type="hidden" name="otp_code" id="final-otp-code" value="">

                <!-- 6 Individual Digit Boxes -->
                <div style="display: flex; gap: 8px; justify-content: center; margin-bottom: 1.75rem; direction: ltr;">
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-digit-box" id="digit-<?= $i ?>" data-index="<?= $i ?>" autocomplete="off" style="width: 54px; height: 62px; text-align: center; font-size: 1.6rem; font-weight: 900; font-family: 'Cairo', monospace; background: #090e1c; border: 2px solid rgba(0, 210, 255, 0.3); border-radius: 12px; color: #00ff88; box-shadow: 0 0 15px rgba(0, 210, 255, 0.08); transition: all 0.2s ease;">
                    <?php endfor; ?>
                </div>

                <!-- Timer & Remaining Attempts Status -->
                <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 10px; padding: 0.65rem 1rem; margin-bottom: 1.75rem; font-size: 0.85rem;">
                    <div style="display: flex; align-items: center; gap: 6px; color: #94a3b8;">
                        <span>⏳ الصلاحية:</span>
                        <span id="countdown-timer" style="color: #ffaa00; font-weight: 700; font-family: monospace;">05:00</span>
                    </div>
                    <div style="color: #94a3b8;">
                        المحاولات المتبقية: <strong style="color: #00d2ff;"><?= $remainingAttempts ?></strong>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="btn-submit-otp" class="btn btn-luxury-shimmer btn-block btn-lg" style="width: 100%; height: 50px; font-weight: 800; font-size: 1.05rem;">
                    تأكيد رمز التحقق والدخول 🚀
                </button>
            </form>

            <!-- Resend Code Bar -->
            <div style="margin-top: 1.75rem; text-align: center; border-top: 1px solid rgba(255, 255, 255, 0.08); padding-top: 1.25rem;">
                <p style="color: #94a3b8; font-size: 0.875rem; margin-bottom: 0.75rem;">
                    لم يصلك رمز الأمان بعد؟
                </p>
                <form action="<?= url('auth/resend-otp') ?>" method="POST" style="display: inline-block;">
                    <?= csrf_field() ?>
                    <button type="submit" id="btn-resend-otp" class="btn" style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.15); color: #cbd5e1; border-radius: 10px; padding: 0.5rem 1.25rem; font-size: 0.875rem; cursor: pointer; transition: all 0.2s ease;">
                        🔄 إعادة إرسال رمز جديد <span id="resend-countdown" style="display: none; font-family: monospace;">(<?= $secondsRemaining ?>s)</span>
                    </button>
                </form>
            </div>

            <!-- Cancel and Return -->
            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="<?= url('logout') ?>" style="color: #64748b; font-size: 0.825rem; text-decoration: none; transition: color 0.2s ease;">
                    &larr; إلغاء وتسجيل الخروج
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const boxes = document.querySelectorAll('.otp-digit-box');
    const hiddenInput = document.getElementById('final-otp-code');
    const form = document.getElementById('otp-form');

    // Auto-focus first digit
    if (boxes[0]) boxes[0].focus();

    boxes.forEach((box, idx) => {
        // Input event
        box.addEventListener('input', function(e) {
            const val = this.value;
            if (val.length === 1 && !isNaN(val)) {
                this.style.borderColor = '#00ff88';
                this.style.boxShadow = '0 0 15px rgba(0, 255, 136, 0.3)';
                if (idx < boxes.length - 1) {
                    boxes[idx + 1].focus();
                }
            } else if (val.length === 0) {
                this.style.borderColor = 'rgba(0, 210, 255, 0.3)';
                this.style.boxShadow = 'none';
            }
            updateHiddenCode();
        });

        // Keydown backspace
        box.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && this.value === '' && idx > 0) {
                boxes[idx - 1].focus();
            }
        });

        // Paste support for 6 digits
        box.addEventListener('paste', function(e) {
            e.preventDefault();
            const text = (e.clipboardData || window.clipboardData).getData('text').trim();
            if (/^\d{6}$/.test(text)) {
                text.split('').forEach((digit, i) => {
                    if (boxes[i]) {
                        boxes[i].value = digit;
                        boxes[i].style.borderColor = '#00ff88';
                        boxes[i].style.boxShadow = '0 0 15px rgba(0, 255, 136, 0.3)';
                    }
                });
                boxes[boxes.length - 1].focus();
                updateHiddenCode();
            }
        });
    });

    function updateHiddenCode() {
        let full = '';
        boxes.forEach(b => full += b.value);
        hiddenInput.value = full;
    }

    form.addEventListener('submit', function(e) {
        updateHiddenCode();
        if (hiddenInput.value.length !== 6) {
            e.preventDefault();
            alert('يرجى إدخال الرمز المكون من 6 أرقام بالكامل.');
            return false;
        }
    });

    // 5-minute countdown timer
    let totalSec = 300;
    const timerEl = document.getElementById('countdown-timer');
    const timerInterval = setInterval(function() {
        totalSec--;
        if (totalSec <= 0) {
            clearInterval(timerInterval);
            timerEl.innerText = 'منتهي الصلاحية';
            timerEl.style.color = '#ef4444';
        } else {
            const m = Math.floor(totalSec / 60).toString().padStart(2, '0');
            const s = (totalSec % 60).toString().padStart(2, '0');
            timerEl.innerText = `${m}:${s}`;
        }
    }, 1000);

    // Resend cooldown handling
    let resendSec = <?= (int)$secondsRemaining ?>;
    const resendBtn = document.getElementById('btn-resend-otp');
    const resendCountdown = document.getElementById('resend-countdown');

    if (resendSec > 0) {
        resendBtn.disabled = true;
        resendBtn.style.opacity = '0.6';
        resendCountdown.style.display = 'inline';

        const resendInterval = setInterval(function() {
            resendSec--;
            resendCountdown.innerText = `(${resendSec}s)`;
            if (resendSec <= 0) {
                clearInterval(resendInterval);
                resendBtn.disabled = false;
                resendBtn.style.opacity = '1';
                resendCountdown.style.display = 'none';
            }
        }, 1000);
    }
});
</script>
@endsection
