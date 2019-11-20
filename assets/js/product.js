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
            e.preventDefault();
            e.stopImmediatePropagation();
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

    $('.file-upload-btn').on('click', function (e) {
        $('.file-upload-input').get(0).click();
    });

    imgArray = [];
    function readURL() {
        var input = $('.file-upload-input').get(0);
        if (input.files) {
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
                            $('.file-upload-content').show().append('<div class="img-container img-container-'+ iterate +'" data-name="'+fileName+'">' +
                                '<img class="file-upload-image" data-name="'+fileName+'" src="'+ imgResult +'"/>' +
                                '<input type="text" style="width:99%" placeholder="name" name="base64ImageName[]" required/>' +
                                '</div>');
                        } else {
                            $('.file-upload-content').show().prepend('<div class="img-container img-container-'+ iterate +'" data-name="'+fileName+'">' +
                                '<img class="file-upload-image" data-name="'+fileName+'" src="'+ imgResult +'"/>' +
                                '<input type="text" style="width:99%" placeholder="name" name="base64ImageName[]" required/>' +
                                '</div>');
                        }

                        if(imgResult.length) {
                            $('.file-upload').append('<input class="file-upload-input-hidden" data-name="'+fileName+'" name="base64Image[]" type="hidden" value="'+ imgResult +'"/>');
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

    $(document).on('change', '.file-upload-input', function (e) {
        readURL();
    });

    $(document).on('click', '.remove-image', function (e) {
        removeUpload($(this).data('name'));
    });

    function removeUpload(target) {

        var imgContainer = $('.img-container[data-name="'+target+'"]');

        imgContainer.remove();

        $('.file-upload-input-hidden[data-name="'+ target +'"]').remove();
        // $('.file-upload-input-hidden-uploaded[data-name="'+ target +'"]').remove();

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
        e.preventDefault();
        e.stopImmediatePropagation();
        var status = '';
        $('.mdc-select--required').on('click', function (e) {
            $(this).find('.mdc-theme--error').remove();
        }).find('input[type=hidden]').each(function (v) {
            if ($(this).val() === '') {
                status = 'error';
                $(this).parent().addClass('mdc-select--invalid').append('<span class="mdc-theme--error">This field is required</span>')
            }
        });
        if(status !== 'error') {
            $.ajax({
                url: $('#create-form').attr('action'),
                type: 'POST',
                data: $('#create-form').serialize(),
                beforeSend: function() {
                    $(".loader-overlay").css('display', 'block');
                },
                success: function (data, status) {
                    console.log(data);
                    if (data === 'Success') {
                        $(".loader-overlay").css('display', 'none');
                        $('.mdc-snackbar__label').empty().text('Product successfully added ! Redirecting ...');
                        setTimeout(function(){ window.location = '/'; }, 4000);
                    } else {
                        $(".loader-overlay").css('display', 'none');
                        $('.mdc-snackbar__label').empty().text('An error happened, try again');
                    }

                    snackbar.open();
                }, error: function () {
                    $(".loader-overlay").css('display', 'none');
                    $('.mdc-snackbar__label').empty().text('An error happened, try again');

                    snackbar.open();
                }
            });
        }
    });

    $('#edit-form').on('submit', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        var status = '';
        $('.mdc-select--required').on('click', function (e) {
            $(this).find('.mdc-theme--error').remove();
        }).find('input[type=hidden]').each(function (v) {
            if ($(this).val() === '') {
                status = 'error';
                $(this).parent().addClass('mdc-select--invalid').append('<span class="mdc-theme--error">This field is required</span>')
            }
        });
        if(status !== 'error') {
            $.ajax({
                url: $('#edit-form').attr('action'),
                type: 'POST',
                data: $('#edit-form').serialize(),
                beforeSend: function() {
                    $(".loader-overlay").css('display', 'block');
                },
                success: function (data, status) {
                    if (data === 'Success') {
                        $(".loader-overlay").css('display', 'none');
                        $('.mdc-snackbar__label').empty().text('Product successfully updated');
                    } else {
                        $(".loader-overlay").css('display', 'none');
                        $('.mdc-snackbar__label').empty().text('An error happened, try again');
                    }

                    snackbar.open();
                }, error: function () {
                    $(".loader-overlay").css('display', 'none');
                    $('.mdc-snackbar__label').empty().text('An error happened, try again');

                    snackbar.open();
                }
            });
        }
    });
});