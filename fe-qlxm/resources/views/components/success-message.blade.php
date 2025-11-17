{{-- Component hiển thị thông báo thành công --}}
@if (session('success'))
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    </div>
@endif

@if (session('status'))
    <div class="alert alert-success">
        <i class="bi bi-info-circle me-2"></i>{{ session('status') }}
    </div>
@endif

@if (session('message'))
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>{{ session('message') }}
    </div>
@endif
