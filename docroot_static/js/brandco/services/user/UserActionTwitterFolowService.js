if (typeof(UserActionTwitterFollowService) === 'undefined') {
    var UserActionTwitterFollowService = (function() {
        var alreadyRead = [];

        return {
            alreadyRead: alreadyRead,

            executeFollowAction: function(target, isAutoLoad) {
                // Remove the event to prevent duplicate submission.
                $('.cmd_execute_follow_action').off('click');

                var form = $(target).parents().filter(".executeFollowActionForm");
                var section = $(target).parents().filter(".jsMessage");
                var csrf_token = document.getElementsByName("csrf_token")[0].value;
                var url = form.attr("action");
                var cp_action_id = $('input[name=cp_action_id]', form).val();
                var cp_user_id = $('input[name=cp_user_id]', form).val();
                var concrete_action_id = $('input[name=concrete_action_id]', form).val();
                var param = {
                    data: {
                        csrf_token: csrf_token,
                        cp_action_id: cp_action_id,
                        cp_user_id: cp_user_id,
                        concrete_action_id: concrete_action_id,
                        status: '1' // skip: '0', finish: '0', connect: '0'
                    },
                    url: url,
                    beforeSend: function() {
                        Brandco.helper.showLoading(section);
                    },
                    success: function(json) {
                        try {
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
                                $('.jsExecuteAction', form).replaceWith('<span class="large1">' + $('.jsExecuteAction', form).html() + '</span>');
                                $('.cmd_execute_follow_skip_action', form).remove();
                            } else {
                                alert("エラーが発生しました");
                            }
                        } finally {
                            // Add the event.
                            $('.cmd_execute_follow_action').on('click', function(event) {
                                event.preventDefault();
                                UserActionTwitterFollowService.executeFollowAction(this);
                            });
                        }
                    },
                    complete: function() {
                        Brandco.helper.hideLoading();
                    }
                };
                Brandco.api.callAjaxWithParam(param, false, false);
            },
            executeFollowSkipAction: function(target, isAutoLoad) {
                // Remove the event to prevent duplicate submission.
                $('.cmd_execute_follow_skip_action').off('click');

                var form = $(target).parents().filter(".executeFollowActionForm");
                var section = $(target).parents().filter(".jsMessage");
                var csrf_token = document.getElementsByName("csrf_token")[0].value;
                var url = form.attr("action");
                var cp_action_id = $('input[name=cp_action_id]', form).val();
                var cp_user_id = $('input[name=cp_user_id]', form).val();
                var concrete_action_id = $('input[name=concrete_action_id]', form).val();
                var param = {
                    data: {
                        csrf_token: csrf_token,
                        cp_action_id: cp_action_id,
                        cp_user_id: cp_user_id,
                        concrete_action_id: concrete_action_id,
                        status: '2' // skip: '1', finish: '0', connect: '0'
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
                                    });
                                }
                                $('.jsExecuteAction', form).replaceWith('<span class="large1">' + $('.jsExecuteAction', form).html() + '</span>');
                                $('.cmd_execute_follow_skip_action', form).remove();
                                $('.cmd_auto_execute_skip_twitter_follow_action').remove();
                            } else {
                                alert("エラーが発生しました");
                            }
                        } finally {
                            // Add the event.
                            $('.cmd_execute_follow_skip_action').on('click', function(event) {
                                event.preventDefault();
                                UserActionTwitterFollowService.executeFollowSkipAction(this);
                            });
                        }
                    },
                    complete: function () {
                        Brandco.helper.hideLoading();
                    }
                };
                Brandco.api.callAjaxWithParam(param, false, false);
            },
            executeFollowAlreadyAction: function(target, isAutoLoad) {
                var form = $(target).parents().filter(".executeFollowActionForm");
                var section = $(target).parents().filter(".jsMessage");
                var csrf_token = document.getElementsByName("csrf_token")[0].value;
                var url = form.attr("action");
                var cp_action_id = $('input[name=cp_action_id]', form).val();
                var cp_user_id = $('input[name=cp_user_id]', form).val();
                var concrete_action_id = $('input[name=concrete_action_id]', form).val();
                var param = {
                    data: {
                        csrf_token: csrf_token,
                        cp_action_id: cp_action_id,
                        cp_user_id: cp_user_id,
                        concrete_action_id: concrete_action_id,
                        status: '3' // skip: '0', finish: '1', connect: '0'
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
                            $('.cmd_execute_follow_skip_action', form).remove();
                        }
                    },
                    complete: function () {
                        Brandco.helper.hideLoading();
                    }
                };
                Brandco.api.callAjaxWithParam(param, false, false);
            },
            executeConnectingAccount: function(target) {
                // Remove the event to prevent duplicate submission.
                $('.cmd_execute_connect_account').off('click');

                var form = $(target).parents().filter(".executeFollowActionForm");
                var section = $(target).parents().filter(".jsMessage");
                var csrf_token = document.getElementsByName("csrf_token")[0].value;
                var cp_action_id = $('input[name=cp_action_id]', form).val();
                var cp_user_id = $('input[name=cp_user_id]', form).val();
                var concrete_action_id = $('input[name=concrete_action_id]', form).val();
                var is_update_token = $(target).data('need_update_token');
                var params = {
                    data: {
                        csrf_token: csrf_token,
                        cp_action_id: cp_action_id,
                        cp_user_id: cp_user_id,
                        concrete_action_id: concrete_action_id,
                        is_update_token: is_update_token
                    },
                    url: $(target).data('pre_execute_url'),
                    beforeSend: function () {
                        Brandco.helper.showLoading(section);
                    },
                    success: function(json) {
                        // Add the event.
                        $('.cmd_execute_connect_account').on('click', function(event) {
                            event.preventDefault();
                            UserActionTwitterFollowService.executeConnectingAccount(this);
                        });

                        if (json.result === 'ok') {
                            window.location.href = $(target).data('redirect_url');
                        }
                    },
                    complete: function () {
                        Brandco.helper.hideLoading();
                    }
                };
                Brandco.api.callAjaxWithParam(params, false, false);
            }
        };
    })();
}

$(document).ready(function() {
    $('.cmd_execute_follow_action').off('click');
    $('.cmd_execute_follow_action').on('click', function(event) {
        event.preventDefault();
        UserActionTwitterFollowService.executeFollowAction(this);
    });

    $('.cmd_execute_connect_account').off('click');
    $('.cmd_execute_connect_account').on('click', function(event) {
        event.preventDefault();
        UserActionTwitterFollowService.executeConnectingAccount(this);
    });

    $('.cmd_execute_follow_skip_action').off('click');
    $('.cmd_execute_follow_skip_action').on('click', function(event) {
        event.preventDefault();
        UserActionTwitterFollowService.executeFollowSkipAction(this);
    });

    $(".cmd_execute_follow_already_action").each(function() {
        var target = this;
        var inview = $(this).parents().filter(".inview");
        inview.on('inview', function(event, isInView, visiblePartX, visiblePartY) {
            if (isInView) {
                if (UserActionTwitterFollowService.alreadyRead[$(target).data('messageid')] === undefined) {
                    UserActionTwitterFollowService.alreadyRead[$(target).data('messageid')] = 1;
                    UserActionTwitterFollowService.executeFollowAlreadyAction(target);
                }
            }
        });
    });

    $(".cmd_execute_follow_close_action_exec").each(function() {
        var target = this;
        var inview = $(this).parents().filter(".inview");
        var form = $(target).parents().filter(".executeFollowActionForm");
        inview.on('inview', function(event, isInView, visiblePartX, visiblePartY) {
            if (isInView) {
                if (UserActionTwitterFollowService.alreadyRead[$(target).data('messageid')] === undefined) {
                    UserActionTwitterFollowService.alreadyRead[$(target).data('messageid')] = 1;
                    $('.jsExecuteAction', form).replaceWith(
                            '<span class="large1">' +
                            $('.jsExecuteAction', form).html() +
                            '</span>'
                            );
                    $('.cmd_execute_follow_skip_action', form).remove();
                }
            }
        });
    });

    $(".cmd_execute_follow_close_action_already").each(function() {
        var target = this;
        var inview = $(this).parents().filter(".inview");
        var form = $(target).parents().filter(".executeFollowActionForm");
        inview.on('inview', function(event, isInView, visiblePartX, visiblePartY) {
            if (isInView) {
                if (UserActionTwitterFollowService.alreadyRead[$(target).data('messageid')] === undefined) {
                    UserActionTwitterFollowService.alreadyRead[$(target).data('messageid')] = 1;
                    $('.cmd_execute_follow_skip_action', form).remove();
                }
            }
        });
    });

    $('.cmd_execute_follow_close_action_connecting').each(function() {
        var target = this;
        var inview = $(this).parents().filter('.inview');
        var form = $(target).parents().filter('.executeFollowActionForm');
        var action_confirm = $('.cmd_execute_follow_action', form);

        inview.on('inview', function(event, isInview, visiblePartX, visiblePartY) {
            if (isInview) {
                if (UserActionTwitterFollowService.alreadyRead[$(target).data('messageId')] === undefined) {
                    UserActionTwitterFollowService.alreadyRead[$(target).data('messageId')] = 1;
                    UserActionTwitterFollowService.executeFollowAction(action_confirm);
                }
            }
        })
    })

    $(".cmd_execute_dead_line").each(function() {
        var target = this;
        var inview = $(this).parents().filter(".inview");
        var form = $(target).parents().filter(".executeFollowActionForm");
        inview.on('inview', function(event, isInView, visiblePartX, visiblePartY) {
            if (isInView) {
                if (UserActionTwitterFollowService.alreadyRead[$(target).data('messageid')] === undefined) {
                    UserActionTwitterFollowService.alreadyRead[$(target).data('messageid')] = 1;
                    $('.cmd_execute_follow_action').replaceWith(
                            '<span class="large1">' +
                            $('.cmd_execute_follow_action').html() +
                            '</span>'
                            );
                    $('.cmd_execute_follow_skip_action', form).remove();
                }
            }
        });
    });

    $(".cmd_auto_execute_skip_twitter_follow_action").each(function () {
        var target = this;
        var inview = $(this).parents().filter(".inview");
        inview.on('inview', function(event, isInView, visiblePartX, visiblePartY) {
            if (isInView) {
                if (UserActionTwitterFollowService.alreadyRead[$(target).data('messageid')] === undefined) {
                    UserActionTwitterFollowService.alreadyRead[$(target).data('messageid')] = 1;
                    UserActionTwitterFollowService.executeFollowSkipAction(target);
                }
            }
        });
    });
});
