@extends('layouts.app')

@section('title', 'Liên Hệ')

@section('content')
<style>
    /* General Contact Styles */
    .contact-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        font-family: Arial, sans-serif;
    }

    .contact-card {
        max-width: 600px; /* Tăng max-width để phù hợp với form dài hơn */
        margin: 40px auto;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    .contact-card h2 {
        background-color: #1a252f; /* Đồng bộ với navbar */
        color: #d4a017; /* Màu vàng đồng bộ */
        font-size: 1.8rem;
        text-align: center;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    /* Form Styles */
    .form-label {
        color: #1a252f;
        font-weight: bold;
    }

    .form-control {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        transition: border-color 0.3s ease;
    }

    .form-control:focus {
        border-color: #d4a017;
        box-shadow: 0 0 5px rgba(212, 160, 23, 0.3);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        color: #dc3545;
        font-size: 0.9rem;
    }

    /* Textarea */
    textarea.form-control {
        resize: vertical; /* Chỉ cho phép thay đổi chiều cao */
        min-height: 100px;
    }

    /* Button Styles */
    .btn-primary {
        background-color: #d4a017; /* Màu vàng đồng bộ */
        border: none;
        color: #1a252f;
        font-weight: bold;
        transition: background-color 0.3s ease;
        padding: 10px 20px;
    }

    .btn-primary:hover {
        background-color: #b58900;
    }

    /* Alert Styles */
    .alert-success {
        color: #3e7c3e;
        background-color: #e6f3e6;
        border-color: #3e7c3e;
        border-radius: 5px;
        text-align: center;
        font-weight: bold;
    }

    .alert-danger {
        color: #dc3545;
        background-color: #f8d7da;
        border-color: #dc3545;
        border-radius: 5px;
        text-align: center;
        font-weight: bold;
    }

    /* Responsive */
    @media (max-width: 576px) {
        .contact-card {
            margin: 20px;
            padding: 15px;
        }

        .contact-card h2 {
            font-size: 1.5rem;
        }

        .btn-primary {
            padding: 8px 15px;
            font-size: 0.9rem;
        }

        .form-control {
            font-size: 0.9rem;
        }
    }
</style>

<div class="contact-container">
    <div class="contact-card">
        <h2>Liên Hệ Với Chúng Tôi</h2>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('submit.contact') }}" method="POST" class="needs-validation" novalidate>
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Họ tên</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Nhập họ tên" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @else
                    <div class="invalid-feedback">Vui lòng nhập họ tên.</div>
                @endif
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Địa chỉ email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Nhập địa chỉ email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @else
                    <div class="invalid-feedback">Vui lòng nhập email hợp lệ.</div>
                @endif
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Điện thoại</label>
                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="Nhập số điện thoại" value="{{ old('phone') }}" required pattern="^(03|05|07|08|09)[0-9]{8}$" maxlength="10">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @else
                    <div class="invalid-feedback">Vui lòng nhập số điện thoại hợp lệ.</div>
                @endif
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Nội dung</label>
                <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="4" placeholder="Nhập nội dung" required>{{ old('message') }}</textarea>
                @error('message')
                    <div class="invalid-feedback">{{ $message }}</div>
                @else
                    <div class="invalid-feedback">Vui lòng nhập nội dung.</div>
                @endif
            </div>
            <div class="mb-3">
                <label for="captcha" class="form-label">Mã xác nhận: 9999</label>
                <input type="text" class="form-control @error('captcha') is-invalid @enderror" id="captcha" name="captcha" placeholder="Nhập mã xác nhận" value="{{ old('captcha') }}" required>
                @error('captcha')
                    <div class="invalid-feedback">{{ $message }}</div>
                @else
                    <div class="invalid-feedback">Vui lòng nhập mã xác nhận chính xác.</div>
                @endif
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Gửi đi</button>
            </div>
        </form>
    </div>
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
                console.log(new FormData(form));
            }, false);
        });
    })();

    document.getElementById('phone').addEventListener('input', function(e) {
        const value = e.target.value;
        const regex = /^(03|05|07|08|09)[0-9]{8}$/;
        if (!regex.test(value) && value.length > 0) {
            e.target.classList.add('is-invalid');
        } else {
            e.target.classList.remove('is-invalid');
        }
    });
</script>
@endsection