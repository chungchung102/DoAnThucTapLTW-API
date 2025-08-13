<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Services\ProductService;

class FilterController extends Controller
{
    protected $apiUrls = [
        6 => 'https://7e85a5afe201.ngrok-free.app/api/boloccon?IDCatalog=6',
        7 => 'https://7e85a5afe201.ngrok-free.app/api/boloccon?IDCatalog=7',
        16 => 'https://7e85a5afe201.ngrok-free.app/api/boloccon?IDCatalog=16',
        17 => 'https://7e85a5afe201.ngrok-free.app/api/boloccon?IDCatalog=17',
    ];

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $categoryId = $request->input('category', null);
        $filters = [];

        if ($categoryId && isset($this->apiUrls[$categoryId])) {
            $client = new Client();
            try {
                $response = $client->get($this->apiUrls[$categoryId], [
                    'headers' => [
                        'ngrok-skip-browser-warning' => 'true',
                    ],
                ]);
                $data = json_decode($response->getBody(), true);

                if ($data['status'] === 'success' && isset($data['data']['data'])) {
                    $filters = $data['data']['data'];
                } else {
                    Log::error("API returned invalid data for category ID {$categoryId}", ['response' => $data]);
                }
            } catch (RequestException $e) {
                Log::error("Error fetching filters for category ID {$categoryId}: " . $e->getMessage());
            }
        }

        $categories = [
            6 => 'Máy tính',
            7 => 'Điện thoại',
            16 => 'Tivi',
            17 => 'Máy lạnh',
        ];

        return view('filters.index', compact('filters', 'categories', 'categoryId'));
    }

    public function apply(Request $request)
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
            Log::warning("Invalid category ID: {$categoryId}");
            return redirect()->back()->with('error', 'Danh mục không hợp lệ.');
        }

        $filters = [];
        if (isset($this->apiUrls[$categoryId])) {
            $client = new Client();
            try {
                $response = $client->get($this->apiUrls[$categoryId], [
                    'headers' => [
                        'ngrok-skip-browser-warning' => 'true',
                    ],
                ]);
                $data = json_decode($response->getBody(), true);

                if ($data['status'] === 'success' && isset($data['data']['data'])) {
                    $filters = $data['data']['data'];
                } else {
                    Log::error("API returned invalid data for category ID {$categoryId}", ['response' => $data]);
                }
            } catch (RequestException $e) {
                Log::error("Error fetching filters for category ID {$categoryId}: " . $e->getMessage());
            }
        }

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
                                    'alias' => $detail['AlilasPath'] ?? '',
                                ];
                            }
                        }
                    }
                }
            }
        }

        $productData = $this->productService->fetchFilteredProducts($category, $filterData);
        $products = $productData['products'] ?? [];

        return view('filters.results', [
            'products' => $products,
            'categoryId' => $categoryId,
            'categories' => [
                6 => 'Máy tính',
                7 => 'Điện thoại',
                16 => 'Tivi',
                17 => 'Máy lạnh',
            ],
        ]);
    }
}