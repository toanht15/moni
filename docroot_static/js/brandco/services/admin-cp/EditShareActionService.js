var EditShareActionService = {
    initPreview: function() {
        var url = $('*[name=api_get_meta_data_url]').attr('value');
        var isExternalShare = $("#share_url_type_2").attr('checked');
        if(isExternalShare == 'checked') {
            var targetUrl = $('[name="share_url"]').val();
            var param = {
                data: "url=" + targetUrl,
                type: 'GET',
                url: url,
                success: function(json) {
                    if (json.result === 'ok') {
                        if(json.data.image == undefined) {
                            $('#og_image').hide();
                        } else {
                            $('#og_image').show();
                            $('#og_image').attr('src',json.data.image);
                        }
                        $('#og_title').html(json.data.title == undefined ? '' : json.data.title);
                        $('#og_description').html(json.data.description == undefined ? '' : json.data.description);
                    } else {
                        $('#og_image').hide();
                        $('#og_title').html('');
                        $('#og_description').html('');
                        if (json.errors['error_message']) {
                            alert(json.errors['error_message']);
                        } else {
                            alert('操作が失敗しました');
                        }
                    }
                }
            };
            Brandco.api.callAjaxWithParam(param, true, true);
        }
    }
}

$(document).ready(function () {
    $(window).load(function(){
        $('#fbShareTab').trigger('click');
    });

    $('#fbShareTab').click(function() {
        if (!$('#throughPreview').hasClass('playing')) {
            $('#shareTab li').removeClass('current');
            $('#fbShareTab').addClass('current');
            $(".message_share").hide();
            $('#fbSharePreview').show();
        }

        if ($('#share_text_area').val()) {
            $("#shareTextPreview").html($('#share_text_area').val());
        }
    });

    $('#share_text_area').on('input', function(){
        $("#shareTextPreview").html($(this).val());
    });

    $("[name='share_url_type']").click(function() {
        var type = $(this).val();
        //Share top page
        if(type == 1){
            //Set preview
            $('#top_page_preview').show();
            $('#external_page_preview').hide();

            //Hide url text box
            var target_wrap = $(this).parents('.jsCheckToggleWrap')[0];
            $(target_wrap).find('.jsCheckToggleTarget').slideUp(300, function() {
                $(this).closest('li').scrollTop(300);
            });

            //Disale textbox
            $("[name='share_url']").attr("disabled", "disabled");

            //Set radio value
            $("#share_url_type_2").removeAttr("checked");
            $(this).attr("checked", "checked");
        } else {
            //Set preview
            $('#top_page_preview').hide();
            $('#external_page_preview').show();

            //show url text box
            var target_wrap = $(this).parents('.jsCheckToggleWrap')[0];
            $(target_wrap).find('.jsCheckToggleTarget').slideDown(300, function() {
                $(this).closest('li').scrollTop(300);
            });

            //enable textbox
            $("[name='share_url']").removeAttr("disabled");

            //Set radio value
            $("#share_url_type_1").removeAttr("checked");
            $(this).attr("checked", "checked");
        }
    });

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker({
        minDate: new Date()
    });

    $('#preview').click(function() {
        EditShareActionService.initPreview();
    });
});
