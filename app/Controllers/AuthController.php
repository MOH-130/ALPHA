<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\User;
use App\Models\Setting;
use App\Services\ActivityLogger;
use App\Services\RateLimiter;
use App\Services\CaptchaService;
use App\Services\OtpService;
use App\Services\GoogleAuthService;

class AuthController {
    public function showLogin() {
        if (Auth::getInstance()->check()) {
            return redirect(Auth::getInstance()->isAdmin() ? url('admin/dashboard') : url('account/dashboard'));
        }
        return view('auth.login');
    }

    public function login(Request $request) {
        $ip = $request->ip();
        $rateKey = "login:ip:{$ip}";

        // 1. Rate Limiting Check
        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $seconds = RateLimiter::availableIn($rateKey);
            Session::flash('error', "تم حظر محاولات تسجيل الدخول مؤقتاً لحماية أمن الحسابات. يرجى المحاولة بعد {$seconds} ثانية.");
            return redirect(url('login'));
        }

        // 2. CAPTCHA Anti-Bot Shield Verification
        $captcha = $request->input('captcha');
        if (!CaptchaService::verify($captcha)) {
            RateLimiter::hit($rateKey, 900);
            Session::flash('error', 'رمز التحقق الأمني (CAPTCHA) غير صحيح أو انتهت صلاحيته. يرجى إعادة المحاولة.');
            return redirect(url('login'));
        }

        // 3. Input Validation
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $user = User::where('email', strtolower(trim($data['email'])))->first();
        if (!$user) {
            RateLimiter::hit($rateKey, 900);
            Session::flash('error', __('auth.failed'));
            return redirect(url('login'));
        }

        if ($user->is_approved == 0) {
            Session::flash('error', __('auth.account_pending_approval'));
            return redirect(url('login'));
        }

        if ($user->status === 'inactive' || $user->status === 'banned') {
            Session::flash('error', 'تم تجميد هذا الحساب من قبل الإدارة. يرجى التواصل مع الدعم الفني.');
            return redirect(url('login'));
        }

        if (!password_verify($data['password'], $user->password)) {
            RateLimiter::hit($rateKey, 900);
            Session::flash('error', __('auth.failed'));
            return redirect(url('login'));
        }

        // Credentials valid - Clear rate limit for this IP
        RateLimiter::clear($rateKey);

        // 4. Two-Factor Authentication (OTP) Check
        // High security: require OTP verification if unverified, 2FA enabled, or system security active
        $requireOtp = ($user->two_factor_enabled == 1)
            || empty($user->email_verified_at)
            || (Setting::get('auth_require_otp_all', '1') === '1');

        if ($requireOtp) {
            Session::set('auth_pending_user_id', $user->id);
            OtpService::generate($user, [
                'ip' => $request->ip(),
                'agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Web Browser'
            ]);
            Session::flash('info', 'تم التحقق من صحة بياناتك. 🔒 تم إرسال كود الأمان (OTP) إلى بريدك على Gmail لإتمام الدخول.');
            return redirect(url('auth/verify-otp'));
        }

        // 5. Standard Secure Login
        Auth::getInstance()->login($user);
        ActivityLogger::log('User Login', 'Auth', "User logged in: {$user->email}");
        Session::flash('success', __('messages.welcome_back', ['name' => $user->name]));

        $intended = Session::get('intended_url');
        if ($intended) {
            Session::remove('intended_url');
            return redirect($intended);
        }

        if (Auth::getInstance()->isAdmin()) {
            return redirect(url('admin/dashboard'));
        }
        return redirect(url('account/dashboard'));
    }

    public function showAdminLogin() {
        if (Auth::getInstance()->check() && Auth::getInstance()->isAdmin()) {
            return redirect(url('admin/dashboard'));
        }
        return view('auth.admin-login');
    }

    public function adminLogin(Request $request) {
        $ip = $request->ip();
        $rateKey = "admin_login:ip:{$ip}";

        // 1. Rate Limiting Check (Strict: 5 attempts = 15min lockout)
        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $seconds = RateLimiter::availableIn($rateKey);
            Session::flash('error', "تم حظر محاولات الدخول إلى بوابة الإدارة لأسباب أمنية. يرجى الانتظار {$seconds} ثانية.");
            return redirect(url('admin/login'));
        }

        // 2. CAPTCHA Verification
        $captcha = $request->input('captcha');
        if (!CaptchaService::verify($captcha)) {
            RateLimiter::hit($rateKey, 900);
            Session::flash('error', 'رمز التحقق الأمني (CAPTCHA) غير صحيح أو انتهت صلاحيته. يرجى إعادة المحاولة.');
            return redirect(url('admin/login'));
        }

        // 3. Input Validation
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $user = User::where('email', strtolower(trim($data['email'])))->first();
        if (!$user || !in_array($user->role, ['admin', 'super_admin', 'manager'])) {
            RateLimiter::hit($rateKey, 900);
            Session::flash('error', __('auth.unauthorized_access'));
            return redirect(url('admin/login'));
        }

        if (!password_verify($data['password'], $user->password)) {
            RateLimiter::hit($rateKey, 900);
            Session::flash('error', __('auth.failed'));
            return redirect(url('admin/login'));
        }

        // Clear rate limit
        RateLimiter::clear($rateKey);

        // 4. Admin 2FA OTP Check
        $requireOtp = ($user->two_factor_enabled == 1) || (Setting::get('admin_require_otp', '0') === '1');
        if ($requireOtp) {
            Session::set('auth_pending_user_id', $user->id);
            OtpService::generate($user);
            Session::flash('info', 'بوابة الإدارة محمية بنظام التحقق المزدوج (2FA). تم إرسال رمز الأمان إلى بريدك الإلكتروني.');
            return redirect(url('auth/verify-otp'));
        }

        Auth::getInstance()->login($user);
        ActivityLogger::log('Admin Login', 'Admin', "Admin logged into dashboard: {$user->email}");
        Session::flash('success', __('messages.welcome_admin', ['name' => $user->name]));
        return redirect(url('admin/dashboard'));
    }

    public function showRegister() {
        if (Auth::getInstance()->check()) {
            return redirect(url('account/dashboard'));
        }
        return view('auth.register');
    }

    public function register(Request $request) {
        $ip = $request->ip();
        $rateKey = "register:ip:{$ip}";

        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $seconds = RateLimiter::availableIn($rateKey);
            Session::flash('error', "تم تجاوز الحد المسموح به لإنشاء الحسابات. يرجى الانتظار {$seconds} ثانية.");
            return redirect(url('register'));
        }

        // CAPTCHA Verification
        $captcha = $request->input('captcha');
        if (!CaptchaService::verify($captcha)) {
            RateLimiter::hit($rateKey, 600);
            Session::flash('error', 'رمز التحقق الأمني (CAPTCHA) غير صحيح أو انتهت صلاحيته. يرجى إعادة المحاولة.');
            return redirect(url('register'));
        }

        $data = $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email',
            'phone' => 'required|min:8',
            'password' => 'required|min:6'
        ]);

        $email = strtolower(trim($data['email']));
        $existing = User::where('email', $email)->first();
        if ($existing) {
            RateLimiter::hit($rateKey, 600);
            Session::flash('error', __('auth.email_already_registered'));
            return redirect(url('register'));
        }

        RateLimiter::clear($rateKey);

        $requiresApproval = Setting::get('registration_requires_approval', '0') === '1';
        $affiliateCode = 'REF-' . strtoupper(substr(md5(uniqid()), 0, 6));

        $user = User::create([
            'name' => htmlspecialchars($data['name']),
            'email' => $email,
            'phone' => htmlspecialchars($data['phone']),
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => 'customer',
            'status' => 'pending_verification',
            'is_approved' => $requiresApproval ? 0 : 1,
            'email_verified_at' => null,
            'affiliate_code' => $affiliateCode
        ]);

        ActivityLogger::log('User Registered', 'Auth', "New customer registered: {$user->email}");

        if ($requiresApproval) {
            Session::flash('info', __('auth.registration_submitted_pending_approval'));
            return redirect(url('login'));
        }

        // High Security Protocol: Verify Email Ownership with OTP
        Session::set('auth_pending_user_id', $user->id);
        OtpService::generate($user, [
            'ip' => $request->ip(),
            'agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Web Browser'
        ]);

        Session::flash('success', 'تم إنشاء حسابك بنجاح! 🔒 أرسلنا كود التحقق الأمني المكون من 6 أرقام إلى بريدك الإلكتروني (' . OtpService::maskEmail($user->email) . '). يرجى إدخال الكود لتأكيد ملكية البريد وإتمام الدخول.');
        return redirect(url('auth/verify-otp'));
    }

    public function logout() {
        $user = Auth::getInstance()->user();
        if ($user) {
            ActivityLogger::log('User Logout', 'Auth', "User logged out: {$user->email}");
        }
        Auth::getInstance()->logout();
        Session::flash('info', __('auth.logged_out_successfully'));
        return redirect(url('/'));
    }

    public function showForgotPassword() {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request) {
        $ip = $request->ip();
        $rateKey = "pwd_reset:ip:{$ip}";

        if (RateLimiter::tooManyAttempts($rateKey, 3)) {
            $seconds = RateLimiter::availableIn($rateKey);
            Session::flash('error', "تم تجاوز عدد محاولات استرجاع كلمة المرور. يرجى الانتظار {$seconds} ثانية.");
            return redirect(url('forgot-password'));
        }

        $data = $request->validate(['email' => 'required|email']);
        $email = strtolower(trim($data['email']));
        $user = User::where('email', $email)->first();

        RateLimiter::hit($rateKey, 300);

        if ($user) {
            ActivityLogger::log('Password Reset Requested', 'Auth', "Reset requested for: {$user->email}");
            Session::flash('success', __('auth.password_reset_link_sent'));
        } else {
            Session::flash('error', __('auth.email_not_found'));
        }
        return redirect(url('forgot-password'));
    }

    // ========================================================
    // GOOGLE OAUTH 2.0 INTEGRATION
    // ========================================================

    public function googleRedirect() {
        if (GoogleAuthService::isConfigured()) {
            return redirect(GoogleAuthService::getAuthUrl());
        }

        Session::flash('error', 'خدمة تسجيل الدخول بحساب Google غير مفعلة حالياً على هذا الخادم. يرجى تسجيل الدخول بالبريد الإلكتروني أو التواصل مع إدارة الموقع.');
        return redirect(url('login'));
    }

    public function googleCallback(Request $request) {
        $oauthError = $request->getQuery('error');
        if (!empty($oauthError)) {
            // Clean up pending state from session
            GoogleAuthService::verifyState($request->getQuery('state'));
            $msg = ($oauthError === 'access_denied')
                ? 'تم إلغاء عملية تسجيل الدخول بحساب Google من قبلك.'
                : 'حدث خطأ أثناء المصادقة مع Google: ' . htmlspecialchars($oauthError);
            Session::flash('error', $msg);
            return redirect(url('login'));
        }

        $state = $request->getQuery('state');
        if (!GoogleAuthService::verifyState($state)) {
            Session::flash('error', 'فشلت عملية التحقق الأمني لمصادقة Google (CSRF State Mismatch). يرجى إعادة المحاولة من جديد.');
            return redirect(url('login'));
        }

        $code = $request->getQuery('code');
        if (empty($code)) {
            Session::flash('error', 'لم يتم استلام رمز التفويض (Authorization Code) من خادم Google.');
            return redirect(url('login'));
        }

        $tokens = GoogleAuthService::exchangeCodeForTokens($code);
        if (!$tokens || empty($tokens['access_token'])) {
            Session::flash('error', 'تعذر استبدال رمز التفويض مع خوادم Google. يرجى التحقق من إعدادات الربط أو المحاولة لاحقاً.');
            return redirect(url('login'));
        }

        $userInfo = GoogleAuthService::fetchUserInfo($tokens['access_token']);
        if (!$userInfo || empty($userInfo['id'])) {
            Session::flash('error', 'تعذر استرجاع بيانات الحساب من Google.');
            return redirect(url('login'));
        }

        if (isset($userInfo['verified']) && $userInfo['verified'] === false) {
            Session::flash('error', 'بريد Google الخاص بك غير مؤكد من قبل شركة Google. يرجى تأكيد بريدك أولاً.');
            return redirect(url('login'));
        }

        $user = GoogleAuthService::findOrCreateUser($userInfo);

        if ($user->status === 'inactive' || $user->status === 'banned') {
            Session::flash('error', 'هذا الحساب معطل أو محظور من الاستخدام.');
            return redirect(url('login'));
        }

        if (isset($user->is_approved) && (int)$user->is_approved === 0) {
            Session::flash('info', __('auth.registration_submitted_pending_approval'));
            return redirect(url('login'));
        }

        // Check if 2FA is enabled
        if ($user->two_factor_enabled == 1) {
            Session::set('auth_pending_user_id', $user->id);
            OtpService::generate($user);
            Session::flash('info', 'تم ربط الدخول بحساب Google. يرجى إدخال رمز التحقق الأمني المزدوج (2FA).');
            return redirect(url('auth/verify-otp'));
        }

        Auth::getInstance()->login($user);
        ActivityLogger::log('User Google Login', 'Auth', "User logged in via Google: {$user->email}");
        Session::flash('success', "مرحباً بك يا {$user->name}! تم تسجيل الدخول بنجاح عبر حساب Google الفعلي.");

        $intended = Session::get('intended_url');
        if ($intended) {
            Session::remove('intended_url');
            return redirect($intended);
        }

        return redirect(Auth::getInstance()->isAdmin() ? url('admin/dashboard') : url('account/dashboard'));
    }

    // ========================================================
    // 2FA ONE-TIME PASSWORD (OTP) FLOW
    // ========================================================

    public function showVerifyOtp() {
        if (!Session::has('auth_pending_user_id')) {
            return redirect(url('login'));
        }

        $userId = (int)Session::get('auth_pending_user_id');
        $user = User::find($userId);
        if (!$user) {
            Session::remove('auth_pending_user_id');
            return redirect(url('login'));
        }

        return view('auth.verify-otp', [
            'user' => $user,
            'maskedEmail' => OtpService::maskEmail($user->email),
            'secondsRemaining' => OtpService::secondsUntilResend($user),
            'remainingAttempts' => OtpService::remainingAttempts($user),
            'debugOtp' => Session::flash('_latest_otp_hint')
        ]);
    }

    public function verifyOtp(Request $request) {
        if (!Session::has('auth_pending_user_id')) {
            Session::flash('error', 'انتهت صلاحية الجلسة، يرجى تسجيل الدخول مجدداً.');
            return redirect(url('login'));
        }

        $userId = (int)Session::get('auth_pending_user_id');
        $user = User::find($userId);
        if (!$user) {
            Session::remove('auth_pending_user_id');
            return redirect(url('login'));
        }

        // Get 6-digit code (either single input or 6 box inputs)
        $code = trim((string)$request->input('otp_code'));
        if (empty($code)) {
            $digits = [];
            for ($i = 1; $i <= 6; $i++) {
                $digits[] = $request->input("digit_{$i}", '');
            }
            $code = implode('', $digits);
        }

        if (empty($code) || strlen($code) !== 6 || !ctype_digit($code)) {
            Session::flash('error', 'يرجى إدخال رمز التحقق الأمني المكون من 6 أرقام بالكامل.');
            return redirect(url('auth/verify-otp'));
        }

        if (!OtpService::verify($user, $code)) {
            $remaining = OtpService::remainingAttempts($user);
            if ($remaining <= 0) {
                Session::remove('auth_pending_user_id');
                Session::flash('error', 'تم استنفاد محاولات إدخال الرمز وإلغاء الجلسة لأسباب أمنية. يرجى تسجيل الدخول مجدداً.');
                return redirect(url('login'));
            }

            Session::flash('error', "رمز التحقق الأمني غير صحيح أو انتهت صلاحيته. تبقى لك {$remaining} محاولات.");
            return redirect(url('auth/verify-otp'));
        }

        // Success! Finalize Login
        Session::remove('auth_pending_user_id');
        Auth::getInstance()->login($user);
        ActivityLogger::log('2FA Verified', 'Auth', "User {$user->email} successfully verified OTP code");

        Session::flash('success', "تم التحقق الأمني بنجاح! مرحباً بك يا {$user->name}.");

        $intended = Session::get('intended_url');
        if ($intended) {
            Session::remove('intended_url');
            return redirect($intended);
        }

        if (Auth::getInstance()->isAdmin()) {
            return redirect(url('admin/dashboard'));
        }
        return redirect(url('account/dashboard'));
    }

    public function resendOtp() {
        if (!Session::has('auth_pending_user_id')) {
            Session::flash('error', 'الجلسة غير صالحة. يرجى تسجيل الدخول أولاً.');
            return redirect(url('login'));
        }

        $userId = (int)Session::get('auth_pending_user_id');
        $user = User::find($userId);
        if (!$user) {
            Session::remove('auth_pending_user_id');
            return redirect(url('login'));
        }

        if (!OtpService::canResend($user)) {
            $sec = OtpService::secondsUntilResend($user);
            Session::flash('error', "يرجى الانتظار {$sec} ثانية قبل طلب رمز أمني جديد.");
            return redirect(url('auth/verify-otp'));
        }

        OtpService::generate($user, [
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Web Browser'
        ]);
        Session::flash('success', 'تم إرسال رمز تحقق أمني جديد إلى بريدك الإلكتروني بنجاح.');
        return redirect(url('auth/verify-otp'));
    }

    public function requestOtpLogin(Request $request) {
        $ip = $request->ip();
        $rateKey = "otp_request:ip:{$ip}";

        if (RateLimiter::tooManyAttempts($rateKey, 5)) {
            $seconds = RateLimiter::availableIn($rateKey);
            Session::flash('error', "تم تجاوز الحد المسموح به لطلبات الأمان. يرجى الانتظار {$seconds} ثانية.");
            return redirect(url('login'));
        }

        $email = strtolower(trim((string)$request->input('email')));
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'يرجى إدخال بريد إلكتروني صحيح.');
            return redirect(url('login'));
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            RateLimiter::hit($rateKey, 600);
            Session::flash('error', 'هذا البريد الإلكتروني غير مسجل لدينا. يمكنك إنشاء حساب جديد خلال 30 ثانية.');
            return redirect(url('register'));
        }

        if ($user->status === 'inactive' || $user->status === 'banned') {
            Session::flash('error', 'هذا الحساب مجمد من قبل الإدارة. يرجى التواصل مع الدعم الفني.');
            return redirect(url('login'));
        }

        RateLimiter::hit($rateKey, 60);

        Session::set('auth_pending_user_id', $user->id);
        OtpService::generate($user, [
            'ip' => $request->ip(),
            'agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Web Browser'
        ]);

        Session::flash('info', 'تم إرسال كود التحقق الأمني (OTP) إلى بريدك الإلكتروني (' . OtpService::maskEmail($user->email) . '). يرجى إدخال الرمز لتسجيل الدخول.');
        return redirect(url('auth/verify-otp'));
    }

    // ========================================================
    // CAPTCHA LIVE REFRESH
    // ========================================================

    public function refreshCaptcha() {
        $captcha = CaptchaService::generate();
        return Response::json([
            'success' => true,
            'question' => $captcha['question']
        ]);
    }
}
