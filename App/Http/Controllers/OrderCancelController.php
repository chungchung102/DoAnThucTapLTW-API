<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderCancelController extends Controller
{
    public function cancel(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer'
        ]);

        try {
            $deleted = DB::table('GDC_Quotation_BangBaoGia')
                ->where('IDBG', $request->order_id)
                ->where('TinhTrangXacNhan', 0)
                ->delete(); 

            if ($deleted > 0) {
                return back()->with('success', 'Huỷ đơn hàng thành công!');
            }

            return back()->with('error', 'Không thể huỷ đơn hàng. Có thể đơn đã được xác nhận.');
        } catch (\Exception $e) {
            Log::error('Huỷ đơn hàng lỗi: ' . $e->getMessage());
            return back()->with('error', 'Đã xảy ra lỗi khi huỷ đơn hàng.');
        }
    }
}
