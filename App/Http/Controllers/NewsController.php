<?php
namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class NewsController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function show(Request $request, $id)
    {
        $data = $this->productService->fetchData(100, 'news');
        $newsItem = null;

        foreach ($data['news'] as $news) {
            if ($news['id'] === $id) {
                $newsItem = $news;
                break;
            }
        }

        if (!$newsItem) {
            abort(404, 'Không tìm thấy tin tức.');
        }

        // Lấy tin tức liên quan (loại trừ tin tức hiện tại)
        $relatedNews = array_filter($data['news'], function ($news) use ($id) {
            return $news['id'] !== $id;
        });

        // Phân trang tin tức liên quan
        $perPage = 20;
        $currentPage = $request->query('page', 1);
        $relatedNewsArray = array_values($relatedNews); // Chuyển về mảng tuần tự
        $total = count($relatedNewsArray);
        $relatedNewsPaginated = array_slice($relatedNewsArray, ($currentPage - 1) * $perPage, $perPage);
        
        $paginator = new LengthAwarePaginator(
            $relatedNewsPaginated,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('news.show', [
            'newsItem' => $newsItem,
            'relatedNews' => $paginator,
            'title' => $newsItem['tieude'] ?? 'Chi tiết tin tức'
        ]);
    }
}
