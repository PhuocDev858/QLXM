/**
 * Cart Page Scripts
 * Xử lý update quantity, remove items, checkout
 */

(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        initCartAnimations();
        initCSRFRefresh();
        initCheckoutForm();
        updateCartCount();
    });
    
    /**
     * Animation khi load trang
     */
    function initCartAnimations() {
        const cards = document.querySelectorAll('.card');
        cards.forEach(function(card, index) {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            setTimeout(function() {
                card.style.transition = 'all 0.6s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 200);
        });
    }
    
    /**
     * Refresh CSRF token định kỳ
     */
    function initCSRFRefresh() {
        // Refresh every 30 minutes
        setInterval(refreshCSRFToken, 30 * 60 * 1000);
    }
    
    /**
     * Refresh CSRF token
     */
    function refreshCSRFToken() {
        fetch('/refresh-csrf', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.token) {
                // Update meta tag
                const metaTag = document.querySelector('meta[name="csrf-token"]');
                if (metaTag) {
                    metaTag.setAttribute('content', data.token);
                }
                // Update form token
                const tokenInput = document.getElementById('csrf-token-input');
                if (tokenInput) {
                    tokenInput.value = data.token;
                }
            }
        })
        .catch(function(error) {
            console.log('CSRF refresh error:', error);
        });
    }
    
    /**
     * Submit form ẩn
     */
    function submitUpdateForm(actionUrl, data) {
        console.log('submitUpdateForm called:', actionUrl, data);
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = actionUrl;
        form.style.display = 'none';

        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = csrfToken.getAttribute('content');
            form.appendChild(csrf);
        } else {
            console.error('CSRF token not found!');
        }

        // Add data
        for (const key in data) {
            if (data.hasOwnProperty(key)) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = data[key];
                form.appendChild(input);
            }
        }

        document.body.appendChild(form);
        console.log('Form created and submitting:', form);
        form.submit();
    }
    
    /**
     * Update quantity
     */
    function updateQuantity(productId, newQuantity) {
        console.log('updateQuantity called:', productId, newQuantity);

        if (newQuantity <= 0) {
            removeItem(productId);
            return;
        }

        try {
            const quantityInput = document.querySelector('input[data-product-id="' + productId + '"]');
            const productRow = quantityInput ? quantityInput.closest('[data-product-row]') : null;

            if (quantityInput) {
                quantityInput.style.opacity = '0.5';
                quantityInput.disabled = true;
            }

            if (productRow) {
                productRow.style.transition = 'all 0.3s ease';
                productRow.style.backgroundColor = '#f8f9ff';
                setTimeout(function() {
                    productRow.style.backgroundColor = '';
                }, 300);
            }

            // Submit form
            submitUpdateForm('/cart/update/' + productId, {
                quantity: newQuantity
            });

        } catch (error) {
            console.error('Error in updateQuantity:', error);
            // Fallback
            submitUpdateForm('/cart/update/' + productId, {
                quantity: newQuantity
            });
        }
    }
    
    /**
     * Remove item
     */
    function removeItem(productId) {
        console.log('removeItem called:', productId);

        if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng không?')) {
            return;
        }

        try {
            const productRow = document.querySelector('[data-product-row="' + productId + '"]');

            if (productRow) {
                // Animation fade out
                productRow.style.transition = 'all 0.5s ease';
                productRow.style.opacity = '0.3';
                productRow.style.transform = 'translateX(-20px)';

                setTimeout(function() {
                    submitUpdateForm('/cart/remove/' + productId, {});
                }, 200);
            } else {
                submitUpdateForm('/cart/remove/' + productId, {});
            }
        } catch (error) {
            console.error('Error in removeItem:', error);
            submitUpdateForm('/cart/remove/' + productId, {});
        }
    }
    
    /**
     * Init checkout form
     */
    function initCheckoutForm() {
        const checkoutForm = document.getElementById('checkout-form');
        if (!checkoutForm) return;
        
        checkoutForm.addEventListener('submit', function() {
            // Refresh token ngay trước khi submit
            refreshCSRFToken();

            const btn = document.getElementById('order-btn');
            if (btn) {
                btn.disabled = true;
                btn.classList.add('btn-loading');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
            }

            // Animation mờ giỏ hàng
            const cartItems = document.querySelectorAll('[data-product-row]');
            cartItems.forEach(function(item, index) {
                setTimeout(function() {
                    item.style.transition = 'all 0.5s ease';
                    item.style.opacity = '0.5';
                    item.style.transform = 'scale(0.95)';
                }, index * 100);
            });
        });
    }
    
    // Expose functions globally để có thể gọi từ inline onclick
    window.updateQuantity = updateQuantity;
    window.removeItem = removeItem;
    window.refreshCSRFToken = refreshCSRFToken;
    window.addToCart = addToCart;
    window.updateCartCount = updateCartCount;
    window.updateQuantityAjax = updateQuantityAjax;
    window.removeItemAjax = removeItemAjax;

    /**
     * Update quantity via AJAX without page reload
     */
    function updateQuantityAjax(productId, newQuantity) {
        console.log('updateQuantityAjax called:', productId, newQuantity);

        if (newQuantity <= 0) {
            removeItemAjax(productId);
            return;
        }

        const quantityInput = document.querySelector('input[data-product-id="' + productId + '"]');
        if (quantityInput) {
            quantityInput.disabled = true;
            quantityInput.style.opacity = '0.5';
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        
        fetch('/cart/update/' + productId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : '',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                quantity: newQuantity
            })
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                // Update subtotal for this item
                const subtotal = data.item.price * data.item.quantity;
                const subtotalEl = document.querySelector('[data-product-row="' + productId + '"] .text-danger');
                if (subtotalEl) {
                    subtotalEl.textContent = formatMoney(subtotal) + ' VNĐ';
                }
                
                // Update total price
                updateTotalDisplay(data.totalPrice);
                
                if (quantityInput) {
                    quantityInput.disabled = false;
                    quantityInput.style.opacity = '1';
                }
            } else {
                alert(data.message || 'Có lỗi xảy ra');
                if (quantityInput) {
                    quantityInput.disabled = false;
                    quantityInput.style.opacity = '1';
                }
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi cập nhật giỏ hàng');
            if (quantityInput) {
                quantityInput.disabled = false;
                quantityInput.style.opacity = '1';
            }
        });
    }

    /**
     * Remove item via AJAX without page reload
     */
    function removeItemAjax(productId) {
        console.log('removeItemAjax called:', productId);

        if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng không?')) {
            return;
        }

        const productRow = document.querySelector('[data-product-row="' + productId + '"]');
        if (productRow) {
            productRow.style.transition = 'all 0.3s ease';
            productRow.style.opacity = '0.3';
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        
        fetch('/cart/remove/' + productId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : '',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                // Remove the row
                if (productRow) {
                    productRow.remove();
                }
                
                // Update total price
                updateTotalDisplay(data.totalPrice);
                
                // Update cart count
                updateCartCount();
                
                // If cart is empty, reload page to show empty state
                if (data.cartCount === 0) {
                    location.reload();
                }
            } else {
                alert(data.message || 'Có lỗi xảy ra');
                if (productRow) {
                    productRow.style.opacity = '1';
                }
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa sản phẩm');
            if (productRow) {
                productRow.style.opacity = '1';
            }
        });
    }

    /**
     * Update total price display
     */
    function updateTotalDisplay(totalPrice) {
        const subtotalEl = document.getElementById('subtotal-display');
        const totalEl = document.getElementById('total-display');
        
        if (subtotalEl) {
            subtotalEl.textContent = formatMoney(totalPrice) + ' VNĐ';
        }
        if (totalEl) {
            totalEl.textContent = formatMoney(totalPrice);
        }
    }

    function formatMoney(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount);
    }
})();
