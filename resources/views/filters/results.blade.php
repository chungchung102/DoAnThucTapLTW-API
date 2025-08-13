@extends('layouts.app')

@section('title', 'Kết quả lọc sản phẩm')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4">Kết quả lọc sản phẩm</h2>

    <div class="mb-3">
        <label>Danh mục: {{ $categories[$categoryId] ?? 'Không xác định' }}</label>
        <a href="{{ route('filters.index', ['category' => $categoryId]) }}" class="btn btn-secondary">Chỉnh sửa bộ lọc</a>
    </div>

    @if (empty($products))
        <p class="text-center">Không tìm thấy sản phẩm nào phù hợp với bộ lọc.</p>
    @else
        <div class="row">
            @foreach ($products as $product)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        @if (!empty($product['hinhdaidien']))
                            <img src="{{ $product['hinhdaidien'] }}" class="card-img-top" alt="{{ $product['tieude'] }}">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $product['tieude'] ?? 'N/A' }}</h5>
                            <p class="card-text">
                                @if (isset($product['thuonghieu']) && !empty($product['thuonghieu']))
                                    Thương hiệu: {{ $product['thuonghieu'][0]['tengoi'] ?? 'N/A' }}<br>
                                @elseif (isset($product['hangsanxuat']) && !empty($product['hangsanxuat']))
                                    Hãng sản xuất: {{ $product['hangsanxuat'][0]['tengoi'] ?? 'N/A' }}<br>
                                @endif
                                @if (isset($product['ram']) && !empty($product['ram']))
                                    RAM: {{ $product['ram'][0]['tengoi'] ?? 'N/A' }}<br>
                                @endif
                                @if (isset($product['cpu']) && !empty($product['cpu']))
                                    CPU: {{ $product['cpu'][0]['tengoi'] ?? 'N/A' }}<br>
                                @endif
                                @if (isset($product['kichcomanhinh']) && !empty($product['kichcomanhinh']))
                                    Kích cỡ màn hình: {{ $product['kichcomanhinh'][0]['tengoi'] ?? 'N/A' }}<br>
                                @endif
                                @if (isset($product['congsuat']) && !empty($product['congsuat']))
                                    Công suất: {{ $product['congsuat'][0]['tengoi'] ?? 'N/A' }}<br>
                                @endif
                                Giá: {{ number_format($product['gia'] ?? 0) }} VNĐ
                            </p>
                            <a href="{{ route('products.show', $product['id']) }}" class="fw-bold text-gold text-decoration-none d-block">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection