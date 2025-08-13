
@extends('layouts.app')

@section('title', 'Đăng Ký')

@section('content')
<style>
    .register-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        font-family: Arial, sans-serif;
    }

    .register-card {
        max-width: 400px;
        margin: 40px auto;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    .register-card h2 {
        background-color: #1a252f;
        color: #d4a017;
        font-size: 1.8rem;
        text-align: center;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

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

    .btn-primary {
        background-color: #d4a017;
        border: none;
        color: #1a252f;
        font-weight: bold;
        transition: background-color 0.3s ease;
        padding: 10px 20px;
    }

    .btn-primary:hover {
        background-color: #b58900;
    }

    .google-btn {
        background-color: #dc3545;
        border: none;
        color: #fff;
        font-weight: bold;
        transition: background-color 0.3s ease;
        padding: 10px 20px;
    }

    .google-btn:hover {
        background-color: #bb2d3b;
    }

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

    .input-group .btn-outline-secondary {
        border: 1px solid #ddd;
        border-radius: 0 5px 5px 0;
        background-color: #f8f9fa;
        transition: background-color 0.3s ease;
    }

    .input-group .btn-outline-secondary:hover {
        background-color: #e0e0e0;
    }

    .text-primary {
        color: #d4a017 !important;
        font-weight: bold;
        transition: color 0.3s ease;
    }

    .text-primary:hover {
        color: #b58900 !important;
    }

    @media (max-width: 576px) {
        .register-card {
            margin: 20px;
            padding: 15px;
        }

        .register-card h2 {
            font-size: 1.5rem;
        }

        .btn-primary, .google-btn {
            padding: 8px 15px;
            font-size: 0.9rem;
        }

        .form-control {
            font-size: 0.9rem;
        }
    }
</style>

<div class="register-container">
    <div class="register-card">
        <h2>Đăng ký tài khoản</h2>

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
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="needs-validation" novalidate>
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Họ và tên</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                       id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @else
                <div class="invalid-feedback">Vui lòng nhập họ và tên.</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                       id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @else
                <div class="invalid-feedback">Vui lòng nhập email hợp lệ.</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                       id="phone" name="phone" value="{{ old('phone') }}" required>
                @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
                @else
                <div class="invalid-feedback">Vui lòng nhập số điện thoại.</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <div class="input-group">
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           id="password" name="password" required>
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                        <i class="bi bi-eye"></i>
                    </button>
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                    <div class="invalid-feedback">Mật khẩu phải có ít nhất 8 ký tự.</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                <div class="input-group">
                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                           id="password_confirmation" name="password_confirmation" required>
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmation')">
                        <i class="bi bi-eye"></i>
                    </button>
                    @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                    <div class="invalid-feedback">Vui lòng xác nhận mật khẩu.</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <button type="submit" class="btn btn-primary">Đăng ký</button>
                <button type="button" class="btn google-btn" onclick="alert('Google login chưa được tích hợp')">
                    <i class="bi bi-google"></i> Google
                </button>
            </div>

            <div class="text-center">
                <p>Đã có tài khoản? <a href="{{ route('login.form') }}" class="text-primary">Đăng nhập</a></p>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        const icon = input.nextElementSibling.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }

    (function () {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
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
