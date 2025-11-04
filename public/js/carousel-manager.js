/**
 * Carousel Manager - Gestor optimizado de carruseles
 * Maneja los carruseles de productos y marcas con paginación avanzada
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Carousel Manager inicializando...');
    
    // Función para inicializar un carrusel específico
    function initCarousel(carouselId, prevButtonId, nextButtonId) {
        const carousel = document.getElementById(carouselId);
        
        if (!carousel) {
            console.warn(`⚠️ Elementos del carrusel ${carouselId} no encontrados`);
            return null;
        }
        
        // Los botones son opcionales (para carruseles con scroll horizontal)
        const prevBtn = prevButtonId ? document.getElementById(prevButtonId) : null;
        const nextBtn = nextButtonId ? document.getElementById(nextButtonId) : null;
        
        // Si no hay botones, el carrusel es solo scroll horizontal - no necesita inicialización
        if (!prevBtn && !nextBtn) {
            console.log(`ℹ️ Carrusel ${carouselId} es de scroll horizontal (sin botones prev/next)`);
            return null;
        }
        
        // Si faltan botones pero se esperaban, mostrar advertencia
        if ((prevButtonId && !prevBtn) || (nextButtonId && !nextBtn)) {
            console.warn(`⚠️ Algunos botones del carrusel ${carouselId} no encontrados`);
            return null;
        }
        
        let currentPage = 0;
        const totalPages = carousel.children.length;
        
        console.log(`✅ Inicializando carrusel ${carouselId} con ${totalPages} páginas`);
        
        // Función para actualizar la posición del carrusel
        function updateCarousel() {
            const translateX = -currentPage * 100;
            carousel.style.transform = `translateX(${translateX}%)`;
            
            // Debug info
            console.log(`🎯 Carrusel ${carouselId}: Página ${currentPage + 1} de ${totalPages}, TranslateX: ${translateX}%`);
            
            // Actualizar estado de botones
            prevBtn.disabled = currentPage === 0;
            nextBtn.disabled = currentPage === totalPages - 1;
            
            // Estilos para botón anterior
            if (currentPage === 0) {
                prevBtn.classList.add('opacity-50', 'cursor-not-allowed');
                prevBtn.classList.remove('hover:scale-110');
            } else {
                prevBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                prevBtn.classList.add('hover:scale-110');
            }
            
            // Estilos para botón siguiente
            if (currentPage === totalPages - 1) {
                nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
                nextBtn.classList.remove('hover:scale-110');
            } else {
                nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                nextBtn.classList.add('hover:scale-110');
            }
            
            // Actualizar indicadores de página
            updatePageIndicators();
            
            // Actualizar contador de páginas
            updatePageCounter();
        }
        
        // Función para actualizar indicadores de página (dots)
        function updatePageIndicators() {
            const indicators = document.querySelectorAll('.page-indicator');
            indicators.forEach((indicator, index) => {
                if (index === currentPage) {
                    indicator.classList.remove('bg-gray-300', 'hover:bg-gray-400');
                    indicator.classList.add('bg-blue-600', 'scale-125');
                } else {
                    indicator.classList.remove('bg-blue-600', 'scale-125');
                    indicator.classList.add('bg-gray-300', 'hover:bg-gray-400');
                }
            });
        }
        
        // Función para actualizar contador de páginas
        function updatePageCounter() {
            const currentPageDisplay = document.getElementById('current-page-display');
            if (currentPageDisplay) {
                currentPageDisplay.textContent = currentPage + 1;
            }
        }
        
        // Event listeners para botones de navegación
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (currentPage > 0) {
                currentPage--;
                updateCarousel();
                console.log(`⬅️ Navegando a página ${currentPage + 1} en ${carouselId}`);
            }
        });
        
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (currentPage < totalPages - 1) {
                currentPage++;
                updateCarousel();
                console.log(`➡️ Navegando a página ${currentPage + 1} en ${carouselId}`);
            }
        });
        
        // Event listeners para indicadores de página (dots clickeables)
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('page-indicator')) {
                const targetPage = parseInt(e.target.dataset.page);
                if (targetPage !== currentPage && targetPage >= 0 && targetPage < totalPages) {
                    currentPage = targetPage;
                    updateCarousel();
                    console.log(`🎯 Navegando directamente a página ${currentPage + 1} en ${carouselId}`);
                }
            }
        });
        
        // Soporte táctil para dispositivos móviles
        let startX = 0;
        let endX = 0;
        
        carousel.addEventListener('touchstart', function(e) {
            startX = e.touches[0].clientX;
        }, { passive: true });
        
        carousel.addEventListener('touchend', function(e) {
            endX = e.changedTouches[0].clientX;
            const diff = startX - endX;
            const threshold = 50;
            
            if (Math.abs(diff) > threshold) {
                if (diff > 0 && currentPage < totalPages - 1) {
                    // Swipe izquierda - siguiente página
                    currentPage++;
                    updateCarousel();
                    console.log(`📱 Swipe izquierda - Página ${currentPage + 1}`);
                } else if (diff < 0 && currentPage > 0) {
                    // Swipe derecha - página anterior
                    currentPage--;
                    updateCarousel();
                    console.log(`📱 Swipe derecha - Página ${currentPage + 1}`);
                }
            }
        }, { passive: true });
        
        // Inicializar carrusel
        updateCarousel();
        
        console.log(`✅ Carrusel ${carouselId} inicializado correctamente`);
        
        // Retornar funciones para uso externo si es necesario
        return {
            goToPage: function(page) {
                if (page >= 0 && page < totalPages) {
                    currentPage = page;
                    updateCarousel();
                }
            },
            getCurrentPage: function() {
                return currentPage;
            },
            getTotalPages: function() {
                return totalPages;
            }
        };
    }
    
    // Inicializar carrusel de productos destacados (solo scroll horizontal, sin botones)
    const productsCarousel = initCarousel(
        'products-carousel',
        null, // Sin botón prev
        null  // Sin botón next
    );
    
    // Inicializar carrusel de marcas (con botones prev/next)
    const brandsCarousel = initCarousel(
        'brands-carousel',
        'brands-prev',
        'brands-next'
    );
    
    console.log('🎉 Carousel Manager inicializado completamente');
    
    // Función global para debug (útil para desarrollo)
    window.debugCarousel = function() {
        console.log('🔍 Estado de los carruseles:');
        console.log('Products carousel:', document.getElementById('products-carousel'));
        console.log('Products prev button:', document.getElementById('products-prev'));
        console.log('Products next button:', document.getElementById('products-next'));
        
        if (productsCarousel) {
            console.log('Products carousel info:', {
                currentPage: productsCarousel.getCurrentPage(),
                totalPages: productsCarousel.getTotalPages()
            });
        }
        
        if (brandsCarousel) {
            console.log('Brands carousel info:', {
                currentPage: brandsCarousel.getCurrentPage(),
                totalPages: brandsCarousel.getTotalPages()
            });
        }
    };
    
    // Función global para navegar a una página específica
    window.goToPage = function(carouselType, page) {
        if (carouselType === 'products' && productsCarousel) {
            productsCarousel.goToPage(page);
        } else if (carouselType === 'brands' && brandsCarousel) {
            brandsCarousel.goToPage(page);
        }
    };
});
