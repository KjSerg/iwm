import 'slick-carousel';
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css'

export default class Slick {
    constructor() {
        this.init();
    }

    setEqualHeight($slider, selector = '.clients-list-item') {
        let maxHeight = 0;
        let $slides = $slider.find('.slick-slide ' + selector);
        $slides.css('min-height', 'auto');
        $slides.each(function () {
            let slideHeight = $(this).outerHeight();
            if (slideHeight > maxHeight) {
                maxHeight = slideHeight;
            }
        });

        $slides.css('min-height', maxHeight + 'px');
    }

    reviewsSliderInit() {
        const t = this;
        $(document).find('.clients-list').each(function () {
            const $slider = $(this);
            const $prev = $(this).closest('section').find('.slick__prev');
            const $next = $(this).closest('section').find('.slick__next');
            $slider.slick({
                slidesToShow: 3,
                arrows: true,
                prevArrow: $prev,
                nextArrow: $next,
                adaptiveHeight: false,
                dots: false,
                responsive: [
                    {
                        breakpoint: 1100,
                        settings: {
                            slidesToShow: 2
                        }
                    },
                    {
                        breakpoint: 767,
                        settings: {
                            slidesToShow: 1
                        }
                    },
                ]
            });
            $slider.on('setPosition', function () {
                t.setEqualHeight($slider);
            });

        });
    }


    init() {
        this.reviewsSliderInit();
    }
}

