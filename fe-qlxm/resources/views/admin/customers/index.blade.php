@extends('layouts.admin')

@section('content')
    <div class="container py-4">
        <h1 class="fw-bold text-center mb-4" style="color:#fff;">Danh sách khách hàng</h1>
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary mb-3">+ Thêm khách hàng</a>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="card shadow-sm border-0" style="background:#23262f; color:#eaeaea; border-radius:1rem;">
            <div class="card-body p-0">
                <table class="table mb-0" style="background:#23262f; color:#eaeaea; border-radius:1rem; overflow:hidden;">
                    <thead style="background:#181a20; color:#fff;">
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Điện thoại</th>
                            <th>Email</th>
                            <th>Địa chỉ</th>
                            <th style="width: 250px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $index => $customer)
                            <tr style="cursor: pointer;" onclick="toggleOrders({{ $customer['id'] }})" 
                                class="customer-row">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $customer['name'] }}</td>
                                <td>{{ $customer['phone'] }}</td>
                                <td>{{ $customer['email'] }}</td>
                                <td>{{ $customer['address'] }}</td>
                                <td onclick="event.stopPropagation()">
                                    <a href="{{ route('admin.customers.edit', $customer['id']) }}"
                                        class="btn btn-sm btn-warning" style="border-radius:0.5rem;">Sửa</a>
                                    <form action="{{ route('admin.customers.destroy', $customer['id']) }}" method="POST"
                                        style="display:inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" style="border-radius:0.5rem;"
                                            onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                            <tr id="orders-row-{{ $customer['id'] }}" style="display: none;">
                                <td colspan="6" style="background: #f3f4f6; padding: 0;">
                                    <div id="orders-{{ $customer['id'] }}" class="orders-container p-3">
                                        <div class="text-center">
                                            <i class="fas fa-spinner fa-spin"></i> Đang tải...
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @if (empty($customers))
                            <tr>
                                <td colspan="6">Không có khách hàng nào.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <style>
        .table th,
        .table td {
            vertical-align: middle !important;
        }

        .table tbody tr {
            border-bottom: 1px solid #23262f;
        }

        .customer-row {
            transition: background 0.2s ease;
        }

        .customer-row:hover {
            background: #f3f4f6 !important;
        }

        .btn-warning {
            background: #f59e42 !important;
            color: #fff !important;
        }

        .btn-danger {
            background: #ef4444 !important;
            color: #fff !important;
        }

        .btn-primary {
            background: #2563eb !important;
            color: #fff !important;
        }

        .btn-info {
            background: #06b6d4 !important;
            color: #fff !important;
        }

        .orders-container {
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

        .order-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            color: #374151;
            transition: all 0.2s ease;
        }

        .order-badge:hover {
            background: #e5e7eb;
            border-color: #9ca3af;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-pending { background: #f59e0b; color: #fff; }
        .status-confirmed { background: #3b82f6; color: #fff; }
        .status-shipping { background: #8b5cf6; color: #fff; }
        .status-completed { background: #10b981; color: #fff; }
        .status-cancelled { background: #ef4444; color: #fff; }
    </style>

    <script>
        let loadedOrders = {};

        function toggleOrders(customerId) {
            const row = document.getElementById('orders-row-' + customerId);
            const container = document.getElementById('orders-' + customerId);
            
            if (row.style.display === 'none') {
                row.style.display = 'table-row';
                
                // Load orders if not loaded yet
                if (!loadedOrders[customerId]) {
                    fetch('/admin/customers/' + customerId + '/orders', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.orders) {
                            loadedOrders[customerId] = true;
                            displayOrders(container, data.orders);
                        } else {
                            container.innerHTML = '<div class="alert alert-warning">Không thể tải đơn hàng</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        container.innerHTML = '<div class="alert alert-danger">Lỗi khi tải đơn hàng</div>';
                    });
                }
            } else {
                row.style.display = 'none';
            }
        }

        function displayOrders(container, orders) {
            if (!orders || orders.length === 0) {
                container.innerHTML = '<div class="alert alert-info">Khách hàng chưa có đơn hàng nào</div>';
                return;
            }

            const statusMap = {
                'pending': 'Chờ xác nhận',
                'confirmed': 'Đã xác nhận',
                'shipping': 'Đang giao',
                'completed': 'Hoàn thành',
                'cancelled': 'Đã hủy'
            };
            
            let html = '<div style="margin-bottom: 0.5rem;"><strong>Danh sách đơn hàng:</strong></div>';
            html += '<div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">';
            orders.forEach(order => {
                const statusText = statusMap[order.status] || order.status;
                const orderJson = JSON.stringify(order).replace(/"/g, '&quot;');
                
                html += `
                    <span class="order-badge" onclick='showOrderDetail(${JSON.stringify(order)}, event)' style="cursor: pointer;">
                        <span style="color: #6b7280;">Mã:</span> <strong>#${order.id}</strong> - 
                        <span style="color: #6b7280;">Trạng thái:</span> <span class="status-badge status-${order.status}">${statusText}</span>
                    </span>
                `;
            });
            html += '</div>';
            
            container.innerHTML = html;
        }

        function showOrderDetail(order, event) {
            if (event) {
                event.stopPropagation();
            }
            
            console.log('Showing order detail:', order);
            displayOrderModal(order);
        }

        function displayOrderModal(order) {
            const statusMap = {
                'pending': 'Chờ xác nhận',
                'confirmed': 'Đã xác nhận',
                'shipping': 'Đang giao',
                'completed': 'Hoàn thành',
                'cancelled': 'Đã hủy'
            };
            
            const statusText = statusMap[order.status] || order.status;
            const orderDate = new Date(order.order_date).toLocaleDateString('vi-VN', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            let itemsHtml = '';
            if (order.items && order.items.length > 0) {
                itemsHtml = '<table class="table table-sm mt-3" style="background: #fff;">';
                itemsHtml += '<thead><tr><th>Sản phẩm</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th></tr></thead><tbody>';
                order.items.forEach(item => {
                    const productName = item.product ? item.product.name : 'Sản phẩm';
                    const subtotal = item.quantity * item.price;
                    itemsHtml += `
                        <tr>
                            <td>${productName}</td>
                            <td>${item.quantity}</td>
                            <td>${formatMoney(item.price)} VNĐ</td>
                            <td><strong>${formatMoney(subtotal)} VNĐ</strong></td>
                        </tr>
                    `;
                });
                itemsHtml += '</tbody></table>';
            }

            const modalHtml = `
                <div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header" style="background: #f3f4f6;">
                                <h5 class="modal-title">
                                    <i class="fas fa-file-invoice"></i> Chi tiết đơn hàng #${order.id}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2">Thông tin đơn hàng</h6>
                                        <p class="mb-1"><strong>Mã đơn hàng:</strong> #${order.id}</p>
                                        <p class="mb-1"><strong>Ngày đặt:</strong> ${orderDate}</p>
                                        <p class="mb-1">
                                            <strong>Trạng thái:</strong> 
                                            <span class="status-badge status-${order.status}">${statusText}</span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2">Thông tin khách hàng</h6>
                                        <p class="mb-1"><strong>Tên:</strong> ${order.customer ? order.customer.name : '-'}</p>
                                        <p class="mb-1"><strong>SĐT:</strong> ${order.customer ? order.customer.phone : '-'}</p>
                                        <p class="mb-1"><strong>Email:</strong> ${order.customer ? order.customer.email || '-' : '-'}</p>
                                        <p class="mb-1"><strong>Địa chỉ:</strong> ${order.customer ? order.customer.address : '-'}</p>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <h6 class="text-muted mb-2">Chi tiết sản phẩm</h6>
                                ${itemsHtml}
                                
                                <div class="text-end mt-3">
                                    <h4 style="color: #10b981;">
                                        <strong>Tổng tiền: ${formatMoney(order.total_amount)} VNĐ</strong>
                                    </h4>
                                </div>
                                
                                ${order.note ? `
                                    <hr>
                                    <h6 class="text-muted mb-2">Ghi chú</h6>
                                    <p>${order.note}</p>
                                ` : ''}
                            </div>
                            <div class="modal-footer">
                                <a href="/admin/orders/${order.id}/edit" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Chỉnh sửa
                                </a>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing modal if any
            const existingModal = document.getElementById('orderDetailModal');
            if (existingModal) {
                existingModal.remove();
            }

            // Add new modal to body
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
            modal.show();

            // Remove modal from DOM when closed
            document.getElementById('orderDetailModal').addEventListener('hidden.bs.modal', function () {
                this.remove();
            });
        }

        function formatMoney(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount);
        }
    </script>
@endpush
