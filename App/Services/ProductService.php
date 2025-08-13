<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProductService
{
    protected $apiBase;
    protected $baseImageUrl;
    protected $defaultImage;

    protected $productIds = [
        'computer' => 35279,
        'phone' => 35278,
        'tv' => 35280,
        'air_conditioner' => 35283,
    ];

    protected $newsId = 35139;

    public function __construct()
    {
        $this->apiBase = env('API_BASE_URL', 'https://demochung.125.atoz.vn/ww2/');
        $this->baseImageUrl = env('BASE_IMAGE_URL', 'https://choixanh.com.vn/');
        $this->defaultImage = env('DEFAULT_IMAGE_URL', 'https://via.placeholder.com/300x200?text=No+Image');
    }

    protected function fixImageUrl($url)
    {
        if (empty($url) || preg_match('/\/[0-9]+\/$/', $url)) {
            return $this->defaultImage;
        }
        return (!str_starts_with($url, 'http'))
            ? $this->baseImageUrl . ltrim($url, '/')
            : $url;
    }

    protected function callRawPhpApi(string $endpoint)
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'User-Agent' => 'Laravel-Client',
                'ngrok-skip-browser-warning' => 'true',
            ])->timeout(15)->get($this->apiBase . $endpoint);

            if ($response->successful()) {
                Log::info("API call successful", ['endpoint' => $endpoint, 'status' => $response->status()]);
                return $response->json();
            }

            Log::error("API call failed", ['endpoint' => $endpoint, 'status' => $response->status()]);
        } catch (\Exception $e) {
            Log::error("Exception when calling API", ['endpoint' => $endpoint, 'error' => $e->getMessage()]);
        }

        return null;
    }

    public function fetchData($limit = 100, $type = 'all', $category = null)
    {
        $cacheKey = "data_{$type}_{$limit}_" . ($category ?? 'all');
        if (Cache::has($cacheKey)) {
            Log::info("Returning cached data", ['cacheKey' => $cacheKey]);
            return Cache::get($cacheKey);
        }

        $products = [];
        $news = [];

        if ($type === 'all' || $type === 'product') {
            foreach ($this->productIds as $cat => $id) {
                if ($category && $cat !== $category) continue;

                $result = $this->callRawPhpApi("module.sanpham.asp?id={$id}&sl={$limit}");

                if (!is_array($result) || !isset($result[0]['data']) || !is_array($result[0]['data'])) {
                    Log::error("Failed to fetch products for category", ['category' => $cat, 'id' => $id]);
                    continue;
                }

                foreach ($result[0]['data'] as &$item) {
                    $item['category'] = $cat;
                    $item['hinhdaidien'] = $this->fixImageUrl($item['hinhdaidien'] ?? null);
                    $item['hinhanh'] = array_filter($item['hinhanh'] ?? [], fn($img) => !empty($img['hinhdaidien']) && !preg_match('/\/[0-9]+\/$/', $img['hinhdaidien']));
                }

                $products = array_merge($products, $result[0]['data']);
            }
        }

        if ($type === 'all' || $type === 'news') {
            $result = $this->callRawPhpApi("module.tintuc.asp?id={$this->newsId}&sl={$limit}");

            if (!is_array($result) || !isset($result[0]['data']) || !is_array($result[0]['data'])) {
                Log::error("Failed to fetch news from API");
            } else {
                foreach ($result[0]['data'] as &$item) {
                    $item['hinhdaidien'] = $this->fixImageUrl($item['hinhdaidien'] ?? null);
                }
                $news = $result[0]['data'];
            }
        }

        $response = [
            'products' => array_values($products),
            'news' => array_values($news)
        ];

        if (empty($response['products']) && empty($response['news'])) {
            Log::warning("API returned empty data", ['limit' => $limit, 'type' => $type, 'category' => $category]);
            return ['error' => 'Không có dữ liệu từ API.'];
        }

        Cache::put($cacheKey, $response, 86400);
        Log::info("Cached new data", ['cacheKey' => $cacheKey]);
        return $response;
    }

    public function fetchProductDetail($productId, $fallbackCategory = null)
    {
        $cacheKey = "product_detail_{$productId}";
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $detail = $this->callRawPhpApi("module.sanpham.chitiet.asp?id={$productId}");
        $images = $this->callRawPhpApi("tinhnang.hinhanh.idpart.php?id={$productId}");

        if (isset($detail[0])) {
            $item = $detail[0];
            $item['hinhdaidien'] = $this->fixImageUrl($item['hinhdaidien'] ?? null);
            $item['category'] = $item['category'] ?? $fallbackCategory;

            if (isset($images[0]['data']) && is_array($images[0]['data'])) {
                $item['hinhlienquan'] = array_map(function ($img) {
                    $img['hinhdaidien'] = $this->fixImageUrl($img['hinhdaidien']);
                    return $img;
                }, array_filter($images[0]['data'], fn($img) => !empty($img['hinhdaidien'])));
            } else {
                $item['hinhlienquan'] = [];
            }

            Cache::put($cacheKey, $item, 86400);
            return $item;
        }

        return ['error' => 'Không thể lấy chi tiết sản phẩm.'];
    }

    public function fetchNewsDetail($newsId)
    {
        $cacheKey = "news_detail_{$newsId}";
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $detail = $this->callRawPhpApi("module.tintuc.chitiet.asp?id={$newsId}");

        if (isset($detail[0])) {
            $item = $detail[0];
            $item['hinhdaidien'] = $this->fixImageUrl($item['hinhdaidien'] ?? null);
            Cache::put($cacheKey, $item, 86400);
            return $item;
        }

        return ['error' => 'Không thể lấy chi tiết tin tức.'];
    }

    public function getCategoryFromProductId($id)
    {
        foreach ($this->productIds as $category => $catId) {
            if ($catId == $id) return $category;
        }
        return null;
    }

    public function fetchFilteredProducts($category, $filterData)
    {
        $data = $this->fetchData(100, 'product', $category);
        $products = $data['products'] ?? [];
        $filteredProducts = [];

        $fieldMaps = [
            'computer' => [
                '32031' => 'thuonghieu',
                '32032' => 'cpu',
                '32033' => 'ram',
                '32034' => 'ocung',
                '32035' => 'carddohoa',
                '32036' => 'nhucau',
                '32111' => 'kichcomanhinh',
                '32117' => 'mainboard',
            ],
            'phone' => [
                '32017' => 'thuonghieu',
                '32018' => 'kichcomanhinh',
                '32037' => 'tinhnang',
                '32038' => 'hieunangpin',
                '32039' => 'camera',
                '32040' => 'bonhotrong',
                '32041' => 'ram',
                '32042' => 'tansoquet',
                '32043' => 'chipxuly',
            ],
            'tv' => [
                '32118' => 'hangsanxuat',
                '32119' => 'kichcomanhinh',
                '32120' => 'dophangiai',
                '32121' => 'phanloai',
                '32122' => 'hedieuhanh',
                '32123' => 'tienich',
            ],
            'air_conditioner' => [
                '32124' => 'hangsanxuat',
                '32125' => 'congsuat',
                '32126' => 'congnghe',
                '32127' => 'loaimay',
                '32128' => 'kieudang',
                '32129' => 'tienich',
            ],
        ];

        $fieldMap = $fieldMaps[$category] ?? [];

        foreach ($products as $product) {
            $productDetail = $this->fetchProductDetail($product['id']);
            if (!isset($productDetail['error'])) {
                $product = array_merge($product, $productDetail);
            } else {
                continue;
            }

            $matchesAllFilters = true;

            foreach ($filterData as $parentFilterId => $filters) {
                if (empty($filters)) continue;

                $field = $fieldMap[$parentFilterId] ?? null;
                if (!$field || !isset($product[$field]) || !is_array($product[$field])) {
                    $matchesAllFilters = false;
                    break;
                }

                $matchesFilter = false;
                foreach ($filters as $filter) {
                    $alias = $filter['alias'] ?? '';
                    foreach ($product[$field] as $fieldItem) {
                        if (isset($fieldItem['url']) && $fieldItem['url'] === $alias) {
                            $matchesFilter = true;
                            break 2;
                        }
                    }
                }

                if (!$matchesFilter) {
                    $matchesAllFilters = false;
                    break;
                }
            }

            if ($matchesAllFilters) {
                $filteredProducts[] = $product;
            }
        }

        return ['products' => $filteredProducts];
    }

    public function getWishlistProducts($ids)
    {
        if (empty($ids)) return [];

        return DB::table('Table_Customer_PartMaster')
            ->whereIn('IDPart', $ids)
            ->get()
            ->mapWithKeys(function ($item) {
                $image = $item->HinhDaiDien;
                if ($image && !str_starts_with($image, 'http')) {
                    $image = $this->baseImageUrl . $image;
                }

                return [$item->IDPart => [
                    'tieude' => $item->PartName,
                    'gia' => $item->GiaBan,
                    'hinhdaidien' => $image ?: $this->defaultImage,
                ]];
            })
            ->toArray();
    }

    public function addToWishlist($id)
    {
        $wishlist = session()->get('wishlist', []);
        if (!in_array($id, $wishlist)) {
            $wishlist[] = $id;
            session(['wishlist' => $wishlist]);
        }
        return true;
    }

    public function removeFromWishlist($id)
    {
        $wishlist = session()->get('wishlist', []);
        if (($key = array_search($id, $wishlist)) !== false) {
            unset($wishlist[$key]);
            session(['wishlist' => array_values($wishlist)]);
        }
        return true;
    }
    
}