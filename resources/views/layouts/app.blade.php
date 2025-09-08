<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Mô tả ngắn gọn về trang web hoặc sản phẩm">
    <meta name="keywords" content="máy tính, điện thoại, tivi, máy lạnh, mua sắm">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BookSaw')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/filters.css') }}">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js" async></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   
    @yield('styles')
</head>
<body>
    <nav class="navbar elegant-navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Close">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav me-auto">
                    <a class="nav-link text-gold" href="{{ route('products.index') }}"><i class="bi bi-house-fill"></i> Trang chủ</a>
                    <div class="nav-item dropdown">
                        <a class="nav-link text-gold dropdown-toggle" href="#" role="button" id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-box"></i> Sản phẩm
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                            <li><a class="dropdown-item" href="{{ route('products.index', ['category' => 'computer']) }}"><i class="bi bi-pc-display"></i> Máy tính</a></li>
                            <li><a class="dropdown-item" href="{{ route('products.index', ['category' => 'phone']) }}"><i class="bi bi-phone-fill"></i> Điện thoại di động</a></li>
                            <li><a class="dropdown-item" href="{{ route('products.index', ['category' => 'tv']) }}"><i class="bi bi-display"></i> Tivi</a></li>
                            <li><a class="dropdown-item" href="{{ route('products.index', ['category' => 'air_conditioner']) }}"><i class="bi bi-fan"></i> Máy lạnh</a></li>
                        </ul>
                    </div>
                    <a class="nav-link text-gold" href="{{ route('products.index', ['category' => 'tintuc']) }}"><i class="bi bi-file-text"></i> Tin tức</a>
                    <a class="nav-link text-gold" href="{{ route('lien-he') }}"><i class="bi bi-envelope-fill"></i> Liên hệ</a>
                </div>
                <a class="nav-link text-gold" href="{{ route('filters.index') }}"><i class="bi bi-funnel fs-3"></i></a>
                <form action="{{ route('products.search') }}" method="GET" class="search-form d-flex">
                    <input type="text" name="query" placeholder="Tìm kiếm sản phẩm..." autocomplete="off" id="search-input" class="form-control me-2">
                    <button type="submit" class="btn btn-dark">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
                <div id="suggestions"></div>
                <div class="navbar-nav ms-3 align-items-lg-center">
                    <a class="nav-link text-gold" href="/cart">
                        <i class="bi bi-cart-check"></i> Giỏ hàng: <span id="cart-count">{{ $cartCount ?? 0 }}</span>
                    </a>
                    <a class="nav-link text-gold" href="/wishlist">
                        <i class="bi bi-heart-fill"></i> Yêu thích: <span id="wishlist-count">{{ $wishlistCount ?? 0 }}</span>
                    </a>
                    @if (session('user_email'))
                        <div class="nav-item dropdown">
                            <a class="nav-link text-gold dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                Xin chào, {{ session('user_name') ?? session('user_email') }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('cart.history') }}"><i class="bi bi-clock"></i> Lịch sử mua hàng</a>
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right"></i> Đăng xuất</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a class="nav-link text-gold" href="{{ route('login.form') }}"><i class="bi bi-box-arrow-in-right"></i> Đăng nhập</a>
                        <a class="nav-link text-gold" href="{{ route('register.form') }}"><i class="bi bi-person-plus"></i> Đăng ký</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    @yield('content')
    @include('footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- <script src="{{ asset('js/cart.js') }}"></script> -->
    <script src="{{ asset('js/wishlist.js') }}"></script>
    <!-- <script src="{{ asset('js/product.js') }}"></script> -->
    <script>
        $(document).ready(function() {
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
                                let label = item.label;
                                suggestionsBox.append(
                                    `<div class="suggestion-item" data-value="${item.value}">${label}</div>`
                                );
                            });

                            $('.suggestion-item').on('click', function() {
                                searchInput.val($(this).data('value'));
                                suggestionsBox.empty().hide();
                                searchInput.closest('form').submit();
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Lỗi AJAX:', status, error);
                            suggestionsBox.empty().append('<div class="suggestion-item">Lỗi khi lấy gợi ý</div>').show();
                        }
                    });
                } else {
                    suggestionsBox.empty().hide();
                }
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.d-flex').length) {
                    suggestionsBox.empty().hide();
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>