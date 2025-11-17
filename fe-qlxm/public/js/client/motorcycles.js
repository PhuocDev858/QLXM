/**
 * Motorcycles Page Scripts
 * Xử lý filter, search và price range slider
 */

(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        initFilterToggle();
        initSearchInput();
        initPriceRangeSlider();
    });
    
    /**
     * Khởi tạo toggle cho filter section
     */
    function initFilterToggle() {
        const filterToggle = document.getElementById('filterToggle');
        const filterContent = document.getElementById('filterContent');
        const filterChevron = document.getElementById('filterChevron');
        
        if (!filterToggle || !filterContent || !filterChevron) return;
        
        filterToggle.addEventListener('click', function() {
            const isHidden = filterContent.style.display === 'none';
            
            filterContent.style.display = isHidden ? 'block' : 'none';
            filterChevron.classList.toggle('fa-chevron-down', !isHidden);
            filterChevron.classList.toggle('fa-chevron-up', isHidden);
        });
    }
    
    /**
     * Khởi tạo search input
     */
    function initSearchInput() {
        const searchInput = document.querySelector('input[name="search"]');
        if (!searchInput) return;
        
        // Auto focus nếu có search query
        if (searchInput.value) {
            searchInput.focus();
            searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
        }
        
        // Submit form khi nhấn Enter
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.form.submit();
            }
        });
    }
    
    /**
     * Khởi tạo price range slider
     */
    function initPriceRangeSlider() {
        const minPriceSlider = document.getElementById('minPriceSlider');
        const maxPriceSlider = document.getElementById('maxPriceSlider');
        const minPriceLabel = document.getElementById('minPriceLabel');
        const maxPriceLabel = document.getElementById('maxPriceLabel');
        
        if (!minPriceSlider || !maxPriceSlider || !minPriceLabel || !maxPriceLabel) return;
        
        /**
         * Format giá theo định dạng Việt Nam
         */
        function formatPrice(price) {
            return new Intl.NumberFormat('vi-VN').format(price);
        }
        
        // Xử lý min price slider
        minPriceSlider.addEventListener('input', function() {
            let minVal = parseInt(this.value, 10);
            let maxVal = parseInt(maxPriceSlider.value, 10);
            
            if (minVal > maxVal) {
                this.value = maxVal;
                minVal = maxVal;
            }
            
            minPriceLabel.textContent = formatPrice(minVal);
        });
        
        // Xử lý max price slider
        maxPriceSlider.addEventListener('input', function() {
            let maxVal = parseInt(this.value, 10);
            let minVal = parseInt(minPriceSlider.value, 10);
            
            if (maxVal < minVal) {
                this.value = minVal;
                maxVal = minVal;
            }
            
            maxPriceLabel.textContent = formatPrice(maxVal);
        });
    }
})();
