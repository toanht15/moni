var UserActionCodeAuthService = (function() {
    var alreadyRead = [];

    return{
        alreadyRead: alreadyRead,
        executeAction: function (target) {
            // Remove the event to prevent duplicate submission.
            $(document).off('click', '.cmd_execute_code_auth_action');

            var form = $(target).parents().filter(".executeCodeAuthActionForm");
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
                    // Add the event.
                    $(document).on('click', '.cmd_execute_code_auth_action', function(event) {
                        event.preventDefault();
                        UserActionCodeAuthService.executeAction(this);
                    });

                    if (json.result === "ok") {
                        if (json.data.next_action === true) {
                            $(target).replaceWith('<span class="middle1">'+$(target).html()+'</span>');
                            var message = $(json.html);
                            message.hide();
                            section.after(message);

                            Brandco.helper.facebookParsing(json.data.sns_action);

                            $('#message_' + json.data.message_id).stop(true, false).show(200, function(){
                                Brandco.unit.createAndJumpToAnchor();
                            });
                        } else {
                            $(target).replaceWith('<span class="middle1">'+$(target).html()+'</span>');
                        }
                        Brandco.unit.createAndJumpToAnchor();
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
        addCodeAuthUser: function(code_input) {
            var cur_form = $(code_input).parents().filter(".executeCodeAuthActionForm"),
                csrf_token = document.getElementsByName("csrf_token")[0].value,
                user_id = $('input[name=user_id]', cur_form).val(),
                cp_action_id = $('input[name=cp_action_id]', cur_form).val(),
                code_auth_code = $('input[name=code_auth_code]', cur_form).val(),
                code_auth_id = $('input[name=code_auth_id]', cur_form).val(),
                cp_user_id = $('input[name=cp_user_id]', cur_form).val(),
                error_msg = $(cur_form).find('.jsCodeAuthCodeInputError'),
                cur_code_list = $(cur_form).find('.jsMsgCodeAuthCodeList'),
                params = {
                    data: {
                        csrf_token: csrf_token,
                        user_id: user_id,
                        cp_action_id: cp_action_id,
                        code_auth_code: code_auth_code,
                        code_auth_id: code_auth_id,
                        cp_user_id: cp_user_id
                    },
                    url: 'messages/api_update_code_auth_users.json',
                    success: function(response) {
                        if (response.result == 'ok') {
                            $(error_msg).hide();
                            $(cur_code_list).html(response.html);
                        } else if (response.result == 'ng') {
                            if (response.errors) {
                                if (response.errors.code_auth_user_locking == 'true' && response.html) {
                                    $(cur_code_list).html(response.html);
                                } else {
                                    $(error_msg).html(response.errors.code_auth_code);
                                    $(error_msg).show();
                                }
                            } else {
                                alert('コード登録失敗しました');
                            }
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(params);
        }
    };
})();

$(document).ready(function() {
    $(document).off('click', '.jsCodeAuthCodeInput');
    $(document).on('click', '.jsCodeAuthCodeInput', function(event) {
        event.preventDefault();
        UserActionCodeAuthService.addCodeAuthUser(this);
    });

    $(document).off('click', '.cmd_execute_code_auth_action');
    $(document).on('click', '.cmd_execute_code_auth_action', function(event) {
        event.preventDefault();
        UserActionCodeAuthService.executeAction(this);
    });

    $(document).on('submit', '.executeCodeAuthActionForm', function(event) {
        return false;
    });
});