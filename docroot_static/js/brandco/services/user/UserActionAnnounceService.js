if(typeof(UserActionAnnounceService) === 'undefined') {
    var UserActionAnnounceService = (function () {
        var alreadyRead = [];

        return{
            alreadyRead: alreadyRead,
            executeAction: function (target) {

                var form = $(target).parents().filter(".executeAnnounceActionForm");
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
                                $(target).replaceWith('<span class="middle1">' + $(target).html() + '</span>');
                                var message = $(json.html);
                                message.hide();
                                section.after(message);

                                Brandco.helper.facebookParsing(json.data.sns_action);

                                $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                                    Brandco.unit.createAndJumpToAnchor(true);
                                });
                            } else {
                                $(target).replaceWith('<span class="middle1">' + $(target).html() + '</span>');
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
            }
        };
    })();
}

$(document).ready(function () {

    $(".cmd_execute_announce_action").each(function(){
        target = this;
        if ($(target).hasClass('cmd_execute_announce_action')) {
            if(UserActionAnnounceService.alreadyRead[$(target).data('messageid')] === undefined){
                UserActionAnnounceService.alreadyRead[$(target).data('messageid')] = 1;
                UserActionAnnounceService.executeAction(target);
            }
        }
    });
});
