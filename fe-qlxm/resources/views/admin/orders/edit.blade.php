@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>Sửa đơn hàng #{{ $order['id'] ?? '' }}</h1>

        <form action="{{ route('admin.orders.update', $order['id']) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label">Khách hàng</label>
                <input type="text" class="form-control" value="{{ ($order['customer']['name'] ?? 'N/A') . ' - ' . ($order['customer']['phone'] ?? 'N/A') }}" disabled>
                <input type="hidden" name="customer_id" value="{{ $order['customer_id'] ?? '' }}">
                <small class="form-text text-muted">Không thể thay đổi khách hàng sau khi tạo đơn</small>
            </div>

            <div class="mb-3">
                <label for="customer_address" class="form-label">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                <textarea name="customer_address" id="customer_address" class="form-control @error('customer_address') is-invalid @enderror" 
                    rows="2" required maxlength="200">{{ old('customer_address', $order['customer_address'] ?? '') }}</textarea>
                @error('customer_address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-control" required>
                    <option value="pending" {{ old('status', $order['status'] ?? '') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="processing" {{ old('status', $order['status'] ?? '') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="shipped" {{ old('status', $order['status'] ?? '') == 'shipped' ? 'selected' : '' }}>Đã giao</option>
                    <option value="delivered" {{ old('status', $order['status'] ?? '') == 'delivered' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="cancelled" {{ old('status', $order['status'] ?? '') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
                @error('status')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3" style="display: none;">
                <label class="form-label">Sản phẩm <span class="text-danger">*</span></label>
                <div id="order-items">
                    @php
                        $orderItems = old('items', $order['items'] ?? []);
                    @endphp
                    @foreach($orderItems as $index => $item)
                        <div class="order-item mb-2 p-3 border rounded">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="number" name="items[{{ $index }}][product_id]" class="form-control" 
                                        placeholder="ID Sản phẩm" value="{{ $item['product_id'] ?? '' }}" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" name="items[{{ $index }}][quantity]" class="form-control" 
                                        placeholder="Số lượng" value="{{ $item['quantity'] ?? '' }}" min="1" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger w-100 remove-item">Xóa</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-item" class="btn btn-secondary btn-sm mt-2">+ Thêm sản phẩm</button>
            </div>

            <button type="submit" id="submitBtn" class="btn btn-success w-100 mb-2" disabled>Lưu</button>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary w-100">Quay lại</a>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addressInput = document.getElementById('customer_address');
            const statusSelect = document.getElementById('status');
            const submitBtn = document.getElementById('submitBtn');

            // Giá trị ban đầu
            const originalAddress = addressInput.value;
            const originalStatus = statusSelect.value;

            function checkChanges() {
                const addressChanged = addressInput.value !== originalAddress;
                const statusChanged = statusSelect.value !== originalStatus;

                if (addressChanged || statusChanged) {
                    submitBtn.disabled = false;
                } else {
                    submitBtn.disabled = true;
                }
            }

            // Lắng nghe thay đổi
            addressInput.addEventListener('input', checkChanges);
            statusSelect.addEventListener('change', checkChanges);

            // Show validation errors in modal if present
            @if ($errors->any())
                const errors = @json($errors->all());
                const errorMessage = errors.join('\\n');
                showCustomAlert(errorMessage, 'Lỗi xác thực', 'error');
            @endif
        });
    </script>
@endsection
