<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\Product;
use App\Models\User;
use App\Services\ImageOptimizer;

class CustomerController {
    public function dashboard(Request $request) {
        $user = Auth::getInstance()->user();
        $recentOrders = Order::where('user_id', $user->id)->orderByDesc('id')->limit(5)->get();
        $totalOrders = Order::where('user_id', $user->id)->count();
        $pendingOrders = Order::where('user_id', $user->id)->where('status', 'pending')->count();
        $completedOrders = Order::where('user_id', $user->id)->where('status', 'completed')->count();

        return view('customer.dashboard', compact('user', 'recentOrders', 'totalOrders', 'pendingOrders', 'completedOrders'));
    }

    public function orders(Request $request) {
        $user = Auth::getInstance()->user();
        $orders = Order::where('user_id', $user->id)->orderByDesc('id')->paginate(10);

        return view('customer.orders', compact('user', 'orders'));
    }

    public function orderDetails(Request $request, int $id) {
        $user = Auth::getInstance()->user();
        $order = Order::where('id', $id)->where('user_id', $user->id)->first();
        if (!$order) {
            return redirect(url('account/orders'));
        }

        $items = $order->items();
        $history = $order->history();

        return view('customer.order-details', compact('user', 'order', 'items', 'history'));
    }

    public function profile(Request $request) {
        $user = Auth::getInstance()->user();

        if ($request->isPost()) {
            $data = $request->validate([
                'name' => 'required|min:3',
                'email' => 'required|email',
                'phone' => 'required'
            ]);

            $updateData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $request->input('address', ''),
                'city' => $request->input('city', ''),
                'country' => $request->input('country', 'Algeria'),
                'two_factor_enabled' => $request->input('two_factor_enabled') ? 1 : 0
            ];

            if ($request->input('password')) {
                if (strlen($request->input('password')) < 6) {
                    Session::flash('error', __('validation.min', ['attribute' => 'password', 'min' => 6]));
                    return redirect(url('account/profile'));
                }
                $updateData['password'] = password_hash($request->input('password'), PASSWORD_DEFAULT);
            }

            $user->update($updateData);
            Session::flash('success', __('messages.profile_updated_successfully'));
            return redirect(url('account/profile'));
        }

        return view('customer.profile', compact('user'));
    }

    public function returns(Request $request) {
        $user = Auth::getInstance()->user();
        $returns = ReturnRequest::where('user_id', $user->id)->orderByDesc('id')->get();
        $eligibleOrders = Order::where('user_id', $user->id)->whereIn('status', ['shipped', 'completed'])->get();

        return view('customer.returns', compact('user', 'returns', 'eligibleOrders'));
    }

    public function storeReturn(Request $request) {
        $user = Auth::getInstance()->user();
        $data = $request->validate([
            'order_id' => 'required|numeric',
            'reason' => 'required|min:3',
            'details' => 'required|min:5'
        ]);

        $order = Order::where('id', $data['order_id'])->where('user_id', $user->id)->first();
        if (!$order) {
            Session::flash('error', __('messages.order_not_found'));
            return redirect(url('account/returns'));
        }

        $imageNames = [];
        if ($request->hasFile('images')) {
            $uploaded = ImageOptimizer::processAndSave($request->file('images'), __DIR__ . '/../../public/uploads/returns');
            if ($uploaded) {
                $imageNames[] = $uploaded;
            }
        }

        ReturnRequest::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'reason' => htmlspecialchars($data['reason']),
            'details' => htmlspecialchars($data['details']),
            'images_json' => json_encode($imageNames),
            'status' => 'pending',
            'refund_amount' => $order->total_amount
        ]);

        Session::flash('success', __('messages.return_request_submitted'));
        return redirect(url('account/returns'));
    }

    public function affiliate(Request $request) {
        $user = Auth::getInstance()->user();

        // Generate affiliate code if doesn't have one
        if (empty($user->affiliate_code)) {
            $code = 'REF-' . strtoupper(substr(md5($user->id . time()), 0, 6));
            $user->update(['affiliate_code' => $code]);
        }

        $referralUrl = url('/', ['ref' => $user->affiliate_code]);
        $commissions = $user->affiliateCommissions();
        $totalEarned = 0.0;
        $pendingPayout = 0.0;
        $paidPayout = 0.0;

        foreach ($commissions as $c) {
            if ($c->status === 'approved' || $c->status === 'paid') {
                $totalEarned += (float)$c->commission_amount;
            }
            if ($c->status === 'pending') {
                $pendingPayout += (float)$c->commission_amount;
            }
            if ($c->status === 'paid') {
                $paidPayout += (float)$c->commission_amount;
            }
        }

        return view('customer.affiliate', compact('user', 'referralUrl', 'commissions', 'totalEarned', 'pendingPayout', 'paidPayout'));
    }
}
