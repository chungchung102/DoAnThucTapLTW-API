@extends('layouts.app')

@section('title', $title ?? 'Giỏ hàng')

@section('content')
<style>
    .cart-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        font-family: Arial, sans-serif;
        margin-bottom: 100px;
    }

    .cart-container h1 {
        color: #d4a017;
        font-size: 2rem;
        text-align: center;
        margin-bottom: 20px;
    }

    .table {
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }

    .table th {
        background-color: #1a252f;
        color: #d4a017;
        font-weight: bold;
        text-align: center;
    }

    .table td {
        vertical-align: middle;
        text-align: center;
    }

    .image-container {
        width: 100px;
        height: 100px;
        overflow: hidden;
        border-radius: 5px;
        margin: auto;
    }

    .image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 5px;
    }

    .btn-primary {
        background-color: #d4a017;
        border: none;
        color: #1a252f;
        font-weight: bold;
    }

    .btn-primary:hover {
        background-color: #b58900;
    }

    .btn-success {
        background-color: #3e7c3e;
        border: none;
        color: #fff;
        font-weight: bold;
    }

    .btn-success:hover {
        background-color: #2e5c2e;
    }

    .btn-danger {
        background-color: #dc3545;
        border: none;
        color: #fff;
        font-weight: bold;
    }

    .btn-danger:hover {
        background-color: #bb2d3b;
    }

    input[type="number"] {
        width: 60px;
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
        text-align: center;
    }

    .total-price {
        color: #d4a017;
        font-size: 1.5rem;
        font-weight: bold;
        text-align: right;
        margin-top: 20px;
    }

    @media (max-width: 576px) {
        .image-container {
            width: 80px;
            height: 80px;
        }

        .table th, .table td {
            font-size: 0.85rem;
            padding: 8px;
        }

        input[type="number"] {
            width: 50px;
        }

        .total-price {
            text-align: center;
            font-size: 1.2rem;
        }
    }
</style>

<div class="cart-container">
    <h1>Giỏ hàng của bạn</h1>

    @if (session('success'))
        <div class="text-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="text-danger">{{ session('error') }}</div>
    @endif

    @if (empty($cart))
        <p class="text-center">Giỏ hàng của bạn đang trống.</p>
        <div class="text-center mt-3">
            <a href="{{ route('products.index') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>Hình ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Tổng</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cart as $id => $item)
                        <tr>
                            <td>
                                <div class="image-container">
                                    <img src="{{ $item['hinhdaidien'] ?? 'https://via.placeholder.com/300x200?text=No+Image' }}"
                                         alt="{{ $item['tieude'] }}">
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('products.show', $id) }}" class="text-decoration-none">
                                    {{ $item['tieude'] }}
                                </a>
                            </td>
                            <td>{{ number_format($item['gia'], 0, ',', '.') }}đ</td>
                            <td>
                                <form action="{{ route('cart.update') }}" method="POST" class="d-flex flex-column align-items-center">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $id }}">
                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1">
                                    <button type="submit" class="btn btn-sm btn-primary mt-1">Cập nhật</button>
                                </form>
                            </td>
                            <td>{{ number_format($item['gia'] * $item['quantity'], 0, ',', '.') }}đ</td>
                            <td>
                                <form action="{{ route('cart.remove', $id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Xoá sản phẩm này?')">
                                        <i class="bi bi-trash"></i> Xoá
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <h4 class="total-price">Tổng cộng: {{ number_format($total, 0, ',', '.') }}đ</h4>

        <div class="text-center mt-4">
            <a href="{{ route('products.index') }}" class="btn btn-primary me-2">Tiếp tục mua sắm</a>
            <a href="{{ route('payment.form') }}" class="btn btn-success">Đặt hàng</a>
        </div>
    @endif
</div>
@endsection
