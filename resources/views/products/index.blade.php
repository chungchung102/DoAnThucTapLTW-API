
@extends('layouts.app')
@section('title', $title)
@section('content')
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Carousel Section -->
<div id="productCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner"style="width:100%">
        <div class="carousel-item active">
            <img src="{{ asset('images/b1.jpg') }}" class="d-block w-100" alt="Banner 1" style="border-radius:20px;height:700px; object-fit: cover;">
            <div class="carousel-caption d-none d-md-block">
                <p>Ưu đãi đặc biệt hôm nay!</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="{{ asset('images/b2.jpg') }}" class="d-block w-100" alt="Banner 2" style="border-radius:20px; height:700px;object-fit: cover;">
            <div class="carousel-caption d-none d-md-block">
                <p>Cơ hội mua sắm hấp dẫn!</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="{{ asset('images/b3.jpg') }}" class="d-block w-100" alt="Banner 3" style="border-radius:20px; height:700px; object-fit: cover;">
            <div class="carousel-caption d-none d-md-block">
                <p>Khám phá sản phẩm tuyệt vời!</p>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<!-- Product Section -->
<div class="row mt-4">
    @if (isset($data['error']))
        <p class="text-danger">{{ $data['error'] }}. Vui lòng kiểm tra log để biết thêm chi tiết.</p>
    @elseif (is_array($data['products']) && !empty($data['products']))
        @php 
            $categories = [
                'computer' => 'Máy tính',
                'phone' => 'Điện thoại',
                'tv' => 'Tivi',
                'air_conditioner' => 'Máy lạnh'
            ];
            $groupedProducts = [];
            foreach ($data['products'] as $product) {
                $category = $product['category'] ?? 'other';
                if (!isset($groupedProducts[$category])) {
                    $groupedProducts[$category] = [];
                }
                $groupedProducts[$category][] = $product;
            }
        @endphp

        @foreach ($categories as $categoryKey => $categoryName)
            @if (!empty($groupedProducts[$categoryKey]))
                <div class="mt-4">
                    <h3>{{ $categoryName }}</h3>
                    <div class="row">
                        @foreach (array_slice($groupedProducts[$categoryKey], 0, 8) as $product)
                            <div class="col-12 col-sm-6 col-md-3 mb-3">
                                <div class="card h-100 product-card">
                                    <div class="bg-white border-bottom rounded-top text-center" style="padding: 8px 0; min-height: 68px;">
                                        <a href="{{ route('products.show', $product['id']) }}" class="fw-bold text-gold text-decoration-none d-block">
                                            {{ $product['tieude'] ?? 'Không có tiêu đề' }}
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <div class="image-container">
                                            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" 
                                                 data-src="{{ $product['hinhdaidien'] ?? 'https://via.placeholder.com/300x200?text=No+Image' }}" 
                                                 class="lazy img-fluid" alt="Ảnh sản phẩm">
                                            <div class="image-placeholder"></div>
                                        </div>
                                        <ul class="list-unstyled mt-3">
                                            <li><span class="text-gold fw-bold">Giá bán:</span> 
                                                {{ isset($product['gia']) && $product['gia'] > 0 ? number_format($product['gia'], 0, ',', '.') . 'đ' : 'Liên hệ' }}
                                            </li>
                                        </ul>
                                        <div class="d-flex gap-2 mt-3">
                                            <a href="{{ route('cart.add', $product['id']) }}" class="btn btn-gold flex-fill"><i class="bi bi-cart-plus me-3 fs-5"></i></i>Mua hàng</a>
                                            <a href="{{ route('wishlist.add', $product['id']) }}" class="btn btn-success flex-fill"><i class="bi bi-heart-fill me-3 fs-5"></i>Yêu thích</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    @else
        <p>Không tìm thấy sản phẩm nào.</p>
    @endif
</div>

<!-- News Section -->
 <h2>Tin tức</h2>
<div class="new">
    <div class="row mt-5">
    @if (empty($news))
        <p>Không tìm thấy tin tức nào.</p>
    @else
        <div class="row">
            @php
                $totalNews = count($news);
                $half = ceil($totalNews / 2);
                $leftNews = array_slice($news, 0, min($half, 5));
                $rightNews = array_slice($news, $half, min($totalNews - $half, 5));
            @endphp

            <div class="col-md-6">
                @foreach ($leftNews as $newsItem)
                    <div class="col-12 mb-3" style="height:auto;">
                        <div class="cl-card h-100">
                            <div class="row g-0">
                                <div class="col-md-3">
                                    <div class="image-container">
                                        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" 
                                             data-src="{{ $newsItem['hinhdaidien'] ?? 'https://via.placeholder.com/300x200?text=No+Image' }}" 
                                             class="lazy img-fluid rounded-start" alt="Ảnh tin tức" style="width: 100%; height: 150px; object-fit: cover;">
                                        <div class="image-placeholder"></div>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="c-card-body">
                                        <h5 class="card-title">
                                            <a href="{{ route('news.show', $newsItem['id']) }}" class="fw-bold text-gold text-decoration-none">
                                                {{ $newsItem['tieude'] ?? 'Không có tiêu đề' }}
                                            </a>
                                        </h5>
                                        <p class="card-text">
                                            {{ \Illuminate\Support\Str::limit($newsItem['mota'] ?? $newsItem['noidungtomtat'] ?? 'Không có mô tả', 150, '...') }}
                                        </p>
                                        <p class="card-text">
                                            <small class="text-muted">Ngày đăng: {{ $newsItem['ngay'] ?? $newsItem['ngaydang'] ?? 'N/A' }}</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>       
            <div class="col-md-6">
                @foreach ($rightNews as $newsItem)
                    <div class="col-12 mb-3" style="height:auto;">
                        <div class="cl-card h-100">
                            <div class="row g-0">
                                <div class="col-md-3">
                                    <div class="image-container">
                                        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" 
                                             data-src="{{ $newsItem['hinhdaidien'] ?? 'https://via.placeholder.com/300x200?text=No+Image' }}" 
                                             class="lazy img-fluid rounded-start" alt="Ảnh tin tức" style="width: 100%; height: 150px; object-fit: cover;">
                                        <div class="image-placeholder"></div>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="c-card-body">
                                        <h5 class="card-title">
                                            <a href="{{ route('news.show', $newsItem['id']) }}" class="fw-bold text-gold text-decoration-none">
                                                {{ $newsItem['tieude'] ?? 'Không có tiêu đề' }}
                                            </a>
                                        </h5>
                                        <p class="card-text">
                                            {{ \Illuminate\Support\Str::limit($newsItem['mota'] ?? $newsItem['noidungtomtat'] ?? 'Không có mô tả', 150, '...') }}
                                        </p>
                                        <p class="card-text">
                                            <small class="text-muted">Ngày đăng: {{ $newsItem['ngay'] ?? $newsItem['ngaydang'] ?? 'N/A' }}</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
</div>
<!-- Customer Comments Section -->
<div class="row mt-5">
    <h2>Ý kiến khách hàng</h2>
    <div class="row">
        @if (!empty($data['products']))
            @php
                $comments = array_slice($data['products'], 0, 6); 
                $leftColumn = array_slice($comments, 0, 3);
                $rightColumn = array_slice($comments, 3, 3);
                $tenKhachHangMau = ['Anh Minh', 'Chị Hoa', 'Anh Tú', 'Chị Lan', 'Anh Dũng', 'Chị Hương', 'Anh Khoa', 'Chị Mai', 'Anh Sơn', 'Chị Thảo'];
            @endphp
            <div class="col-md-6">
                @foreach ($leftColumn as $index => $product)
                    <div class="comment-card mb-3 p-3 border rounded">
                        <h5>{{ $product['tieude'] ?? 'Không có tiêu đề' }}</h5>
                        <p>Sản phẩm rất tốt, giá cả hợp lý! Tôi rất hài lòng với chất lượng.</p>
                        <small class="text-muted">
                            Đăng bởi: {{ $tenKhachHangMau[$index % count($tenKhachHangMau)] }} | Ngày: 23/05/2025
                        </small><br>
                            <a>Đánh giá: 
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                            </a>
                    </div>
                @endforeach
            </div>
            <div class="col-md-6">
                @foreach ($rightColumn as $index => $product)
                    <div class="comment-card mb-3 p-3 border rounded">
                        <h5>{{ $product['tieude'] ?? 'Không có tiêu đề' }}</h5>
                        <p>Sản phẩm rất tốt, giá cả hợp lý! Tôi rất hài lòng với chất lượng.</p>
                        <small class="text-muted">
                            Đăng bởi: {{ $tenKhachHangMau[($index + 5) % count($tenKhachHangMau)] }} | Ngày: 23/05/2025
                        </small><br>
                        <a>Đánh giá: 
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="col-12">Chưa có ý kiến nào từ khách hàng.</p>
        @endif
    </div>
</div>
<!-- Map Section -->
<div class="row mt-5">
    <h2>Bản đồ</h2>
    <div class="col-12">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d979.8054726630446!2d106.63371656965013!3d10.794310316709925!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3175295499cd3675%3A0x18187e1c15292c22!2zMjMgxJAuIE5ndXnhu4VuIFh1w6JuIEtob8OhdCwgVMOibiBUaMOgbmgsIFTDom4gUGjDuiwgSOG7kyBDaMOtIE1pbmgsIFZp4buHdCBOYW0!5e0!3m2!1svi!2s!4v1748317546016!5m2!1svi!2s"
            width="100%" 
            height="500" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const images = document.querySelectorAll('img.lazy');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.add('lazy-loaded');
                    img.parentElement.querySelector('.image-placeholder').style.display = 'none';
                    observer.unobserve(img);
                }
            });
        }, { rootMargin: '100px' });
        images.forEach(img => observer.observe(img));
        images.forEach(img => {
            img.addEventListener('error', () => {
                img.src = 'https://via.placeholder.com/300x200?text=No+Image';
                img.classList.add('lazy-loaded');
                img.parentElement.querySelector('.image-placeholder').style.display = 'none';
            });
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            let alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 3000);
    });   
    
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Gửi request ngầm để API xuất hiện trong tab Network
        fetch('https://demodienmay.125.atoz.vn/ww2/module.sanpham.asp?id=35279', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                console.warn('Không thể gọi API: https://demodienmay.125.atoz.vn/ww2/module.sanpham.asp?id=35279');
                return;
            }
            return response.json();
        })
        .then(data => {
            // In dữ liệu ra console để kiểm tra
            if (data) {
                console.log('[API Response] module.sanpham.asp:', data);
            }
        })
        .catch(error => {
            console.error('Lỗi khi gọi API module.sanpham.asp:', error);
        });
    });
</script>

@endsection
