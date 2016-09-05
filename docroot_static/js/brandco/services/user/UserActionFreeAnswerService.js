if(typeof(UserActionFreeAnswerService) === 'undefined') {
    var UserActionFreeAnswerService = (function () {
        return{

            executeFreeAnswerAction: function (target) {
                // Remove the event to prevent duplicate submission.
                $(".freeAnswerSubmit").off("click");

                var form = $(target).closest('form'),
                    section = $(target).parents().filter(".jsMessage"),
                    csrf_token = document.getElementsByName("csrf_token")[0].value,
                    url = form.attr("action"),
                    cp_action_id = $('input[name=cp_action_id]', form).val(),
                    cp_user_id = $('input[name=cp_user_id]', form).val(),
                    free_answer = $('textarea[name=free_answer]', form).val();

                var param = {

                    data: {
                        csrf_token: csrf_token,
                        cp_action_id: cp_action_id,
                        cp_user_id: cp_user_id,
                        free_answer: free_answer
                    },
                    url: url,

                    beforeSend: function () {
                        Brandco.helper.showLoading(section);
                    },

                    success: function (json) {
                        try {
                            if (json.result === "ok") {
                                Brandco.unit.disableForm($(target).closest('form'));
                                $('#free_answer_error').hide();
                                if (json.data.next_action === true) {
                                    $(target).replaceWith('<span class="large1">' + $(target).html() + '</span>');
                                    $('textarea[name=free_answer]', form).attr('disabled', 'disabled');
                                    var message = $(json.html);
                                    message.hide();
                                    section.after(message);

                                    Brandco.helper.facebookParsing(json.data.sns_action);

                                    $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                                        Brandco.unit.createAndJumpToAnchor();
                                    });
                                } else {
                                    $(target).replaceWith('<span class="large1">' + $(target).html() + '</span>');
                                }
                            } else {
                                $('#free_answer_error').html(json.errors.free_answer);
                                $('#free_answer_error').show();
                            }
                        } finally {
                            // Add the event.
                            $(".freeAnswerSubmit").on("click", function (event) {
                                event.preventDefault();
                                UserActionFreeAnswerService.executeFreeAnswerAction(this);
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
    $(".freeAnswerSubmit").off("click");
    $(".freeAnswerSubmit").on("click", function (event) {
        event.preventDefault();
        UserActionFreeAnswerService.executeFreeAnswerAction(this);
    });
});
