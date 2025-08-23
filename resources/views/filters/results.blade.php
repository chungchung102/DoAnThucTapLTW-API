@extends('layouts.app')

@section('title', 'Kết quả lọc sản phẩm')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4">Kết quả lọc sản phẩm</h2>

    <div class="mb-3">
        <label>Danh mục: {{ $categories[$categoryId]['tieude'] ?? 'Không xác định' }}</label>
        <a href="{{ route('category.slug', ['slug' => $categories[$categoryId]['url'] ?? '']) }}" class="btn btn-secondary">Chỉnh sửa bộ lọc</a>
    </div>

    @if (!empty($selectedFilters))
        <div class="mb-3">
            <h4>Bộ lọc đã chọn:</h4>
            @foreach ($selectedFilters as $groupId => $filterIds)
                @foreach ($filterIds as $filterId)
                    <span class="badge bg-primary me-1">{{ $filterId }}</span>
                @endforeach
            @endforeach
        </div>
    @endif

    <div class="row">
        @forelse ($products as $product)
            <div class="col-md-4 mb-4">
                <div class="card">
                    @if (!empty($product['hinhdaidien']))
                        <img src="{{ $product['hinhdaidien'] }}" class="card-img-top" alt="{{ $product['tieude'] ?? 'N/A' }}">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $product['tieude'] ?? 'N/A' }}</h5>
                        <p class="card-text">
                            @php
                                $fields = [
                                    'thuonghieu' => 'Thương hiệu',
                                    'kichcomanhinh' => 'Kích cỡ màn hình',
                                    'tinhnangdacbiet' => 'Tính năng đặc biệt',
                                    'hieunangvapin' => 'Hiệu năng và pin',
                                    'camera' => 'Camera',
                                    'bonhotrong' => 'Bộ nhớ trong',
                                    'dungluongram' => 'Dung lượng RAM',
                                    'tansoquet' => 'Tần số quét',
                                    'chipxuli' => 'Chip xử lý',
                                    'cpu' => 'CPU',
                                    'mainboard' => 'Mainboard',
                                    'ram' => 'RAM',
                                    'ocung' => 'Ổ cứng',
                                    'carddohoa' => 'Card đồ họa',
                                    'nhucau' => 'Nhu cầu'
                                ];
                            @endphp
                            @foreach ($fields as $key => $label)
                                @if (!empty($product[$key]) && is_array($product[$key]))
                                    {{ $label }}: {{ $product[$key][0]['tengoi'] ?? 'N/A' }}<br>
                                @endif
                            @endforeach
                            Giá: {{ number_format($product['giakhuyenmai'] ?: $product['gia'] ?? 0, 0, ',', '.') }} VNĐ
                        </p>
                        @if (!empty($product['url']))
                            <a href="{{ url($product['url']) }}" class="fw-bold text-gold text-decoration-none d-block">Xem chi tiết</a>
                        @else
                            <span class="text-danger">Không có link chi tiết</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center">Không tìm thấy sản phẩm nào phù hợp với bộ lọc.</p>
        @endforelse
    </div>
</div>
@endsection
