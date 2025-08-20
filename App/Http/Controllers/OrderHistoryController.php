<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderHistoryController extends Controller
{
    public function index(Request $request)
    {
        // Đã có middleware 'auth' ở route nên không cần kiểm tra đăng nhập ở đây nữa

        // Lấy toàn bộ lịch sử đơn hàng từ session
        $history = session('order_history', []);

        // Truyền dữ liệu sang view
        return view('cart.history', [
            'purchase_history' => collect($history)
        ]);
    }
}
