@extends('layouts.admin')

@section('title', 'Sửa Thương hiệu')

@section('content')
    <div class="container py-4">
        <h1 class="fw-bold text-center mb-4" style="color:#fff;">Sửa thương hiệu</h1>
        
        <!-- Form cập nhật thông tin và logo brand (POST, multipart) -->
        <form action="{{ route('admin.brands.update', $brand['id']) }}" method="POST" enctype="multipart/form-data"
            class="card p-4 mb-4" style="background:#23262f; color:#eaeaea; border-radius:1rem;">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Tên thương hiệu <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                    value="{{ old('name', $brand['name'] ?? '') }}" required minlength="2" maxlength="50" 
                    pattern="^[a-zA-ZÀ-ỹ0-9\s\-\_]+$"
                    title="Tên thương hiệu chỉ được chứa chữ cái, số, khoảng trắng và dấu gạch">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Độ dài: 2-50 ký tự. Chỉ chữ cái, số, khoảng trắng và dấu gạch.</small>
            </div>
            <div class="mb-3">
                <label for="country" class="form-label">Quốc gia <span class="text-danger">*</span></label>
                <select name="country" id="country" class="form-select @error('country') is-invalid @enderror" required>
                    <option value="">-- Chọn quốc gia --</option>
                    <option value="Việt Nam" {{ old('country', $brand['country'] ?? '') == 'Việt Nam' ? 'selected' : '' }}>Việt Nam</option>
                    <option value="Nhật Bản" {{ old('country', $brand['country'] ?? '') == 'Nhật Bản' ? 'selected' : '' }}>Nhật Bản</option>
                    <option value="Trung Quốc" {{ old('country', $brand['country'] ?? '') == 'Trung Quốc' ? 'selected' : '' }}>Trung Quốc</option>
                    <option value="Hàn Quốc" {{ old('country', $brand['country'] ?? '') == 'Hàn Quốc' ? 'selected' : '' }}>Hàn Quốc</option>
                    <option value="Thái Lan" {{ old('country', $brand['country'] ?? '') == 'Thái Lan' ? 'selected' : '' }}>Thái Lan</option>
                    <option value="Ý" {{ old('country', $brand['country'] ?? '') == 'Ý' ? 'selected' : '' }}>Ý</option>
                    <option value="Đức" {{ old('country', $brand['country'] ?? '') == 'Đức' ? 'selected' : '' }}>Đức</option>
                    <option value="Áo" {{ old('country', $brand['country'] ?? '') == 'Áo' ? 'selected' : '' }}>Áo</option>
                    <option value="Mỹ" {{ old('country', $brand['country'] ?? '') == 'Mỹ' ? 'selected' : '' }}>Mỹ</option>
                    <option value="Anh" {{ old('country', $brand['country'] ?? '') == 'Anh' ? 'selected' : '' }}>Anh</option>
                    <option value="Pháp" {{ old('country', $brand['country'] ?? '') == 'Pháp' ? 'selected' : '' }}>Pháp</option>
                    <option value="Tây Ban Nha" {{ old('country', $brand['country'] ?? '') == 'Tây Ban Nha' ? 'selected' : '' }}>Tây Ban Nha</option>
                    <option value="Ấn Độ" {{ old('country', $brand['country'] ?? '') == 'Ấn Độ' ? 'selected' : '' }}>Ấn Độ</option>
                    <option value="Đài Loan" {{ old('country', $brand['country'] ?? '') == 'Đài Loan' ? 'selected' : '' }}>Đài Loan</option>
                    <option value="Indonesia" {{ old('country', $brand['country'] ?? '') == 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
                    <option value="Malaysia" {{ old('country', $brand['country'] ?? '') == 'Malaysia' ? 'selected' : '' }}>Malaysia</option>
                    <option value="Philippines" {{ old('country', $brand['country'] ?? '') == 'Philippines' ? 'selected' : '' }}>Philippines</option>
                    <option value="Úc" {{ old('country', $brand['country'] ?? '') == 'Úc' ? 'selected' : '' }}>Úc</option>
                    <option value="Brazil" {{ old('country', $brand['country'] ?? '') == 'Brazil' ? 'selected' : '' }}>Brazil</option>
                    <option value="Canada" {{ old('country', $brand['country'] ?? '') == 'Canada' ? 'selected' : '' }}>Canada</option>
                    <option value="Nga" {{ old('country', $brand['country'] ?? '') == 'Nga' ? 'selected' : '' }}>Nga</option>
                    <option value="Nam Phi" {{ old('country', $brand['country'] ?? '') == 'Nam Phi' ? 'selected' : '' }}>Nam Phi</option>
                    <option value="Mexico" {{ old('country', $brand['country'] ?? '') == 'Mexico' ? 'selected' : '' }}>Mexico</option>
                    <option value="Argentina" {{ old('country', $brand['country'] ?? '') == 'Argentina' ? 'selected' : '' }}>Argentina</option>
                    <option value="Thụy Sĩ" {{ old('country', $brand['country'] ?? '') == 'Thụy Sĩ' ? 'selected' : '' }}>Thụy Sĩ</option>
                    <option value="Thụy Điển" {{ old('country', $brand['country'] ?? '') == 'Thụy Điển' ? 'selected' : '' }}>Thụy Điển</option>
                    <option value="Na Uy" {{ old('country', $brand['country'] ?? '') == 'Na Uy' ? 'selected' : '' }}>Na Uy</option>
                    <option value="Phần Lan" {{ old('country', $brand['country'] ?? '') == 'Phần Lan' ? 'selected' : '' }}>Phần Lan</option>
                    <option value="Hà Lan" {{ old('country', $brand['country'] ?? '') == 'Hà Lan' ? 'selected' : '' }}>Hà Lan</option>
                    <option value="Bỉ" {{ old('country', $brand['country'] ?? '') == 'Bỉ' ? 'selected' : '' }}>Bỉ</option>
                    <option value="Ba Lan" {{ old('country', $brand['country'] ?? '') == 'Ba Lan' ? 'selected' : '' }}>Ba Lan</option>
                    <option value="Séc" {{ old('country', $brand['country'] ?? '') == 'Séc' ? 'selected' : '' }}>Séc</option>
                </select>
                @error('country')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                    rows="3" maxlength="255">{{ old('description', $brand['description'] ?? '') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Tối đa 255 ký tự.</small>
            </div>
            <div class="mb-3">
                <label for="logo" class="form-label">Logo</label>
                @if (!empty($brand['logo_url']))
                    <div class="mb-2">
                        <img src="{{ $brand['logo_url'] }}" alt="{{ $brand['name'] }}"
                            style="width:60px; height:60px; object-fit:cover; border-radius:4px;">
                    </div>
                @endif
                <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror" 
                    accept="image/jpeg,image/jpg,image/png,image/webp,image/gif">
                @error('logo')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Định dạng: jpg, jpeg, png, webp, gif. Tối đa 2MB.</small>
            </div>
            <button type="submit" id="submitBtn" class="btn btn-success w-100 mb-2" disabled>Lưu</button>
            <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary w-100">Quay lại</a>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const logoInput = document.getElementById('logo');
            const submitBtn = document.getElementById('submitBtn');
            
            // Store original values
            const originalValues = {
                name: document.getElementById('name').value,
                country: document.getElementById('country').value,
                description: document.getElementById('description').value,
                logo: ''
            };
            
            // Check for changes
            function checkChanges() {
                const currentValues = {
                    name: document.getElementById('name').value,
                    country: document.getElementById('country').value,
                    description: document.getElementById('description').value,
                    logo: logoInput.files.length > 0 ? 'changed' : ''
                };
                
                const hasChanges = Object.keys(originalValues).some(key => 
                    originalValues[key] != currentValues[key]
                );
                
                submitBtn.disabled = !hasChanges;
            }
            
            // Add event listeners
            document.getElementById('name').addEventListener('input', checkChanges);
            document.getElementById('country').addEventListener('change', checkChanges);
            document.getElementById('description').addEventListener('input', checkChanges);
            logoInput.addEventListener('change', checkChanges);
            
            // Validate logo file
            logoInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    // Kiểm tra kích thước file (2MB = 2 * 1024 * 1024 bytes)
                    const maxSize = 2 * 1024 * 1024;
                    if (file.size > maxSize) {
                        const fileSize = (file.size / 1024 / 1024).toFixed(2);
                        showCustomAlert(
                            `Hình ảnh không được vượt quá 2MB.\nKích thước file hiện tại: ${fileSize}MB`,
                            'Lỗi kích thước file',
                            'error'
                        );
                        this.value = ''; // Clear file input
                        return;
                    }
                    
                    // Kiểm tra định dạng file
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];
                    if (!allowedTypes.includes(file.type)) {
                        showCustomAlert(
                            'Chỉ chấp nhận ảnh định dạng: jpg, jpeg, png, webp, gif.',
                            'Lỗi định dạng file',
                            'error'
                        );
                        this.value = ''; // Clear file input
                        return;
                    }
                }
            });
            
            // Validate trước khi submit
            form.addEventListener('submit', function(e) {
                const file = logoInput.files[0];
                if (file) {
                    const maxSize = 2 * 1024 * 1024;
                    if (file.size > maxSize) {
                        e.preventDefault();
                        showCustomAlert(
                            'Hình ảnh không được vượt quá 2MB. Vui lòng chọn file khác.',
                            'Không thể gửi form',
                            'error'
                        );
                        return false;
                    }
                }
            });
            
            // Show validation errors in modal if present
            @if ($errors->any())
                const errors = @json($errors->all());
                const errorMessage = errors.join('\\n');
                showCustomAlert(errorMessage, 'Lỗi xác thực', 'error');
            @endif
        });
    </script>
@endsection
