export function makeActiveStars() {
    $('.rating-input').on('mouseenter touchstart', function () {
        const $t = $(this);
        const $wrapper = $t.closest('.rating-inputs');
        $wrapper.find('.rating-input').removeClass('active');
        const index = $t.index();
        $t.addClass('active');
        for (let a = 0; a <= index; a++) {
            $wrapper.find('.rating-input').eq(a).addClass('active');
        }
    }).on('mouseleave touchend', function () {
        const $t = $(this);
        const $wrapper = $t.closest('.rating-inputs');
        $wrapper.find('.rating-input').removeClass('active');
    });
    $('.rating-input input').change(function () {
        const $t = $(this);
        const $label = $t.closest('.rating-input');
        const $wrapper = $t.closest('.rating-inputs');
        const index = $label.index();
        $wrapper.find('.rating-input').removeClass('current');
        for (let a = 0; a <= index; a++) {
            $wrapper.find('.rating-input').eq(a).addClass('current');
        }
    }).trigger('change');
    $('.rating-input').on('click touchend', function (e) {
        e.preventDefault(); // Запобігає зайвому спрацьовуванню
        const $t = $(this);
        const $wrapper = $t.closest('.rating-inputs');
        $wrapper.find('input[checked]').removeAttr('checked');
        $t.find('input').prop('checked', true).change();
    });
}