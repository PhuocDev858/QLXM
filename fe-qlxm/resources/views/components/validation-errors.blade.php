{{-- Component hiển thị lỗi validation --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <h5><i class="bi bi-exclamation-triangle me-2"></i>Có lỗi xảy ra:</h5>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
