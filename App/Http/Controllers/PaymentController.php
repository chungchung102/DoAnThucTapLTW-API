<?php

/* namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function showPaymentForm()
    {
        return view('payment.form');
    }

    public function processPayment(Request $request)
    {
        // 1. Kiểm tra form đầu vào
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'sdt' => 'required|regex:/^[0-9]{10}$/',
            'address' => 'required|string|max:500',
            'payment_method' => 'required|in:cod,bank_transfer,online_payment',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // 2. Lấy giỏ hàng từ session
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->back()->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        // 3. Gửi từng sản phẩm trong giỏ hàng tới API
        foreach ($cart as $productId => $item) {
        $payload = [
                'customer_name' => $request->input('full_name'), 
                'email'         => $request->input('email'),
                'tel'           => $request->input('sdt'),
                'address'       => $request->input('address'),
                'note'          => $request->input('payment_method'),
                'idpart'        => $productId,
                'quantity'      => $item['quantity'],
                'total_price'   => $item['gia'] * $item['quantity'],
            ];


            try {
                $response = Http::withOptions(['verify' => false])
                    ->post('https://7e85a5afe201.ngrok-free.app/api/order.php', $payload);

                Log::info('Order API response', [
                    'request' => $payload,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

               if ($response->failed() || $response->json('status') !== 'success') {

                    Log::warning('Đặt hàng thất bại với sản phẩm', [
                        'product_id' => $productId,
                        'api_response' => $response->body()
                    ]);
                    return redirect()->back()->with('error', 'Gửi đơn hàng thất bại: ' . ($response->json('message') ?? 'Lỗi không xác định.'));
                }
            } catch (\Exception $e) {
                Log::error('Order API Exception', ['error' => $e->getMessage()]);
                return redirect()->back()->with('error', 'Lỗi khi gửi đơn hàng: ' . $e->getMessage());
            }
        }

        // 4. Xóa giỏ hàng khi đặt thành công
        $request->session()->forget('cart');

        return redirect()->route('payment.form')->with('success', 'Đặt hàng thành công! Đơn hàng đã được gửi.');
    }
}
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Hiển thị form đặt hàng
     */
    public function showPaymentForm()
    {
        return view('payment.form');
    }

    /**
     * Xử lý đặt hàng khi nhấn nút "Đặt hàng"
     * - Validate dữ liệu
     * - Nếu thành công: xóa giỏ hàng, hiển thị thông báo thành công
     * - Nếu thất bại: hiển thị thông báo lỗi
     */
    public function processPayment(Request $request)
    {
        // 1. Kiểm tra form đầu vào
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'sdt' => 'required|regex:/^[0-9]{10}$/',
            'address' => 'required|string|max:500',
            'payment_method' => 'required|in:cod,bank_transfer,online_payment',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // 2. Lấy giỏ hàng từ session
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->back()->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        // 3. Xử lý đơn hàng (ở đây chỉ giả lập thành công, không gọi API)
         try {
            // Có thể lưu đơn hàng vào DB tại đây nếu muốn

            // Xóa giỏ hàng sau khi đặt thành công
            $request->session()->forget('cart');

            // Thông báo thành công (sửa route về trang chủ)
            return redirect()->route('payment.form')->with('success', 
                '✔Cám ơn đã đặt hàng!<br>Đơn đặt hàng đã được chuyển đi, chúng tôi sẽ liên hệ với quý khách sớm nhất.<br>Vui lòng click <a href="'.url('/').'">vào đây</a> về trang chủ'
            );
        } catch (\Exception $e) {
            Log::error('Lỗi đặt hàng: '.$e->getMessage());
            return redirect()->back()->with('error', 'Đặt hàng thất bại. Vui lòng thử lại sau!');
        }
    }
}