if(typeof(UserActionEngagementService) === 'undefined') {
    var UserActionEngagementService = (function () {
        var alreadyRead = [];

        return {
            alreadyRead: alreadyRead,

            executeEngagementAction: function (target, isAutoLoad) {
                var form = $(target).parents().filter(".executeEngagementActionForm");
                var section = $(target).parents().filter(".jsMessage");
                var csrf_token = document.getElementsByName("csrf_token")[0].value;
                var url = form.attr("action");
                var cp_action_id = $('input[name=cp_action_id]', form).val();
                var cp_user_id = $('input[name=cp_user_id]', form).val();

                var param = {
                    data: {
                        csrf_token: csrf_token,
                        cp_action_id: cp_action_id,
                        cp_user_id: cp_user_id
                    },
                    url: url,
                    beforeSend: function () {
                        Brandco.helper.showLoading(section);
                    },
                    success: function (json) {
                        if (json.result === "ok") {
                            if (json.data.next_action === true) {
                                $('.cmd_execute_engagement_action').replaceWith('<span class="small1">' + $('.cmd_execute_engagement_action').html() + '</span>');
                                var message = $(json.html);
                                message.hide();
                                section.after(message);

                                Brandco.helper.facebookParsing(json.data.sns_action);

                                $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                                    Brandco.unit.createAndJumpToAnchor(isAutoLoad);
                                });
                            } else {
                                $('.cmd_execute_engagement_action').replaceWith('<span class="small1">' + $('.cmd_execute_engagement_action').html() + '</span>');
                            }
                        } else {
                            alert("エラーが発生しました");
                        }
                    },
                    complete: function () {
                        Brandco.helper.hideLoading();
                    }
                };
                Brandco.api.callAjaxWithParam(param, false, false);
            },
            executeFBLikeAction: function (target, action_url, status) {
                var form = $(target).parents().filter('.executeEngagementActionForm');

                var csrf_token = document.getElementsByName('csrf_token')[0].value;
                var cp_action_id = $('input[name=cp_action_id]', form).val();
                var cp_user_id = $('input[name=cp_user_id]', form).val();
                var brand_social_account_id = $('input[name=brand_social_account_id]', form).val();

                var param = {
                    data: {
                        csrf_token: csrf_token,
                        cp_action_id: cp_action_id,
                        cp_user_id: cp_user_id,
                        brand_social_account_id: brand_social_account_id,
                        status: status
                    },
                    url: action_url,
                    success: function(json) {
                        if (json.result !== "ok") {
                            /* すでにいいねした時アラート表示
                             alert("エラーが発生しました");
                            */
                        }
                    }
                };
                Brandco.api.callAjaxWithParam(param, false, false);
            },
            executeSNSGetDataAction: function (target) {
                var form = $(target).parents().filter('.executeEngagementActionForm');

                var csrf_token = document.getElementsByName('csrf_token')[0].value;
                var brand_social_account_id = $('input[name=brand_social_account_id]', form).val();
                var action_url = document.getElementsByName('sns_get_data_action_url')[0].value;

                var param = {
                    data: {
                        csrf_token: csrf_token,
                        brand_social_account_id: brand_social_account_id
                    },
                    url: action_url,
                    success: function (json_data) {
                        var isAutoLoad = true;
                        UserActionEngagementService.executeEngagementAction(target, isAutoLoad);
                        FB.api(
                            '/' + json_data.data.user_info.social_media_account_id + '/likes/' + json_data.data.brand_social_account.social_media_account_id,
                            {
                                access_token: json_data.data.user_info.social_media_access_token
                            },
                            function (response) {
                                if (response && !response.error && response.data[0]) {
                                    var action_url = document.getElementsByName('engagement_log_action_url')[0].value;
                                    UserActionEngagementService.executeFBLikeAction(target, action_url, 2);
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
    $('.cmd_execute_engagement_action').off('click');
    $('.cmd_execute_engagement_action').on('click', function (event) {
        event.preventDefault();
        UserActionEngagementService.executeEngagementAction(this);
    });

    $(".cmd_execute_engagement_action").each(function(){
        target = this;
        inview = $(this).parents().filter(".inview");
        btnSet = $(this).parents().filter(".btnSet");

        inview.on('inview', function (event, isInView, visiblePartX, visiblePartY) {
            if (isInView && $(target).hasClass('cmd_execute_engagement_action')) {
                // Facebookしか自動ロード実行しません （FacebookID = 2)）
                if (btnSet.data('socialappid') == '2' && UserActionEngagementService.alreadyRead[$(target).data('messageid')] === undefined) {
                    UserActionEngagementService.alreadyRead[$(target).data('messageid')] = 1;

                    // 既にいいねしたかどうかをチェック
                    UserActionEngagementService.executeSNSGetDataAction(target);
                }
            }
        });
    });
});
