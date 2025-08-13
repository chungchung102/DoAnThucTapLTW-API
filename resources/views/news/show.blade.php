
@extends('layouts.app')
@section('title', $title)
@section('content')
<div class="news-detail-page">
    <h1>{{ $newsItem['tieude'] ?? 'Không có tiêu đề' }}</h1>
    <div class="news-detail-container">
        <div class="news-image">
            <div class="image-container">
                <img id="main-image" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" 
                     data-src="{{ $newsItem['hinhdaidien'] ?? 'https://via.placeholder.com/300x200?text=No+Image' }}" 
                     class="lazy img-fluid" alt="Ảnh tin tức">
                <div class="image-placeholder">Đang tải...</div>
            </div>
        </div>
        <div class="news-info">
            <p class="text-muted">Ngày đăng: {{ $newsItem['ngay'] ?? $newsItem['ngaydang'] ?? 'N/A' }}</p>
            @if (!empty($newsItem['mota']))
                <h3>Mô tả</h3>
                <p>{{ $newsItem['mota'] }}</p>
            @endif
            @if (!empty($newsItem['noidungtomtat']))
                <h3>Nội dung tóm tắt</h3>
                <p>{{ $newsItem['noidungtomtat'] }}</p>
            @endif
        </div>
    </div>
    <div class="related-news">
    <h2>Tin tức liên quan</h2>
    <div class="news-container">
        @if (empty($relatedNews))
            <p>Không có tin tức liên quan.</p>
        @else
            <div class="news-column">
                @foreach ($relatedNews->take(10) as $relatedNewsItem)
                    <div class="news-card">
                        <div class="cc border-bottom rounded-top text-center" style="padding: 8px 0; min-height: 50px;">
                            <a href="{{ route('news.show', $relatedNewsItem['id']) }}" class="fw-bold  text-decoration-none d-block">
                                {{ $relatedNewsItem['tieude'] ?? 'Không có tiêu đề' }}
                            </a>
                        </div>
                        <div class="news-details">
                            <div class="image-container">
                                <img src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" 
                                     data-src="{{ $relatedNewsItem['hinhdaidien'] ?? 'https://via.placeholder.com/300x200?text=No+Image' }}" 
                                     class="lazy img-fluid" alt="Ảnh tin tức" style="width: 100%; height: 150px; object-fit: cover;">
                                <div class="image-placeholder">Đang tải...</div>
                            </div>
                            <p class="news-text">
                                {{ \Illuminate\Support\Str::limit($relatedNewsItem['mota'] ?? $relatedNewsItem['noidungtomtat'] ?? 'Không có mô tả', 100, '...') }}
                            </p>
                            <p class="news-date">
                                <small class="text-muted">Ngày đăng: {{ $relatedNewsItem['ngay'] ?? $relatedNewsItem['ngaydang'] ?? 'N/A' }}</small>
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="news-column">
                @foreach ($relatedNews->slice(10, 10) as $relatedNewsItem)
                    <div class="news-card">
                        <div class="cc border-bottom rounded-top text-center" style="padding: 8px 0; min-height: 50px;">
                            <a href="{{ route('news.show', $relatedNewsItem['id']) }}" class="fw-bold  text-decoration-none d-block">
                                {{ $relatedNewsItem['tieude'] ?? 'Không có tiêu đề' }}
                            </a>
                        </div>
                        <div class="news-details">
                            <div class="image-container">
                                <img src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" 
                                     data-src="{{ $relatedNewsItem['hinhdaidien'] ?? 'https://via.placeholder.com/300x200?text=No+Image' }}" 
                                     class="lazy img-fluid" alt="Ảnh tin tức" style="width: 100%; height: 150px; object-fit: cover;">
                                <div class="image-placeholder">Đang tải...</div>
                            </div>
                            <p class="news-text">
                                {{ \Illuminate\Support\Str::limit($relatedNewsItem['mota'] ?? $relatedNewsItem['noidungtomtat'] ?? 'Không có mô tả', 100, '...') }}
                            </p>
                            <p class="news-date">
                                <small class="text-muted">Ngày đăng: {{ $relatedNewsItem['ngay'] ?? $relatedNewsItem['ngaydang'] ?? 'N/A' }}</small>
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
            
        @endif
    </div>
</div>
    <a href="{{ route('products.index') }}" class="back-btn btn btn-primary mt-3">Quay lại danh sách</a>
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
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        fetch('https://demodienmay.125.atoz.vn/ww2/module.Tintuc.asp?id=35152', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (!res.ok) {
                console.warn('Không thể gọi API: module.Tintuc.asp');
                return;
            }
            return res.json();
        })
        .then(data => {
            if (data) {
                console.log('[API] Dữ liệu:', data);
            }
        })
        .catch(err => {
            console.error('Lỗi khi gọi API:', err);
        });
    });
</script>



@endsection
