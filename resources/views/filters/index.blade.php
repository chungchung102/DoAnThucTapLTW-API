@extends('layouts.app')

@section('title', 'Bộ lọc sản phẩm')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/filters.css') }}">
@endsection

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4">Bộ lọc sản phẩm</h2>

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
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('filters.index') }}" method="GET" class="mb-5">
        <div class="mb-3">
            <label for="category" class="form-label">Chọn danh mục</label>
            <select name="category" id="category" class="form-select" required onchange="this.form.submit()">
                <option value="">Chọn danh mục</option>
                @foreach ($categories as $id => $name)
                    <option value="{{ $id }}" {{ $categoryId == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
    </form>

    @if (!empty($filters))
        <form id="filters-form" action="{{ route('filters.apply') }}" method="POST" class="needs-validation" novalidate>
            @csrf
            <input type="hidden" name="category" value="{{ $categoryId }}">
            <div class="filter-container">
                @foreach ($filters as $group)
                    <div class="filter-group">
                        <h3 data-bs-toggle="collapse" 
                            data-bs-target="#filters-{{ $group['IDParentFilter'] }}"
                            aria-expanded="true" 
                            aria-controls="filters-{{ $group['IDParentFilter'] }}">
                            {{ $group['FilterParentName'] }}
                        </h3>
                        <div class="collapse show" id="filters-{{ $group['IDParentFilter'] }}">
                            @foreach ($group['details'] as $filter)
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="filters[{{ $group['IDParentFilter'] }}][]" 
                                           id="filter-{{ $filter['IDFilter'] }}" 
                                           value="{{ $filter['IDFilter'] }}" 
                                           data-alias="{{ $filter['AlilasPath'] }}">
                                    <label class="form-check-label" for="filter-{{ $filter['IDFilter'] }}">{{ $filter['FilterName'] }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="submit" class="btn btn-primary">Áp dụng bộ lọc</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Tiếp tục mua sắm</a>
        </form>
    @elseif ($categoryId)
        <p class="text-center">Không có bộ lọc nào!</p>
    @endif
</div>
@endsection

@section('scripts')
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