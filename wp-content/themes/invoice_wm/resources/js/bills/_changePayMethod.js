import {$doc} from "../utils/_helpers";

export const changePayMethod = () => {
    $doc.on('change', '#payment-method', function (e) {
        const val = $(this).val();
        $doc.find('.checkout-form').hide();
        $doc.find('.checkout-form#' + val).show();
    });
}