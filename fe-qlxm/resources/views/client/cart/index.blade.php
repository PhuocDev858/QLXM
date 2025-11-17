@extends('layouts.client')

@section('title', 'Giỏ hàng & Đặt hàng - QLXM')

{{-- 1. TẤT CẢ CSS ĐÃ ĐƯỢC GOM VÀO ĐÂY --}}
@push('styles')
    <style>
        /* CSS cho trang và tiêu đề */
        body {
            background: #fff;
            min-height: 100vh;
        }

        .page-heading {
            background: #f7f7f7;
            color: #2a2a2a;
            padding: 60px 0;
            margin-bottom: 40px;
        }

        .page-heading h4,
        .page-heading h2 {
            color: #2a2a2a;
            text-shadow: none;
        }

        /* CSS cho Card */
        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            border-bottom: 1px solid #ddd;
            padding: 15px 20px;
            font-weight: bold;
            background: #f8f9fa;
            color: #2a2a2a;
        }

        /* CSS cho các item trong giỏ hàng */
        .cart-item-image {
            height: 80px;
            width: 100%;
            object-fit: cover;
            border-radius: 8px;
        }

        .cart-quantity-input {
            width: 60px !important;
            border-radius: 8px;
            text-align: center;
        }

        .btn-quantity-control {
            border: 1px solid #dc3545;
            color: #dc3545;
            transition: all 0.2s ease;
            border-radius: 4px;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            background: #fff;
        }

        .btn-quantity-control:hover {
            background: #dc3545;
            border-color: #dc3545;
            color: white;
        }

        .btn-remove-item {
            border: 1px solid #dc3545;
            color: #dc3545;
            transition: all 0.2s ease;
            border-radius: 4px;
            font-size: 0.85rem;
            background: #fff;
        }

        .btn-remove-item:hover {
            background: #dc3545;
            border-color: #dc3545;
            color: white;
        }

        /* CSS cho Form */
        .form-control {
            border-radius: 4px;
            border: 1px solid #ddd;
            padding: 10px 12px;
            transition: border-color 0.2s ease;
        }

        .form-control:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
        }

        /* CSS cho các nút chính */
        .btn-checkout {
            background: #dc3545;
            border: none;
            border-radius: 4px;
            padding: 12px 24px;
            font-weight: 600;
            transition: background 0.2s ease;
            color: #fff;
        }

        .btn-checkout:hover {
            background: #c82333;
            color: #fff;
        }

        .btn-checkout:disabled,
        .btn-checkout.btn-loading {
            background: #6c757d;
            opacity: 0.7;
        }

        /* CSS cho giỏ hàng trống */
        .empty-cart-container {
            max-width: 600px;
        }

        .empty-cart {
            background: white;
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .empty-cart i {
            color: #bdc3c7;
            margin-bottom: 30px;
        }

        .empty-cart h3 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .empty-cart p {
            color: #7f8c8d;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .empty-cart .btn {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border: none;
            border-radius: 10px;
            padding: 15px 30px;
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .empty-cart .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4);
        }

        /* CSS Helper chung */
        .bg-primary {
            background: #dc3545 !important;
            color: #fff !important;
        }

        .bg-success {
            background: #28a745 !important;
            color: #fff !important;
        }

        .bg-light {
            background: #f8f9fa !important;
        }

        .text-primary {
            color: #dc3545 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .text-success {
            color: #28a745 !important;
        }

        .alert {
            border-radius: 4px;
            border: 1px solid transparent;
        }

        .empty-cart .btn {
            background: #dc3545;
            border: none;
            border-radius: 4px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            transition: background 0.2s ease;
        }

        .empty-cart .btn:hover {
            background: #c82333;
        }
    </style>
@endpush


@section('content')
    <div class="page-heading products-heading header-text">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="text-content">
                        <h4>Giỏ hàng & Đặt hàng</h4>
                        <h2>Hoàn tất đơn hàng của bạn</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <h6>Vui lòng kiểm tra lại thông tin:</h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (!empty($cartItems) && count($cartItems) > 0)
            <form action="{{ route('client.checkout.process') }}" method="POST" id="checkout-form">
                @csrf
                <input type="hidden" name="_token" value="{{ csrf_token() }}" id="csrf-token-input">
                <div class="row">
                    <div class="col-lg-7 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Giỏ hàng của bạn</h5>
                            </div>
                            <div class="card-body p-0" id="cart-items-container">
                                @foreach ($cartItems as $item)
                                    <div class="border-bottom p-3" data-product-row="{{ $item['id'] }}">
                                        <div class="row align-items-center">
                                            <div class="col-3">
                                                {{-- 2. ĐÃ XÓA INLINE STYLE --}}
                                                <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}"
                                                    class="img-fluid rounded cart-item-image">
                                            </div>
                                            <div class="col-6">
                                                <h6 class="mb-1 fw-bold">{{ $item['name'] }}</h6>
                                                <div class="text-primary fw-bold">
                                                    {{ number_format($item['price'], 0, ',', '.') }} VNĐ
                                                </div>
                                            </div>
                                            <div class="col-3 text-end">
                                                <div class="fw-bold text-danger">
                                                    {{ number_format($item['subtotal'], 0, ',', '.') }} VNĐ
                                                </div>
                                                <div class="mt-2">
                                                    <input type="number"
                                                        class="form-control form-control-sm cart-quantity-input mb-2"
                                                        value="{{ $item['quantity'] }}" min="1"
                                                        data-product-id="{{ $item['id'] }}"
                                                        onchange="updateQuantityAjax('{{ $item['id'] }}', this.value)">
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger w-100 btn-remove-item"
                                                        onclick="removeItemAjax('{{ $item['id'] }}')" title="Xóa sản phẩm">
                                                        Xóa
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="p-3 bg-light">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Tạm tính:</span>
                                                <span id="subtotal-display">{{ number_format($totalPrice, 0, ',', '.') }} VNĐ</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Phí vận chuyển:</span>
                                                <span class="text-success">Miễn phí</span>
                                            </div>
                                        </div>
                                        <div class="col-6 text-end">
                                            <h5 class="text-danger fw-bold mb-0">
                                                Tổng: <span id="total-display">{{ number_format($totalPrice, 0, ',', '.') }}</span> VNĐ
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="card shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">Thông tin giao hàng</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        Họ và tên <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}" placeholder="VD: Nguyễn Văn A" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        Số điện thoại <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" name="phone"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        value="{{ old('phone') }}" placeholder="VD: 0912345678" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}" placeholder="VD: example@gmail.com" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        Địa chỉ giao hàng <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3"
                                        placeholder="VD: 123 Đường ABC, Phường XYZ, Quận DEF, TP.HCM" required>{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        Ghi chú (tùy chọn)
                                    </label>
                                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2"
                                        placeholder="Ghi chú thêm về đơn hàng...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-danger w-100 btn-lg mb-3 btn-checkout"
                                    id="order-btn">
                                    Đặt hàng ngay
                                </button>

                                <a href="{{ route('client.motorcycles') }}" class="btn btn-outline-secondary w-100">
                                    Tiếp tục mua hàng
                                </a>

                                <div class="mt-3 p-3 bg-light rounded">
                                    <h6 class="mb-2">Cam kết của chúng tôi:</h6>
                                    <ul class="list-unstyled mb-0 small">
                                        <li>✓ Giao hàng nhanh chóng trong 2-3 ngày</li>
                                        <li>✓ Hỗ trợ đổi trả trong 7 ngày</li>
                                        <li>✓ Thanh toán khi nhận hàng (COD)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @else
            <div class="text-center py-5">
                {{-- 2. ĐÃ XÓA INLINE STYLE --}}
                <div class="empty-cart mx-auto empty-cart-container">
                    <h3 class="mb-3">Giỏ hàng trống</h3>
                    <p class="mb-4">Bạn chưa có sản phẩm nào trong giỏ hàng.<br>Hãy khám phá các sản phẩm xe máy chất
                        lượng cao của chúng tôi!</p>
                    <a href="{{ route('client.motorcycles') }}" class="btn btn-lg">
                        Khám phá sản phẩm
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection

{{-- 4. TẤT CẢ JAVASCRIPT ĐÃ ĐƯỢC GOM VÀO ĐÂY --}}
@push('scripts')
<script src="{{ asset('js/client/cart.js') }}"></script>
@endpush
