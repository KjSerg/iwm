import 'selectric';

export const selectrickInit = () => {
    const $select = $(document).find('.select').not('.selectric-init');
    $select.selectric(
        {
            labelBuilder: optionsItemBuilder,
            arrowButtonMarkup: arrowButtonMarkup,
            optionsItemBuilder: optionsItemBuilder
        }
    );
    $select.addClass('selectric-init');
    $select.closest('.catalog-filter-sort').addClass('active');
}

const arrowButtonMarkup = '<b class="selectric__button"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none">\n' +
    '  <path d="M5 8L10 13L15 8" stroke="#A7A9C5" stroke-linecap="round" stroke-linejoin="round"/>\n' +
    '</svg></b>';

function optionsItemBuilder(itemData) {
    const element = itemData.element;
    if (element.attr('data-icon') === undefined) {
        return itemData.text;
    }
    const img = `<img src="${element.attr('data-icon')}" alt="">`
    return '<span class="selectric__icon">' + img + '</span>' + itemData.text;
}