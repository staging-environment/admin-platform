import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();

// Slider functionality for welcome page
document.addEventListener('DOMContentLoaded', () => {
    const sliderContainer = document.getElementById('slider-container');
    const prevButton = document.getElementById('prev-slide');
    const nextButton = document.getElementById('next-slide');

    if (sliderContainer && prevButton && nextButton) {
        const slides = sliderContainer.querySelectorAll('img');
        let currentIndex = 0;
        const totalSlides = slides.length;

        function updateSliderPosition() {
            const slideWidth = slides[0].clientWidth;
            sliderContainer.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
        }

        function showNextSlide() {
            currentIndex = (currentIndex + 1) % totalSlides;
            updateSliderPosition();
        }

        function showPrevSlide() {
            currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
            updateSliderPosition();
        }

        // Event Listeners
        nextButton.addEventListener('click', showNextSlide);
        prevButton.addEventListener('click', showPrevSlide);

        // Optional: Auto-advance slider
        // setInterval(showNextSlide, 5000); // Change slide every 5 seconds
    }
});
