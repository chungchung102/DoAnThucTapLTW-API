<?php
// app/Http/Controllers/ContactController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function show()
    {
        Log::info('ContactController::show called');
        return view('pages.contact', [
            'title' => 'Liên hệ'
        ]);
    }

    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'message' => 'required|string',
            'captcha' => 'required|in:9999', 
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'message.required' => 'Vui lòng nhập nội dung.',
            'captcha.required' => 'Vui lòng nhập mã xác nhận.',
            'captcha.in' => 'Mã xác nhận không đúng.',
        ]);

        return redirect()->back()->with('success', 'Tin nhắn của bạn đã được gửi!');
    }
}