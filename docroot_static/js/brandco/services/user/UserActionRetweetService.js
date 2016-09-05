if (typeof(UserActionRetweetService) === 'undefined') {
    var UserActionRetweetService = {
        alreadyClicked: false,
        initActionMessage: function() {
            $('.cmd_execute_retweet_action').each(function(){
                var form = $(this).parents().filter('.executeRetweetActionForm');
                if (form.data('retweet-failure')) {
                    form.data('retweet-failure', 0);
                    UserActionRetweetService.preExecuteActionRetweet(this, 1);
                }
            });
        },

        preExecuteActionRetweet: function (target, post_retweet) {
            if (!UserActionRetweetService.alreadyClicked) {
                UserActionRetweetService.alreadyClicked = true;
            } else {
                return false;
            }

            // Remove the event to prevent duplicate submission.
            $(document).off('click', '.cmd_execute_retweet_action');

            var form            = $(target).parents().filter('.executeRetweetActionForm');
            var cpActionId      = $('input[name=cp_action_id]', form).val();
            var cpUserId        = $('input[name=cp_user_id]', form).val();
            var csrfToken       = $('input[name=csrf_token]', form).val();
            var section         = $(form).parents().filter(".jsMessage");
            var url             = $(form).attr('action');

            var param = {
                data: {
                    csrf_token      : csrfToken,
                    cp_action_id    : cpActionId,
                    cp_user_id      : cpUserId,
                    post_retweet    : post_retweet
                },

                url: url,

                beforeSend: function() {
                    Brandco.helper.showLoading(section);
                },

                success: function(json) {
                     try {
                         if (json.result === 'ok') {
                             if (json.data.post_retweet) {
                                 if (json.data.post_retweet === 'api_error') {
                                     alert('リツイートが失敗しました。');
                                 } else {
                                     UserActionRetweetService.executeActionRetweet(target, 0);
                                 }
                             } else {
                                 window.location.href = form.data('redirect-url');
                             }
                         } else {
                             alert('エラーが発生しました。');
                         }
                     } finally {
                         // Add the event.
                         $(document).on('click', '.cmd_execute_retweet_action', function(event) {
                             UserActionRetweetService.preExecuteActionRetweet(this);
                         });
                     }
                },

                complete: function() {
                    Brandco.helper.hideLoading();
                }
            };
            Brandco.api.callAjaxWithParam(param, false, false);
        },

        executeActionRetweet: function (target, skipped) {

            if (UserActionRetweetService.alreadyClicked && skipped) return false;
            UserActionRetweetService.alreadyClicked = true;

            var form = $(target).parents().filter('.executeRetweetActionForm');
            form.attr('action', form.data('execute-url'));
            var url             = $(form).attr('action');
            var section         = $(form).parents().filter(".jsMessage");
            var cpActionId      = $('input[name=cp_action_id]', form).val();
            var cpUserId        = $('input[name=cp_user_id]', form).val();
            var csrfToken       = $('input[name=csrf_token]', form).val();

            var param = {
                data: {
                    csrf_token      : csrfToken,
                    cp_action_id    : cpActionId,
                    cp_user_id      : cpUserId,
                    skipped         : skipped
                },

                url: url,

                beforeSend: function() {
                    Brandco.helper.showLoading(section);
                },

                success: function(json) {
                    if (json.result === 'ok') {
                        UserActionRetweetService.displayAfterActionDone(section.attr('id'));
                        if (json.data.next_action === true) {
                            var message = $(json.html);
                            message.hide();
                            section.after(message);

                            Brandco.helper.facebookParsing(json.data.sns_action);
                            $('#message_' + json.data.message_id).stop(true, false).show(200, function() {
                                Brandco.unit.createAndJumpToAnchor();
                            });
                        }
                        UserActionRetweetService.alreadyClicked = false;
                    } else {
                        if (json.errors.retweet_error) {
                            alert(json.errors.retweet_error);
                        } else {
                            alert('エラーが発生しました。')
                        }
                    }
                },

                complete: function() {
                    Brandco.helper.hideLoading();
                }
            };
            Brandco.api.callAjaxWithParam(param, false, false);
        },

        displayAfterActionDone: function(section_id) {
            $('#' + section_id + ' .jsRetweetBtnElement').html('<span class="middle1">リツイート</span>');
            $('#' + section_id + ' .executeRetweetActionForm .messageSkip a').css('pointer-events', 'none');
            $('#' + section_id + ' .executeRetweetActionForm .messageSkip a').hide();
        }
    }
}
$(document).ready(function () {
    UserActionRetweetService.initActionMessage();

    $(document).off('click', '.cmd_execute_retweet_action');
    $(document).on('click', '.cmd_execute_retweet_action', function(event) {
        UserActionRetweetService.preExecuteActionRetweet(this);
    });

    $(document).off('click', '.executeRetweetActionForm .messageSkip a');
    $(document).on('click', '.executeRetweetActionForm .messageSkip a', function() {
        UserActionRetweetService.executeActionRetweet(this, 1);

    });
});