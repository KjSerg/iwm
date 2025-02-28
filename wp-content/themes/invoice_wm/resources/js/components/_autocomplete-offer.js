import {hidePreloader, isJsonString, showPreloader} from "../utils/_helpers";
import {showMsg} from "../plugins/_fancybox-init";

export const autocompleteOffer = () => {
    $(document).on('input', '.autocomplete-offer', function () {
        const $input = $(this);
        const $list = $input.closest('.form-autocomplete').find('.form-autocomplete-list');
        const $selected = $input.closest('.form-autocomplete').find('[name="selected_offers"]');
        let val = $input.val().trim();
        if (val.length < 3) {
            $list.html('');
            $list.slideUp();
            return;
        }
        showPreloader();
        $.ajax({
            type: "POST",
            url: adminAjax,
            data: {
                action: 'get_offers_html', val, selected: $selected.val() || ''
            },
        }).done((response) => {
            $list.html(response);
            if (response) {
                $list.slideDown();
            } else {
                $list.slideUp();
            }
            hidePreloader();
        });
    });
    $(document).on('change', '.offer-checkbox', function () {
        const $input = $(this);
        let val = $input.val();
        val = Number(val);
        const $wrap = $input.closest('.form-autocomplete');
        const $selected = $wrap.find('[name="selected_offers"]');
        const $list = $wrap.find('.form-autocomplete-list');
        const list = $selected.val();
        let arr = list ? list.split(",") : [];
        if (arr) arr = arr.map((item) => Number(item));
        if ($input.prop('checked') === true) {
            arr.push(val);
        } else {
            arr = arr.filter(item => item !== val);
        }
        $selected.val(arr.join(','));
    });
}