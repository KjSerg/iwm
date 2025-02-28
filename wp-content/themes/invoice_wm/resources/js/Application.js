import {copyToClipboard, detectBrowser, hidePreloader, isMobile, showPreloader} from "./utils/_helpers";
import {accordion} from "./ui/_accardion";
import {numberInput} from "./forms/_number-input";
import {showPassword} from "./forms/_show-password";
import {fancyboxInit, showNotices} from "./plugins/_fancybox-init";
import FormHandler from "./forms/FormHandler";
import {autocompleteOffer} from "./components/_autocomplete-offer";
export default class Application {
    constructor() {
        this.$doc = $(document);
        this.$body = $("body");
        this.parser = new DOMParser();
        this.init();
    }
    init() {
        this.initBrowserAttributes();
        this.initComponents();
    }

    initBrowserAttributes() {
        const browserName = detectBrowser();
        this.$body.attr("data-browser", browserName).addClass(browserName);

        if (isMobile) {
            this.$body.attr("data-mobile", "mobile");
        }
    }

    initComponents() {
        this.$doc.ready(() => {
            autocompleteOffer();
            accordion();
            numberInput();
            showPassword();
            fancyboxInit();
            this.loadMore();
            this.showLoaderOnClick();
            const form = new FormHandler('.form-js');
            this.$doc.on('click', '.copy-link', function (e) {
                e.preventDefault();
                let $t = $(this);
                let url = $t.attr('href');
                copyToClipboard(url);
            });
        });

    }

    loadMore() {
        let load = false;
        const parser = new DOMParser();
        $(document).on('click', '.next-post-link', function (e) {
            e.preventDefault();
            const $t = $(this);
            const href = $t.attr('href');
            if (load) return;
            const $pagination = $(document).find('.pagination-wrapper');
            showPreloader();
            $pagination.addClass('not-active');
            $t.addClass('not-active');
            $.ajax({
                type: 'GET',
                url: href,
            }).done(function (r) {
                hidePreloader();
                let $requestBody = $(parser.parseFromString(r, "text/html"));
                $(document).find('.container-js').append($requestBody.find('.container-js').html());
                $pagination.html($requestBody.find('.pagination-wrapper').html());
                load = false;
                $pagination.removeClass('not-active');
                $t.remove();
            });
        });
    }

    showLoaderOnClick() {
        this.$doc.on('click', 'a.show-load, .header a, .footer a', function (e) {
            if (!$(this).attr('href').includes('#')) showPreloader();
        });
    }
}