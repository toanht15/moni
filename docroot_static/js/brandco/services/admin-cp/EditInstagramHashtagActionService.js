var EditInstagramHashtagActionService = {
    initPreview: function() {

        if ($('.jsSkip').prop('checked')) {
            $('.jsSkipTgt').show();
        }else{
            $('.jsSkipTgt').hide();
        }

        if ($('.jsAutoload').prop('checked')) {
            $('.jsAutoloadTgt').hide();
        }else{
            $('.jsAutoloadTgt').show();
        }

        $('.jsBtnPreview').text($('.jsBtnText').val());

        if($('#image_url').val() != ''){
            $('.imagePreview').attr('src', $('#image_url').val());
        } else {
            $('.imagePreview').parent().hide();
        }

        if ($('#jsTextArea').val()) {
            var text_content = $('#jsTextArea').val();
            var param = {
                data: {
                    text_content: text_content
                },
                url: 'admin-cp/parse_markdown',
                success: function(response) {
                    if (response.result == 'ok') {
                        $(".jsTextPreview").html(response.data.html_content);
                    }
                }
            };
            Brandco.api.callAjaxWithParam(param, false,  false);
        }

        EditInstagramHashtagActionService.syncHashtagTarget();
    },
    entryHashtag: function(target) {
        var duplicate_flg = 0;
        var hashtag = $(target).val();

        if (!hashtag){
            return false;
        }

        var sharpHashtag = '#' + hashtag;

        $('.jsHashtag').each(function(){
            if ($(this).data('hashtag') == sharpHashtag) {
                duplicate_flg = 1;
            }
        });

        if (duplicate_flg == 1) {
            if (!$('#jsEntryError').children(".iconError1")[0]) {
                $('#jsEntryError').append("<p class='iconError1'>一度に同じタグは設定できません</p>");
                return false;
            }
        }else{
            $('.jsHashtagList').append("<li class='jsHashtag' data-hashtag=" + sharpHashtag + ">"+ sharpHashtag + "<a href='javascript:void(0);' class='iconBtnDelete jsHashtagDelete'>削除する</a></li>");
            $('.jsHashtagList').append("<input type='hidden' name='hashtags[]' value=" + hashtag + ">");
            $(target).val('');
            $('#jsEntryError').children(".iconError1").remove();
            $('.jsHashtagTextTgt').append('#' + hashtag + ' ');
        }
    },
    activateConnected: function() {
        $('.jsDisconnectedTgt').hide();
        $('.jsConnectedTgt').show();

        $('.jsDisconnectedTabTgt').removeClass('current');
        $('.jsConnectedTabTgt').addClass('current');
    },
    activateDisConnected: function() {
        $('.jsDisconnectedTgt').show();
        $('.jsConnectedTgt').hide();

        $('.jsDisconnectedTabTgt').addClass('current');
        $('.jsConnectedTabTgt').removeClass('current');
    },
    syncHashtagTarget: function() {
        var tagText = '';
        $(".jsHashtag").each(function(){
            if ($(this).data('hashtag') != 'undefined') {
                tagText += $(this).data('hashtag') + ' ';
            }
        });
        $('.jsHashtagTextTgt').text(tagText);
    },
    openPreview: function(target) {
        var modal_id = $(target).data('modal_id');
        var media_url = $(target).data('modal_url');
        var param = {
            data: 'media_url='+media_url,
            url: 'instagram/api_get_instagram_embed_media_for_thread.json',
            success: function(response) {
                if (response.result == 'ok') {
                    $('#instagram_embed_modal').html(response.data.embed_media);
                    instgrm.Embeds.process();

                    $(modal_id).height($('body').height()).fadeIn(300, function(){
                        $(this).find('.jsModalCont').css({
                            display: 'block',
                            opacity: 0,
                            top: $(window).scrollTop()
                        }).animate({
                                top: $(window).scrollTop() + 30,
                                opacity: 1
                            }, 300, function() {
                                var modal_height = $(modal_id).find('.jsModalCont').position().top + $(modal_id).find('.jsModalCont').outerHeight(true);
                                var body_height = $('body').outerHeight(true);

                                if (body_height < modal_height) {
                                    $('body').data('prev_height', body_height);
                                    $('body').height(modal_height + 10);
                                    $(modal_id).height($('body').height());
                                } else {
                                    $('body').data('prev_height', 0);
                                }
                            });
                    });
                }
            }
        }
        Brandco.api.callAjaxWithParam(param);
    }
}

$(document).ready(function(){

    $('.jsBrandSocialAccount').on('click', function(){
        $('.jsBrandSocalAccountTgtImg').attr('src', $(this).data('img'));
        $('.jsBrandSocalAccountTgtName').text($(this).data('name'));
        $('.jsBrandSocalAccountTgtAbout').text($(this).data('about'));
    });

    $('#image_file').on('change', function(){
        var input = $(this)[0];
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('.imagePreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
            $('.imagePreview').parent().show();
        }else{
            $('.imagePreview').parent().hide();
        }
    });

    $('.jsAccountSetting').on('click', function(){
        EditInstagramHashtagActionService.activateDisConnected();
    })

    $('.jsSkip').on('click', function(){
        $('.jsSkipTgt').toggle();
        EditInstagramHashtagActionService.activateDisConnected();
    });

    $('.jsDisconnected').on('click', function(){
        EditInstagramHashtagActionService.activateDisConnected();
    })

    $('.jsConnected').on('click', function(){
        EditInstagramHashtagActionService.activateConnected();
    });

    $('.jsHashtagClick').on('click', function(){
        EditInstagramHashtagActionService.activateConnected();
    });

    $('.jsHashtagEntry').on('keypress', function(event){
        if (event.which == 13) {
            EditInstagramHashtagActionService.entryHashtag(this)
        }
    });

    $('.jsHashtagEntry').blur(function(){
        EditInstagramHashtagActionService.entryHashtag(this)
    });

    $('.jsHashtagAdd').on('click', function(){
        var target = $('.jsHashtagEntry').get(0);
        EditInstagramHashtagActionService.entryHashtag(target);
    });

    $(document).on('click','.jsHashtagDelete',function(){
        $(this).parent("li").next("input").remove();
        $(this).parent("li").remove();

        EditInstagramHashtagActionService.syncHashtagTarget();
    });

    $('.jsAutoload').on('click', function(){
        $('.jsAutoloadTgt').toggle();
        EditInstagramHashtagActionService.activateConnected();
    });

    $('.jsDisconnectedTabTgt').on('click', function(){
        EditInstagramHashtagActionService.activateDisConnected();
    })

    $('.jsConnectedTabTgt').on('click', function(){
        EditInstagramHashtagActionService.activateConnected();
    })

    $('.jsApproval').on('click', function(){
        EditInstagramHashtagActionService.activateConnected();
    });

    $('.jsBtnText').on('input', function(){
        $('.jsBtnPreview').text($('.jsBtnText').val());
    });

    $('.jsBtnText').on('click', function(){
        $('.jsBtnPreview').text($('.jsBtnText').val());
        EditInstagramHashtagActionService.activateConnected();
    });

    EditInstagramHashtagActionService.initPreview();

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker({
        minDate: new Date()
    });

    $('#jsTextArea').on('input', function(){
        var text_content = $('#jsTextArea').val();
        var param = {
            data: {
                text_content: text_content
            },
            url: 'admin-cp/parse_markdown',
            success: function(response) {
                if (response.result == 'ok') {
                    $(".jsTextPreview").html(response.data.html_content);
                }
            }
        };
        Brandco.api.callAjaxWithParam(param, false,  false);
    });

    $('.jsPreviewInstagramUserPost').on('click', function(){
        EditInstagramHashtagActionService.openPreview(this);
    });
});
