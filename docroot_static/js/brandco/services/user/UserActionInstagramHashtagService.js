var UserActionInstagramHashtagService = (function(){
    var alreadyRead = [];

    return {
        alreadyRead: alreadyRead,

        executeAction: function(target, isAutoload){
            var form = $(target).parents().filter(".executeInstagramHashtagActionForm");
            var section = $(target).parents().filter(".jsMessage");
            var csrf_token = document.getElementsByName("csrf_token")[0].value;
            var url = form.attr("action");
            var cp_action_id = $("input[name=cp_action_id]", form).val();
            var cp_user_id = $("input[name=cp_user_id]", form).val();

            var param = {
                data: {
                    csrf_token: csrf_token,
                    cp_action_id: cp_action_id,
                    cp_user_id: cp_user_id
                },
                url: url,
                beforeSend: function() {
                    Brandco.helper.showLoading(section);
                },
                success: function(json) {
                    if (json.result === "ok") {

                        if (json.data.next_action === true) {
                            var message = $(json.html);
                            message.hide();
                            section.after(message);

                            Brandco.helper.facebookParsing(json.data.sns_action);

                            $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                                Brandco.unit.createAndJumpToAnchor(isAutoload);
                            });

                            if (section.find('input[name="instagram_user_name"]')) {
                                section.find('input[name="instagram_user_name"]').replaceWith('<input type="text" disabled="disabled" name="instagram_user_name" placeholder="ユーザーネームを入力してください。">');
                            }

                            if (section.find('.cmd_execute_instagram_hashtag_action')) {
                                section.find('.cmd_execute_instagram_hashtag_action').replaceWith('<span class="large1">登録する</span>');
                            }
                        }

                        var next_text = section.find('.cmd_execute_instagram_hashtag_action_next:first').text();
                        section.find('.cmd_execute_instagram_hashtag_action_next').replaceWith("<li class='btn1'><span class='middle1'>" + next_text + "</span></li>");

                    } else {
                        alert('エラーが発生しました');
                    }
                },
                complete: function() {
                    Brandco.helper.hideLoading();
                }
            };
            Brandco.api.callAjaxWithParam(param, false, false);
        },
        registerAccount: function(target) {
            var form = $(target).parents().filter(".executeInstagramHashtagActionForm");
            var csrf_token = document.getElementsByName("csrf_token")[0].value;
            var cp_action_id = $("input[name=cp_action_id]", form).val();
            var cp_user_id = $("input[name=cp_user_id]", form).val();
            var url = $("input[name=api_execute_instagram_hashtag_account_register]", form).val();
            var instagram_user_name = $("input[name=instagram_user_name]", form).val();
            var section = $(target).parents().filter(".jsMessage");

            var param = {
                data: {
                    csrf_token: csrf_token,
                    cp_action_id: cp_action_id,
                    cp_user_id: cp_user_id,
                    instagram_user_name: instagram_user_name
                },
                url: url,
                beforeSend: function() {
                    Brandco.helper.showLoading(section);
                },
                success: function(json) {
                    if (json.result === "ok") {
                        $('.instagram_user_name').text('');

                        var message = $(json.html);
                        message.hide();
                        section.replaceWith(message);

                        $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                            Brandco.unit.createAndJumpToAnchor();
                        });
                    }else{
                        var instagram_hashtag_error_flg = 0;
                        $.each(json.errors, function(index, value) {
                            if (value) {
                                section.find('.' + index).text(value);
                                section.find('.' + index).show();
                                instagram_hashtag_error_flg = 1;
                            }
                        });

                        if (!instagram_hashtag_error_flg) {
                            alert('エラーが発生しました');
                        }
                    }
                },
                complete: function() {
                    Brandco.helper.hideLoading();
                }
            };
            Brandco.api.callAjaxWithParam(param, false, false)
        },
        checkStatus: function(target) {
            var form = $(target).parents().filter(".executeInstagramHashtagActionForm");
            var csrf_token = document.getElementsByName("csrf_token")[0].value;
            var url = $("input[name=status_check_url]", form).val();
            var cp_action_id = $("input[name=cp_action_id]", form).val();
            var cp_user_id = $("input[name=cp_user_id]", form).val();

            var param = {
                data: {
                    csrf_token: csrf_token,
                    cp_action_id: cp_action_id,
                    cp_user_id: cp_user_id
                },
                url: url,
                success: function (json) {
                    if (json.result === "ok") {
                        var isAutoLoad = true
                        UserActionInstagramHashtagService.executeAction(target, isAutoLoad);
                    }
                }
            };
            Brandco.api.callAjaxWithParam(param, false, false);
        },
        openPreviewModal: function(target) {
            var instagram_hashtag_user_post_id = $(target).data('instagram_hashtag_user_post_id');
            var modal_id = $(target).data('modal_id');
            var media_url = $(target).data('media_url');
            var param = {
                data: 'media_url='+media_url+'&instagram_hashtag_user_post_id='+instagram_hashtag_user_post_id,
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
})();

$(function(){
    $(".cmd_execute_instagram_hashtag_action_next").off("click");
    $(".cmd_execute_instagram_hashtag_action_next").on("click", function(event){
        event.preventDefault();
        UserActionInstagramHashtagService.executeAction(this);
    });

    $(".cmd_execute_instagram_hashtag_action_skip").off("click");
    $(".cmd_execute_instagram_hashtag_action_skip").on("click", function(event){
        event.preventDefault();
        UserActionInstagramHashtagService.executeAction(this);
        $(this).hide();
    });

    $(".cmd_execute_instagram_hashtag_action_autoload").each(function(){
        var target = this;
        var inview = $(this).parents().filter(".inview");
        var form = $(target).parents().filter(".executeInstagramHashtagActionForm");
        var autoload_flg = $("input[name=autoload_flg]", form).val();

        inview.on('inview', function (event, isInView, visiblePartX, visiblePartY) {
            if (isInView && $(target).hasClass('cmd_execute_instagram_hashtag_action_autoload') && autoload_flg === "1") {
                if (UserActionInstagramHashtagService.alreadyRead[$(target).data('messageid')] === undefined) {
                    UserActionInstagramHashtagService.alreadyRead[$(target).data('messageid')] = 1;
                    UserActionInstagramHashtagService.checkStatus(target);
                }
            }
        });
    });

    $(".cmd_execute_instagram_hashtag_action").off("click");
    $(".cmd_execute_instagram_hashtag_action").on("click", function(){
        UserActionInstagramHashtagService.registerAccount(this);
    });

    $("input[name=instagram_user_name]").off("keypress");
    $("input[name=instagram_user_name]").on("keypress", function(event){
        if (event.which == 13) {
            UserActionInstagramHashtagService.registerAccount(this);
            return false;
        }
        if (!e) var e = window.event;
        if(e.keyCode == 13) {
            return false;
        }
    });

    $('.jsPreviewInstagramUserPost').on('click', function(){
        UserActionInstagramHashtagService.openPreviewModal(this);

    });

    $('.jsCheckUserName').on('click', function(){
        Brandco.unit.openModal('#howUserNameRegister');
    });

});
