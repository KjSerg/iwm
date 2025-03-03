import {showMsg} from "../plugins/_fancybox-init";
import responser from "../forms/_responser";
import {hidePreloader, isJsonString, showPreloader} from "../utils/_helpers";

export default function editInvoiceLink() {
    $(document).on('click', '.edit-invoice-link', function (e) {
        e.preventDefault();
        const $t = $(this);
        const href = $t.attr('href');
        showPreloader();
        $.ajax({
            type: "POST",
            url: href
        }).done((response) => {
            if (response) {
                responser(response);
            }
            hidePreloader();
        });
    });
}