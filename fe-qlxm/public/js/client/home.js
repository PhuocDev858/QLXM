/**
 * Home Page Scripts
 * Xử lý lazy load brand products với carousel
 */

(function() {
    'use strict';
    
    // API URL từ Laravel config
    const API_BASE_URL = window.APP_CONFIG?.apiUrl || '';
    
    document.addEventListener('DOMContentLoaded', function() {
        initMainProductsCarousel();
        initBrandLazyLoad();
    });
    
    /**
     * Khởi tạo lazy load cho brand sections
     */
    function initBrandLazyLoad() {
        const brandSections = document.querySelectorAll('.lazy-load-section');
        const loadedBrands = new Set();

        // Intersection Observer để detect khi scroll đến section
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const section = entry.target;
                    const brandId = section.dataset.brandId;
                    const brandName = section.dataset.brandName;
                    const brandIndex = section.dataset.brandIndex;
                    
                    if (!loadedBrands.has(brandId)) {
                        loadedBrands.add(brandId);
                        loadBrandProducts(section, brandId, brandName, brandIndex);
                    }
                    
                    observer.unobserve(section);
                }
            });
        }, {
            rootMargin: '200px',
            threshold: 0.1
        });

        brandSections.forEach(function(section) {
            observer.observe(section);
        });
    }
    
    /**
     * Load products theo brand
     */
    function loadBrandProducts(section, brandId, brandName, brandIndex) {
        const placeholder = section.querySelector('.loading-placeholder');
        const carouselWrapper = section.querySelector('.brand-carousel-wrapper');
        const container = section.querySelector('.brand-products-container');
        
        if (!placeholder || !carouselWrapper || !container) {
            console.error('Missing required elements in section');
            return;
        }
        
        // Tạo API URL
        const apiUrl = API_BASE_URL + '/api/client/products';
        const params = new URLSearchParams({
            brand_id: brandId,
            per_page: 12
        });
        
        console.log('Fetching products from:', apiUrl + '?' + params.toString());
        
        fetch(apiUrl + '?' + params.toString())
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('HTTP error! status: ' + response.status);
                }
                return response.json();
            })
            .then(function(data) {
                console.log('Loaded products for brand:', brandName, data);
                const products = data.data || [];
                
                if (products.length > 0) {
                    renderBrandProducts(container, products);
                    placeholder.style.display = 'none';
                    carouselWrapper.style.display = 'block';
                    
                    // Initialize carousel
                    if (typeof initBrandCarousel === 'function') {
                        initBrandCarousel(brandIndex);
                    }
                } else {
                    // Ẩn hoàn toàn section nếu không có sản phẩm
                    console.log('No products found for ' + brandName + ', hiding section');
                    section.style.display = 'none';
                }
            })
            .catch(function(error) {
                console.error('Error loading brand products:', error);
                // Ẩn section nếu có lỗi
                section.style.display = 'none';
            });
    }
    
    /**
     * Render products vào container
     */
    function renderBrandProducts(container, products) {
        container.innerHTML = '';
        const defaultImage = '/img/product_01.jpg';
        
        products.forEach(function(product) {
            const imageUrl = product.image_url || defaultImage;
            const productUrl = '/motorcycles/' + product.id;
            const price = new Intl.NumberFormat('vi-VN').format(product.price);
            
            const productHtml = 
                '<div class="carousel-item-wrapper">' +
                    '<div class="product-item">' +
                        '<a href="' + productUrl + '">' +
                            '<img src="' + imageUrl + '" ' +
                                 'alt="' + escapeHtml(product.name) + '" ' +
                                 'style="width: 100%; height: 250px; object-fit: cover;">' +
                        '</a>' +
                        '<div class="down-content">' +
                            '<a href="' + productUrl + '">' +
                                '<h4>' + escapeHtml(product.name) + '</h4>' +
                            '</a>' +
                            '<h6>' + price + ' VNĐ</h6>' +
                            (product.brand ? '<p><strong>Hãng:</strong> ' + escapeHtml(product.brand.name) + '</p>' : '') +
                            (product.category ? '<p><strong>Loại:</strong> ' + escapeHtml(product.category.name) + '</p>' : '') +
                            '<ul class="stars">' +
                                '<li><i class="fa fa-star"></i></li>' +
                                '<li><i class="fa fa-star"></i></li>' +
                                '<li><i class="fa fa-star"></i></li>' +
                                '<li><i class="fa fa-star"></i></li>' +
                                '<li><i class="fa fa-star"></i></li>' +
                            '</ul>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            
            container.insertAdjacentHTML('beforeend', productHtml);
        });
    }
    
    /**
     * Escape HTML để tránh XSS
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    /**
     * Expose initMainProductsCarousel globally if carousel.js exists
     */
    window.initMainProductsCarousel = window.initMainProductsCarousel || function() {
        console.log('Main products carousel initialization');
    };
})();
