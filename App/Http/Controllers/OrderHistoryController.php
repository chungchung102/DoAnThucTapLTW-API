<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrderHistoryController extends Controller
{
    public function index(Request $request)
    {
        $email = session('user_email');

        if (!$email) {
            return redirect()->route('login.form')->with('error', 'Bạn cần đăng nhập để xem lịch sử mua hàng.');
        }

        try {
            // KHÔNG có dấu cách thừa trước URL!
            $response = Http::post('https://7e85a5afe201.ngrok-free.app/api/order.history.php', [
                'email' => $email
            ]);

            // Parse JSON safely
            $data = $response->successful() ? $response->json() : [];

            // Nếu có đơn hàng
            if (is_array($data) && ($data['status'] ?? null) === 'success') {
                $orders = $data['orders'] ?? [];

                $formatted = collect($orders)->map(function ($order) {
                    return [
                        'order_id'     => $order['IDBG'] ?? null,
                        'created_at'   => $order['DateTime'] ?? null,
                        'total'        => $order['GiaTriBangBaoGia'] ?? 0,
                          'status' => $order['Status'] ?? 0,
                        'payment_data' => [
                            'full_name' => $order['CustomerName'] ?? '',
                            'email'     => $order['EmailAddress'] ?? '',
                            'sdt'       => $order['Tel'] ?? '',
                            'address'   => $order['Address'] ?? '',
                        ],
                        'items' => [
                            $order['id'] ?? 0 => [
                                'tieude'  => $order['name'] ?? 'Không rõ',
                                'gia'     => $order['price'] ?? 0,
                                'quantity'=> $order['quantity'] ?? 1,
                            ]
                        ]
                    ];
                });

                return view('cart.history', [
                    'purchase_history' => $formatted
                ]);
            }

            // Không có đơn hàng
            if (($data['status'] ?? null) === 'empty') {
                return view('cart.history', [
                    'purchase_history' => collect([])
                ]);
            }

            // Lỗi hoặc không xác định
            return back()->with('error', $data['message'] ?? 'Không tìm thấy đơn hàng.');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi lấy lịch sử: ' . $e->getMessage());
        }
    }
}
