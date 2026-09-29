<?php

namespace App\Services;

use App\Core\Session;

class CaptchaService {
    public static function generate(): array {
        Session::getInstance();

        $num1 = random_int(4, 25);
        $num2 = random_int(2, 15);
        $ops = ['+', '-', 'x'];
        $op = $ops[random_int(0, count($ops) - 1)];

        if ($op === '+') {
            $answer = $num1 + $num2;
            $text = "{$num1} + {$num2}";
        } elseif ($op === '-') {
            if ($num1 < $num2) {
                [$num1, $num2] = [$num2, $num1];
            }
            $answer = $num1 - $num2;
            $text = "{$num1} - {$num2}";
        } else {
            // multiplication with smaller numbers
            $num1 = random_int(2, 9);
            $num2 = random_int(2, 9);
            $answer = $num1 * $num2;
            $text = "{$num1} × {$num2}";
        }

        // Store hashed answer and plain in session with 10-minute expiry
        $_SESSION['_captcha_answer'] = (string)$answer;
        $_SESSION['_captcha_expires'] = time() + 600;

        return [
            'question' => $text,
            'answer' => $answer
        ];
    }

    public static function verify(?string $input): bool {
        Session::getInstance();

        if (empty($input) && $input !== '0') {
            return false;
        }

        $expected = $_SESSION['_captcha_answer'] ?? null;
        $expires = $_SESSION['_captcha_expires'] ?? 0;

        if ($expected === null || time() > $expires) {
            return false;
        }

        $isValid = trim((string)$input) === (string)$expected;

        // Invalidate after attempt so it cannot be re-used
        unset($_SESSION['_captcha_answer'], $_SESSION['_captcha_expires']);

        return $isValid;
    }

    public static function getQuestion(): string {
        Session::getInstance();
        if (empty($_SESSION['_captcha_answer']) || time() > ($_SESSION['_captcha_expires'] ?? 0)) {
            $data = self::generate();
            return $data['question'];
        }
        return $_SESSION['_captcha_question'] ?? (self::generate()['question']);
    }

    public static function renderHtml(): string {
        $data = self::generate();
        $q = htmlspecialchars($data['question']);

        return <<<HTML
        <div class="form-group captcha-container" style="margin-bottom: 1.25rem;">
            <label class="form-label" style="display:flex; justify-content:space-between; align-items:center; font-size:0.875rem;">
                <span>🔒 رمز التحقق الأمني (CAPTCHA) *</span>
                <span style="font-size:0.75rem; color:#94a3b8;">درع الحماية ضد الروبوتات</span>
            </label>
            <div style="display:flex; gap:10px; align-items:center;">
                <div id="captcha-badge" style="background:#090d18; border:1px solid rgba(0, 210, 255, 0.4); border-radius:10px; padding:0.6rem 1.2rem; font-family:'Cairo', monospace; font-size:1.15rem; font-weight:900; color:#00ff88; letter-spacing:2px; user-select:none; box-shadow:0 0 15px rgba(0, 255, 136, 0.15); display:inline-flex; align-items:center; gap:8px;">
                    <span id="captcha-question-text">{$q} = ?</span>
                </div>
                <button type="button" onclick="refreshCaptcha()" title="تحديث المسألة" style="background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15); color:#cbd5e1; border-radius:10px; width:44px; height:44px; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:1.2rem; transition:all 0.2s ease;">
                    🔄
                </button>
                <div style="flex:1;">
                    <input type="text" name="captcha" id="captcha-input" class="form-control" placeholder="اكتب الناتج هنا" required autocomplete="off" style="height:44px; background:#131c2e; border:1px solid rgba(255,255,255,0.12); color:#ffffff; border-radius:10px; font-weight:700; font-size:1rem; text-align:center;">
                </div>
            </div>
        </div>
        <script>
        function refreshCaptcha() {
            fetch('/captcha/refresh')
                .then(r => r.json())
                .then(d => {
                    const el = document.getElementById('captcha-question-text');
                    if (el) el.innerText = d.question + ' = ?';
                    const inp = document.getElementById('captcha-input');
                    if (inp) {
                        inp.value = '';
                        inp.focus();
                    }
                })
                .catch(() => {});
        }
        </script>
HTML;
    }
}
