if(typeof(UserActionYoutubeChannelService) === 'undefined') {
    var UserActionYoutubeChannelService = (function () {
        var alreadyRead = [];

        return{
            alreadyRead: alreadyRead,
            executeAction: function (target, auto_follow) {
                var form = $(target).parents().filter(".executeYoutubeChannelActionForm");
                var section = $(target).parents().filter(".message_engagement");
                var csrf_token = document.getElementsByName("csrf_token")[0].value;
                var url = form.attr("action");
                var cp_action_id = $('input[name=cp_action_id]', form).val();
                var cp_user_id = $('input[name=cp_user_id]', form).val();
                var channel_id = $('input[name=channel_id]', form).val();

                var param = {

                    data: {
                        csrf_token: csrf_token,
                        cp_action_id: cp_action_id,
                        cp_user_id: cp_user_id,
                        auto_follow: auto_follow,
                        channel_id: channel_id
                    },
                    url: url,

                    beforeSend: function () {
                        Brandco.helper.showLoading(section);
                    },

                    success: function (json) {
                        if (json.result === "ok") {
                            if (json.data.next_action === true) {
                                var message = $(json.html);
                                message.hide();
                                section.after(message);

                                Brandco.helper.facebookParsing(json.data.sns_action);

                                $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                                    Brandco.unit.createAndJumpToAnchor();
                                });
                            }
                            $("#skip_execute_youtube_channel_action").hide();
                            if (auto_follow) {
                                $("#btnYtUnFollowed").replaceWith('<span class="btnYtFollow"><span>登録済み</span></span>');
                            }
                        } else {
                            $('#btnYtUnFollowed').after($('<br><span class="iconError1">失敗しました。再度お試し下さい。</span>'));
                        }
                    },

                    complete: function () {
                        Brandco.helper.hideLoading();
                    }
                };
                Brandco.api.callAjaxWithParam(param, true, true);
            }
        };
    })();
}

$(document).ready(function () {

    $("#skip_execute_youtube_channel_action").off("click");
    $("#skip_execute_youtube_channel_action").on("click", function (event) {
        event.preventDefault();
        UserActionYoutubeChannelService.executeAction(this, 0);
        return false;
    });

    if ($(".cmd_execute_youtube_channel_action")[0]) {
        UserActionYoutubeChannelService.executeAction($(".cmd_execute_youtube_channel_action"), 1);
    }
});
