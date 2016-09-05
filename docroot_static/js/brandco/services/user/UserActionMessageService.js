if(typeof(UserActionMessageService) === 'undefined') {
    var UserActionMessageService = (function () {
        var alreadyRead = [];

        return {
            alreadyRead: alreadyRead,
            executeAction: function (target, isAutoload) {
                // Remove the event to prevent duplicate submission.
                $(".btn_execute_message_action").click(function() { return false; });

                var form = $(target).parents().filter(".executeMessageActionForm");
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
                        try {
                            if (json.result === "ok") {
                                $(target).attr("message_completed", "yes");
                                if (json.data.next_action === true) {
                                    var message = $(json.html);
                                    message.hide();
                                    section.after(message);

                                    Brandco.helper.facebookParsing(json.data.sns_action);

                                    $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                                        Brandco.unit.createAndJumpToAnchor(isAutoload);
                                    });
                                    if ($(target).hasClass('btn_execute_message_action')) {
                                        $(target).removeClass('btn_execute_message_action');
                                        $(target).replaceWith('<span class="large1">' + $(target).html() + '</span>');
                                    } else {
                                        $(target).removeClass('cmd_execute_message_action');
                                    }
                                } else {
                                    $(target).removeClass('cmd_execute_message_action');
                                }
                            } else {
                                alert("エラーが発生しました");
                            }
                        } finally {
                            // Add the event.
                            $(".btn_execute_message_action").click(function() {
                                target = this;
                                if ($(target).hasClass('btn_execute_message_action')) {
                                    if(UserActionMessageService.alreadyRead[$(target).data('messageid')] === undefined){
                                        UserActionMessageService.alreadyRead[$(target).data('messageid')] = 1;
                                        UserActionMessageService.executeAction(target);
                                    }
                                }

                                return false;
                            });
                        }
                    },

                    complete: function () {
                        Brandco.helper.hideLoading();
                    }
                };

                Brandco.api.callAjaxWithParam(param, false, false);
            }
        };
    })();
}

$(document).ready(function () {
    $(".cmd_execute_message_action").each(function(){
        target = this;
        if ($(target).hasClass('cmd_execute_message_action')) {
            if(UserActionMessageService.alreadyRead[$(target).data('messageid')] === undefined){
                UserActionMessageService.alreadyRead[$(target).data('messageid')] = 1;
                UserActionMessageService.executeAction(target, true);
            }
        }
    });

    $(".btn_execute_message_action").click(function() {
        target = this;
        if ($(target).hasClass('btn_execute_message_action')) {
            if(UserActionMessageService.alreadyRead[$(target).data('messageid')] === undefined){
                UserActionMessageService.alreadyRead[$(target).data('messageid')] = 1;
                UserActionMessageService.executeAction(target);
            }
        }

        return false;
    });
});
