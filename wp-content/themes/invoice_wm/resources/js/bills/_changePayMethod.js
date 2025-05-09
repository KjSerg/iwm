import {$doc} from "../utils/_helpers";
import responser from "../forms/_responser";

export const changePayMethod = () => {
    $doc.on('change', '#payment-method', function (e) {
        const $t = $(this);
        const val = $t.val();
        $doc.find('.checkout-form').hide();
        $doc.find('.checkout-form#' + val).show();
        $.ajax({
            type: "POST",
            url: adminAjax,
            data: {
                'action': 'save_payment_method',
                'payment_method': val,
                'id': $t.attr('data-id'),
            },
        }).done((response) => {
            if (response) {
                responser(response);
            }
        });
    });
}