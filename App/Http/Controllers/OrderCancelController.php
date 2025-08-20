<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderCancelController extends Controller
{
    public function cancel(Request $request)
    {
        // Kiểm tra đăng nhập nếu không dùng middleware
        if (!session('user_email')) {
            return redirect()->route('login.form')->with('error', 'Bạn phải đăng nhập để huỷ đơn hàng!');
        }

        $request->validate([
            'order_id' => 'required'
        ]);

        $history = session('order_history', []);
        $newHistory = [];

        $found = false;
        foreach ($history as $order) {
            if ($order['order_id'] == $request->order_id) {
                $found = true;
                continue; // Bỏ qua đơn này (huỷ)
            }
            $newHistory[] = $order;
        }

        session(['order_history' => $newHistory]);

        if ($found) {
            return back()->with('success', 'Huỷ đơn hàng thành công!');
        } else {
            return back()->with('error', 'Không tìm thấy đơn hàng để huỷ.');
        }
    }
}
