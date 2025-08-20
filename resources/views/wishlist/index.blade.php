@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="wishlist-container container mt-5">
    <h1 class="mb-4 text-center">Danh sách yêu thích</h1>

    @if (session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger text-center">{{ session('error') }}</div>
    @endif

    @if (empty($wishlist))
        <p class="text-center">Danh sách yêu thích của bạn đang trống.</p>
        <div class="text-center mt-3">
            <a href="{{ route('products.index') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
        </div>
    @else
        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>Hình ảnh</th>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($wishlist as $id => $item)
                    <tr id="row-wishlist-{{ $id }}">
                        <td>
                            <img src="{{ $item['hinhdaidien'] }}" width="100" alt="{{ $item['tieude'] }}">
                        </td>
                        <td>
                            <a href="{{ route('products.show', $id) }}">
                                {{ $item['tieude'] }}
                            </a>
                        </td>
                        <td>{{ $item['gia'] > 0 ? number_format($item['gia'], 0, ',', '.') . 'đ' : 'Liên hệ' }}</td>
                        <td>
                            <button class="btn btn-danger btn-sm btn-remove-wishlist" data-id="{{ $id }}">
                                <i class="bi bi-trash"></i> Xoá
                            </button>
                            <button class="btn btn-primary btn-sm mt-2 btn-buy-wishlist" data-id="{{ $id }}">Mua hàng</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<script src="{{ asset('js/wishlist.js') }}"></script>
@endsection
