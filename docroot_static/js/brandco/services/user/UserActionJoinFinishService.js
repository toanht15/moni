if(typeof(UserActionJoinFinishService) === 'undefined') {
    var UserActionJoinFinishService = (function () {

        return{
            alreadyRead: [],
            executeAction: function (target) {

                var form = $(target).parents().filter(".executeJoinFinishActionForm");
                var section = $(target).parents().filter(".jsMessage");
                var csrf_token = document.getElementsByName("csrf_token")[0].value;
                var url = form.attr("action");
                var last_action_flg = $(target).data('last_action_flg');
                var cp_action_id = $('input[name=cp_action_id]', form).val();
                var cp_user_id = $('input[name=cp_user_id]', form).val();

                var param = {
                    data: {
                        csrf_token: csrf_token,
                        cp_action_id: cp_action_id,
                        cp_user_id: cp_user_id
                    },

                    url: url,

                    success: function (json) {
                        if (json.result === "ok") {
                            if (json.data.next_action === true) {
                                var message = $(json.html);
                                message.hide();
                                section.after(message);

                                Brandco.helper.facebookParsing(json.data.sns_action);

                                $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                                    Brandco.unit.createAndJumpToAnchor(true);
                                });

                                $(target).removeClass('cmd_execute_join_finish_action');
                            } else {
                                $(target).removeClass('cmd_execute_join_finish_action');
                            }
                        } else {
                            alert("エラーが発生しました");
                        }
                    }
                };

                if (parseInt(last_action_flg) != 1) {
                    param.beforeSend = function() {
                        Brandco.helper.showLoading(section);
                    };
                    param.complete = function() {
                        Brandco.helper.hideLoading();
                    };
                }

                Brandco.api.callAjaxWithParam(param, false, false);
            }
        };
    })();
}

$(document).ready(function () {
    $(".cmd_execute_join_finish_action").each(function(){
        target = this;
        if ($(target).hasClass('cmd_execute_join_finish_action')) {
            if (UserActionJoinFinishService.alreadyRead[$(target).data('messageid')] === undefined) {
                UserActionJoinFinishService.alreadyRead[$(target).data('messageid')] = 1;
                UserActionJoinFinishService.executeAction(target);
            }
        }
    });
});
