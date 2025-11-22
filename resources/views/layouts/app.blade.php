<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Chợ Xanh - Mua sắm máy tính, điện thoại, thiết bị công nghệ chất lượng">
    <meta name="keywords" content="máy tính, điện thoại, tivi, máy lạnh, mua sắm">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Chợ Xanh - Máy tính & Thiết bị số')</title>
    
    <!-- External CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://kit-free.fontawesome.com/releases/latest/css/free.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.0/jquery.fancybox.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-simplyscroll/2.0.5/jquery.simplyscroll.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Local CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom-choixanh.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/filters.css') }}">
    <link rel="stylesheet" href="{{ asset('css/external-styles.css') }}">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    @yield('styles')
</head>
<body>
    <!-- Top Navigation Bar -->
    <div class="top-navigation">
        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <span><i class="fas fa-book"></i> Thư viện số | <i class="fas fa-mobile-alt"></i> Điện thoại di động | <i class="fas fa-cogs"></i> Công nghệ</span>
                </div>
                <div class="col-6 text-end">
                    <span><i class="fas fa-phone"></i> Máy tính CHỢ XANH 0909256266</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="main-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-2 col-md-3">
                    <h3 class="text-white mb-0">
                        <i class="fas fa-bars me-2"></i>Danh mục
                    </h3>
                </div>
                <div class="col-lg-6 col-md-6">
                    <form action="{{ route('products.search') }}" method="GET" class="search-form">
                        <div class="search-container d-flex">
                            <input type="text" name="query" class="form-control" placeholder="Tìm kiếm sản phẩm..." id="search-input">
                            <input type="text" class="form-control" placeholder="Chọn thương hiệu...">
                            <button class="btn" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div id="suggestions"></div>
                    </form>
                </div>
                <div class="col-lg-4 col-md-3 text-end">
                    <button class="btn btn-light btn-sm">
                        <i class="fas fa-sign-in-alt"></i> Đăng nhập
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar elegant-navbar navbar-expand-lg">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav me-auto">
                    <a class="nav-link" href="{{ route('products.index') }}">
                        <i class="bi bi-house-fill"></i> Trang chủ
                    </a>
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-box"></i> Danh mục
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('products.index', ['category' => 'computer']) }}">
                                <i class="bi bi-pc-display"></i> Máy tính</a></li>
                            <li><a class="dropdown-item" href="{{ route('products.index', ['category' => 'phone']) }}">
                                <i class="bi bi-phone-fill"></i> Điện thoại</a></li>
                            <li><a class="dropdown-item" href="{{ route('products.index', ['category' => 'tv']) }}">
                                <i class="bi bi-display"></i> Tivi</a></li>
                            <li><a class="dropdown-item" href="{{ route('products.index', ['category' => 'air_conditioner']) }}">
                                <i class="bi bi-fan"></i> Máy lạnh</a></li>
                        </ul>
                    </div>
                    <a class="nav-link" href="{{ route('news.index') }}">
                        <i class="bi bi-file-text"></i> Tin tức
                    </a>
                    <a class="nav-link" href="{{ route('lien-he') }}">
                        <i class="bi bi-envelope-fill"></i> Liên hệ
                    </a>
                </div>
                
                <div class="navbar-nav align-items-center">
                    <a class="nav-link" href="{{ route('filters.index') }}">
                        <i class="bi bi-funnel fs-4"></i>
                    </a>
                    <a class="nav-link" href="/cart">
                        <i class="bi bi-cart-check"></i> Giỏ hàng: <span id="cart-count">{{ $cartCount ?? 0 }}</span>
                    </a>
                    <a class="nav-link" href="/wishlist">
                        <i class="bi bi-heart-fill"></i> Yêu thích: <span id="wishlist-count">{{ $wishlistCount ?? 0 }}</span>
                    </a>
                    
                    @if (session('user_email'))
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                Xin chào, {{ session('user_name') ?? session('user_email') }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('cart.history') }}">
                                    <i class="bi bi-clock"></i> Lịch sử mua hàng</a></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right"></i> Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a class="nav-link" href="{{ route('login.form') }}">
                            <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                        </a>
                        <a class="nav-link" href="{{ route('register.form') }}">
                            <i class="bi bi-person-plus"></i> Đăng ký
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 col-md-4">
                <!-- Categories Sidebar -->
                <div class="sidebar-container">
                    <div class="sidebar-header">
                        <i class="fas fa-list"></i> Danh mục sản phẩm
                    </div>
                    <ul class="category-list">
                        <li class="category-item">
                            <span><i class="fas fa-laptop"></i> Laptop</span>
                            <i class="fas fa-plus"></i>
                        </li>
                        <li class="category-item">
                            <span><i class="fas fa-desktop"></i> Khoa học</span>
                            <i class="fas fa-plus"></i>
                        </li>
                        <li class="category-item">
                            <span><i class="fas fa-cogs"></i> Công nghệ</span>
                            <i class="fas fa-plus"></i>
                        </li>
                    </ul>
                </div>

                <!-- Login Widget -->
                <div class="login-widget">
                    <div class="login-header">
                        LUM Software Đăng nhập hệ thống
                    </div>
                    <div class="login-body">
                        <form>
                            <input type="text" class="form-control" placeholder="demodienmay.125.atoz.vn">
                            <input type="password" class="form-control" placeholder="••••••••">
                            <button type="submit" class="btn-login">Đăng nhập</button>
                            <button type="button" class="btn-register">Đăng ký ngay</button>
                        </form>
                        <div class="text-center mt-3">
                            <small class="text-muted">Đăng ký để có thêm tài khoản</small><br>
                            <small><i class="fas fa-question-circle"></i> Hướng dẫn sử dụng</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9 col-md-8">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Footer -->
    @include('footer')

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.0/jquery.fancybox.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-simplyscroll/2.0.5/jquery.simplyscroll.min.js"></script>
    <script src="{{ asset('js/wishlist.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Search suggestions
            const searchInput = $('#search-input');
            const suggestionsBox = $('#suggestions');

            searchInput.on('input', function() {
                let query = $(this).val().trim();
                if (query.length >= 2) {
                    $.ajax({
                        url: '{{ route("products.suggestions") }}',
                        method: 'GET',
                        data: { query: query },
                        success: function(response) {
                            suggestionsBox.empty().show();
                            if (response.length === 0) {
                                suggestionsBox.append('<div class="suggestion-item">Không có gợi ý</div>');
                                return;
                            }
                            response.forEach(item => {
                                suggestionsBox.append(
                                    `<div class="suggestion-item" data-value="${item.value}">${item.label}</div>`
                                );
                            });

                            $('.suggestion-item').on('click', function() {
                                searchInput.val($(this).data('value'));
                                suggestionsBox.empty().hide();
                                searchInput.closest('form').submit();
                            });
                        }
                    });
                } else {
                    suggestionsBox.empty().hide();
                }
            });

            // Animation on scroll
            $(window).on('scroll', function() {
                $('.product-card').each(function() {
                    if ($(this).offset().top < $(window).scrollTop() + $(window).height() - 50) {
                        $(this).addClass('fade-in-up');
                    }
                });
            });
        });
    </script>
    @yield('scripts')
</body>
</html>