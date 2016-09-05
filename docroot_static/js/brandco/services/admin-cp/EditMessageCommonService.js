$(document).ready(function () {

    initPreview();

    onChangePreview();
});

function onChangePreview() {
    $('#text_area').on('input', function() {
        var text_content = $(this).val();
        var param = {
            data: {
                text_content: text_content
            },
            url: 'admin-cp/parse_markdown',
            success: function(response) {
                if (response.result == 'ok') {
                    $("#textPreview_normal").html(response.data.html_content);
                }
            }
        };
        Brandco.api.callAjaxWithParam(param, false,  false);
    });

    $('#image_url').on('change', function(){
        if($(this).val() == ''){
            $('#imagePreview_normal').parent().hide();
        }else{
            $('#imagePreview_normal').attr('src', $(this).val());
            $('#imagePreview_normal').parent().show();
        }
    });

    $('#image_file').on('change', function(){
        var input = $(this)[0];
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#imagePreview_normal').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
            $('#imagePreview_normal').parent().show();
        }else{
            $('#imagePreview_normal').parent().hide();
        }
    });

    $('input[name=design_type]').change(function () {
        var msgType = $(this).val();

        $('#message_type').children().each(function () {
            if ($(this).attr("id") == 'message_type_' + msgType) {
                $(this).show();
            } else {
                $(this).hide();
            }
        })
    });

    $('.labelTitle').change(function(){
        var actionImage = $(this).parent('li').find('.actionImage');

        if (actionImage.attr('id') == 'image_file') {
            var input = actionImage[0];

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#imagePreview_normal').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
                $('#imagePreview_normal').parent().show();
            }
        } else if (actionImage.attr('id') == 'image_url') {
            if ( actionImage.val() != '') {
                $('#imagePreview_normal').attr('src', actionImage.val());
                $('#imagePreview_normal').parent().show();
            }
        } else {
            $('#imagePreview_normal').parent().hide();
        }
    });
}

function initPreview() {
    var text_content = $('#text_area').val(),
        param = {
        data: {
            text_content: text_content
        },
        url: 'admin-cp/parse_markdown',
        success: function(response) {
            if (response.result == 'ok') {
                $("#textPreview_normal").html(response.data.html_content);
            }
        }
    };
    Brandco.api.callAjaxWithParam(param, false,  false);

    if ($('#image_url').val() != '') {
        $('#imagePreview_normal').attr('src', $('#image_url').val());
    } else {
        $('#imagePreview_normal').parent().hide();
    }
}