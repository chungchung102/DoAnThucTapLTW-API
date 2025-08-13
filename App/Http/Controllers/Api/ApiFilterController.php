<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ApiFilterController extends Controller
{
    protected $productService;
    protected $apiUrls;
    protected $categories;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
        $this->apiUrls = [
                6 => 'https://bee34880885b.ngrok-free.app/api/boloccon?IDCatalog=6', 
                7 => 'https://bee34880885b.ngrok-free.app/api/boloccon?IDCatalog=7',  
                16 => 'https://bee34880885b.ngrok-free.app/api/boloccon?IDCatalog=16', 
                17 => 'https://bee34880885b.ngrok-free.app/api/boloccon?IDCatalog=17', 
        ];
        $this->categories = [
            6 => 'Máy tính',
            7 => 'Điện thoại',
            16 => 'Tivi',
            17 => 'Máy lạnh',
        ];
    }
    /**
     * Lấy danh sách bộ lọc theo danh mục
     */
    public function getFilters(Request $request)
    {
        $categoryId = $request->query('category', 6); // Mặc định là Máy tính
        if (!isset($this->apiUrls[$categoryId])) {
            return response()->json(['error' => 'Danh mục không hợp lệ'], 400);
        }

        $cacheKey = 'filters_category_' . $categoryId;
        $filters = Cache::remember($cacheKey, 86400, function () use ($categoryId) {
            $response = $this->productService->callRawPhpApi(
                ltrim(parse_url($this->apiUrls[$categoryId], PHP_URL_PATH) . '?' . parse_url($this->apiUrls[$categoryId], PHP_URL_QUERY), '/')
            );
            return $response['status'] === 'success' ? $response['data']['data'] : [];
        });

        return response()->json([
            'status' => 'success',
            'category' => $this->categories[$categoryId] ?? 'Không xác định',
            'filters' => $filters
        ]);
    }

    /**
     * Áp dụng bộ lọc và trả về sản phẩm
     */
    public function applyFilters(Request $request)
    {
        $categoryId = $request->input('category');
        $selectedFilters = $request->input('filters', []);

        $categoryMap = [
            6 => 'computer',
            7 => 'phone',
            16 => 'tv',
            17 => 'air_conditioner',
        ];
        $category = $categoryMap[$categoryId] ?? null;

        if (!$category) {
            return response()->json(['error' => 'Danh mục không hợp lệ'], 400);
        }

        // Lấy danh sách bộ lọc từ cache hoặc API
        $cacheKey = 'filters_category_' . $categoryId;
        $filters = Cache::remember($cacheKey, 86400, function () use ($categoryId) {
            if (!isset($this->apiUrls[$categoryId])) {
                return [];
            }
            $response = $this->productService->callRawPhpApi(
                ltrim(parse_url($this->apiUrls[$categoryId], PHP_URL_PATH) . '?' . parse_url($this->apiUrls[$categoryId], PHP_URL_QUERY), '/')
            );
            return $response['status'] === 'success' ? $response['data']['data'] : [];
        });

        // Tạo mảng filterData
        $filterData = [];
        foreach ($selectedFilters as $parentFilterId => $filterIds) {
            $filterData[$parentFilterId] = [];
            foreach ($filterIds as $filterId) {
                foreach ($filters as $parentFilter) {
                    if ($parentFilter['IDParentFilter'] == $parentFilterId) {
                        foreach ($parentFilter['details'] as $detail) {
                            if ($detail['IDFilter'] == $filterId) {
                                $filterData[$parentFilterId][] = [
                                    'id' => $filterId,
                                    'alias' => $detail['AlilasPath'],
                                ];
                            }
                        }
                    }
                }
            }
        }

        Log::info('Filter data before fetching products:', [
            'category' => $category,
            'filterData' => $filterData
        ]);

        // Lấy sản phẩm đã lọc
        $productData = $this->productService->fetchFilteredProducts($category, $filterData);
        $products = $productData['products'] ?? [];

        return response()->json([
            'status' => 'success',
            'category' => $this->categories[$categoryId] ?? 'Không xác định',
            'products' => $products,
            'total' => count($products)
        ]);
    }
}