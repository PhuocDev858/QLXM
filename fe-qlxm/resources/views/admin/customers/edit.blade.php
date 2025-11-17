@extends('layouts.admin')

@section('content')
    <div class="container py-4">
        <h1 class="fw-bold text-center mb-4" style="color:#fff;">Sửa khách hàng</h1>
        
        <div class="card shadow-sm border-0" style="background:#fff; border-radius:1rem; max-width:600px; margin:0 auto;">
            <div class="card-body p-4">
                <form id="customerForm" action="{{ route('admin.customers.update', $customer['id']) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $customer['name']) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Điện thoại <span class="text-danger">*</span></label>
                        <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $customer['phone']) }}" pattern="[0-9]{10,11}" title="Số điện thoại phải có 10-11 chữ số" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $customer['email']) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ</label>
                        <input type="text" id="address" name="address" class="form-control" value="{{ old('address', $customer['address']) }}">
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" id="submitBtn" class="btn btn-success" disabled>Lưu</button>
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Quay lại</a>
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

    // Store original values
    const originalValues = {
        name: document.getElementById('name').value,
        phone: document.getElementById('phone').value,
        email: document.getElementById('email').value,
        address: document.getElementById('address').value
    };

    function validateForm() {
        const name = document.getElementById('name').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const email = document.getElementById('email').value.trim();
        const address = document.getElementById('address').value;
        const submitBtn = document.getElementById('submitBtn');
        
        const phonePattern = /^[0-9]{10,11}$/;
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        const isValid = name.length >= 3 && 
                       phonePattern.test(phone) && 
                       emailPattern.test(email);
        
        const hasChanges = originalValues.name !== name ||
                          originalValues.phone !== phone ||
                          originalValues.email !== email ||
                          originalValues.address !== address;
        
        submitBtn.disabled = !isValid || !hasChanges;
    }

    document.getElementById('name').addEventListener('input', validateForm);
    document.getElementById('phone').addEventListener('input', validateForm);
    document.getElementById('email').addEventListener('input', validateForm);
    document.getElementById('address').addEventListener('input', validateForm);
    
    validateForm();
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
