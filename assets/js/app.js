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

$(document).ready(function (e) {
    const textFields = [].map.call(document.querySelectorAll('.mdc-text-field'), function(el) {
        return new MDCTextField(el);
    });

    const select = [].map.call(document.querySelectorAll('.mdc-select'), function(el) {
        return new MDCSelect(el);
    });

    const switchControl = new MDCSwitch(document.querySelector('.mdc-switch'));

    window.readURL = function(input) {
        if (input.files && input.files[0]) {

            var reader = new FileReader();

            reader.onload = function(e) {
                $('.image-upload-wrap').hide();

                $('.file-upload-image').attr('src', e.target.result);
                $('.file-upload-content').show();

                $('.image-title').html(input.files[0].name);
            };

            reader.readAsDataURL(input.files[0]);

        } else {
            removeUpload();
        }
    };

    function removeUpload() {
        $('.file-upload-input').replaceWith($('.file-upload-input').clone());
        $('.file-upload-content').hide();
        $('.image-upload-wrap').show();
    }
    $('.image-upload-wrap').bind('dragover', function () {
        $('.image-upload-wrap').addClass('image-dropping');
    });
    $('.image-upload-wrap').bind('dragleave', function () {
        $('.image-upload-wrap').removeClass('image-dropping');
    });
});

