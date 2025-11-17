@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>Thêm đơn hàng mới</h1>

        <form action="{{ route('admin.orders.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="customer_id" class="form-label">Khách hàng</label>
                <select name="customer_id" id="customer_id" class="form-control">
                    <option value="">-- Khách hàng mới --</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer['id'] ?? '' }}" {{ old('customer_id') == ($customer['id'] ?? '') ? 'selected' : '' }}>
                            {{ $customer['name'] ?? '' }} - {{ $customer['phone'] ?? '' }}
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Chọn khách hàng có sẵn hoặc để trống để tạo mới</small>
            </div>

            <div id="newCustomerFields">
                <div class="mb-3">
                    <label for="customer_name" class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                    <input type="text" name="customer_name" id="customer_name" class="form-control @error('customer_name') is-invalid @enderror" 
                        value="{{ old('customer_name') }}" maxlength="100">
                    @error('customer_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="customer_phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                    <input type="text" name="customer_phone" id="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" 
                        value="{{ old('customer_phone') }}" pattern="^0[0-9]{9}$" maxlength="10"
                        title="Số điện thoại phải có 10 số, bắt đầu bằng 0">
                    @error('customer_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="customer_email" class="form-label">Email</label>
                    <input type="email" name="customer_email" id="customer_email" class="form-control @error('customer_email') is-invalid @enderror" 
                        value="{{ old('customer_email') }}" maxlength="100">
                    @error('customer_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="customer_address" class="form-label">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                <textarea name="customer_address" id="customer_address" class="form-control @error('customer_address') is-invalid @enderror" 
                    rows="2" required maxlength="200">{{ old('customer_address') }}</textarea>
                @error('customer_address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Sản phẩm <span class="text-danger">*</span></label>
                <div id="order-items">
                    <div class="order-item mb-2 p-3 border rounded">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="number" name="items[0][product_id]" class="form-control" placeholder="ID Sản phẩm" required>
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="items[0][quantity]" class="form-control" placeholder="Số lượng" min="1" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger w-100 remove-item">Xóa</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" id="add-item" class="btn btn-secondary btn-sm mt-2" style="display: none;">+ Thêm sản phẩm</button>
            </div>

            <button type="submit" class="btn btn-success w-100 mb-2">Lưu</button>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary w-100">Quay lại</a>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const customerSelect = document.getElementById('customer_id');
            const newCustomerFields = document.getElementById('newCustomerFields');
            const customerName = document.getElementById('customer_name');
            const customerPhone = document.getElementById('customer_phone');

            function toggleNewCustomerFields() {
                if (customerSelect.value === '') {
                    newCustomerFields.style.display = 'block';
                    customerName.required = true;
                    customerPhone.required = true;
                } else {
                    newCustomerFields.style.display = 'none';
                    customerName.required = false;
                    customerPhone.required = false;
                }
            }

            customerSelect.addEventListener('change', toggleNewCustomerFields);
            toggleNewCustomerFields();

            // Quản lý items
            let itemIndex = 1;
            const orderItems = document.getElementById('order-items');
            const addItemBtn = document.getElementById('add-item');

            addItemBtn.addEventListener('click', function() {
                const newItem = document.createElement('div');
                newItem.className = 'order-item mb-2 p-3 border rounded';
                newItem.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <input type="number" name="items[${itemIndex}][product_id]" class="form-control" placeholder="ID Sản phẩm" required>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="items[${itemIndex}][quantity]" class="form-control" placeholder="Số lượng" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger w-100 remove-item">Xóa</button>
                        </div>
                    </div>
                `;
                orderItems.appendChild(newItem);
                itemIndex++;
            });

            orderItems.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-item')) {
                    if (orderItems.children.length > 1) {
                        e.target.closest('.order-item').remove();
                    } else {
                        showCustomAlert('Phải có ít nhất 1 sản phẩm', 'Lỗi', 'error');
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
