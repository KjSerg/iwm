import {showMsg} from "../plugins/_fancybox-init";

export function removeArrayElement(element, array) {
    const index = array.indexOf(element);
    if (index !== -1) {
        array.splice(index, 1);
    }
    return array;
}

export function showPreloader() {
    $('.preloader').addClass('active');
}

export function hidePreloader() {
    $('.preloader').removeClass('active')
}

export function isJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

export const isObjectEmpty = (objectName) => {
    return JSON.stringify(objectName) === "{}";
};

export function isElementInViewport(el) {
    if (typeof jQuery === "function" && el instanceof jQuery) {
        el = el[0];
    }
    let rect = el.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

export function getCurrentDate() {
    let today = new Date();
    let day = today.getDate();
    let month = today.getMonth() + 1;
    let year = today.getFullYear();
    day = (day < 10) ? "0" + day : day;
    month = (month < 10) ? "0" + month : month;
    return day + "-" + month + "-" + year;
}

export function setCookie(name, value, days) {
    let expires = "";
    if (days) {
        let date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + encodeURIComponent(value) + expires + "; path=/; Secure; SameSite=None";
}

export function getCookie(name) {
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

export const bytesToMB = (bytes) => {
    return (bytes / (1024 * 1024)).toFixed(2);
}

export const bytesToKB = (bytes) => {
    return Math.floor(bytes / 1000);
}

export function copyToClipboard(text) {
    const tempInput = document.createElement('textarea');
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    tempInput.setSelectionRange(0, 99999);
    document.execCommand('copy');
    document.body.removeChild(tempInput);
    showMsg('Скопійовано в буфер обміну');
}

export function isImageUrl(string) {
    return /(jpg|gif|png|JPG|GIF|PNG|JPEG|jpeg|HEIF|heif)$/.test(string);
}

export function detectBrowser() {
    var userAgent = navigator.userAgent;
    var browserName;
    if (userAgent.indexOf("Firefox") > -1) {
        browserName = "firefox";
    } else if (userAgent.indexOf("SamsungBrowser") > -1) {
        browserName = "samsung-internet";
    } else if (userAgent.indexOf("Opera") > -1 || userAgent.indexOf("OPR") > -1) {
        browserName = "opera";
    } else if (userAgent.indexOf("Trident") > -1) {
        browserName = "internet-explorer";
    } else if (userAgent.indexOf("Edge") > -1) {
        browserName = "edge";
    } else if (userAgent.indexOf("Chrome") > -1) {
        browserName = "chrome";
    } else if (userAgent.indexOf("Safari") > -1) {
        browserName = "safari";
    } else {
        browserName = "unknown";
    }

    return browserName;
}

export function isEven(number) {
    return number % 2 === 0;
}

export function randomIntFromInterval(min, max) {
    return Math.floor(Math.random() * (max - min + 1) + min);
}

export function invertNumber(num) {
    return -num;
}

export const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

export function getQueryParams() {
    const urlParams = new URLSearchParams(window.location.search);
    const params = {};

    for (const [key, value] of urlParams.entries()) {
        params[key] = value;
    }
    return params;
}

export function moveToElement($el) {
    if ($el.length === 0) return;
    $('html, body').animate({
        scrollTop: $el.offset().top
    });
}