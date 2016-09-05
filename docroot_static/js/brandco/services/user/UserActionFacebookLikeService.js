if (typeof(UserActionFacebookLikeService) === 'undefined') {
    var UserActionFacebookLikeService = (function () {
        var alreadyRead = [];

        return {
            alreadyRead: alreadyRead,
            executeFBLikeLogAction: function (target, action_url, status) {
                var form = $(target).parents().filter('.executeFbLikeActionForm');
                var section = $(target).parents().filter(".jsMessage");
                var csrf_token = $('input[name=csrf_token]', form).val();
                var cp_action_id = $('input[name=cp_action_id]', form).val();
                var cp_user_id = $('input[name=cp_user_id]', form).val();
                var brand_social_account_id = $('input[name=brand_social_account_id]', form).val();
                if (action_url == '') {
                    if ($('.cmd_execute_like_skip_action', form).size() > 0 ||
                        $(target).hasClass('cmd_execute_like_unread_action')
                    ) {
                        action_url = form.attr("action");
                    } else {
                        action_url = $('input[name=fb_like_log_action_url]', form).val();
                    }
                }
                var param = {
                    data: {
                        csrf_token: csrf_token,
                        cp_action_id: cp_action_id,
                        cp_user_id: cp_user_id,
                        brand_social_account_id: brand_social_account_id,
                        status: status
                    },
                    url: action_url,
                    success: function (json) {
                        if (json.result === "ok") {
                            if ($('.cmd_execute_like_skip_action', form).size() > 0 ||
                                    $(target).hasClass('cmd_execute_like_unread_action')
                               ) {
                                $('.cmd_execute_like_skip_action', form).remove();
                                if (json.data.next_action === true) {
                                    var isAutoLoad = false;
                                    var message = $(json.html);
                                    message.hide();
                                    section.after(message);
                                    Brandco.helper.facebookParsing(json.data.sns_action);
                                    $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                                        Brandco.unit.createAndJumpToAnchor(isAutoLoad);
                                    });
                                }
                            }
                            if (status == 1) {
                                $('.engagementFb_pc', form).remove();
                                $('.engagementFb_sp', form).remove();
                                $('#like_1_action', form).css('display', 'block');
                            }
                        }
                    }
                };
                Brandco.api.callAjaxWithParam(param);
            },
            executeSNSGetDataAction: function (target) {
                var form = $(target).parents().filter('.executeFbLikeActionForm');
                var csrf_token = $('input[name=csrf_token]', form).val();
                var brand_social_account_id = $('input[name=brand_social_account_id]', form).val();
                var action_url = $('input[name=sns_get_data_action_url]', form).val();
                var param = {
                    data: {
                        csrf_token: csrf_token,
                        brand_social_account_id: brand_social_account_id
                    },
                    url: action_url,
                    success: function (json_data) {
                        FB.api(
                            '/' + json_data.data.user_info.social_media_account_id + '/likes/' + json_data.data.brand_social_account.social_media_account_id,
                            {
                                access_token: json_data.data.user_info.social_media_access_token
                            },
                            function (response) {
                                if (response && !response.error && response.data[0]) {
                                    var action_url = form.attr("action");
                                    UserActionFacebookLikeService.executeFBLikeLogAction(target, action_url, 2);
                                    $('.engagementFb_pc', form).remove();
                                    $('.engagementFb_sp', form).remove();
                                    $('#like_1_already', form).css('display', 'block');
                                }
                            }
                        );
                    }
                };
                Brandco.api.callAjaxWithParam(param, false, false);
            }
        };
    })();
}

$(document).ready(function () {
    $(".cmd_execute_like_check_action").each(function(){
        var target = this;
        var inview = $(this).parents().filter(".inview");
        inview.on('inview', function (event, isInView, visiblePartX, visiblePartY) {
            if (isInView) {
                if (UserActionFacebookLikeService.alreadyRead[$(target).data('messageid')] === undefined) {
                    UserActionFacebookLikeService.alreadyRead[$(target).data('messageid')] = 1;
                    UserActionFacebookLikeService.executeSNSGetDataAction(target);
                }
            }
        });
    });
    $(".cmd_execute_like_unread_action").each(function(){
        var target = this;
        var inview = $(this).parents().filter(".inview");
        var form = $(this).parents().filter(".executeFbLikeActionForm");
        inview.on('inview', function (event, isInView, visiblePartX, visiblePartY) {
            if (isInView) {
                if (UserActionFacebookLikeService.alreadyRead[$(target).data('messageid')] === undefined) {
                    UserActionFacebookLikeService.alreadyRead[$(target).data('messageid')] = 1;
                    var status = 0;
                    var action_url = form.attr("action");
                    UserActionFacebookLikeService.executeFBLikeLogAction(target, action_url, status);
                }
            }
        });
    });
    $(".cmd_execute_like_close_action").each(function(){
        var target = this;
        var inview = $(this).parents().filter(".inview");
        var form = $(this).parents().filter(".executeFbLikeActionForm");
        inview.on('inview', function (event, isInView, visiblePartX, visiblePartY) {
            if (isInView) {
                if (UserActionFacebookLikeService.alreadyRead[$(target).data('messageid')] === undefined) {
                    UserActionFacebookLikeService.alreadyRead[$(target).data('messageid')] = 1;
                    $('.cmd_execute_like_skip_action', form).remove();
                }
            }
        });
    });
    $('.cmd_execute_like_skip_action').off('click');
    $('.cmd_execute_like_skip_action').on('click', function(event) {
        event.preventDefault();
        var target = this;
        var status = 3;
        var form = $(this).parents().filter(".executeFbLikeActionForm");
        var action_url = form.attr("action");
        UserActionFacebookLikeService.executeFBLikeLogAction(target, action_url, status);
    });
    $('.cmd_execute_dead_line_action').each(function() {
        var target = this;
        var inview = $(this).parents().filter(".inview");
        var form = $(this).parents().filter(".executeFbLikeActionForm");
        inview.on('inview', function (event, isInView, visiblePartX, visiblePartY) {
            if (isInView) {
                if (UserActionFacebookLikeService.alreadyRead[$(target).data('messageid')] === undefined) {
                    UserActionFacebookLikeService.alreadyRead[$(target).data('messageid')] = 1;
                    $('.engagementFb_pc', form).remove();
                    $('.engagementFb_sp', form).remove();
                    $('.cmd_execute_like_skip_action', form).remove();
                    $('#dead_line').css('display', 'block');
                }
            }
        });
    });
});
