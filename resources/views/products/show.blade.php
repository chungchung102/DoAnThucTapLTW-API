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
<div class="product-detail-page">
    <h1 class="text-gold">{{ $product['tieude'] ?? 'Không có tiêu đề' }}</h1>
    <div class="product-detail-container">
        <div class="product-images">
            <div class="product-main-image">
                <div class="image-container">
                    <img id="main-image" 
                         src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" 
                         data-src="{{ $product['hinhdaidien'] ?? 'https://via.placeholder.com/300x200?text=No+Image' }}" 
                         class="lazy img-fluid" 
                         alt="Ảnh sản phẩm">
                    <div class="image-placeholder"></div>
                </div>
            </div>
            <div class="product-additional-images">
                @if (empty($product['hinhanh']))
                    <p>Không có hình ảnh bổ sung.</p>
                @else
                    @foreach ($product['hinhanh'] ?? [] as $ha)
                        <div class="image-container">
                            <img src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" 
                                 data-src="{{ filter_var($ha['hinhdaidien'], FILTER_VALIDATE_URL) ? $ha['hinhdaidien'] : 'https://via.placeholder.com/300x200?text=No+Image' }}" 
                                 class="lazy img-fluid" 
                                 alt="Hình ảnh bổ sung" 
                                 onclick="document.getElementById('main-image').src=this.getAttribute('data-src');"
                                 onerror="this.src='https://via.placeholder.com/300x200?text=Error'; console.log('Error loading image:', this.src);"
                                 loading="lazy">
                            <div class="image-placeholder"></div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        <div class="product-info">
            <div class="product-specs">
                <ul>
                    @if (isset($product['thuonghieu']) || isset($product['hangsanxuat']))
                        <li><strong class="text-gold">Thương hiệu:</strong> 
                            {{ collect($product['thuonghieu'] ?? $product['hangsanxuat'] ?? [])->first()['tengoi'] ?? 'Khác' }}
                        </li>
                    @endif
                    @if (isset($product['cpu']))
                        <li><strong class="text-gold">CPU:</strong> 
                            {{ collect($product['cpu'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                    @if (isset($product['ram']))
                        <li><strong class="text-gold">Dung lượng RAM:</strong> 
                            {{ collect($product['ram'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                    @if (isset($product['mainboard']))
                        <li><strong class="text-gold">Mainboard:</strong> 
                            {{ collect($product['mainboard'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                    @if (isset($product['nhucau']))
                        <li><strong class="text-gold">Phù hợp với:</strong> 
                            {{ collect($product['nhucau'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                      @if (isset($product['kichcomanhinh']))
                        <li><strong class="text-gold">Kích cỡ:</strong> 
                            {{ collect($product['kichcomanhinh'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                    @if (isset($product['ocung']))
                        <li><strong class="text-gold">Ổ cứng:</strong> 
                            {{ collect($product['ocung'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                    @if (isset($product['carddohoa']))
                        <li><strong class="text-gold">Card đồ họa:</strong> 
                            {{ collect($product['carddohoa'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                    @if (isset($product['kichcomanhinhtivi']) || isset($product['man_hinh']) || isset($product['kich_thuoc_man_hinh']))
                        <li><strong class="text-gold">Màn hình:</strong> 
                            {{ collect($product['kichcomanhinhtivi'] ?? $product['man_hinh'] ?? $product['kich_thuoc_man_hinh'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                    @if (isset($product['dophangiai']))
                        <li><strong class="text-gold">Độ phân giải:</strong> 
                            {{ collect($product['dophangiai'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                    @if (isset($product['phanloai']))
                        <li><strong class="text-gold">Phân loại:</strong> 
                            {{ collect($product['phanloai'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                      @if (isset($product['tinhnangdacbiet']))
                        <li><strong class="text-gold">Tính năng:</strong> 
                            {{ collect($product['tinhnangdacbiet'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                     @if (isset($product['hieunangvapin']))
                        <li><strong class="text-gold">Hiệu năng:</strong> 
                            {{ collect($product['hieunangvapin'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                     @if (isset($product['bonhotrong']))
                        <li><strong class="text-gold">Bộ nhớ:</strong> 
                            {{ collect($product['bonhotrong'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                      @if (isset($product['tansoquet']))
                        <li><strong class="text-gold">Tần số quét:</strong> 
                            {{ collect($product['tansoquet'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                     @if (isset($product['chipxuli']))
                        <li><strong class="text-gold">Chip:</strong> 
                            {{ collect($product['chipxuli'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                    @if (isset($product['dungluongram']))
                        <li><strong class="text-gold">Dung lượng:</strong> 
                            {{ collect($product['dungluongram'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                    @if (isset($product['loaimay']))
                        <li><strong class="text-gold">Loại máy:</strong> 
                            {{ collect($product['loaimay'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                    @if (isset($product['kieudang']))
                        <li><strong class="text-gold">Kiểu dáng:</strong> 
                            {{ collect($product['kieudang'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                    @if (isset($product['tienich']))
                        <li><strong class="text-gold">Tiện ích:</strong> 
                            {{ collect($product['tienich'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                    @if (isset($product['pin']))
                        <li><strong class="text-gold">Pin:</strong> 
                            {{ collect($product['pin'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                    @if (isset($product['he_dieu_hanh']))
                        <li><strong class="text-gold">Hệ điều hành:</strong> 
                            {{ collect($product['he_dieu_hanh'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                    @if (isset($product['camera']))
                        <li><strong class="text-gold">Camera:</strong> 
                            {{ collect($product['camera'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                 
                    @if (isset($product['hedieuhanhtivi']))
                        <li><strong class="text-gold">Hệ điều hành:</strong> 
                            {{ collect($product['hedieuhanhtivi'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                    @if (isset($product['congsuat']))
                        <li><strong class="text-gold">Công suất:</strong> 
                            {{ collect($product['congsuat'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                     @if (isset($product['congnghe']))
                        <li><strong class="text-gold">Công nghệ:</strong> 
                            {{ collect($product['congnghe'] ?? [])->first()['tengoi'] ?? 'N/A' }}
                        </li>
                    @endif
                    <li><strong class="">Giá bán:</strong> 
                        {{ isset($product['gia']) && $product['gia'] > 0 ? number_format($product['gia'], 0, ',', '.') . 'đ' : 'Liên hệ' }}
                    </li>
                </ul>
            </div>
            <div class="product-actions mt-3">
                <a href="{{ route('cart.add', $product['id']) }}" class="btn btn-success flex-fill"><i class="bi bi-cart-plus me-1"></i>Mua hàng</a>
                <button class="btn-add-wishlist btn btn-outline-danger flex-fill" data-id="{{ $product['id'] }}">
                    <i class="bi bi-heart me-1"></i>Yêu thích
                </button>
            </div>
        </div>
    </div>
    @if (isset($product['noidungchitiet']))
        <div class="product-description mt-4">
            <h3 class="text-gold">Mô tả chi tiết</h3>
            <div class="description-content" id="description-content">
                {!! htmlspecialchars_decode($product['noidungchitiet']) !!}
            </div>
            <a href="javascript:void(0)" class="read-more-btn" id="read-more-btn">Xem thêm</a>
        </div>
    @endif
    <div class="related-products mt-5">
        <h2 class="text-gold">Sản phẩm liên quan</h2>
        <div class="row">
            @if (empty($relatedProducts))
                <p>Không có sản phẩm liên quan trong danh mục này.</p>
            @else
                @foreach ($relatedProducts as $relatedProduct)
                    <div class="col-12 col-sm-6 col-md-3 mb-3">
                        <div class="card h-100 product-card">
                            <div class="bg-white border-bottom rounded-top text-center" style="padding: 8px 0; min-height: 68px;">
                                <a href="{{ route('products.show', $relatedProduct['id']) }}" class="fw-bold text-gold text-decoration-none d-block">
                                    {{ $relatedProduct['tieude'] ?? 'Không có tiêu đề' }}
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="image-container">
                                    <img src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" 
                                         data-src="{{ $relatedProduct['hinhdaidien'] ?? 'https://via.placeholder.com/300x200?text=No+Image' }}" 
                                         class="lazy img-fluid" 
                                         alt="Ảnh sản phẩm" 
                                         loading="lazy">
                                    <div class="image-placeholder"></div>
                                </div>
                                <ul class="list-unstyled mt-3">
                                    <li><span class="text-gold fw-bold">Giá bán:</span> 
                                        {{ isset($relatedProduct['gia']) && $relatedProduct['gia'] > 0 ? number_format($relatedProduct['gia'], 0, ',', '.') . 'đ' : 'Liên hệ' }}
                                    </li>
                                </ul>
                                <div class="d-flex gap-2 mt-3">
                                    <a href="{{ route('cart.add', $relatedProduct['id']) }}" class="btn btn-gold flex-fill">Mua hàng</a>
                                    <button class="btn-add-wishlist btn btn-outline-danger flex-fill" data-id="{{ $relatedProduct['id'] }}">
                                        <i class="bi bi-heart"></i> Yêu thích
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <a href="{{ route('products.index') }}" class="btn btn-success mt-3">Quay lại danh sách</a>
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
                img.src = 'https://via.placeholder.com/300x200?text=Error';
                img.classList.add('lazy-loaded');
                img.parentElement.querySelector('.image-placeholder').style.display = 'none';
            });
        });

        const descriptionContent = document.getElementById('description-content');
        const readMoreBtn = document.getElementById('read-more-btn');

        if (descriptionContent && readMoreBtn) {
            readMoreBtn.addEventListener('click', function() {
                if (descriptionContent.classList.contains('expanded')) {
                    descriptionContent.classList.remove('expanded');
                    readMoreBtn.textContent = 'Xem thêm';
                } else {
                    descriptionContent.classList.add('expanded');
                    readMoreBtn.textContent = 'Thu gọn';
                }
            });
        }

        setTimeout(function() {
            let alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 3000);
    });
    
</script>

 <!-- ===== BÌNH LUẬN SECTION ===== -->
    <div class="comments-section" id="comments-section" style="display: none;">
        <div class="container mt-5">
            <h3 class="text-gold mb-4">Bình luận sản phẩm</h3>
            
            <!-- Form thêm bình luận -->
            <div class="comment-form mb-4">
                <div class="card">
                    <div class="card-body">
                        <form id="comment-form">
                            @csrf
<input type="hidden" id="product-id" value="{{ $product['id'] }}">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="comment-name" class="form-label">Họ và tên *</label>
                                    <input type="text" class="form-control" id="comment-name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="comment-email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="comment-email" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="comment-phone" class="form-label">Điện thoại</label>
                                    <input type="tel" class="form-control" id="comment-phone">
                                </div>
                                <div class="col-md-6">
                                    <label for="comment-rating" class="form-label">Đánh giá *</label>
                                    <div class="rating-input">
                                        <div class="stars" id="rating-stars">
                                            <span class="star" data-value="1">★</span>
                                            <span class="star" data-value="2">★</span>
                                            <span class="star" data-value="3">★</span>
                                            <span class="star" data-value="4">★</span>
                                            <span class="star" data-value="5">★</span>
                                        </div>
                                        <input type="hidden" id="comment-rating" value="5">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="comment-content" class="form-label">Nội dung bình luận *</label>
                                <textarea class="form-control" id="comment-content" rows="4" placeholder="Nhập nội dung bình luận..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-warning">Gửi bình luận</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Danh sách bình luận -->
            <div class="comments-list">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold" id="comments-count">0 bình luận</span>
                    <select class="form-select" style="width: auto;" id="comments-sort">
                        <option value="newest">Mới nhất</option>
                        <option value="oldest">Cũ nhất</option>
                    </select>
                </div>
                <div id="comments-container">
                    <!-- Comments sẽ được load bằng JS -->
                </div>
                <div class="text-center mt-3">
                    <button id="load-more-btn" class="btn btn-outline-secondary" style="display: none;">Xem thêm bình luận</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.comments-section {
    background-color: #f8f9fa;
    padding: 2rem 0;
    margin-top: 2rem;
    border-top: 1px solid #eee;
}

.rating-input .stars {
    font-size: 1.5rem;
    color: #ddd;
    display: inline-block;
}

.rating-input .star {
    cursor: pointer;
    transition: color 0.2s;
    display: inline-block;
}

.rating-input .star:hover,
.rating-input .star.active {
    color: #ffc107;
}

.comment-item {
    background: white;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    border-left: 4px solid #d4a017;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.comment-author {
    font-weight: bold;
    color: #1a252f;
}

.comment-date {
    color: #666;
    font-size: 0.9rem;
}

.comment-rating {
    color: #ffc107;
    font-size: 0.9rem;
    margin-left: 0.5rem;
}

.comment-content {
    color: #333;
    line-height: 1.6;
    white-space: pre-line;
}

.like-button {
    background: none;
    border: none;
    color: #666;
    cursor: pointer;
    font-size: 0.9rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.like-button:hover {
    background-color: #f0f0f0;
    color: #d4a017;
}

.like-button.liked {
    color: #d4a017;
    background-color: #fff8e1;
}

.comment-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

#load-more-btn {
    transition: all 0.3s;
}

#load-more-btn:hover {
    background-color: #d4a017;
    color: white;
    border-color: #d4a017;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productId = parseInt('{{ $product["id"] ?? "0" }}') || 0;
    let commentsData = [];
    let currentPage = 1;
    let totalPages = 1;
    let isLoading = false;
    
    // Kiểm tra và tải bình luận
    checkAndLoadComments();
    
    function checkAndLoadComments() {
        console.log('[COMMENT] Checking product settings for ID:', productId);
        
        // Ẩn section bình luận mặc định
        document.getElementById('comments-section').style.display = 'none';
        
        // Giả định sản phẩm cho phép bình luận (trong thực tế cần gọi API kiểm tra)
        // Bạn có thể bỏ comment đoạn code sau nếu muốn kiểm tra qua API
        
        fetch(`/api/proxy-product-info/${productId}`)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                if (data && data.length > 0) {
                    const productInfo = data[0];
                    const allowComments = productInfo.chophepbinhluan === "True";
                    const showComments = productInfo.hienthibinhluan === "True";
                    
                    if (allowComments && showComments) {
                        document.getElementById('comments-section').style.display = 'block';
                        loadComments();
                    }
                }
            })
            .catch(error => {
                console.error('Lỗi khi kiểm tra cài đặt bình luận:', error);
            });
        
        
        // Tạm thời hiển thị luôn section bình luận để test
        document.getElementById('comments-section').style.display = 'block';
        loadComments();
    }
    
    function loadComments(page = 1) {
        if (isLoading) return;
        
        isLoading = true;
        showLoadingIndicator(true);
        
        fetch(`/api/proxy-binhluan/${productId}/${page}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                // Xử lý dữ liệu trả về từ API
                if (Array.isArray(data) && data.length > 0) {
                    const responseData = data[0];
                    
                    if (Array.isArray(responseData.data)) {
                        if (page === 1) {
                            commentsData = responseData.data;
                        } else {
                            commentsData = [...commentsData, ...responseData.data];
                        }
                        
                        totalPages = parseInt(responseData.total_pages) || 1;
                        currentPage = page;
                        
                        updateCommentsCount();
                        renderComments();
                        
                        // Hiển thị nút "Xem thêm" nếu còn trang
                        const loadMoreBtn = document.getElementById('load-more-btn');
                        loadMoreBtn.style.display = currentPage < totalPages ? 'inline-block' : 'none';
                    }
                }
            })
            .catch(error => {
                console.error('Error loading comments:', error);
                showError('Đã có lỗi xảy ra khi tải bình luận. Vui lòng thử lại sau.');
            })
            .finally(() => {
                isLoading = false;
                showLoadingIndicator(false);
            });
    }
    
    function updateCommentsCount() {
        const count = commentsData.length;
        document.getElementById('comments-count').textContent = `${count} bình luận`;
    }
    
    function renderComments() {
        const container = document.getElementById('comments-container');
        const sortBy = document.getElementById('comments-sort').value;
        
        // Sắp xếp comments
        let sortedComments = [...commentsData];
        if (sortBy === 'newest') {
            sortedComments.sort((a, b) => new Date(b.ngaydang) - new Date(a.ngaydang));
        } else {
            sortedComments.sort((a, b) => new Date(a.ngaydang) - new Date(b.ngaydang));
        }
        
        // Nếu là trang đầu tiên, xóa hết nội dung cũ
        if (currentPage === 1) {
            container.innerHTML = '';
        }
        
        // Thêm comments mới
        sortedComments.slice((currentPage - 1) * 10, currentPage * 10).forEach(comment => {
            const commentHtml = createCommentHtml(comment);
            container.insertAdjacentHTML('beforeend', commentHtml);
        });
        
        // Gắn sự kiện like cho các comment
        document.querySelectorAll('.like-button').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.dataset.commentId;
                likeComment(commentId, this);
            });
        });
    }
    
    function createCommentHtml(comment) {
    // Lấy rating từ 0–100, ép kiểu và giới hạn đúng phạm vi
    const rawRating = parseInt(comment.rating || 0);
    const clampedRating = Math.max(0, Math.min(100, rawRating));
    const ratingValue = clampedRating / 20; // chuyển thành 0–5
    const roundedRating = Math.round(ratingValue * 2) / 2; // làm tròn nửa sao

    let starsHtml = '';
    const fullStars = Math.floor(roundedRating);
    const hasHalfStar = roundedRating % 1 !== 0;
    const emptyStars = 5 - Math.ceil(roundedRating);

    for (let i = 0; i < fullStars; i++) starsHtml += '★';
    if (hasHalfStar) starsHtml += '½';
    for (let i = 0; i < emptyStars; i++) starsHtml += '☆';

    const likeCount = parseInt(comment.soluongthich || 0);
    const isLiked = localStorage.getItem(`liked_${comment.id}`) === 'true';

    return `
        <div class="comment-item" id="comment-${comment.id}">
            <div class="comment-header">
                <div>
                    <span class="comment-author">${escapeHtml(comment.nguoidang || 'Ẩn danh')}</span>
                    <span class="comment-rating">${starsHtml}</span>
                </div>
                <span class="comment-date">${formatDate(comment.ngaydang)}</span>
            </div>
            <div class="comment-content">
                ${escapeHtml(comment.noidungbinhluan || '')}
            </div>
            <div class="comment-actions">
                <button class="like-button ${isLiked ? 'liked' : ''}" data-comment-id="${comment.id}">
                    <i class="bi bi-hand-thumbs-up${isLiked ? '-fill' : ''}"></i> 
                    Thích (${likeCount})
                </button>
            </div>
        </div>
    `;
    }
    
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
    
    function formatDate(dateString) {
        if (!dateString) return '';
        
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return '';
        
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays === 0) {
            return 'hôm nay';
        } else if (diffDays === 1) {
            return 'hôm qua';
        } else if (diffDays < 7) {
            return `cách đây ${diffDays} ngày`;
        } else {
            return date.toLocaleDateString('vi-VN', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }
    }
    
    // Xử lý like comment
    function likeComment(commentId, buttonElement) {
        if (!commentId || !buttonElement) return;

        if (localStorage.getItem(`liked_${commentId}`) === 'true') {
            showError('Bạn đã thích bình luận này rồi');
            return;
        }

        fetch('/api/proxy-save-binhluan-thich', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                id: productId,
                id2: commentId
            })
        })
        .then(res => res.json())
        .then(result => {
            if (Array.isArray(result) && result.length > 0) {
                const res = result[0];
                if (res.maloi === '1') {
                    localStorage.setItem(`liked_${commentId}`, 'true');
                    const newLikeCount = parseInt(res.soluongthich || 1);

                    // Cập nhật giao diện nút like
                    buttonElement.classList.add('liked');
                    buttonElement.innerHTML = `
                        <i class="bi bi-hand-thumbs-up-fill"></i> Thích (${newLikeCount})
                    `;

                    // Cập nhật dữ liệu trong commentsData nếu có
                    const comment = commentsData.find(c => c.id == commentId);
                    if (comment) {
                        comment.soluongthich = newLikeCount;
                    }

                    showSuccess('Bạn đã thích bình luận này!');
                } else {
                    showError(res.ThongBao || 'Không thể thích bình luận.');
                }
            } else {
                showError('Phản hồi không hợp lệ từ máy chủ.');
            }
        })
        .catch(() => {
            showError('Đã xảy ra lỗi khi gửi yêu cầu.');
        });
    }
    
    // Xử lý rating stars
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('comment-rating');
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const value = parseInt(this.getAttribute('data-value'));
            ratingInput.value = value;
            
            stars.forEach((s, index) => {
                if (index < value) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        });
        
        star.addEventListener('mouseover', function() {
            const value = parseInt(this.getAttribute('data-value'));
            
            stars.forEach((s, index) => {
                if (index < value) {
                    s.style.color = '#ffc107';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });
    });
    
    document.querySelector('.stars').addEventListener('mouseleave', function() {
        const currentRating = parseInt(ratingInput.value);
        stars.forEach((s, index) => {
            if (index < currentRating) {
                s.style.color = '#ffc107';
            } else {
                s.style.color = '#ddd';
            }
        });
    });
    
    // Set default 5 stars
    stars.forEach(star => star.classList.add('active'));
    
    // Xử lý gửi bình luận mới
    document.getElementById('comment-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const name = document.getElementById('comment-name').value.trim();
        const email = document.getElementById('comment-email').value.trim();
        const phone = document.getElementById('comment-phone').value.trim();
        const rating = document.getElementById('comment-rating').value;
        const content = document.getElementById('comment-content').value.trim();
        const productId = document.getElementById('product-id').value;
        
        if (!name || !email || !content) {
            showError('Vui lòng điền đầy đủ thông tin bắt buộc (Họ tên, Email, Nội dung)');
            return;
        }
        
        if (!validateEmail(email)) {
            showError('Email không hợp lệ');
            return;
        }
        
        // Chuẩn bị dữ liệu gửi đi
        const formData = new FormData();
        formData.append('id', productId);
        formData.append('tenkh', name);
        formData.append('txtemail', email);
        formData.append('txtdienthoai', phone);
        formData.append('noidungtxt', content);
        formData.append('id2', rating * 20); // Chuyển rating từ 1-5 sang 20-100
        
        // Hiển thị trạng thái loading
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang gửi...';
        
        // Gửi bình luận
        fetch('/api/proxy-save-binhluan', {
            method: 'POST',
            body: new URLSearchParams(formData),
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (Array.isArray(data) && data.length > 0) {
                const result = data[0];
                
                if (result.maloi === '00') {
                    // Gửi thành công
                    showSuccess('Bình luận của bạn đã được gửi và đang chờ duyệt!');
                    document.getElementById('comment-form').reset();
                    ratingInput.value = 5;
                    stars.forEach(star => star.classList.add('active'));

                    // RESET lại dữ liệu cũ
                    commentsData = [];
                    currentPage = 1;
                    loadComments();
                } else {
                    showError(result.ThongBao || 'Có lỗi xảy ra khi gửi bình luận');
                }
            }
        })
        .catch(error => {
            console.error('Error submitting comment:', error);
            showError('Đã có lỗi xảy ra khi gửi bình luận. Vui lòng thử lại sau.');
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.textContent = 'Gửi bình luận';
        });
    });
    
    // Sắp xếp bình luận
    document.getElementById('comments-sort').addEventListener('change', function() {
        currentPage = 1;
        renderComments();
    });
    
    // Xem thêm bình luận
    document.getElementById('load-more-btn').addEventListener('click', function() {
        if (currentPage < totalPages) {
            loadComments(currentPage + 1);
        }
    });
    
    // Helper functions
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    function showLoadingIndicator(show) {
        // Có thể thêm loading indicator nếu cần
    }
    
    function showSuccess(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible fade show';
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        const container = document.getElementById('comments-section');
        container.insertBefore(alertDiv, container.firstChild);
        
        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 150);
        }, 3000);
    }
    
    function showError(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        const container = document.getElementById('comments-section');
        container.insertBefore(alertDiv, container.firstChild);
        
        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 150);
        }, 3000);
    }
});

// Global functions
window.likeComment = function(commentId, buttonElement) {
    const event = new Event('click');
    buttonElement.dispatchEvent(event);
};

function likeComment(commentId) {
    const buttonElement = document.querySelector(`.like-button[data-comment-id="${commentId}"]`);
    if (!buttonElement || localStorage.getItem(`liked_${commentId}`) === 'true') return;

    // Nếu có userid/pass thì truyền vào, nếu không thì bỏ qua
    const formData = new FormData();
    formData.append('id', commentId);
    if (typeof userid !== 'undefined') formData.append('userid', userid);
    if (typeof pass !== 'undefined') formData.append('pass', pass);

    fetch('/api/proxy-save-binhluan-thich', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(result => {
        if (Array.isArray(result) && result.length > 0) {
            const res = result[0];
            if (res.maloi === "1") {
                localStorage.setItem(`liked_${commentId}`, 'true');
                // Cập nhật giao diện nút Thích
                if (buttonElement) {
                    const newLikeCount = parseInt(res.soluongthich || 1);
                    buttonElement.classList.add('liked');
                    buttonElement.innerHTML = `
                        <i class="bi bi-hand-thumbs-up-fill"></i> Thích (${newLikeCount})
                    `;
                }
                // Cập nhật mảng commentsData nếu có
                if (typeof commentsData !== 'undefined') {
                    const comment = commentsData.find(c => c.id == commentId);
                    if (comment) comment.like = parseInt(res.soluongthich || 1);
                }
                showSuccess('Bạn đã thích bình luận này!');
            } else {
                showError(res.ThongBao || 'Không thể thích bình luận.');
            }
        } else {
            showError('Phản hồi không hợp lệ từ máy chủ.');
        }
    })
    .catch(() => {
        showError('Đã xảy ra lỗi khi gửi yêu cầu.');
    });
}
</script>

<script src="{{ asset('js/wishlist.js') }}"></script>

@endsection