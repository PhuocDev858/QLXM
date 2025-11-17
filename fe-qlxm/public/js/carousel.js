/**
 * Product Carousel Component
 * Handles carousel functionality for product listings
 */

// Initialize main products carousel (Latest Motorcycles)
function initMainProductsCarousel() {
    const carousel = document.getElementById('productsCarousel');
    if (!carousel) return;
    
    const track = carousel.querySelector('.products-carousel-track');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const dotsContainer = document.getElementById('carouselDots');
    const items = track.querySelectorAll('.carousel-item-wrapper');
    
    let currentIndex = 0;
    let itemsPerPage = 3;
    let totalPages = Math.ceil(items.length / itemsPerPage);
    
    // Detect items per page based on screen size
    function updateItemsPerPage() {
        if (window.innerWidth <= 576) {
            itemsPerPage = 1;
        } else if (window.innerWidth <= 992) {
            itemsPerPage = 2;
        } else {
            itemsPerPage = 3;
        }
        totalPages = Math.ceil(items.length / itemsPerPage);
        createDots();
        updateCarousel();
    }
    
    // Create dots
    function createDots() {
        dotsContainer.innerHTML = '';
        for (let i = 0; i < totalPages; i++) {
            const dot = document.createElement('button');
            dot.className = 'carousel-dot' + (i === 0 ? ' active' : '');
            dot.addEventListener('click', () => goToPage(i));
            dotsContainer.appendChild(dot);
        }
    }
    
    // Update carousel position
    function updateCarousel() {
        const translateX = -currentIndex * (100 / itemsPerPage);
        track.style.transform = `translateX(${translateX}%)`;
        
        // Update buttons state
        prevBtn.disabled = currentIndex === 0;
        nextBtn.disabled = currentIndex >= totalPages - 1;
        
        // Update dots
        const dots = dotsContainer.querySelectorAll('.carousel-dot');
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentIndex);
        });
    }
    
    // Go to specific page
    function goToPage(page) {
        currentIndex = page;
        updateCarousel();
    }
    
    // Previous button
    prevBtn.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            updateCarousel();
        }
    });
    
    // Next button
    nextBtn.addEventListener('click', () => {
        if (currentIndex < totalPages - 1) {
            currentIndex++;
            updateCarousel();
        }
    });
    
    // Touch/Mouse drag support
    let startX = 0;
    let currentX = 0;
    let isDragging = false;
    
    carousel.addEventListener('mousedown', startDrag);
    carousel.addEventListener('touchstart', startDrag);
    
    carousel.addEventListener('mousemove', drag);
    carousel.addEventListener('touchmove', drag);
    
    carousel.addEventListener('mouseup', endDrag);
    carousel.addEventListener('touchend', endDrag);
    carousel.addEventListener('mouseleave', endDrag);
    
    function startDrag(e) {
        isDragging = true;
        startX = e.type.includes('mouse') ? e.pageX : e.touches[0].pageX;
        carousel.style.cursor = 'grabbing';
    }
    
    function drag(e) {
        if (!isDragging) return;
        e.preventDefault();
        currentX = e.type.includes('mouse') ? e.pageX : e.touches[0].pageX;
    }
    
    function endDrag() {
        if (!isDragging) return;
        isDragging = false;
        carousel.style.cursor = 'grab';
        
        const diff = startX - currentX;
        
        // Swipe threshold: 50px
        if (Math.abs(diff) > 50) {
            if (diff > 0 && currentIndex < totalPages - 1) {
                // Swipe left - next
                currentIndex++;
            } else if (diff < 0 && currentIndex > 0) {
                // Swipe right - prev
                currentIndex--;
            }
            updateCarousel();
        }
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft' && currentIndex > 0) {
            currentIndex--;
            updateCarousel();
        } else if (e.key === 'ArrowRight' && currentIndex < totalPages - 1) {
            currentIndex++;
            updateCarousel();
        }
    });
    
    // Responsive
    window.addEventListener('resize', updateItemsPerPage);
    updateItemsPerPage();
}

// Initialize carousel for brand products
function initBrandCarousel(index) {
    const carousel = document.getElementById(`brandCarousel-${index}`);
    if (!carousel) return;
    
    const track = carousel.querySelector('.products-carousel-track');
    const prevBtn = carousel.parentElement.querySelector(`.prev-btn[data-carousel="brand-${index}"]`);
    const nextBtn = carousel.parentElement.querySelector(`.next-btn[data-carousel="brand-${index}"]`);
    const dotsContainer = document.getElementById(`brandDots-${index}`);
    const items = track.querySelectorAll('.carousel-item-wrapper');
    
    let currentIndex = 0;
    let itemsPerPage = 3;
    let totalPages = Math.ceil(items.length / itemsPerPage);
    
    function updateItemsPerPage() {
        if (window.innerWidth <= 576) {
            itemsPerPage = 1;
        } else if (window.innerWidth <= 992) {
            itemsPerPage = 2;
        } else {
            itemsPerPage = 3;
        }
        totalPages = Math.ceil(items.length / itemsPerPage);
        createDots();
        updateCarousel();
    }
    
    function createDots() {
        dotsContainer.innerHTML = '';
        for (let i = 0; i < totalPages; i++) {
            const dot = document.createElement('button');
            dot.className = 'carousel-dot' + (i === 0 ? ' active' : '');
            dot.addEventListener('click', () => goToPage(i));
            dotsContainer.appendChild(dot);
        }
    }
    
    function updateCarousel() {
        const translateX = -currentIndex * (100 / itemsPerPage);
        track.style.transform = `translateX(${translateX}%)`;
        
        prevBtn.disabled = currentIndex === 0;
        nextBtn.disabled = currentIndex >= totalPages - 1;
        
        const dots = dotsContainer.querySelectorAll('.carousel-dot');
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === currentIndex);
        });
    }
    
    function goToPage(page) {
        currentIndex = page;
        updateCarousel();
    }
    
    prevBtn.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            updateCarousel();
        }
    });
    
    nextBtn.addEventListener('click', () => {
        if (currentIndex < totalPages - 1) {
            currentIndex++;
            updateCarousel();
        }
    });
    
    // Touch/Mouse drag support
    let startX = 0;
    let currentX = 0;
    let isDragging = false;
    
    carousel.addEventListener('mousedown', startDrag);
    carousel.addEventListener('touchstart', startDrag);
    carousel.addEventListener('mousemove', drag);
    carousel.addEventListener('touchmove', drag);
    carousel.addEventListener('mouseup', endDrag);
    carousel.addEventListener('touchend', endDrag);
    carousel.addEventListener('mouseleave', endDrag);
    
    function startDrag(e) {
        isDragging = true;
        startX = e.type.includes('mouse') ? e.pageX : e.touches[0].pageX;
        carousel.style.cursor = 'grabbing';
    }
    
    function drag(e) {
        if (!isDragging) return;
        e.preventDefault();
        currentX = e.type.includes('mouse') ? e.pageX : e.touches[0].pageX;
    }
    
    function endDrag() {
        if (!isDragging) return;
        isDragging = false;
        carousel.style.cursor = 'grab';
        
        const diff = startX - currentX;
        
        if (Math.abs(diff) > 50) {
            if (diff > 0 && currentIndex < totalPages - 1) {
                currentIndex++;
            } else if (diff < 0 && currentIndex > 0) {
                currentIndex--;
            }
            updateCarousel();
        }
    }
    
    window.addEventListener('resize', updateItemsPerPage);
    updateItemsPerPage();
}

// Export functions for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { initMainProductsCarousel, initBrandCarousel };
}
