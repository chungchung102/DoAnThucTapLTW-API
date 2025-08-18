<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FilterController extends Controller
{
   public function index(Request $request)
    {
        $categories = $this->getCategories();
        $categoryId = $request->input('category', '35278'); // Mặc định là 35278 (Điện thoại)
        $filters = $this->getFilters($categoryId);
        
        // Debug: Kiểm tra categories ngay tại đây
        // dd($categories); // Uncomment để check, sau đó xóa

        Log::info('Dữ liệu danh mục và bộ lọc', [
            'categories' => $categories,
            'filters' => $filters,
            'categoryId' => $categoryId
        ]);
        
        return view('filters.index', compact('categories', 'filters', 'categoryId'));
    }
    public function apply(Request $request)
    {
        $categories = $this->getCategories();
        $categoryId = $request->input('category');
        $selectedFilters = $request->input('filters', []);
        $keyword = $request->input('keyword');

        if (empty($categoryId)) {
            Log::warning('Không có categoryId được cung cấp trong apply');
            $products = [];
        } else {
            $url = "https://demodienmay.125.atoz.vn/ww2/module.laytimkiem.web.asp?id=" . urlencode($categoryId) . "&id2=";
            
            if (!empty($selectedFilters)) {
                $filterParams = [];
                foreach ($selectedFilters as $groupId => $filterIds) {
                    foreach ($filterIds as $filterId) {
                        $filterParams[] = "id3={$filterId}";
                    }
                }
                if (!empty($filterParams)) {
                    $url .= '&' . implode('&', $filterParams);
                }
            }

            try {
                $response = Http::get($url);
                $json = $response->json()[0] ?? [];
                $products = $this->mapProductsToView($json['data'] ?? []);
            } catch (\Exception $e) {
                Log::error('apply: Gọi API thất bại', ['error' => $e->getMessage(), 'url' => $url]);
                $products = [];
            }
        }

        return view('filters.results', compact('products', 'categoryId', 'keyword', 'selectedFilters', 'categories'));
    }

   private function getCategories()
    {
        try {
            $response = Http::get("https://demodienmay.125.atoz.vn/ww2/crm.boloc.danhmuc.asp");
            $master = $response->json();
            
            $categories = [];
            $allowedIds = ['35278', '35279','35283','35285','35280']; // Chỉ id tồn tại trong API, thêm nếu có (không có 35285 v.v. trong dữ liệu)
            foreach ($master as $cat) {
                if (in_array($cat['id'], $allowedIds)) {
                    $categories[$cat['id']] = $cat['tieude']; // Giữ tieude có dấu
                    // Nếu muốn không dấu: $categories[$cat['id']] = Str::ascii($cat['tieude']);
                }
            }
            return $categories;
        } catch (\Exception $e) {
            Log::error('getCategories: Gọi API thất bại', ['error' => $e->getMessage()]);
            return [
                '35278' => 'Điện thoại',
                '35279' => 'Máy tính',
                '35285' => 'Máy Tính Bảng', // Dự phòng, dù không có trong API
                '35284' => 'Màn hình máy tính',
                '35283' => 'Máy lạnh',
                '35280' => 'Ti Vi'
            ];
        }
    }

    private function getFilters($categoryId)
    {
        if (empty($categoryId)) {
            return [];
        }
        try {
            $response = Http::get("https://demodienmay.125.atoz.vn/ww2/crm.boloc.master.asp?id=" . urlencode($categoryId));
            $filters = $response->json();
            
            if (is_array($filters)) {
                foreach ($filters as &$filter) {
                    $filter['details'] = $this->getFilterDetails($filter['id']);
                }
            }
            return is_array($filters) ? $filters : [];
        } catch (\Exception $e) {
            Log::error('getFilters: Gọi API thất bại', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function getFilterDetails($filterId)
    {
        try {
            $response = Http::get("https://demodienmay.125.atoz.vn/ww2/crm.boloc.chitiet.asp?id=" . urlencode($filterId));
            $details = $response->json()[0] ?? [];
            return $details['thamso'] ?? [];
        } catch (\Exception $e) {
            Log::error('getFilterDetails: Gọi API thất bại', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private function mapProductsToView(array $items)
    {
        return array_map(function ($item_web) {
            return [
                'id' => $item_web['id'] ?? null,
                'tieude' => $item_web['tieude'] ?? '',
                'hinhdaidien' => $item_web['hinhdaidien'] ?? '',
                'thuonghieu' => $item_web['thuonghieu'] ?? [],
                'kichcomanhinh' => $item_web['kichcomanhinh'] ?? [],
                'tinhnangdacbiet' => $item_web['tinhnangdacbiet'] ?? [],
                'hieunangvapin' => $item_web['hieunangvapin'] ?? [],
                'camera' => $item_web['camera'] ?? [],
                'bonhotrong' => $item_web['bonhotrong'] ?? [],
                'dungluongram' => $item_web['dungluongram'] ?? [],
                'tansoquet' => $item_web['tansoquet'] ?? [],
                'chipxuli' => $item_web['chipxuli'] ?? [],
                'cpu' => $item_web['cpu'] ?? [],
                'mainboard' => $item_web['mainboard'] ?? [],
                'ram' => $item_web['ram'] ?? [],
                'ocung' => $item_web['ocung'] ?? [],
                'carddohoa' => $item_web['carddohoa'] ?? [],
                'nhucau' => $item_web['nhucau'] ?? [],
                'gia' => $item_web['gia'] ?? 0,
                'giakhuyenmai' => $item_web['giakhuyenmai'] ?? 0
            ];
        }, $items);
    }
}