// slider.js
let currentSlide = 0;
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.slider-dot');
const totalSlides = slides.length;

export function initSlider() {
    document.getElementById('next')?.addEventListener('click', () => {
        showSlide(currentSlide + 1);
    });

    document.getElementById('prev')?.addEventListener('click', () => {
        showSlide(currentSlide - 1);
    });

    dots.forEach(dot => {
        dot.addEventListener('click', () => {
            showSlide(parseInt(dot.getAttribute('data-slide')));
        });
    });

    setInterval(() => {
        showSlide(currentSlide + 1);
    }, 5000);
}

function showSlide(index) {
    if (index >= totalSlides) {
        currentSlide = 0;
    } else if (index < 0) {
        currentSlide = totalSlides - 1;
    } else {
        currentSlide = index;
    }

    const slider = document.getElementById('slider');
    slider.style.transform = `translateX(-${currentSlide * 20}%)`;

    dots.forEach(dot => dot.classList.remove('active'));
    dots[currentSlide].classList.add('active');
}
