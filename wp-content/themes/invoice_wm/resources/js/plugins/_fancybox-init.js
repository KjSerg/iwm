import $ from 'jquery';

window.$ = $;
window.jQuery = $;
import '@fancyapps/fancybox';

export const fancyboxInit = () => {
    $('[data-fancybox]').fancybox({});
    $(document).on('click', '.fancybox', function (e) {
        e.preventDefault();
        const $t = $(this);
        const href = $t.attr('href');
        if (href === undefined) return;
        const $el = $(document).find(href);
        if ($el.length === 0) return;
        $.fancybox.open($el, {
            touch: false
        });
    });
    $(document).on('click', '.close-fancybox-modal', function (e) {
        e.preventDefault();
        $.fancybox.close();
    });
};

export const openModal = ($modal) => {
    $.fancybox.open($modal, {
        touch: false
    });
};

export function showMsg(msg, title = '', url = '') {
    const selector = '#dialog';
    const $modal = $(document).find(selector);
    console.log($modal)
    if ($modal.length === 0) {
        alert(msg);
        if (url) {
            window.location.href = url;
        }
        return;
    }
    $modal.find('.modal__title').html(title);
    $modal.find('.modal__text').html(msg);
    $.fancybox.open($modal, {
        afterClose: function() {
            if (url) {
                window.location.href = url;
            }
        }
    });
    setTimeout(function (){
        $.fancybox.close()
    }, 3000)
}

export function showNotices(index = 0) {
    const $notices = $(document).find('.notices > *');
    if ($notices.length === 0) return;
    const $item = $notices[index];
    if ($item === undefined) return;
    let nextIndex = index + 1;
    const $itemNext = $notices[nextIndex];
    const args = {};
    if ($itemNext !== undefined) args.afterClose = function () {
        showNotices(nextIndex);
    };
    $.fancybox.open($item, args);
}