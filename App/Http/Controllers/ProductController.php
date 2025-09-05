<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    protected function hasValidImage($product)
    {
        return !empty($product['hinhdaidien']) && strpos($product['hinhdaidien'], 'placeholder') === false;
    }

    public function index(Request $request)
    {
        $category = $request->query('category');
        $data = $this->productService->fetchData(100, 'all', $category);

        $data['products'] = array_filter($data['products'] ?? [], function ($product) use ($category) {
            if (!empty($category) && $product['category'] !== $category) {
                return false;
            }
            return $this->hasValidImage($product);
        });

        return view('products.index', [
            'data' => ['products' => array_values($data['products'])],
            'news' => array_values($data['news'] ?? []),
            'title' => 'Trang chủ'
        ]);
    }
public function search(Request $request)
{
    $query = $request->query('query');
    $data = $this->productService->fetchData(100, 'all');

    if (isset($data['error'])) {
        return view('products.index', [
            'data' => $data,
            'news' => [],
            'title' => 'Kết quả tìm kiếm: ' . $query
        ]);
    }

    // Danh sách từ khóa liên quan
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
                $targetCategory = 'computer'; // Gắn danh mục máy tính
            }
            $searchTerms = array_merge($searchTerms, $related);
        }
    }

    $data['products'] = array_filter($data['products'], function ($product) use ($searchTerms, $targetCategory) {
        if ($targetCategory && $product['category'] !== $targetCategory) {
            return false; // Chỉ lấy sản phẩm thuộc danh mục "computer"
        }
        foreach ($searchTerms as $term) {
            if (
                stripos($product['tieude'], $term) !== false ||
                stripos($product['category'], $term) !== false ||
                (isset($product['thuonghieu']) && is_array($product['thuonghieu']) && in_array($term, array_column($product['thuonghieu'], 'ten')))
            ) {
                return $this->hasValidImage($product);
            }
        }
        return false;
    });

    $data['news'] = array_filter($data['news'], function ($news) use ($searchTerms) {
        foreach ($searchTerms as $term) {
            if (stripos($news['tieude'], $term) !== false) {
                return true;
            }
        }
        return false;
    });

    return view('products.index', [
        'data' => ['products' => array_values($data['products'])],
        'news' => array_values($data['news']),
        'title' => 'Kết quả tìm kiếm: ' . $query
    ]);
}
public function show(Request $request, $id)
{
    // Lấy fallback danh mục từ Service
    $fallbackCategory = $this->productService->getCategoryFromProductId($id);

    // Gọi chi tiết sản phẩm
    $product = $this->productService->fetchProductDetail($id, $fallbackCategory);

    if (isset($product['error'])) {
        abort(404, 'Không tìm thấy sản phẩm.');
    }

    // Nếu có slug/url thì redirect sang URL SEO-friendly
    if (!empty($product['url'])) {
        return redirect('/' . $product['url']);
    }

    // Nếu không có slug thì vẫn render như cũ (trường hợp hiếm)
    $category = $product['category'] ?? $fallbackCategory;
    $relatedData = $this->productService->fetchData(100, 'product', $category);

    // Sửa lại điều kiện lọc:
    $relatedProducts = array_filter($relatedData['products'] ?? [], function ($p) use ($id) {
        return $p['id'] !== $id;
    });

    Log::debug('Danh mục sản phẩm hiện tại:', ['category' => $category]);
    Log::debug('Số sản phẩm liên quan sau khi lọc:', ['count' => count($relatedProducts)]);

    return view('products.show', [
        'product' => $product,
        'relatedProducts' => array_slice(array_values($relatedProducts), 0, 4),
        'title' => $product['tieude'] ?? 'Chi tiết sản phẩm'
    ]);
}
public function showBySlug($slug)
{
    Log::debug('showBySlug called', ['slug' => $slug]);
    $product = app(\App\Services\ProductService::class)->fetchProductBySlug($slug);
    if (!$product) abort(404, 'Không tìm thấy sản phẩm');

    // Debug: Log thông tin sản phẩm
    Log::debug('Product data in showBySlug:', [
        'id' => $product['id'] ?? 'N/A',
        'category' => $product['category'] ?? 'N/A',
        'title' => $product['tieude'] ?? 'N/A'
    ]);

    // Lấy sản phẩm liên quan cùng danh mục - Sử dụng fallback category nếu cần
    $category = $product['category'] ?? 'computer'; // Fallback to computer category
    $relatedProducts = [];
    
    $relatedData = $this->productService->fetchData(100, 'product', $category);
    
    // Debug: Log dữ liệu raw
    Log::debug('Related data raw:', [
        'category' => $category,
        'products_count' => count($relatedData['products'] ?? [])
    ]);
    
    // Nới lỏng điều kiện lọc
    $relatedProducts = array_filter($relatedData['products'] ?? [], function ($p) use ($product) {
        return $p['id'] !== $product['id'];
    });
    
    // Debug: Log kết quả sau lọc
    Log::debug('Filtered related products:', [
        'count' => count($relatedProducts),
        'sample_titles' => array_slice(array_column($relatedProducts, 'tieude'), 0, 3)
    ]);

    return view('products.show', [
        'product' => $product,
        'relatedProducts' => array_slice(array_values($relatedProducts), 0, 4),
        'title' => $product['tieude'] ?? 'Chi tiết sản phẩm'
    ]);
}


   public function refreshCache(Request $request)
{
    $id = $request->input('id');
    if ($id) {
        Cache::forget('product_detail_' . $id);
    }

    Cache::forget('data_all_100_all');
    Cache::forget('data_product_100_all');

    return redirect()->route('products.index')->with('success', 'Đã làm mới dữ liệu.');
}

}
