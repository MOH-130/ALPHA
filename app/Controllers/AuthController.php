<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\User;
use App\Models\Setting;
use App\Services\ActivityLogger;

class AuthController {
    public function showLogin() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            Session::flash('error', __('auth.failed'));
            return redirect(url('login'));
        }

        if ($user->is_approved == 0) {
            Session::flash('error', __('auth.account_pending_approval'));
            return redirect(url('login'));
        }

        if (Auth::getInstance()->attempt($data['email'], $data['password'])) {
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

        Session::flash('error', __('auth.failed'));
        return redirect(url('login'));
    }

    public function showAdminLogin() {
        return view('auth.admin-login');
    }

    public function adminLogin(Request $request) {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $user = User::where('email', $data['email'])->first();
        if (!$user || !in_array($user->role, ['admin', 'super_admin', 'manager'])) {
            Session::flash('error', __('auth.unauthorized_access'));
            return redirect(url('admin/login'));
        }

        if (Auth::getInstance()->attempt($data['email'], $data['password'])) {
            ActivityLogger::log('Admin Login', 'Admin', "Admin logged into dashboard: {$user->email}");
            Session::flash('success', __('messages.welcome_admin', ['name' => $user->name]));
            return redirect(url('admin/dashboard'));
        }

        Session::flash('error', __('auth.failed'));
        return redirect(url('admin/login'));
    }

    public function showRegister() {
        return view('auth.register');
    }

    public function register(Request $request) {
        $data = $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email',
            'phone' => 'required|min:8',
            'password' => 'required|min:6'
        ]);

        $existing = User::where('email', $data['email'])->first();
        if ($existing) {
            Session::flash('error', __('auth.email_already_registered'));
            return redirect(url('register'));
        }

        $requiresApproval = Setting::get('registration_requires_approval', '0') === '1';
        $affiliateCode = 'REF-' . strtoupper(substr(md5(uniqid()), 0, 6));

        $user = User::create([
            'name' => htmlspecialchars($data['name']),
            'email' => $data['email'],
            'phone' => htmlspecialchars($data['phone']),
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => 'customer',
            'status' => 'active',
            'is_approved' => $requiresApproval ? 0 : 1,
            'affiliate_code' => $affiliateCode
        ]);

        ActivityLogger::log('User Registered', 'Auth', "New customer registered: {$user->email}");

        if ($requiresApproval) {
            Session::flash('info', __('auth.registration_submitted_pending_approval'));
            return redirect(url('login'));
        }

        Auth::getInstance()->login($user);
        Session::flash('success', __('messages.account_created_success'));
        return redirect(url('account/dashboard'));
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
        $data = $request->validate(['email' => 'required|email']);
        $user = User::where('email', $data['email'])->first();
        if ($user) {
            // Simulated reset token
            Session::flash('success', __('auth.password_reset_link_sent'));
        } else {
            Session::flash('error', __('auth.email_not_found'));
        }
        return redirect(url('forgot-password'));
    }
}
