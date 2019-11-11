/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.scss');


// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');
global.$ = global.jQuery = $;

import {MDCTextField} from '@material/textfield';
import {MDCSelect} from '@material/select';
import {MDCSwitch} from '@material/switch';
import {MDCRipple} from '@material/ripple';
import {MDCSnackbar} from '@material/snackbar';
import {MDCTabBar} from '@material/tab-bar';
import {MDCDataTable} from '@material/data-table';

$(document).ready(function (e) {
    const textFields = [].map.call(document.querySelectorAll('.mdc-text-field'), function(el) {
        return new MDCTextField(el);
    });

    const select = [].map.call(document.querySelectorAll('.mdc-select'), function(el) {
        return new MDCSelect(el);
    });

    $('.mdc-list-item').on('click', function () {
       var value = $.trim($(this).data('value'));
        $(this).parent().parent().parent().parent().find('.input-select').val(value);
    });

    const switchControl = [].map.call(document.querySelectorAll('.mdc-switch'), function (el) {
        return new MDCSwitch(el);
    });

    const buttonRipple = [].map.call(document.querySelectorAll('.mdc-button'), function (el) {
        return new MDCRipple(el);
    });

    global.snackbar = new MDCSnackbar(document.querySelector('.mdc-snackbar'));

    const tabBar = [].map.call(document.querySelectorAll('.mdc-tab-bar'), function (el) {
        return new MDCTabBar(el)
    });

    const dataTable = [].map.call(document.querySelector('.mdc-data-table'), function (el) {
        return new MDCDataTable(el);
    });
});

