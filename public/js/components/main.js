import { initSlider } from './slider.js';
import { initProductsSlider } from './productSlider.js';
import { initDropdowns } from './dropdown.js';

document.addEventListener('DOMContentLoaded', () => {
    try {
        if (document.querySelector('.slide')) {
            initSlider();
        }
        
        if (document.querySelector('.product-slide-item')) {
            initProductsSlider();
        }
        
        if (document.querySelector('.dropdown')) {
            initDropdowns();
        }
    } catch (error) {
        console.error('Error al inicializar componentes:', error);
    }
});
