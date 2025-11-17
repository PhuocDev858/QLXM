@extends('layouts.admin')

@section('title', 'Thêm Sản phẩm')
@section('page-title', 'Thêm sản phẩm mới')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold mb-0" style="color:#fff;">Thêm sản phẩm mới</h1>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Quay lại
            </a>
        </div>

        {{-- Thông báo --}}
        <x-alert-messages />

        {{-- Display Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Vui lòng kiểm tra lại:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm border-0" style="background:#23262f; color:#eaeaea; border-radius:1rem;">
            <div class="card-header" style="background:#181a20; color:#fff; border-radius:1rem 1rem 0 0;">
                <h5 class="mb-0">
                    <i class="bi bi-plus-circle me-2"></i>Thông tin sản phẩm mới
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data"
                    class="needs-validation" novalidate>
                    @csrf

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
                                            value="{{ old('name') }}" required placeholder="Nhập tên sản phẩm..."
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
                                            value="{{ old('price') }}" min="0" step="1000" required
                                            placeholder="0"
                                            style="background:#fff; color:#000; border:1px solid #ced4da;">
                                        <div class="invalid-feedback">
                                            Vui lòng nhập giá sản phẩm hợp lệ.
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="stock" class="form-label fw-semibold">Số lượng tồn kho</label>
                                        <input type="number" name="stock" id="stock"
                                            class="form-control @error('stock') is-invalid @enderror"
                                            value="{{ old('stock', 1) }}" min="1" placeholder="1"
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
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand['id'] ?? '' }}"
                                                    {{ old('brand_id') == ($brand['id'] ?? '') ? 'selected' : '' }}>
                                                    {{ $brand['name'] ?? '' }}
                                                </option>
                                            @endforeach
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
                                            @forelse ($categories ?? [] as $category)
                                                <option value="{{ $category['id'] ?? '' }}"
                                                    {{ old('category_id') == ($category['id'] ?? '') ? 'selected' : '' }}>
                                                    {{ $category['name'] ?? ($category['title'] ?? 'Unknown') }}
                                                </option>
                                            @empty
                                                <option value="" disabled>Không có danh mục nào</option>
                                            @endforelse
                                        </select>
                                        <div class="invalid-feedback">
                                            Vui lòng chọn danh mục.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Mô tả --}}
                            <div class="mb-3">
                                <label for="description" class="form-label fw-semibold">Mô tả sản phẩm</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                    rows="4" placeholder="Nhập mô tả chi tiết về sản phẩm..."
                                    style="background:#fff; color:#000; border:1px solid #ced4da;">{{ old('description') }}</textarea>
                                <div class="invalid-feedback">
                                    Mô tả sản phẩm không hợp lệ.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            {{-- Ảnh sản phẩm --}}
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-3 text-white">Hình ảnh sản phẩm</h6>

                                {{-- Upload area --}}
                                <div class="upload-area text-center p-4 rounded"
                                    style="border: 2px dashed #34495e; background:#ffffff;">
                                    <input type="file" name="image" id="image"
                                        class="form-control d-none @error('image') is-invalid @enderror" accept="image/*">

                                    <div id="uploadPlaceholder">
                                        <i class="bi bi-cloud-upload fs-2 text-secondary mb-2 d-block"></i>
                                        <p class="text-dark mb-2 small">Tải lên ảnh sản phẩm</p>
                                        <button type="button" class="btn btn-outline-dark btn-sm"
                                            onclick="document.getElementById('image').click()">
                                            Chọn file
                                        </button>
                                        <small class="d-block mt-2 text-muted">
                                            JPG, PNG, GIF tối đa 2MB
                                        </small>
                                    </div>

                                    <!-- Image Preview -->
                                    <div id="imagePreview" class="mt-3" style="display: none;">
                                        <p class="mb-2 fw-semibold">Xem trước:</p>
                                        <img id="previewImg" src="" alt="Preview"
                                            class="img-fluid rounded shadow"
                                            style="max-width: 100%; max-height: 200px; object-fit: cover;">
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="clearImagePreview()">
                                                <i class="bi bi-trash me-1"></i>Xóa ảnh
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Thông tin hướng dẫn --}}
                            <div class="card border-0" style="background:#181a20; color:#adb5bd;">
                                <div class="card-body">
                                    <h6 class="fw-semibold mb-2 text-info">
                                        <i class="bi bi-info-circle me-1"></i>Hướng dẫn
                                    </h6>
                                    <ul class="mb-0 small">
                                        <li>Tên sản phẩm nên rõ ràng, mô tả chính xác</li>
                                        <li>Giá sản phẩm tính bằng VND</li>
                                        <li>Chọn thương hiệu và danh mục phù hợp</li>
                                        <li>Ảnh nên có chất lượng tốt, dung lượng &lt; 2MB</li>
                                        <li>Mô tả chi tiết giúp khách hàng hiểu rõ sản phẩm</li>
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
                                <button type="submit" id="submitBtn" class="btn btn-success" disabled>
                                    <i class="bi bi-check-lg me-1"></i>Thêm sản phẩm
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
                background: #f8f9fa !important;
            }

            .btn-success:disabled {
                background: #6c757d !important;
                border-color: #6c757d !important;
                cursor: not-allowed;
                opacity: 0.65;
            }

            .form-select option {
                background: #181a20;
                color: #eaeaea;
            }

            .btn-success {
                background: #10b981 !important;
                border-color: #10b981 !important;
                color: #fff !important;
            }

            .btn-success:hover {
                background: #059669 !important;
                border-color: #059669 !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Validate form and enable/disable submit button
            function validateForm() {
                const name = document.getElementById('name').value.trim();
                const price = document.getElementById('price').value;
                const brandId = document.getElementById('brand_id').value;
                const categoryId = document.getElementById('category_id').value;
                const submitBtn = document.getElementById('submitBtn');

                // Check if all required fields are filled
                if (name && price && brandId && categoryId) {
                    submitBtn.disabled = false;
                } else {
                    submitBtn.disabled = true;
                }
            }

            // Add event listeners to required fields
            document.addEventListener('DOMContentLoaded', function() {
                const requiredFields = ['name', 'price', 'brand_id', 'category_id'];
                requiredFields.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (field) {
                        field.addEventListener('input', validateForm);
                        field.addEventListener('change', validateForm);
                    }
                });

                // Initial validation
                validateForm();
            });

            // Image preview functionality
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
