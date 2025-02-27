import {isObjectEmpty, moveToElement, showPreloader} from "../utils/_helpers";
import {showMsg, showNotices} from "../plugins/_fancybox-init";

export default class FormHandler {
    constructor(selector) {
        this.selector = selector;
        this.$document = $(document);
        this.forms = $(document).find(selector);
        this.$sendengForm = $(document).find(selector);
        this.initialize();
        this.selectInit();
    }

    selectInit() {
        const t = this;
        $(document).on('change', '.trigger-form-js', function (e) {
            const $select = $(this);
            $select.closest('form').submit();
        });
    }

    initialize() {
        this.$document.on('submit', this.selector, (e) => this.handleSubmit(e));
    }

    handleSubmit(event) {
        event.preventDefault();

        const $form = $(event.target);
        const formId = $form.attr('id');


        if (!this.validateForm($form)) return;

        const formData = new FormData(document.getElementById(formId));
        $form.addClass('sending');
        this.$sendengForm = $form;
        this.sendRequest({
            type: $form.attr('method') || "POST",
            url: $form.attr('action') || adminAjax,
            processData: false,
            contentType: false,
            data: formData,
        });

        if (!$form.hasClass('no-reset')) $form.trigger('reset');
    }

    validateForm($form) {
        let isValid = true;
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/;

        // Validate inputs and textareas
        $form.find('input, textarea').each((_, input) => {
            const $input = $(input);
            const $label = $input.closest('.form-label');
            const value = $input.val().trim();
            const regExp = $input.data('reg') ? new RegExp($input.data('reg')) : null;

            if ($input.attr('required') && (!value || (regExp && !regExp.test(value)))) {
                isValid = false;
                $input.addClass('error');
                $label.addClass('error');
                moveToElement($label);
            } else {
                $input.removeClass('error');
                $label.removeClass('error');
            }
        });

        // Validate select elements
        $form.find('select[required]').each((_, select) => {
            const $select = $(select);
            const $label = $select.closest('.form-label');
            const value = $select.val();
            const test = !value || value === null || (Array.isArray(value) && value.length === 0);

            if (test) {
                isValid = false;
                $label.addClass('error');
                moveToElement($label);
            } else {
                $label.removeClass('error');
            }
        });

        // Validate custom required inputs
        if (!this.validateRequiredInputs($form)) isValid = false;

        // Validate consent checkbox
        const $consent = $form.find('input[name="consent"]');
        if ($consent.length && !$consent.prop('checked')) {
            $consent.closest('.form-consent').addClass('error');
            isValid = false;
            moveToElement($consent.closest('.form-consent'));
        } else {
            $consent.closest('.form-consent').removeClass('error');
        }

        if ($form.find('.address-js').length > 0) {
            if ($form.find('.address-js').val() !== $form.find('.address-js').attr('data-selected')) {
                isValid = false;
                $form.find('.address-js').closest('.form-label').addClass('error');
                $form.find('.address-js').addClass('error');
                moveToElement($form.find('.address-js').closest('.form-label'));
            } else {
                $form.find('.address-js').closest('.form-label').removeClass('error');
                $form.find('.address-js').removeClass('error');
            }
        }

        return isValid;
    }

    validateRequiredInputs($form) {
        const inputsGroup = {};
        let isValid = true;

        $form.find('[data-required]').each((_, input) => {
            const $input = $(input);
            const name = $input.attr('name');

            if (name) {
                if (!inputsGroup[name]) inputsGroup[name] = [];
                if ($input.prop('checked')) {
                    inputsGroup[name].push($input.val());
                }
            }
        });

        Object.keys(inputsGroup).forEach((key) => {
            const isChecked = inputsGroup[key].length > 0;
            $form.find(`[name="${key}"]`).closest('.form-label').toggleClass('error', !isChecked);
            if (!isChecked) isValid = false;
        });

        return isValid;
    }

    sendRequest(options) {
        if (this.$document.find('body').hasClass('loading')) {
            showMsg('Error');
            return;
        }
        this.showPreloader();
        this.$document.find('body').addClass('loading').addClass('sending-form');
        $.ajax(options).done((response) => {
            if (response) {
                const isJson = this.isJsonString(response);
                this.$document.find('body').removeClass('loading').removeClass('sending-form');
                this.$document.find('.loading-button').removeClass('loading-button').removeClass('not-active');
                this.$sendengForm.removeClass('sending');
                if (isJson) {
                    const data = JSON.parse(response);
                    const message = data.msg || '';
                    const text = data.msg_text || '';
                    const type = data.type || '';
                    const url = data.url || '';
                    const reload = data.reload || '';
                    if (message) {
                        showMsg(message, text, url);
                    }else {
                        if (url) {
                            showPreloader();
                            window.location.href = url;
                            return;
                        }
                    }
                    if (reload === 'true') {
                        if (message) {
                            setTimeout(function () {
                                window.location.reload();
                            }, 10000);
                            return;
                        }
                        window.location.reload();
                        return;
                    }
                } else {
                    showMsg(response);
                }

            }
            this.hidePreloader();
        });
    }

    isJsonString(str) {
        try {
            JSON.parse(str);
            return true;
        } catch {
            return false;
        }
    }


    showPreloader() {
        $('.preloader').addClass('active');
    }

    hidePreloader() {
        $('.preloader').removeClass('active');
    }
}


