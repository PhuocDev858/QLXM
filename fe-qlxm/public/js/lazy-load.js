/**
 * Lazy Loading for Brand Sections
 * Uses Intersection Observer API to load brand products on scroll
 */

// Configuration
const LAZY_LOAD_CONFIG = {
    rootMargin: '200px',
    threshold: 0.1
};

// Lazy load observer for brand sections
function initLazyLoadBrandSections() {
    const brandSections = document.querySelectorAll('.brand-section');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const section = entry.target;
                const brandId = section.dataset.brandId;
                const brandIndex = section.dataset.brandIndex;
                
                // Load products for this brand
                loadBrandProducts(brandId, brandIndex, section);
                
                // Stop observing this section
                observer.unobserve(section);
            }
        });
    }, LAZY_LOAD_CONFIG);
    
    // Start observing all brand sections
    brandSections.forEach(section => observer.observe(section));
}

// Load brand products
function loadBrandProducts(brandId, brandIndex, section) {
    const container = section.querySelector('.brand-products-container');
    const loading = section.querySelector('.loading-spinner');
    const carousel = section.querySelector('.products-carousel-wrapper');
    
    // Show loading spinner
    loading.style.display = 'block';
    
    // Fetch products
    fetch(`/api/client/products?brand_id=${brandId}&per_page=12`)
        .then(response => response.json())
        .then(data => {
            if (data.data && data.data.length > 0) {
                // Render products
                const productsHTML = data.data.map(product => `
                    <div class="carousel-item-wrapper">
                        <div class="product-item">
                            <a href="/motorcycles/${product.id}">
                                <img src="${product.image_url || '/img/photos/default-product.jpg'}" 
                                     alt="${product.name}"
                                     onerror="this.src='/img/photos/default-product.jpg'">
                            </a>
                            <div class="down-content">
                                <a href="/motorcycles/${product.id}">
                                    <h4>${product.name}</h4>
                                </a>
                                <h6>${formatPrice(product.price)}</h6>
                                <p>${product.description || ''}</p>
                            </div>
                        </div>
                    </div>
                `).join('');
                
                carousel.querySelector('.products-carousel-track').innerHTML = productsHTML;
                
                // Hide loading, show carousel
                loading.style.display = 'none';
                carousel.style.display = 'block';
                
                // Initialize carousel for this brand
                initBrandCarousel(brandIndex);
            } else {
                // No products found - hide the entire section
                section.style.display = 'none';
            }
        })
        .catch(error => {
            console.error(`Error loading products for brand ${brandId}:`, error);
            // Hide section on error
            section.style.display = 'none';
        });
}

// Format price helper
function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    initLazyLoadBrandSections();
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { initLazyLoadBrandSections, loadBrandProducts };
}
