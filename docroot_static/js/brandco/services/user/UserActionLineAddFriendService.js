if(typeof(UserActionLineAddFriendService) === 'undefined') {

    var UserActionLineAddFriendService = (function () {
        return{
            executeAction: function (target, add_friend_status) {
                // Remove the event to prevent duplicate submission.
                $(".jsBntExecuteLineAddFriendAction").off("click");
                $(".jsLineAddFriend").off("click");

                var form = $(target).parents().filter(".jsExecuteLineAddFriendActionForm");
                var section = $(target).parents().filter(".message_engagement");
                var csrf_token = document.getElementsByName("csrf_token")[0].value;
                var url = form.attr("action");

                var cp_action_id = $('input[name=cp_action_id]', form).val();
                var cp_user_id = $('input[name=cp_user_id]', form).val();
                var cp_line_add_friend_action_id = '';
                var add_friend_url = section.find(".jsLineAddFriend").data('target_url');

                if(add_friend_status === 1) {
                    cp_line_add_friend_action_id = $('input[name=cp_line_add_friend_action_id]', form).val()
                }

                var data = {
                    csrf_token: csrf_token,
                    cp_action_id: cp_action_id,
                    cp_user_id: cp_user_id,
                    cp_line_add_friend_action_id: cp_line_add_friend_action_id
                };

                var param = {
                    data: data,
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

                                    $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                                        Brandco.unit.createAndJumpToAnchor();
                                    });
                                }

                                section.find(".jsSkipExecuteLineAddFriendAction").remove();
                                section.find(".jsLineAddFriend").attr('href', add_friend_url);
                                section.find(".jsLineAddFriend").attr('target', '_blank');
                                section.find(".jsLineAddFriend").removeClass('jsLineAddFriend');

                            } else {
                                alert('操作が失敗しました！');
                            }
                        } finally {

                            $(".jsLineAddFriend").on("click", function (event) {
                                event.preventDefault();
                                UserActionLineAddFriendService.executeAction(this, 1);
                            });

                            $(".jsSkipExecuteLineAddFriendAction").on("click", function (event) {
                                event.preventDefault();
                                UserActionLineAddFriendService.executeAction(this, 0);
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

    $(".jsSkipExecuteLineAddFriendAction").off("click");
    $(".jsSkipExecuteLineAddFriendAction").on("click", function (event) {
        event.preventDefault();
        UserActionLineAddFriendService.executeAction(this, 0);
    });

    $(".jsLineAddFriend").off("click");
    $(".jsLineAddFriend").on("click", function (event) {
        event.preventDefault();
        UserActionLineAddFriendService.executeAction(this, 1);
        var add_friend_url = $(this).data('target_url');
        window.open(add_friend_url,'_blank');
    });
});