if(typeof(UserActionShareService) === 'undefined') {
    var UserActionShareService = (function () {
        var alreadyRead = [];

        return {
            alreadyRead: alreadyRead,
            alreadyClicked: false,

            executeShareAction: function (target, isAutoLoad) {
                if (!this.alreadyClicked) {
                    this.alreadyClicked = true;
                } else {
                    return false;
                }

                // Remove the event to prevent duplicate submission.
                $('.cmd_execute_share_action').off('click');

                var form = $(target).parents().filter(".executeShareActionForm");
                var section = $(target).parents().filter(".jsMessage");
                var csrf_token = document.getElementsByName("csrf_token")[0].value;
                var url = form.attr("action");
                var cp_action_id = $('input[name=cp_action_id]', form).val();
                var cp_user_id = $('input[name=cp_user_id]', form).val();
                var shared_flg = $('input[name=shared_flg]', form).val();
                var share_message = $('textarea[name=share_message]', form).val();
                var execute_share_action = $(target);
                var execute_skip_action = $(target).parents().find('a.cmd_execute_share_skip_action');

                var param = {
                    data: {
                        csrf_token: csrf_token,
                        cp_action_id: cp_action_id,
                        cp_user_id: cp_user_id,
                        message: share_message,
                        skip_flg: '0',
                        unread_flg: '0'
                    },
                    url: url,
                    beforeSend: function () {
                        Brandco.helper.showLoading(section);
                    },
                    success: function (json) {
                        try {
                            if (json.result === "ok") {
                                if (json.data.next_action === true) {
                                    if (shared_flg == '0') {
                                        var message = $(json.html);
                                        message.hide();
                                        section.after(message);

                                        Brandco.helper.facebookParsing(json.data.sns_action);

                                        $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                                            Brandco.unit.createAndJumpToAnchor();
                                        });
                                    }
                                }
                                execute_share_action.replaceWith('<span class="large1">' + execute_share_action.html() + '</span>');
                                execute_skip_action.hide();
                                $('textarea[name=share_message]', form).attr('readonly', 'readonly');

                            } else {
                                alert("エラーが発生しました");
                            }
                        } finally {
                            // Add the event.
                            $('.cmd_execute_share_action').on('click', function (event) {
                                event.preventDefault();
                                UserActionShareService.executeShareAction(this);
                            });
                        }
                    },
                    complete: function () {
                        Brandco.helper.hideLoading();
                    }
                };
                Brandco.api.callAjaxWithParam(param, false, false);
            },
            executeShareSkipAction: function (target, isAutoLoad) {
                if (!this.alreadyClicked) {
                    this.alreadyClicked = true;
                } else {
                    return false;
                }

                // Remove the event to prevent duplicate submission.
                $('.cmd_execute_share_skip_action').off('click');

                var form = $(target).parents().filter(".executeShareActionForm");
                var section = $(target).parents().filter(".jsMessage");
                var csrf_token = document.getElementsByName("csrf_token")[0].value;
                var url = form.attr("action");
                var cp_action_id = $('input[name=cp_action_id]', form).val();
                var cp_user_id = $('input[name=cp_user_id]', form).val();
                var shared_flg = $('input[name=shared_flg]', form).val();
                var execute_share_action = $(target).parents().find('a.cmd_execute_share_action');
                var execute_skip_action = $(target);

                var param = {
                    data: {
                        csrf_token: csrf_token,
                        cp_action_id: cp_action_id,
                        cp_user_id: cp_user_id,
                        skip_flg: '1',
                        unread_flg:'0'
                    },
                    url: url,
                    beforeSend: function () {
                        Brandco.helper.showLoading(section);
                    },
                    success: function (json) {
                        try {
                            if (json.result === "ok") {
                                if (json.data.next_action === true) {
                                    if (shared_flg == '0') {
                                        var message = $(json.html);
                                        message.hide();
                                        section.after(message);

                                        Brandco.helper.facebookParsing(json.data.sns_action);

                                        $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                                            Brandco.unit.createAndJumpToAnchor();
                                        });
                                    }
                                }
                                execute_share_action.replaceWith('<span class="large1">' + execute_share_action.html() + '</span>');
                                execute_skip_action.hide();
                                $('textarea[name=share_message]', form).attr('readonly', 'readonly');

                            } else {
                                alert("エラーが発生しました");
                            }
                        } finally {
                            // Add the event.
                            $('.cmd_execute_share_skip_action').on('click', function (event) {
                                event.preventDefault();
                                UserActionShareService.executeShareSkipAction(this);
                            });
                        }
                    },
                    complete: function () {
                        Brandco.helper.hideLoading();
                    }
                };
                Brandco.api.callAjaxWithParam(param, false, false);
            },
            executeShareUnreadAction: function (target, isAutoLoad) {
                var form = $(target).filter(".executeShareUnreadForm");
                var section = $(target).parents().filter(".jsMessage");
                var csrf_token = document.getElementsByName("csrf_token")[0].value;
                var url = form.attr("action");
                var cp_action_id = $('input[name=cp_action_id]', form).val();
                var cp_user_id = $('input[name=cp_user_id]', form).val();
                var shared_flg = $('input[name=shared_flg]', form).val();

                var param = {
                    data: {
                        csrf_token: csrf_token,
                        cp_action_id: cp_action_id,
                        cp_user_id: cp_user_id,
                        skip_flg: '0',
                        unread_flg:'1'
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
                        } else {
                            alert("エラーが発生しました");
                        }
                    },
                    complete: function () {
                        Brandco.helper.hideLoading();
                        section.hide();
                    }
                };
                Brandco.api.callAjaxWithParam(param, false, false);
            }
        };
    })();
}

$(document).ready(function () {

    $('.cmd_execute_share_action').off('click');
    $('.cmd_execute_share_action').on('click', function (event) {
        event.preventDefault();
        UserActionShareService.executeShareAction(this);
    });

    $('.cmd_execute_share_skip_action').off('click');
    $('.cmd_execute_share_skip_action').on('click', function (event) {
        event.preventDefault();
        UserActionShareService.executeShareSkipAction(this);
    });

    $(".executeShareUnreadForm").each(function(){
        target = this;
        inview = $(this).parents().filter(".inview");
        inview.on('inview', function(event, isInView, visiblePartX, visiblePartY) {
            if (isInView) {
                UserActionShareService.executeShareUnreadAction(target);
            }
        });
    });

    // tweet text count
    var textarea = $('.jsTweetText');
    var counter = $('.jsTweetCounter');
    textarea.on('change keyup', function () {
        var value = textarea.val();
        var count = twttr.txt.getTweetLength(value);

        counter.text(count).toggleClass('attention1', count > 140);
    }).change();

});