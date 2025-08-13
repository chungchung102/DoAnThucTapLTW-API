<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WishlistController extends Controller
{
        // Lấy số WishlistMabaogia từ API ngoài, trả về cho JS
        public function getWishlistCookie()
        {
            $response = Http::withOptions(['verify' => false])->get('https://demodienmay.125.atoz.vn/ww1/cookie.mabaogia.asp');
            $json = $response->json();
            $wishlist = null;
            if (is_array($json)) {
                foreach ($json as $item) {
                    if (isset($item['WishlistMabaogia'])) {
                        $wishlist = $item['WishlistMabaogia'];
                        break;
                    }
                }
            }
            return response()->json(['WishlistMabaogia' => $wishlist]);
        }
    public function index(Request $request)
    {
        $cookie = $request->cookie('WishlistMabaogia');
        $response = Http::withCookies([
            'WishlistMabaogia' => $cookie,
        ], 'demodienmay.125.atoz.vn')->get('https://demodienmay.125.atoz.vn/ww1/wishlisthientai.asp');

        $json = $response->json();
        $wishlist = [];
        if (!empty($json['items'])) {
            foreach ($json['items'] as $item) {
                $image = $item['image'] ?? '';
                if ($image && !str_starts_with($image, 'http')) {
                    $image = 'https://demodienmay.125.atoz.vn' . $image;
                }
                $wishlist[$item['id']] = [
                    'tieude' => $item['partName'],
                    'hinhdaidien' => $image,
                    'gia' => $item['price'],
                ];
            }
        }

        return view('wishlist.index', [
            'wishlist' => $wishlist,
            'title' => 'Danh sách yêu thích'
        ]);
    }

    public function add(Request $request, $id)
    {
        $userid = $request->user() ? $request->user()->id : null;
        $pass = $request->user() ? $request->user()->password : null;
        $cookie = $request->cookie('WishlistMabaogia');

        if ($userid && $pass) {
            $apiUrl = "https://demodienmay.125.atoz.vn/ww1/save.wishlist.asp?userid=$userid&pass=$pass&id=$id";
        } else {
            $apiUrl = "https://demodienmay.125.atoz.vn/ww1/addwishlist.asp?IDPart=$id&id=$cookie";
        }

        $response = Http::get($apiUrl);
        $json = $response->json();
        $thongbao = isset($json['thongbao']) ? strip_tags($json['thongbao']) : 'Đã thêm vào yêu thích!';
        return redirect()->back()->with('success', $thongbao);
    }

    public function remove(Request $request, $id)
    {
   $cookie = $request->input('wishlistCookie') ?: $request->cookie('WishlistMabaogia');
    $apiUrl = "https://demodienmay.125.atoz.vn/cart/xoawl.asp?IDPart=$id&id=$cookie";
    $response = Http::withOptions(['verify' => false])->get($apiUrl);

    // API trả về JavaScript object: var info = {sl: 2, ...}
    $responseBody = $response->body();
    $thongbao = 'Đã xoá khỏi yêu thích!';

    // Parse JavaScript object để lấy thongbao
    $pattern = '/var info = \{([^}]*)\};/';
    if (preg_match($pattern, $responseBody, $matches)) {
        $jsContent = $matches[1];
        $thongbaoPattern = '/thongbao:\s*[\'"]([^\'"]*)[\'"]*/';
        if (preg_match($thongbaoPattern, $jsContent, $thongbaoMatch)) {
            $thongbao = strip_tags($thongbaoMatch[1]);
        }
    }
     // Nếu là request AJAX thì trả về JSON, không redirect
    if ($request->ajax()) {
        return response()->json(['thongbao' => $thongbao]);
    }

    return redirect()->route('wishlist.index')->with('success', $thongbao);
    }
    // Lấy số lượng wishlist (API nội bộ cho JS)
    public function getCount(Request $request)
    {
        $cookie = $request->cookie('WishlistMabaogia');
        $response = Http::withCookies([
            'WishlistMabaogia' => $cookie,
        ], 'demodienmay.125.atoz.vn')->get('https://demodienmay.125.atoz.vn/ww1/wishlisthientai.asp');
        $json = $response->json();
        $count = 0;
        if (!empty($json['items'])) {
            $count = count($json['items']);
        }
        return response()->json(['count' => $count]);
    }
}
