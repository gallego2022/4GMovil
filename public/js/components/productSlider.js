// productsSlider.js
let currentProductSlide = 0;

export function initProductsSlider() {
    try {
        // Obtener elementos del DOM
        const productSlideItems = document.querySelectorAll('.product-slide-item');
        const productsSlide = document.getElementById('products-slide');
        const nextButton = document.getElementById('products-next');
        const prevButton = document.getElementById('products-prev');

        // Verificar si existen los elementos necesarios
        if (!productsSlide || productSlideItems.length === 0) {
            console.warn('No se encontraron elementos necesarios para el slider de productos');
            return;
        }

        const totalProductSlides = productSlideItems.length;

        // Función para actualizar la posición del slider
        function updateSlider() {
            try {
                const itemWidth = productSlideItems[0].offsetWidth;
                const translation = currentProductSlide * itemWidth;
                productsSlide.style.transform = `translateX(-${translation}px)`;
            } catch (error) {
                console.error('Error al actualizar el slider:', error);
            }
        }

        // Configurar botones de navegación
        if (nextButton) {
            nextButton.addEventListener('click', () => {
                if (currentProductSlide < totalProductSlides - 4) {
                    currentProductSlide++;
                    updateSlider();
                }
            });
        }

        if (prevButton) {
            prevButton.addEventListener('click', () => {
                if (currentProductSlide > 0) {
                    currentProductSlide--;
                    updateSlider();
                }
            });
        }

        // Inicializar el slider
        updateSlider();

        // Actualizar en resize
        window.addEventListener('resize', () => {
            requestAnimationFrame(updateSlider);
        });

        console.log('Slider de productos inicializado correctamente');
    } catch (error) {
        console.error('Error al inicializar el slider de productos:', error);
    }
}
