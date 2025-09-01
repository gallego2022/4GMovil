// Responsive Pagination Handler
class ResponsivePagination {
    constructor() {
        this.init();
    }

    init() {
        this.detectDeviceAndRedirect();
        this.setupEventListeners();
        this.showDebugInfo();
    }

    getDeviceType() {
        const width = window.innerWidth;
        if (width <= 768) return 'mobile';
        if (width <= 1024) return 'tablet';
        return 'desktop';
    }

    detectDeviceAndRedirect() {
        const currentDeviceType = this.getDeviceType();
        const urlParams = new URLSearchParams(window.location.search);
        const urlDeviceType = urlParams.get('device_type');

        console.log('Current device:', currentDeviceType);
        console.log('URL device type:', urlDeviceType);

        if (urlDeviceType !== currentDeviceType) {
            this.updateUrlWithDeviceType(currentDeviceType);
        }
    }

    updateUrlWithDeviceType(deviceType) {
        const url = new URL(window.location);
        url.searchParams.set('device_type', deviceType);
        
        // Reset to first page when changing device type
        if (url.searchParams.has('page')) {
            url.searchParams.delete('page');
        }

        console.log('Redirecting to:', url.toString());
        window.location.href = url.toString();
    }

    setupEventListeners() {
        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                this.detectDeviceAndRedirect();
            }, 300);
        });

        // Add loading effect to pagination links
        document.querySelectorAll('nav a').forEach(link => {
            link.addEventListener('click', () => {
                this.addLoadingEffect();
            });
        });
    }

    addLoadingEffect() {
        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach(card => {
            card.classList.add('loading');
        });
    }

    showDebugInfo() {
        const deviceType = this.getDeviceType();
        const urlParams = new URLSearchParams(window.location.search);
        const urlDeviceType = urlParams.get('device_type');

        const debugInfo = document.createElement('div');
        debugInfo.className = 'fixed top-4 right-4 bg-blue-600 text-white px-3 py-2 rounded-lg text-sm font-medium z-50 shadow-lg';
        debugInfo.innerHTML = `
            <div>Detected: ${deviceType}</div>
            <div>URL: ${urlDeviceType || 'none'}</div>
            <div>Width: ${window.innerWidth}px</div>
        `;
        
        document.body.appendChild(debugInfo);

        // Remove after 5 seconds
        setTimeout(() => {
            debugInfo.style.opacity = '0';
            debugInfo.style.transform = 'translateY(-10px)';
            setTimeout(() => debugInfo.remove(), 300);
        }, 5000);
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new ResponsivePagination();
});
