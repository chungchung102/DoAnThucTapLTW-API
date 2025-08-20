<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->only(['showLoginForm', 'showRegisterForm']);
    }

    public function showLoginForm()
    {
        if (auth()->check()) {
            return redirect()->route('products.index')->with('info', 'Bạn đã đăng nhập!');
        }
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        if (auth()->check()) {
            return redirect()->route('products.index')->with('info', 'Bạn đã đăng nhập. Không cần đăng ký thêm!');
        }
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $apiUrl = 'https://demodienmay.125.atoz.vn/ww1/userlogin.asp';

        try {
            $response = Http::withOptions(['verify' => false])
                ->get($apiUrl, [
                    'userid' => $validatedData['email'],
                    'pass' => $validatedData['password'],
                ]);

            Log::info('Login API response', ['status' => $response->status(), 'body' => $response->body()]);

            if ($response->successful()) {
                $body = $response->body();
                try {
                    $data = json_decode($body, true);
                    Log::info('Login API response decoded', $data);

                    if (isset($data[0]['maloi']) && $data[0]['maloi'] === '1' && isset($data[0]['user']) && $data[0]['user'] !== '0' && isset($data[0]['memberid']) && $data[0]['memberid'] !== '0') {
                        Session::put('user_email', $validatedData['email']);
                        Session::put('user_id', $data[0]['user']);
                        Session::put('user_name', $data[0]['tenkh'] ?? $validatedData['email']);

                        if ($request->expectsJson()) {
                            return response()->json([
                                'status' => 'success',
                                'message' => 'Đăng nhập thành công!',
                                'data' => [
                                    'user_id' => $data[0]['user'],
                                    'member_id' => $data[0]['memberid'],
                                    'email' => $validatedData['email'],
                                    'name' => $data[0]['tenkh'] ?? $validatedData['email'],
                                ],
                            ], 200);
                        }
                        return redirect()->route('products.index')->with('success', 'Đăng nhập thành công!');
                    } else {
                        $errorMessage = $data[0]['ThongBao'] ?? 'Đăng nhập thất bại. Vui lòng kiểm tra email/mật khẩu hoặc kích hoạt tài khoản.';
                        if ($request->expectsJson()) {
                            return response()->json(['status' => 'error', 'message' => $errorMessage], 401);
                        }
                        return back()->withErrors(['email' => $errorMessage]);
                    }
                } catch (\Exception $e) {
                    Log::error('JSON decode error', ['error' => $e->getMessage(), 'body' => $body]);
                    if ($request->expectsJson()) {
                        return response()->json(['status' => 'error', 'message' => 'Phản hồi API không hợp lệ.'], 500);
                    }
                    return back()->withErrors(['email' => 'Phản hồi API không hợp lệ.']);
                }
            }

            $errorMessage = 'Không thể kết nối đến API. Mã trạng thái: ' . $response->status();
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => $errorMessage], $response->status());
            }
            return back()->withErrors(['email' => $errorMessage]);
        } catch (\Exception $e) {
            Log::error('API login exception', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()], 500);
            }
            return back()->withErrors(['email' => 'Đã xảy ra lỗi: ' . $e->getMessage()]);
        }
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'password' => 'required|min:8|confirmed',
        ]);

        $apiUrl = 'https://demodienmay.125.atoz.vn/ww1/userlogin.asp';

        try {
            $response = Http::withOptions(['verify' => false])
                ->get($apiUrl, [
                    'id2' => 'Chophepdangky',
                    'loaithanhvien' => 1,
                    'tenkh' => $validatedData['name'],
                    'email' => $validatedData['email'],
                    'userid' => $validatedData['email'],
                    'pass' => $validatedData['password'],
                    'tel' => $validatedData['phone'],
                ]);

            Log::info('Register API response', ['status' => $response->status(), 'body' => $response->body()]);

            if ($response->successful()) {
                $body = $response->body();
                try {
                    $data = json_decode($body, true);
                    Log::info('Register API response decoded', $data);

                    if (isset($data[0]['maloi']) && $data[0]['maloi'] === '0' && isset($data[0]['user']) && $data[0]['user'] !== '0' && isset($data[0]['memberid']) && $data[0]['memberid'] !== '0') {
                        Session::put('user_email', $validatedData['email']);
                        Session::put('user_id', $data[0]['user']);
                        Session::put('user_name', $validatedData['name']);

                        if ($request->expectsJson()) {
                            return response()->json([
                                'status' => 'success',
                                'message' => 'Đăng ký thành công! Vui lòng kiểm tra email để kích hoạt tài khoản.',
                                'data' => [
                                    'user_id' => $data[0]['user'],
                                    'member_id' => $data[0]['memberid'],
                                    'email' => $validatedData['email'],
                                    'name' => $validatedData['name'],
                                ],
                            ], 201);
                        }
                        return redirect()->route('login.form')->with('success', 'Đăng ký thành công! Vui lòng kiểm tra email để kích hoạt tài khoản.');
                    } else {
                        $errorMessage = $data[0]['ThongBao'] ?? 'Đăng ký thất bại. Vui lòng kiểm tra thông tin hoặc liên hệ hỗ trợ.';
                        if (strpos($errorMessage, 'Tài khoản chưa kích hoạt') !== false) {
                            $errorMessage .= ' Vui lòng kiểm tra email (bao gồm thư mục spam) hoặc liên hệ hỗ trợ.';
                        }
                        if ($request->expectsJson()) {
                            return response()->json(['status' => 'error', 'message' => $errorMessage], 400);
                        }
                        return back()->withErrors(['email' => $errorMessage]);
                    }
                } catch (\Exception $e) {
                    Log::error('JSON decode error', ['error' => $e->getMessage(), 'body' => $body]);
                    if ($request->expectsJson()) {
                        return response()->json(['status' => 'error', 'message' => 'Phản hồi API không hợp lệ.'], 500);
                    }
                    return back()->withErrors(['email' => 'Phản hồi API không hợp lệ.']);
                }
            }

            $errorMessage = 'Không thể kết nối đến API. Mã trạng thái: ' . $response->status();
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => $errorMessage], $response->status());
            }
            return back()->withErrors(['email' => $errorMessage]);
        } catch (\Exception $e) {
            Log::error('API register exception', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()], 500);
            }
            return back()->withErrors(['email' => 'Đã xảy ra lỗi: ' . $e->getMessage()]);
        }
    }

    public function logout(Request $request)
    {
        Log::info('Logout called', [
            'session_before' => session()->all(),
            'cookie_WishlistMabaogia' => $request->cookie('WishlistMabaogia'),
        ]);
        // Xóa toàn bộ session và đăng xuất user
        Session::flush();
        if (auth()->check()) {
            auth()->logout();
        }
        // Xóa cookie session và WishlistMabaogia
        $response = redirect()->route('login.form')->with('success', 'Đăng xuất thành công!');
        $response->headers->clearCookie('WishlistMabaogia', '/');
        $response->headers->clearCookie(config('session.cookie'), '/');
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Đăng xuất thành công!',
            ], 200)
            ->withCookie(cookie()->forget('WishlistMabaogia'))
            ->withCookie(cookie()->forget(config('session.cookie')));
        }
        Log::info('Logout finished', [
            'session_after' => session()->all(),
        ]);
        return $response;
    }
}