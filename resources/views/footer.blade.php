<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        /* Footer Styles */
        footer {
            background-color: #1a252f; /* Đồng bộ với màu nền navbar */
            color: #d4a017; /* Màu chữ vàng đồng bộ với navbar */
            padding: 40px 0;
            font-family: Arial, sans-serif;
            margin-top: 50px; /* Giảm margin-top để phù hợp hơn */
            z-index: 1000;
        }

        footer a {
            color: #d4a017;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: #ffffff; /* Hiệu ứng hover tương tự navbar */
        }

        footer .bi {
            font-size: 1.5rem;
            margin-right: 10px;
        }

        footer h5 {
            color: #d4a017;
            font-weight: bold;
            margin-bottom: 20px;
            font-size: 1.2rem;
        }

        footer p {
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        footer .social-links a {
            margin-right: 15px;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            footer .col-md-3 {
                margin-bottom: 30px;
            }
        }

        @media (max-width: 576px) {
            footer h5 {
                font-size: 1rem;
            }

            footer p {
                font-size: 0.85rem;
            }

            footer .bi {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <footer class="text-white py-3">
        <div class="container-fluid">
            <div class="row">
                <!-- Cột 1: Giới thiệu -->
                <div class="col-md-3">
                    <h5>Giới thiệu</h5>
                    <p>
                        Chồi xanh cung cấp các loại máy tính, laptop và thiết bị công nghệ chất lượng cao, đáp ứng mọi nhu cầu của doanh nghiệp và cá nhân.
                    </p>
                    <p>
                        <i class="bi bi-envelope me-2"></i>
                        <a href="mailto:info@choixanh.vn">info@thanhdo.vn</a>
                    </p>
                </div>

                <!-- Cột 2: Thông tin công ty -->
                <div class="col-md-3">
                    <h5>Công ty TNHH Tư Vấn Dịch Vụ Chồi Xanh</h5>
                    <p>
                        82A -B, P. Tân Sơn Nhì, Q. Tân Phú, TP. HCM<br>
                        MST: 0314583179<br>
                        <i class="bi bi-telephone me-2"></i> 028 3974 3179
                    </p>
                </div>

                <!-- Cột 3: Theo dõi -->
                <div class="col-md-3">
                    <h5>Theo dõi chúng tôi</h5>
                    <p class="social-links">
                        <a href="https://www.facebook.com/"><i class="bi bi-facebook"></i></a>
                        <a href="https://www.youtube.com/"><i class="bi bi-youtube"></i></a>
                        <a href="https://www.instagram.com/"><i class="bi bi-instagram"></i></a>
                        <a href="https://x.com/"><i class="bi bi-twitter"></i></a>
                        <a href="https://www.linkedin.com/"><i class="bi bi-linkedin"></i></a>
                    </p>
                    <p>
                        Thành viên của <a href="https://ato.vn">Ato.vn</a>
                    </p>
                </div>

                <!-- Cột 4: Chính sách -->
                <div class="col-md-3">
                    <h5>Chính sách</h5>
                    <p>
                        <a href="https://choixanh.com.vn/dieu-khoan-va-dieu-kien-su-dung-trang-web">Điều khoản sử dụng</a><br>
                        <a href="https://choixanh.com.vn/chinh-sach-xu-ly-du-lieu">Chính sách xử lý dữ liệu</a><br>
                          <a href="https://choixanh.com.vn/chinh-sach-su-dung-cookie">Chính sách cookie</a><br>
                        <a href="https://choixanh.com.vn/chinh-sach-hoat-dong-va-hop-tac">Hợp tác</a>
                      
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>