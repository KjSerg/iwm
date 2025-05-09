import {$doc} from "../utils/_helpers";

export const billConsentInputInit = () => {
    $doc.on('change', '.bill-consent__input', function () {
        const $t = $(this);
        const $forms = $doc.find('.checkout-form');
        if ($t.prop('checked') === true) {
            makeFormsActive($forms);
        } else {
            makeFormsDisabled($forms);
        }
    });
}

function makeFormsActive($forms) {
    $forms.each(function () {
        const $form = $(this);
        const $button = $form.find('button');
        $form.attr('action', $form.attr('data-action'));
        $form.removeAttr('disabled');
        $button.removeAttr('disabled');
        $button.removeClass('not-active');
    });
}

function makeFormsDisabled($forms) {
    $forms.each(function () {
        const $form = $(this);
        const $button = $form.find('button');
        $form.removeAttr('action');
        $form.attr('disabled', 'disabled');
        $button.attr('disabled', 'disabled');
        $button.addClass('not-active');
    });
}