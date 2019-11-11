$(document).ready(function () {
    if(location.hash) {
        $('.mdc-tab[data-table='+ location.hash.substring(1) +']').get(0).click();
        $('.' + location.hash.substring(1)).show();
    } else {
        $('.brands-table').show();
    }

    $('.mdc-tab').on('click', function (e) {
        $('.attribute-table').hide();
        var table = $(this).data('table');
        $('.' + table).show();
        location.hash = table;

        if($('.attribute-edit-input').val()) {
            $('.edit-attribute-form').replaceWith($('.attribute-edit-input').val());
        }
        if($('.attribute-edit-code').val()) {
            $('.edit-attribute-form-code').replaceWith($('.attribute-edit-code').val());
        }
    });

    $('.mdc-button').on('click', function (e) {

        var input = $(this).prev().find('input');

        if(input.val()) {
           var attributeType = input.attr('name');
           var xml = '';
           if(attributeType === 'brand-attribute') {
               xml = "<Attribute><Type>Brand</Type><Text>"+ input.val() +"</Text></Attribute>";
           } else if(attributeType === 'supplier-attribute') {
               xml = "<Attribute><Type>Supplier</Type><Code>"+ $('#supplier-code').val() +"</Code><Text>"+ input.val() +"</Text></Attribute>";
           } else if(attributeType === 'type-attribute') {
               xml = "<Attribute><Type>ProductType</Type><Text>"+ input.val() +"</Text></Attribute>";
           } else if(attributeType === 'size-attribute') {
               xml = "<Attribute><Type>Size</Type><Text>"+ input.val() +"</Text></Attribute>";
           } else if(attributeType === 'color-attribute') {
               xml = "<Attribute><Type>Colour</Type><Text>"+ input.val() +"</Text></Attribute>";
           }

            $.ajax({
                url: 'addUpdateAttribute',
                type: 'POST',
                data: {
                    'attributeXml': xml,
                    'attributeType': attributeType,
                    'attributeName': input.val(),
                    'attributeCode': $('#supplier-code').val(),
                    'action': 'create'
                },
                beforeSend: function() {
                    $(".loader-overlay").css('display', 'block');
                },
                success: function (data, status) {
                    $(".loader-overlay").css('display', 'none');
                    var tbody = input.parent().parent().next().find('tbody');
                    var xmlDoc = $.parseXML( data );
                    var $xml = $(xmlDoc);
                    var $id = $xml.find("AttributeID");

                    if(attributeType === 'supplier-attribute') {
                        tbody.prepend('<tr class="mdc-data-table__row">' +
                            '<td class="mdc-data-table__cell">'+ $id.text() +'</td>' +
                            '<td class="mdc-data-table__cell">'+ $('#supplier-code').val() +'</td>' +
                            '<td class="mdc-data-table__cell">'+ input.val() +'</td>' +
                            '<td class="mdc-data-table__cell">\n' +
                            '<span class="attribute-edit" data-id="'+ $id.text() +'" data-attribute="'+ attributeType +'"><i class="material-icons">edit</i></span>\n' +
                            '<span class="attribute-delete" data-id="'+ $id.text() +'" data-attribute="'+ attributeType +'"><i class="material-icons">delete</i></span>\n' +
                            '</td>' +
                            '</tr>');
                    } else {
                        tbody.prepend('<tr class="mdc-data-table__row">' +
                            '<td class="mdc-data-table__cell">'+ $id.text() +'</td>' +
                            '<td class="mdc-data-table__cell">'+ input.val() +'</td>' +
                            '<td class="mdc-data-table__cell">\n' +
                            '<span class="attribute-edit" data-id="'+ $id.text() +'" data-attribute="'+ attributeType +'"><i class="material-icons">edit</i></span>\n' +
                            '<span class="attribute-delete" data-id="'+ $id.text() +'" data-attribute="'+ attributeType +'"><i class="material-icons">delete</i></span>\n' +
                            '</td>' +
                            '</tr>');
                    }
                    snackbar.open();
                    $('.mdc-snackbar__label').empty().text('Attribute Added !');
                }, error: function () {
                    $(".loader-overlay").css('display', 'none');
                    snackbar.open();
                    $('.mdc-snackbar__label').empty().text('An error happened, try again');
                }
            })
        }
    });

    $(document).on('click', '.attribute-delete', function (e) {
        var id = $(this).data('id');
        var attributeType = $(this).data('attribute');
        var tr = $(this).parent().parent();

        $.ajax({
            url: 'removeAttribute',
            type: 'POST',
            data: {
                'attributeType': attributeType,
                'attributeId': id,
            },
            beforeSend: function () {
                $(".loader-overlay").css('display', 'block');
            },
            success: function (data, status) {
                $(".loader-overlay").css('display', 'none');
                tr.remove();
                $('.mdc-data-table').prepend('<span class="mdc-theme--error">Hint : You have to remove the attribute from REX as well !</span>')
                setTimeout(function() { $(".mdc-theme--error").remove(); }, 9000)
                snackbar.open();
                $('.mdc-snackbar__label').empty().text('Attribute Removed !');
            }, error: function () {
                $(".loader-overlay").css('display', 'none');
                snackbar.open();
                $('.mdc-snackbar__label').empty().text('An error happened, try again');
            }
        })
    });

    $(document).on('click', '.attribute-edit', function (e) {
        var attributeType = $(this).data('attribute');
        var tr = $(this).parent().parent();

        if (attributeType !== 'supplier-attribute') {
            if($('.attribute-edit-input').val()) {
                $('.edit-attribute-form').replaceWith($('.attribute-edit-input').val());
            }

            tr.children().eq(1).html('<div class="edit-attribute-form"><input type="text" class="attribute-edit-input" value="'+ tr.children().eq(1).text() +'"/>' +
                '<span class="attribute-edit-submit" data-attribute="' + attributeType + '"><i class="material-icons">\n' +
                'done\n' +
                '</i></span></div>');
        } else {
            if($('.attribute-edit-input').val() && $('.attribute-edit-code').val()) {
                $('.edit-attribute-form').replaceWith($('.attribute-edit-input').val());
                $('.edit-attribute-form-code').replaceWith($('.attribute-edit-code').val());
            }

            tr.children().eq(1).html('<div class="edit-attribute-form-code"><input type="text" class="attribute-edit-code" value="'+ tr.children().eq(1).text() +'"/></div>');

            tr.children().eq(2).html('<div class="edit-attribute-form"><input type="text" class="attribute-edit-input" value="'+ tr.children().eq(2).text() +'"/>' +
                '<span class="attribute-edit-submit" data-attribute="' + attributeType + '"><i class="material-icons">\n' +
                'done\n' +
                '</i></span></div>');

        }

    });

    $(document).on('click', '.attribute-edit-submit', function (e) {
        var id = $(this).parent().parent().parent().children().first().text();
        var attributeType = $(this).data('attribute');
        var xml = '';
        if(attributeType === 'brand-attribute') {
            xml = "<Attribute><Type>Brand</Type><ID>"+ id +"</ID><Text>"+ $('.attribute-edit-input').val() +"</Text></Attribute>";
        } else if(attributeType === 'supplier-attribute') {
            xml = "<Attribute><Type>Supplier</Type><ID>"+ id +"</ID><Code>"+ $('.attribute-edit-code').val() +"</Code><Text>"+ $('.attribute-edit-input').val() +"</Text></Attribute>";
        } else if(attributeType === 'type-attribute') {
            xml = "<Attribute><Type>ProductType</Type><ID>"+ id +"</ID><Text>"+ $('.attribute-edit-input').val() +"</Text></Attribute>";
        } else if(attributeType === 'size-attribute') {
            xml = "<Attribute><Type>Size</Type><ID>"+ id +"</ID><Text>"+ $('.attribute-edit-input').val() +"</Text></Attribute>";
        } else if(attributeType === 'colour-attribute') {
            xml = "<Attribute><Type>Color</Type><ID>"+ id +"</ID><Text>"+ $('.attribute-edit-input').val() +"</Text></Attribute>";
        }

        $.ajax({
            url: 'addUpdateAttribute',
            type: 'POST',
            data: {
                'attributeType': attributeType,
                'attributeXml': xml,
                'attributeId': id,
                'attributeName': $('.attribute-edit-input').val(),
                'attributeCode': $('.attribute-edit-code').val(),
                'action': 'update'
            },
            beforeSend: function() {
                $(".loader-overlay").css('display', 'block');
            },
            success: function (data, status) {
                $(".loader-overlay").css('display', 'none');

                if($('.attribute-edit-input').val()) {
                    $('.edit-attribute-form').replaceWith($('.attribute-edit-input').val());
                }
                if($('.attribute-edit-code').val()) {
                    $('.edit-attribute-form-code').replaceWith($('.attribute-edit-code').val());
                }

                snackbar.open();
                $('.mdc-snackbar__label').empty().text('Attribute Updated !');
            }, error: function () {
                $(".loader-overlay").css('display', 'none');
                snackbar.open();
                $('.mdc-snackbar__label').empty().text('An error happened, try again');
            }
        })
    });

});
