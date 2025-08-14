@extends('layouts.app')

@section('title', 'Thanh Toán')

@section('styles')
 
@endsection

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4">Đặt hàng</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {!! session('success') !!}
        </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif


    <form action="{{ route('payment.process') }}" method="POST" class="needs-validation" novalidate>
        @csrf

        <div class="mb-3">
            <label for="full_name" class="form-label">Họ và Tên</label>
            <input type="text" class="form-control @error('full_name') is-invalid @enderror" id="full_name" name="full_name" value="{{ old('full_name') }}" required>
            @error('full_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @else
                <div class="invalid-feedback">Vui lòng nhập họ và tên.</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @else
                <div class="invalid-feedback">Vui lòng nhập email hợp lệ.</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="sdt" class="form-label">SĐT</label>
            <input type="tel" class="form-control @error('sdt') is-invalid @enderror" id="sdt" name="sdt" value="{{ old('sdt') }}" required pattern="[0-9]{10}" maxlength="10">
            @error('sdt')
                <div class="invalid-feedback">{{ $message }}</div>
            @else
                <div class="invalid-feedback">Số điện thoại phải gồm đúng 10 chữ số.</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Địa Chỉ</label>
            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" required>{{ old('address') }}</textarea>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @else
                <div class="invalid-feedback">Vui lòng nhập địa chỉ.</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Phương thức thanh toán</label>
            <div class="form-check">
                <input class="form-check-input @error('payment_method') is-invalid @enderror" type="radio" name="payment_method" id="cod" value="cod" {{ old('payment_method') == 'cod' ? 'checked' : '' }} required>
                <label class="form-check-label" for="cod">Thanh toán khi nhận hàng (COD)</label>
            </div>
            <div class="form-check">
                <input class="form-check-input @error('payment_method') is-invalid @enderror" type="radio" name="payment_method" id="bank_transfer" value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'checked' : '' }} required>
                <label class="form-check-label" for="bank_transfer">Chuyển khoản ngân hàng</label>
            </div>
            <div class="form-check">
                <input class="form-check-input @error('payment_method') is-invalid @enderror" type="radio" name="payment_method" id="online_payment" value="online_payment" {{ old('payment_method') == 'online_payment' ? 'checked' : '' }} required>
                <label class="form-check-label" for="online_payment">Thanh toán trực tuyến</label>
            </div>
            @error('payment_method')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @else
                <div class="invalid-feedback d-block">Vui lòng chọn phương thức thanh toán.</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Đặt hàng</button>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Tiếp tục mua sắm</a>
    </form>
</div>

<script>
    (function() {
        'use strict';
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>
@endsection
