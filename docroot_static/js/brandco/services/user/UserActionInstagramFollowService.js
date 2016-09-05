if(typeof(UserActionInstagramFollowService) === 'undefined') {
    var UserActionInstagramFollowService = (function () {
        var alreadyRead = [];

        return{
            alreadyRead: alreadyRead,
            executeAction: function (target) {
                // Remove the event to prevent duplicate submission.
                $(".btn_execute_instagram_follow_action").off("click");

                var form = $(target).parents().filter(".executeInstagramFollowActionForm");
                var section = $(target).parents().filter(".message_engagement");
                var csrf_token = document.getElementsByName("csrf_token")[0].value;
                var url = form.attr("action");
                var cp_action_id = $('input[name=cp_action_id]', form).val();
                var cp_user_id = $('input[name=cp_user_id]', form).val();

                var param = {

                    data: {
                        csrf_token: csrf_token,
                        cp_action_id: cp_action_id,
                        cp_user_id: cp_user_id,
                    },
                    url: url,

                    beforeSend: function () {
                        Brandco.helper.showLoading(section);
                    },

                    success: function (json) {
                        try {
                            if (json.result === "ok") {
                                if (json.data.next_action === true) {
                                    var message = $(json.html);
                                    message.hide();
                                    section.after(message);

                                    Brandco.helper.facebookParsing(json.data.sns_action);

                                    $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                                        Brandco.unit.createAndJumpToAnchor();
                                        $(target).replaceWith('<span class="middle1">次へ</span>');
                                    });
                                } else {
                                    $(target).removeClass('cmd_execute_instagram_follow_action');
                                }
                            }
                        } finally {
                            // Add the event.
                            $(".btn_execute_instagram_follow_action").on("click", function (event) {
                                event.preventDefault();
                                UserActionInstagramFollowService.executeAction(this);
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

    if ( typeof window.instgrm !== 'undefined' ) {
        window.instgrm.Embeds.process();
    }

    $(".btn_execute_instagram_follow_action").off("click");
    $(".btn_execute_instagram_follow_action").on("click", function (event) {
        event.preventDefault();
        UserActionInstagramFollowService.executeAction(this);
        return false;
    });

    if ($(".cmd_execute_instagram_follow_action")[0]) {
        UserActionInstagramFollowService.executeAction($(".cmd_execute_instagram_follow_action"));
    }
});
