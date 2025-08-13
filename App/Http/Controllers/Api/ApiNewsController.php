<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ApiNewsController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function show($id)
    {
        $data = $this->productService->fetchData(100, 'all');

        $newsItem = null;
        foreach ($data['news'] as $item) {
            if ($item['id'] == $id) {
                $newsItem = $item;
                break;
            }
        }

        if (!$newsItem) {
            return response()->json(['error' => 'Không tìm thấy bản tin.'], 404);
        }

        return response()->json(['news' => $newsItem], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
