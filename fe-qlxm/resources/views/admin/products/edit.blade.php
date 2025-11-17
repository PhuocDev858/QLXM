@extends('layouts.admin')

@section('title', 'Chỉnh sửa Sản phẩm')
@section('page-title', 'Chỉnh sửa sản phẩm')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold mb-0" style="color:#fff;">Chỉnh sửa sản phẩm</h1>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Quay lại
            </a>
        </div>

        {{-- Thông báo --}}
        <x-alert-messages />

        {{-- Hiển thị lỗi nếu có --}}
        @if (isset($error))
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ $error }}
            </div>
        @endif

        <div class="card shadow-sm border-0" style="background:#23262f; color:#eaeaea; border-radius:1rem;">
            <div class="card-header" style="background:#181a20; color:#fff; border-radius:1rem 1rem 0 0;">
                <h5 class="mb-0">
                    <i class="bi bi-pencil-square me-2"></i>Chỉnh sửa thông tin sản phẩm
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.products.update', ['product' => $product['id']]) }}" method="POST"
                    enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-8">
                            {{-- Thông tin cơ bản --}}
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-3 text-white">Thông tin cơ bản</h6>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="name" class="form-label fw-semibold">
                                            Tên sản phẩm <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="name" id="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $product['name'] ?? '') }}" required
                                            style="background:#fff; color:#000; border:1px solid #ced4da;">
                                        <div class="invalid-feedback">
                                            Vui lòng nhập tên sản phẩm.
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="price" class="form-label fw-semibold">
                                            Giá (VND) <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" name="price" id="price"
                                            class="form-control @error('price') is-invalid @enderror"
                                            value="{{ old('price', $product['price'] ?? '') }}" min="0"
                                            step="1000" required
                                            style="background:#fff; color:#000; border:1px solid #ced4da;">
                                        <div class="invalid-feedback">
                                            Vui lòng nhập giá sản phẩm hợp lệ.
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="stock" class="form-label fw-semibold">Số lượng tồn kho</label>
                                        <input type="number" name="stock" id="stock"
                                            class="form-control @error('stock') is-invalid @enderror"
                                            value="{{ old('stock', $product['stock'] ?? 0) }}" min="0"
                                            style="background:#fff; color:#000; border:1px solid #ced4da;">
                                        <div class="invalid-feedback">
                                            Số lượng tồn kho không hợp lệ.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Phân loại --}}
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-3 text-white">Phân loại sản phẩm</h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="brand_id" class="form-label fw-semibold">
                                            Thương hiệu <span class="text-danger">*</span>
                                        </label>
                                        <select name="brand_id" id="brand_id"
                                            class="form-select @error('brand_id') is-invalid @enderror" required
                                            style="background:#fff; color:#000; border:1px solid #ced4da;">
                                            <option value="">-- Chọn thương hiệu --</option>
                                            @if (isset($brands))
                                                @foreach ($brands as $brand)
                                                    <option value="{{ $brand['id'] ?? '' }}"
                                                        {{ old('brand_id', $product['brand_id'] ?? '') == ($brand['id'] ?? '') ? 'selected' : '' }}>
                                                        {{ $brand['name'] ?? '' }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="invalid-feedback">
                                            Vui lòng chọn thương hiệu.
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="category_id" class="form-label fw-semibold">
                                            Danh mục <span class="text-danger">*</span>
                                        </label>
                                        <select name="category_id" id="category_id"
                                            class="form-select @error('category_id') is-invalid @enderror" required
                                            style="background:#fff; color:#000; border:1px solid #ced4da;">
                                            <option value="">-- Chọn danh mục --</option>
                                            @if (isset($categories))
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category['id'] ?? '' }}"
                                                        {{ old('category_id', $product['category_id'] ?? '') == ($category['id'] ?? '') ? 'selected' : '' }}>
                                                        {{ $category['name'] ?? '' }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="invalid-feedback">
                                            Vui lòng chọn danh mục.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Trạng thái --}}
                            <div class="mb-4">
                                <label for="status" class="form-label fw-semibold">Trạng thái</label>
                                <select name="status" id="status"
                                    class="form-select @error('status') is-invalid @enderror"
                                    style="background:#fff; color:#000; border:1px solid #ced4da;">
                                    <option value="available"
                                        {{ old('status', $product['status'] ?? '') == 'available' ? 'selected' : '' }}>Còn
                                        hàng</option>
                                    <option value="unavailable"
                                        {{ old('status', $product['status'] ?? '') == 'unavailable' ? 'selected' : '' }}>
                                        Hết hàng</option>
                                </select>
                            </div>

                            {{-- Mô tả --}}
                            <div class="mb-3">
                                <label for="description" class="form-label fw-semibold">Mô tả sản phẩm</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                    rows="4" placeholder="Nhập mô tả chi tiết về sản phẩm..."
                                    style="background:#fff; color:#000; border:1px solid #ced4da;">{{ old('description', $product['description'] ?? '') }}</textarea>
                                <div class="invalid-feedback">
                                    Mô tả sản phẩm không hợp lệ.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            {{-- Ảnh hiện tại --}}
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-3 text-white">Hình ảnh sản phẩm</h6>

                                {{-- Ảnh hiện tại --}}
                                @if (!empty($product['image_url']))
                                    <div class="current-image mb-3">
                                        <p class="mb-2 fw-semibold">Ảnh hiện tại:</p>
                                        <div class="position-relative">
                                            <img src="{{ $product['image_url'] }}" alt="Ảnh hiện tại"
                                                class="img-fluid rounded shadow"
                                                style="max-width: 100%; max-height: 200px; object-fit: cover; width: 100%;">
                                        </div>
                                    </div>
                                @endif

                                {{-- Upload ảnh mới --}}
                                <div class="upload-area text-center p-4 rounded"
                                    style="border: 2px dashed #34495e; background:#181a20;">
                                    <input type="file" name="image" id="image"
                                        class="form-control d-none @error('image') is-invalid @enderror" accept="image/*">

                                    <div id="uploadPlaceholder">
                                        <i class="bi bi-cloud-upload fs-2 text-muted mb-2 d-block"></i>
                                        <p class="text-muted mb-2 small">Tải lên ảnh mới (tùy chọn)</p>
                                        <button type="button" class="btn btn-outline-light btn-sm"
                                            onclick="document.getElementById('image').click()">
                                            Chọn file
                                        </button>
                                        <small class="d-block mt-2 text-muted">
                                            JPG, PNG, GIF tối đa 2MB
                                        </small>
                                    </div>

                                    <!-- New Image Preview -->
                                    <div id="imagePreview" class="mt-3" style="display: none;">
                                        <p class="mb-2 fw-semibold">Ảnh mới:</p>
                                        <img id="previewImg" src="" alt="Preview"
                                            class="img-fluid rounded shadow"
                                            style="max-width: 100%; max-height: 200px; object-fit: cover;">
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="clearImagePreview()">
                                                <i class="bi bi-trash me-1"></i>Xóa ảnh mới
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Thông tin hiện tại --}}
                            <div class="card border-0" style="background:#181a20; color:#adb5bd;">
                                <div class="card-body">
                                    <h6 class="fw-semibold mb-2 text-info">
                                        <i class="bi bi-info-circle me-1"></i>Thông tin hiện tại
                                    </h6>
                                    <ul class="mb-0 small">
                                        <li><strong>ID:</strong> {{ $product['id'] ?? 'N/A' }}</li>
                                        <li><strong>Thương hiệu:</strong> {{ $product['brand']['name'] ?? 'Chưa có' }}</li>
                                        <li><strong>Danh mục:</strong> {{ $product['category']['name'] ?? 'Chưa có' }}</li>
                                        <li><strong>Tạo:</strong>
                                            {{ isset($product['created_at']) ? \Carbon\Carbon::parse($product['created_at'])->format('d/m/Y') : 'N/A' }}
                                        </li>
                                        <li><strong>Cập nhật:</strong>
                                            {{ isset($product['updated_at']) ? \Carbon\Carbon::parse($product['updated_at'])->format('d/m/Y') : 'N/A' }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Nút hành động --}}
                    <div class="row">
                        <div class="col-12">
                            <hr style="border-color: #34495e;">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-lg me-1"></i>Hủy
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-check-lg me-1"></i>Cập nhật sản phẩm
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .upload-area {
                transition: all 0.3s ease;
                cursor: pointer;
            }

            .upload-area:hover {
                border-color: #2563eb !important;
                background: #1a1e26 !important;
            }

            .form-select option {
                background: #181a20;
                color: #eaeaea;
            }

            .btn-warning {
                background: #f59e42 !important;
                border-color: #f59e42 !important;
                color: #fff !important;
            }

            .btn-warning:hover {
                background: #e8890b !important;
                border-color: #e8890b !important;
            }

            .current-image img {
                transition: transform 0.3s ease;
            }

            .current-image img:hover {
                transform: scale(1.05);
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Image upload functionality
            document.getElementById('image').addEventListener('change', function(e) {
                const file = e.target.files[0];
                const preview = document.getElementById('imagePreview');
                const previewImg = document.getElementById('previewImg');
                const placeholder = document.getElementById('uploadPlaceholder');

                if (file) {
                    // Check file size (2MB = 2 * 1024 * 1024 bytes)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('File quá lớn! Vui lòng chọn file nhỏ hơn 2MB.');
                        e.target.value = '';
                        preview.style.display = 'none';
                        placeholder.style.display = 'block';
                        return;
                    }

                    // Check file type
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                    if (!allowedTypes.includes(file.type)) {
                        alert('Định dạng file không hỗ trợ! Vui lòng chọn file JPG, PNG hoặc GIF.');
                        e.target.value = '';
                        preview.style.display = 'none';
                        placeholder.style.display = 'block';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        preview.style.display = 'block';
                        placeholder.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                } else {
                    clearImagePreview();
                }
            });

            // Clear image preview
            function clearImagePreview() {
                document.getElementById('image').value = '';
                document.getElementById('imagePreview').style.display = 'none';
                document.getElementById('uploadPlaceholder').style.display = 'block';
            }

            // Form validation
            (() => {
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

            // Drag and drop functionality
            const uploadArea = document.querySelector('.upload-area');

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, unhighlight, false);
            });

            function highlight(e) {
                uploadArea.style.borderColor = '#2563eb';
                uploadArea.style.background = '#1a1e26';
            }

            function unhighlight(e) {
                uploadArea.style.borderColor = '#34495e';
                uploadArea.style.background = '#181a20';
            }

            uploadArea.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;

                if (files.length > 0) {
                    document.getElementById('image').files = files;
                    document.getElementById('image').dispatchEvent(new Event('change'));
                }
            }
        </script>
    @endpush
@endsection
