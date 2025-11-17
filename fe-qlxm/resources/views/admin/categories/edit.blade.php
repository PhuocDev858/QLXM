@extends('layouts.admin')

@section('title', 'Sửa Danh mục')

@section('content')
    <div class="container py-4">
        <h1 class="fw-bold text-center mb-4" style="color:#fff;">Sửa danh mục</h1>
        
        <div class="card shadow-sm border-0" style="background:#fff; border-radius:1rem; max-width:600px; margin:0 auto;">
            <div class="card-body p-4">
                <form id="categoryForm" action="{{ route('admin.categories.update', $category['id']) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $category['name'] ?? '') }}" required minlength="3" maxlength="100">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $category['description'] ?? '') }}</textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" id="submitBtn" class="btn btn-success" disabled>Lưu</button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    @if ($errors->any())
        let errorMessages = [];
        @foreach ($errors->all() as $error)
            errorMessages.push("{{ $error }}");
        @endforeach
        showCustomAlert(errorMessages.join('<br>'), 'error');
    @endif

    const originalValues = {
        name: document.getElementById('name').value,
        description: document.getElementById('description').value
    };

    function checkChanges() {
        const currentValues = {
            name: document.getElementById('name').value,
            description: document.getElementById('description').value
        };

        const hasChanges = originalValues.name !== currentValues.name || 
                          originalValues.description !== currentValues.description;
        
        const isValid = currentValues.name.trim().length >= 3;
        
        document.getElementById('submitBtn').disabled = !hasChanges || !isValid;
    }

    document.getElementById('name').addEventListener('input', checkChanges);
    document.getElementById('description').addEventListener('input', checkChanges);
    checkChanges();
</script>

<style>
    .form-control {
        background: #fff !important;
        border: 1px solid #dee2e6 !important;
        color: #212529 !important;
    }
    .form-control:focus {
        background: #fff !important;
        border-color: #2563eb !important;
        color: #212529 !important;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25) !important;
    }
    .form-label {
        color: #212529 !important;
        font-weight: 500;
    }
    .btn-success {
        background: #10b981 !important;
        border: none !important;
    }
    .btn-success:disabled {
        background: #6b7280 !important;
        cursor: not-allowed;
    }
    .btn-secondary {
        background: #6b7280 !important;
        border: none !important;
    }
</style>
@endpush
