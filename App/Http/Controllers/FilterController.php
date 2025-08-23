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

        return view('filters.results', [
            'products'        => $products,
            'categoryId'      => $categoryId,
            'selectedFilters' => $selectedFilters,
            'categories'      => $categories, // <-- phải là mảng ID => ['tieude', 'url']
        ]);
    }

    

   private function getCategories()
    {
        try {
            $response = Http::get("https://demodienmay.125.atoz.vn/ww2/crm.boloc.danhmuc.asp");
            $master = $response->json();

            $categories = [];
            $allowedIds = ['35278', '35279', '35280', '35283', '35284', '35285'];
            foreach ($master as $cat) {
                if (in_array($cat['id'], $allowedIds)) {
                    $categories[$cat['id']] = [
                        'tieude' => $cat['tieude'],
                        'url'    => $cat['url'],
                    ];
                }
            }
            return $categories;
        } catch (\Exception $e) {
            Log::error('getCategories: Gọi API thất bại', ['error' => $e->getMessage()]);
            return [
                '35278' => ['tieude' => 'Điện thoại di động', 'url' => 'mua-ban-dien-thoai-di-dong'],
                '35279' => ['tieude' => 'Máy vi tính', 'url' => 'mua-ban-may-tinh'],
                '35280' => ['tieude' => 'Tivi', 'url' => 'tivi'],
                '35283' => ['tieude' => 'Máy lạnh', 'url' => 'may-lanh'],
                '35284' => ['tieude' => 'Màn hình máy tính', 'url' => 'man-hinh-may-tinh'],
                '35285' => ['tieude' => 'Máy Tính Bảng', 'url' => 'may-tinh-bang'],
            ];
        }
    }
    
public function categorySlug($slug)
{
    $categories = $this->getCategories();
    $categoryId = null;
    foreach ($categories as $id => $cat) {
        if ($cat['url'] === $slug) {
            $categoryId = $id;
            break;
        }
    }
    if (!$categoryId) {
        abort(404, 'Không tìm thấy danh mục');
    }
    $filters = $this->getFilters($categoryId);
    // Lấy sản phẩm nếu cần...

    return view('filters.index', [
        'categories' => $categories,   // <-- BẮT BUỘC PHẢI TRUYỀN
        'categoryId' => $categoryId,
        'filters'    => $filters,
        // 'products' => $products, // nếu có
    ]);
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
            $url = $item_web['url'] ?? '';
            if (empty($url) && !empty($item_web['id'])) {
                // Lấy url từ API chi tiết nếu thiếu
                $detail = app(\App\Services\ProductService::class)->fetchProductDetail($item_web['id']);
                $url = $detail['url'] ?? '';
            }
            return [
                'id' => $item_web['id'] ?? null,
                'tieude' => $item_web['tieude'] ?? '',
                'url' => $url,
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
                'giakhuyenmai' => $item_web['giakhuyenmai'] ?? 0,
                ];
        }, $items);
    }
}