<?php

namespace App\Core;

use App\Models\User;

class Auth {
    private static ?Auth $instance = null;
    private ?User $user = null;

    public static function getInstance(): Auth {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function check(): bool {
        return Session::has('user_id');
    }

    public function guest(): bool {
        return !$this->check();
    }

    public function id(): ?int {
        return Session::get('user_id');
    }

    public function user(): ?User {
        if ($this->user === null && $this->check()) {
            $this->user = User::find($this->id());
        }
        return $this->user;
    }

    public function login(User $user): void {
        $this->user = $user;
        if (session_status() === PHP_SESSION_ACTIVE) {
            @session_regenerate_id(true);
        }
        Session::set('user_id', $user->id);
        Session::set('user_role', $user->role);
        Session::set('user_name', $user->name);
        Session::set('user_email', $user->email);
    }

    public function logout(): void {
        $this->user = null;
        Session::remove('user_id');
        Session::remove('user_role');
        Session::remove('user_name');
        Session::remove('user_email');
        if (session_status() === PHP_SESSION_ACTIVE) {
            @session_regenerate_id(true);
        }
    }

    public function attempt(string $email, string $password): bool {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return false;
        }

        if (password_verify($password, $user->password)) {
            // Check status & admin approval if required
            if ($user->status === 'inactive' || $user->status === 'banned') {
                return false;
            }
            $this->login($user);
            return true;
        }
        return false;
    }

    public function isAdmin(): bool {
        if (!$this->check()) return false;
        $role = Session::get('user_role') ?? ($this->user() ? $this->user()->role : null);
        return in_array($role, ['admin', 'super_admin', 'manager']);
    }

    public function isCustomer(): bool {
        return $this->check() && !$this->isAdmin();
    }
}
