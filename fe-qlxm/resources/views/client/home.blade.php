@extends('layouts.client')

@section('title', 'Trang Chủ - QLXM')
@section('description', 'Hệ thống quản lý xe máy hiện đại, cung cấp thông tin về các dòng xe máy mới nhất')

@section('content')
    <!-- Banner Starts Here -->
    <div class="banner header-text">
        <div class="owl-banner owl-carousel">
            <div class="banner-item-01">
                <div class="text-content">
                    <h4>Khuyến Mãi Đặc Biệt</h4>
                    <h2>Xe Máy Mới Nhất</h2>
                </div>
            </div>
            <div class="banner-item-02">
                <div class="text-content">
                    <h4>Ưu Đãi Flash</h4>
                    <h2>Xe Máy Chất Lượng Cao</h2>
                </div>
            </div>
            <div class="banner-item-03">
                <div class="text-content">
                    <h4>Phút Cuối</h4>
                    <h2>Giảm Giá Sốc</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Banner Ends Here -->

    <!-- Latest Motorcycles -->
    <div class="latest-products">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-heading">
                        <h2>Xe Máy Mới Nhất</h2>
                        <a href="{{ route('client.motorcycles') }}">xem tất cả xe máy <i class="fa fa-angle-right"></i></a>
                    </div>
                </div>

                @if (isset($error))
                    <div class="col-md-12">
                        <div class="alert alert-warning text-center">
                            <h4>{{ $error }}</h4>
                            <p>Vui lòng thử lại sau hoặc liên hệ quản trị viên.</p>
                        </div>
                    </div>
                @endif

                @if (count($products) > 0)
                    <div class="col-md-12">
                        <div class="products-carousel-wrapper position-relative">
                            <!-- Navigation Arrows -->
                            <button class="carousel-nav-btn prev-btn" id="prevBtn">
                                <i class="fa fa-angle-left"></i>
                            </button>
                            <button class="carousel-nav-btn next-btn" id="nextBtn">
                                <i class="fa fa-angle-right"></i>
                            </button>

                            <!-- Products Carousel -->
                            <div class="products-carousel-container" id="productsCarousel">
                                <div class="products-carousel-track">
                                    @foreach ($products as $product)
                                        <div class="carousel-item-wrapper">
                                            <div class="product-item">
                                                <a href="{{ route('client.motorcycles.show', $product['id']) }}">
                                                    @if ($product['image_url'])
                                                        <img src="{{ $product['image_url'] }}" alt="{{ $product['name'] }}"
                                                            style="width: 100%; height: 250px; object-fit: cover;">
                                                    @else
                                                        <img src="{{ asset('img/product_01.jpg') }}" alt="{{ $product['name'] }}"
                                                            style="width: 100%; height: 250px; object-fit: cover;">
                                                    @endif
                                                </a>
                                                <div class="down-content">
                                                    <a href="{{ route('client.motorcycles.show', $product['id']) }}">
                                                        <h4>{{ $product['name'] }}</h4>
                                                    </a>
                                                    <h6>{{ number_format($product['price'], 0, ',', '.') }} VNĐ</h6>

                                                    @if (isset($product['brand']['name']))
                                                        <p><strong>Hãng:</strong> {{ $product['brand']['name'] }}</p>
                                                    @endif

                                                    @if (isset($product['category']['name']))
                                                        <p><strong>Loại:</strong> {{ $product['category']['name'] }}</p>
                                                    @endif

                                                    <ul class="stars">
                                                        <li><i class="fa fa-star"></i></li>
                                                        <li><i class="fa fa-star"></i></li>
                                                        <li><i class="fa fa-star"></i></li>
                                                        <li><i class="fa fa-star"></i></li>
                                                        <li><i class="fa fa-star"></i></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Dots Indicator -->
                            <div class="carousel-dots" id="carouselDots"></div>
                        </div>
                    </div>
                @else
                    <div class="col-md-12">
                        <div class="text-center py-5">
                            <h4>Không có sản phẩm nào</h4>
                            <p>Hiện tại chưa có xe máy nào để hiển thị.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="{{ asset('css/carousel.css') }}">

    <style>
        /* Custom gradient for navigation buttons */
        .carousel-nav-btn {
            background: linear-gradient(135deg, #ed1b24 0%, #c41e3a 100%) !important;
            border: none !important;
            box-shadow: 0 4px 15px rgba(237, 27, 36, 0.4);
        }

        .carousel-nav-btn:hover:not(:disabled) {
            background: linear-gradient(135deg, #c41e3a 0%, #ed1b24 100%) !important;
            box-shadow: 0 6px 20px rgba(237, 27, 36, 0.6);
        }

        .carousel-nav-btn:disabled {
            background: #ccc !important;
            box-shadow: none;
        }

        .carousel-dot.active {
            background: #ed1b24;
        }

        .carousel-dot:hover {
            background: #ed1b24;
        }
    </style>

    <script>
        // Global config cho client scripts
        window.APP_CONFIG = {
            apiUrl: '{{ rtrim(config("app.be_api_url"), "/") }}'
        };
    </script>
    <script src="{{ asset('js/carousel.js') }}"></script>
    <script src="{{ asset('js/client/home.js') }}"></script>

    <!-- Pagination (Hidden for homepage, keep for other pages) -->
    <div style="display: none;">
        @include('components.pagination')
    </div>

    <!-- Products by Brand (Lazy Load) -->
    @if (count($brands) > 0)
        @foreach ($brands as $index => $brand)
            <div class="brand-products-section lazy-load-section" data-brand-id="{{ $brand['id'] }}" data-brand-name="{{ $brand['name'] }}" data-brand-index="{{ $index }}">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="section-heading">
                                <h2>Xe Máy {{ $brand['name'] }}</h2>
                                <a href="{{ route('client.brands.show', $brand['id']) }}">xem tất cả <i class="fa fa-angle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="loading-placeholder text-center py-5">
                                <div class="spinner-border text-danger" role="status">
                                    <span class="sr-only">Đang tải...</span>
                                </div>
                                <p class="mt-3">Đang tải sản phẩm {{ $brand['name'] }}...</p>
                            </div>
                            
                            <!-- Brand Products Carousel -->
                            <div class="brand-carousel-wrapper" style="display: none;">
                                <button class="carousel-nav-btn prev-btn" data-carousel="brand-{{ $index }}">
                                    <i class="fa fa-angle-left"></i>
                                </button>
                                <button class="carousel-nav-btn next-btn" data-carousel="brand-{{ $index }}">
                                    <i class="fa fa-angle-right"></i>
                                </button>
                                
                                <div class="brand-carousel-container" id="brandCarousel-{{ $index }}">
                                    <div class="products-carousel-track brand-products-container"></div>
                                </div>
                                
                                <div class="carousel-dots" id="brandDots-{{ $index }}"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <style>
        .brand-products-section {
            padding: 60px 0;
            background: #f8f9fa;
            margin-bottom: 30px;
        }
        
        .brand-products-section:nth-child(even) {
            background: #ffffff;
        }
        
        .loading-placeholder {
            min-height: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .brand-products-container .product-item {
            margin-bottom: 0;
        }
    </style>

    <script src="{{ asset('js/lazy-load.js') }}"></script>
    
@endsection
