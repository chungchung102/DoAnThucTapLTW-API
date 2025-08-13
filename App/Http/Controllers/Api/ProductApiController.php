<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductApiController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    protected function hasValidImage($product)
    {
        $hinhdaidien = $product['hinhdaidien'] ?? '';
        return !empty($hinhdaidien) && strpos($hinhdaidien, 'placeholder') === false && !preg_match('/\/[0-9]+\/$/', $hinhdaidien);
    }

    public function index(Request $request)
    {
        $category = $request->query('category');
        $data = $this->productService->fetchData(100, 'all', $category);

        if (isset($data['error'])) {
            return response()->json(['error' => $data['error']], 500);
        }

        $products = array_filter($data['products'], function ($product) {
            return $this->hasValidImage($product);
        });

        return response()->json([
            'products' => array_values($products),
            'news' => array_values($data['news'] ?? [])
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function search(Request $request)
    {
        $query = $request->query('query');
        $data = $this->productService->fetchData(100, 'all');

        if (isset($data['error'])) {
            return response()->json(['error' => 'Không thể lấy dữ liệu.'], 500, [], JSON_UNESCAPED_UNICODE);
        }

        $relatedKeywords = [
            'máy tính' => ['laptop', 'máy tính xách tay', 'PC', 'máy tính để bàn', 'desktop', 'computer', 'may tinh', 'may vi tinh'],
            'điện thoại' => ['smartphone', 'điện thoại thông minh', 'mobile', 'phone', 'dien thoai'],
            'tivi' => ['tv', 'television', 'smart tv', 'tivi'],
            'máy lạnh' => ['điều hòa', 'máy điều hòa', 'air conditioner', 'may lanh'],
        ];

        $searchTerms = [$query];
        $targetCategory = null;

        foreach ($relatedKeywords as $keyword => $related) {
            if (stripos($keyword, $query) !== false || in_array(strtolower($query), array_map('strtolower', $related))) {
                if ($keyword === 'máy tính') {
                    $targetCategory = 'computer';
                } elseif ($keyword === 'điện thoại') {
                    $targetCategory = 'phone';
                } elseif ($keyword === 'tivi') {
                    $targetCategory = 'tv';
                } elseif ($keyword === 'máy lạnh') {
                    $targetCategory = 'air_conditioner';
                }
                $searchTerms = array_merge($searchTerms, $related);
            }
        }

        $products = array_filter($data['products'], function ($product) use ($searchTerms, $targetCategory) {
            if ($targetCategory && $product['category'] !== $targetCategory) {
                return false;
            }
            foreach ($searchTerms as $term) {
                if (
                    stripos($product['tieude'], $term) !== false ||
                    stripos($product['category'], $term) !== false ||
                    (isset($product['thuonghieu']) && is_array($product['thuonghieu']) && in_array($term, array_column($product['thuonghieu'], 'tengoi')))
                ) {
                    return $this->hasValidImage($product);
                }
            }
            return false;
        });

        $news = array_filter($data['news'], function ($news) use ($searchTerms) {
            foreach ($searchTerms as $term) {
                if (stripos($news['tieude'], $term) !== false) {
                    return true;
                }
            }
            return false;
        });

        return response()->json([
            'products' => array_values($products),
            'news' => array_values($news),
            'query' => $query
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function suggestions(Request $request)
    {
        $query = $request->query('query');
        $data = $this->productService->fetchData(100, 'all');

        if (isset($data['error'])) {
            return response()->json([], 200, [], JSON_UNESCAPED_UNICODE);
        }

        $suggestions = [];

        $relatedKeywords = [
            'máy tính' => ['laptop', 'máy tính xách tay', 'PC', 'máy tính để bàn', 'desktop', 'computer', 'may tinh', 'may vi tinh'],
            'điện thoại' => ['smartphone', 'điện thoại thông minh', 'mobile', 'phone', 'dien thoai'],
            'tivi' => ['tv', 'television', 'smart tv', 'tivi'],
            'máy lạnh' => ['điều hòa', 'máy điều hòa', 'air conditioner', 'may lanh'],
        ];

        $targetCategory = null;
        foreach ($relatedKeywords as $keyword => $related) {
            if (stripos($keyword, $query) !== false || in_array(strtolower($query), array_map('strtolower', $related))) {
                if ($keyword === 'máy tính') {
                    $targetCategory = 'computer';
                }
                break;
            }
        }

        $products = array_filter($data['products'], function ($product) use ($query, $targetCategory) {
            if ($targetCategory && $product['category'] !== $targetCategory) {
                return false;
            }
            return stripos($product['tieude'], $query) !== false && $this->hasValidImage($product);
        });

        foreach ($products as $product) {
            $suggestions[] = [
                'label' => $product['tieude'],
                'value' => $product['tieude'],
                'type' => 'product',
                'id' => $product['id'],
                'category' => $product['category'],
            ];
        }

        if ($targetCategory === 'computer') {
            foreach ($relatedKeywords['máy tính'] as $relKeyword) {
                $suggestions[] = [
                    'label' => $relKeyword,
                    'value' => $relKeyword,
                    'type' => 'keyword',
                    'category' => 'computer',
                ];
            }
        }

        $suggestions = array_unique($suggestions, SORT_REGULAR);
        $suggestions = array_slice($suggestions, 0, 10);

        return response()->json($suggestions, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function show($id)
    {
        $product = $this->productService->fetchProductDetail($id);

        if (isset($product['error'])) {
            return response()->json(['error' => 'Không tìm thấy sản phẩm.'], 404);
        }

        return response()->json(['product' => $product], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function refreshCache()
    {
        // Xóa cache liên quan đến sản phẩm và dữ liệu
        Cache::forget('data_all_100_all');
        Cache::forget('data_product_100_all');
        Cache::forget('product_detail_' . request()->input('id', ''));

        Log::info("Cache cleared", ['id' => request()->input('id', '')]);
        return response()->json(['message' => 'Đã làm mới dữ liệu.'], 200, [], JSON_UNESCAPED_UNICODE);
    }
}