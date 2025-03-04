import {openModal, showMsg} from "../plugins/_fancybox-init";
import {isJsonString, showPreloader} from "../utils/_helpers";

export default function responser(response) {
    const $document = $(document);
    if (response) {
        const isJson = isJsonString(response);
        if (isJson) {
            const data = JSON.parse(response);
            const message = data.msg || '';
            const text = data.msg_text || '';
            const type = data.type || '';
            const url = data.url || '';
            const reload = data.reload || '';
            const editFormHTML = data.edit_form_html || '';
            const editFormID = data.edit_form_id || '';
            const viewsHTML = data.views_html || '';
            if (message) {
                showMsg(message, text, url);
            } else {
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
            if (editFormHTML) {
                $document.find('.modal-edit-invoice-container').html(editFormHTML);
                openModal($('#modal-edit-invoice'));
            }
            if (viewsHTML) {
                $document.find('.modal-views-container').html(viewsHTML);
                openModal($('#modal-views'));
            }
        } else {
            showMsg(response);
        }
    }
}