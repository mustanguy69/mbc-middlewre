$(document).ready(function () {
    $(".search-input").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(this).parent().next().children().filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    var tags = [];

    if($('.input-tag').val()) {
        if ($('.input-tag').val().indexOf(',')) {
            var tagsArray = $('.input-tag').val().split(',');
            tagsArray.forEach(function (value) {
                tags.push(value);
            });

        } else {
            tags.push($('.input-tag').val());
        }
    }

    $('input[name=tags-field]').on('keyup', function (e) {
        if(e.keyCode === 13 || e.keyCode === 32) {

            if($.trim($(this).val()) !== ''  && $.trim($(this).val()) !== null) {
                tags.push($.trim($(this).val()));
                $('.tag-container').append('<span class="tag"><span class="remove-tag" data-value="' + $.trim($(this).val()) +'"><i class="material-icons">close</i></span>' + $.trim($(this).val()) +'</span>');
                $(this).val('');
                $('.input-tag').val(tags);
            }
        }
    });

    $(document).on('click', '.remove-tag', function (e) {
        tags.splice($.inArray($(this).data('value'), tags),1);
        $('.input-tag').val(tags);
        $(this).parent().remove();
    });

    $(document).on('change', '.file-upload-input', function (e) {
        readURL();
    });

    $('.file-upload-btn').on('click', function (e) {
        $('.file-upload-input').get(0).click();
    });

    function readURL() {
        var input = $('.file-upload-input').get(0);
        if (input.files) {
            console.log('test');
            for(var i=0; i< input.files.length; i++){
                (function(file) {
                    var fileName = file.name;
                    var reader = new FileReader();
                    var button = '<button type="button" data-name="'+ fileName +'" class="remove-image"><i class="material-icons">close</i></button>';
                    var iterate = i;
                    if($('.img-container-0').length) {
                        iterate = iterate + $('.img-container').length;
                    }
                    reader.onload = function(e) {
                        $('.image-upload-wrap').hide();
                        var imgResult = e.target.result;
                        if($('.img-container-0').length) {
                            $('.file-upload-content').show().append('<div class="img-container img-container-'+ iterate +'" data-name="'+fileName+'"><img class="file-upload-image" data-name="'+fileName+'" src="'+ imgResult +'"/></div>');
                        } else {
                            $('.file-upload-content').show().prepend('<div class="img-container img-container-'+ iterate +'" data-name="'+fileName+'"><img class="file-upload-image" data-name="'+fileName+'" src="'+ imgResult +'"/></div>');

                        }

                        $('.img-container[data-name="'+fileName+'"]').prepend(button);
                    };
                    reader.readAsDataURL(file);
                })(input.files[i]);

            }


        } else {
            removeUpload();
        }
    }

    $(document).on('click', '.remove-image', function (e) {
        removeUpload($(this).data('name'));
    });

    function removeUpload(target) {

        var imgContainer = $('.img-container[data-name="'+target+'"]');

        imgContainer.remove();

        if($('.file-upload-image').length === 0) {
            $('.file-upload-input').val('');
            $('.file-upload-input').replaceWith($('.file-upload-input').clone());
            $('.file-upload-content').hide();
            $('.image-upload-wrap').show();
        }

    }

    $('.image-upload-wrap').bind('dragover', function () {
        $('.image-upload-wrap').addClass('image-dropping');
    });
    $('.image-upload-wrap').bind('dragleave', function () {
        $('.image-upload-wrap').removeClass('image-dropping');
    });

    $('#create-form').on('submit', function (e) {
        $('.mdc-select--required').on('click', function (e) {
            console.log('trte');
            $(this).find('.mdc-theme--error').remove();
        }).find('input[type=hidden]').each(function (v) {
            if ($(this).val() === '') {
                e.preventDefault();
                $(this).parent().addClass('mdc-select--invalid').append('<span class="mdc-theme--error">This field is required</span>')
            }
        });
    });
});