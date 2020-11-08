function Slider(){}
/**
 * Addы click handler to еру given area
 *
 * @param {object} DOM element - Are which should be listened for clicks
 */
Slider.areaHandler = function(area) {
    area.addEventListener('click', function(event) {
        const button = event.target;
        
        // If it is not slider button, exit
        if (button.classList.contains('right-slider-button')) {
            Slider.nextSlide(button.parentElement);
        } else if (button.parentElement.classList.contains('right-slider-button')) {
            Slider.nextSlide(button.parentElement.parentElement);
        }

        if (button.classList.contains('left-slider-button')) {
            Slider.previousSlide(button.parentElement);
        } else if (button.parentElement.classList.contains('left-slider-button')) {
            Slider.nextSlide(button.parentElement.parentElement);
        }
    });
};
/**
 * Changes current slide to next
 *
 * @param {object} DOM element
 */
Slider.nextSlide = function(slider) {
    const currentSlide = slider.querySelector('.current-slide');

    if (currentSlide.nextElementSibling.classList.contains('slider-button')) {
        currentSlide.classList = 'slide';
        slider.firstElementChild.classList = 'current-slide';
    } else {
        currentSlide.classList = 'slide'
        currentSlide.nextElementSibling.classList = 'current-slide';
    }
};
/**
 * Change current slide to previous
 *
 * @param {object} DOM element
 */
Slider.previousSlide = function(slider) {
    const currentSlide = slider.querySelector('.current-slide');
    const lastSlide = slider.children[slider.children.length - 3];

    if (currentSlide.previousElementSibling == null) {
        currentSlide.classList = 'slide';
        lastSlide.classList = 'current-slide';
    } else {
        currentSlide.classList = 'slide'
        currentSlide.previousElementSibling.classList = 'current-slide';
    }
};