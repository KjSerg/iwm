import {$doc} from "../utils/_helpers";
import responser from "../forms/_responser";

export const changePayMethod = () => {
    if($doc.find('#payment-method').length === 0) return;
    $doc.on('change', '#payment-method', function (e) {
        const $t = $(this);
        const val = $t.val();
        showComponent(val, $t.attr('data-id'));
    });
    showComponent($doc.find('#payment-method').val(), $doc.find('#payment-method').attr('data-id'));
}

function showComponent(paymentMethodName, billID) {
    const val = paymentMethodName;
    $doc.find('.checkout-form').hide();
    $doc.find('.checkout-form#' + val).show();
    $.ajax({
        type: "POST",
        url: adminAjax,
        data: {
            'action': 'save_payment_method',
            'payment_method': val,
            'id': billID,
        },
    }).done((response) => {
        if (response) {
            responser(response);
        }
    });
}