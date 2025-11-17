@extends('layouts.client')

@section('title', 'Danh Sách Xe Máy - QLXM')
@section('description',
    'Xem danh sách đầy đủ các dòng xe máy từ các hãng uy tín như Honda, Yamaha, Suzuki với giá cả
    hợp lý')

@section('content')
    <!-- Page Heading -->
    <div class="page-heading products-heading header-text">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="text-content">
                        <h4>danh mục sản phẩm</h4>
                        <h2>xe máy chất lượng</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Motorcycles Section -->
    <div class="products">
        <div class="container">
            <div class="row">
                <!-- Search Bar -->
                <div class="col-md-12 mb-4">
                    <div class="search-section">
                        <form method="GET" action="{{ route('client.motorcycles') }}" class="search-form">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <input type="text" 
                                               name="search" 
                                               class="form-control" 
                                               placeholder="Tìm kiếm xe máy theo tên, hãng, loại..." 
                                               value="{{ request('search') }}"
                                               style="height: 45px; border-radius: 25px; padding-left: 20px;">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-search" style="height: 45px; border-radius: 25px; width: 100%;">
                                            <i class="fa fa-search"></i> Tìm kiếm
                                        </button>
                                        @if(request('search'))
                                            <a href="{{ route('client.motorcycles') }}" class="btn btn-outline-secondary mt-2" style="width: 100%;">
                                                <i class="fa fa-times"></i> Xóa bộ lọc
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="filters-section mb-4">
                        <button class="btn btn-outline-primary filter-toggle-btn" type="button" id="filterToggle">
                            <i class="fa fa-filter"></i> Bộ lọc <i class="fa fa-chevron-down" id="filterChevron"></i>
                        </button>
                        
                        <div class="filter-content" id="filterContent" style="display: none;">
                            <div class="card card-body mt-3">
                                <form method="GET" action="{{ route('client.motorcycles') }}" id="filterForm">
                                    <!-- Keep existing search param if any -->
                                    @if(request('search'))
                                        <input type="hidden" name="search" value="{{ request('search') }}">
                                    @endif
                                    
                                    <div class="row">
                                        <!-- Lọc theo hãng -->
                                        @if (count($brands) > 0)
                                            <div class="col-md-6 mb-3">
                                                <h6 class="fw-bold">Hãng xe</h6>
                                                <select name="brand_id" class="form-control">
                                                    <option value="">-- Tất cả hãng --</option>
                                                    @foreach ($brands as $brand)
                                                        <option value="{{ $brand['id'] }}" {{ request('brand_id') == $brand['id'] ? 'selected' : '' }}>
                                                            {{ $brand['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                        
                                        <!-- Lọc theo danh mục -->
                                        @if (count($categories) > 0)
                                            <div class="col-md-6 mb-3">
                                                <h6 class="fw-bold">Loại xe</h6>
                                                <select name="category_id" class="form-control">
                                                    <option value="">-- Tất cả loại --</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category['id'] }}" {{ request('category_id') == $category['id'] ? 'selected' : '' }}>
                                                            {{ $category['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Lọc theo giá -->
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <h6 class="fw-bold">Khoảng giá</h6>
                                            <div class="price-range-container">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="price-label">Từ: <strong id="minPriceLabel">{{ number_format($minPrice, 0, ',', '.') }}</strong> VNĐ</span>
                                                    <span class="price-label">Đến: <strong id="maxPriceLabel">{{ number_format($maxPrice, 0, ',', '.') }}</strong> VNĐ</span>
                                                </div>
                                                <div class="price-inputs">
                                                    <input type="range" name="min_price" id="minPriceSlider" class="form-range" 
                                                           min="{{ $minPrice }}" 
                                                           max="{{ $maxPrice }}" 
                                                           value="{{ request('min_price', $minPrice) }}" 
                                                           step="1000000">
                                                    <input type="range" name="max_price" id="maxPriceSlider" class="form-range" 
                                                           min="{{ $minPrice }}" 
                                                           max="{{ $maxPrice }}" 
                                                           value="{{ request('max_price', $maxPrice) }}" 
                                                           step="1000000">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-3">
                                        <div class="col-md-12 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary flex-fill">
                                                <i class="fa fa-check"></i> Xem kết quả
                                            </button>
                                            <a href="{{ route('client.motorcycles') }}" class="btn btn-outline-secondary flex-fill">
                                                <i class="fa fa-times"></i> Xóa bộ lọc
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
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

                {{-- Hiển thị kết quả tìm kiếm --}}
                @if(request('search'))
                    <div class="col-md-12">
                        <div class="search-results-info mb-3">
                            <div class="alert alert-info">
                                <i class="fa fa-search"></i> 
                                Kết quả tìm kiếm cho: <strong>"{{ request('search') }}"</strong>
                                @if(!empty($products))
                                    - Tìm thấy {{ count($products) }} sản phẩm
                                @else
                                    - Không tìm thấy sản phẩm nào
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-md-12">
                    <div class="filters-content">
                        <div class="row grid">
                            @if (!empty($products) && is_array($products))
                                @foreach ($products as $product)
                                    <div
                                        class="col-lg-4 col-md-4 all 
                                        @if (isset($product['brand']['id'])) brand-{{ $product['brand']['id'] }} @endif
                                        @if (isset($product['category']['id'])) category-{{ $product['category']['id'] }} @endif">
                                        <div class="product-item">
                                            <a href="{{ route('client.motorcycles.show', $product['id'] ?? 0) }}">
                                                @if (!empty($product['image_url']))
                                                    <img src="{{ $product['image_url'] }}"
                                                        alt="{{ $product['name'] ?? 'Xe máy' }}"
                                                        style="width: 100%; height: 250px; object-fit: cover;">
                                                @else
                                                    <img src="{{ asset('img/product_01.jpg') }}"
                                                        alt="{{ $product['name'] ?? 'Xe máy' }}"
                                                        style="width: 100%; height: 250px; object-fit: cover;">
                                                @endif
                                            </a>
                                            <div class="down-content">
                                                <a href="{{ route('client.motorcycles.show', $product['id'] ?? 0) }}">
                                                    <h4>{{ $product['name'] ?? 'Không rõ tên' }}</h4>
                                                </a>
                                                <h6 class="price">{{ isset($product['price']) ? number_format($product['price'], 0, ',', '.') : '0' }}
                                                    VNĐ</h6>

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
                            @else
                                <div class="col-md-12">
                                    <div class="text-center py-5">
                                        <h4>Không có sản phẩm nào</h4>
                                        <p>Hiện tại chưa có xe máy nào để hiển thị.</p>
                                        <a href="{{ route('client.home') }}" class="btn btn-primary">
                                            Về Trang Chủ
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pagination -->
    @include('components.pagination')

@endsection

@push('styles')
<style>
.search-section {
    background: #f8f9fa;
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

/* Style cho giá sản phẩm */
.down-content .price {
    color: #ff4444;
    font-weight: bold;
    font-size: 16px;
    margin-top: 10px;
    margin-bottom: 10px;
}

.btn-search {
    background: linear-gradient(45deg, #ff4444, #ff6666);
    border: none;
    font-weight: bold;
    transition: all 0.3s ease;
}

.btn-search:hover {
    background: linear-gradient(45deg, #ff3333, #ff5555);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(255, 68, 68, 0.3);
}

.search-form .form-control:focus {
    border-color: #ff4444;
    box-shadow: 0 0 0 0.2rem rgba(255, 68, 68, 0.25);
}

.search-results-info .alert {
    border-left: 4px solid #17a2b8;
    background-color: #d1ecf1;
    border-color: #bee5eb;
}

/* Filter section styles */
.filter-toggle-btn {
    border-radius: 25px;
    padding: 10px 30px;
    font-weight: bold;
    transition: all 0.3s ease;
}

.filter-toggle-btn:hover {
    background: #ff4444;
    border-color: #ff4444;
    color: white;
}

.filter-toggle-btn i.fa-chevron-down,
.filter-toggle-btn i.fa-chevron-up {
    font-size: 12px;
    margin-left: 5px;
    transition: all 0.3s ease;
}

.filter-content {
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.filter-btn {
    border-radius: 20px;
    transition: all 0.3s ease;
}

.filter-btn.active {
    background: #ff4444;
    border-color: #ff4444;
    color: white;
}

.filter-btn:hover {
    background: #ff6666;
    border-color: #ff6666;
    color: white;
}

.filters-section .card {
    border: 1px solid #dee2e6;
    border-radius: 10px;
}

/* Price Range Slider */
.price-range-container {
    padding: 10px;
    background: #f8f9fa;
    border-radius: 8px;
}

.price-label {
    font-size: 14px;
    color: #666;
}

.price-label strong {
    color: #ff4444;
    font-size: 16px;
}

.price-inputs {
    position: relative;
    height: 40px;
}

.price-inputs input[type="range"] {
    position: absolute;
    width: 100%;
    pointer-events: none;
    -webkit-appearance: none;
    appearance: none;
    height: 5px;
    background: transparent;
    outline: none;
}

.price-inputs input[type="range"]::-webkit-slider-thumb {
    pointer-events: auto;
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    background: #ff4444;
    cursor: pointer;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.price-inputs input[type="range"]::-moz-range-thumb {
    pointer-events: auto;
    width: 20px;
    height: 20px;
    background: #ff4444;
    cursor: pointer;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.price-inputs input[type="range"]::-webkit-slider-runnable-track {
    width: 100%;
    height: 5px;
    background: #ddd;
    border-radius: 5px;
}

.price-inputs input[type="range"]::-moz-range-track {
    width: 100%;
    height: 5px;
    background: #ddd;
    border-radius: 5px;
}

.price-inputs input[type="range"]:first-child::-webkit-slider-runnable-track {
    background: linear-gradient(to right, #ddd 0%, #ff4444 100%);
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/client/motorcycles.js') }}"></script>
@endpush
