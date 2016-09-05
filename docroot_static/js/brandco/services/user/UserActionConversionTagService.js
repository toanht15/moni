if (typeof(UserActionConversionTagService) === 'undefined') {
    var UserActionConversionTagService = (function () {
        return {
            alreadyRead: [],
            executeActionConversionTag: function (target) {

                var form = $(target).parents().filter('.executeConversionTagActionForm');
                var url = $(form).attr('action');
                var section = $(form).parents().filter(".jsMessage");
                var cpActionId = $('input[name=cp_action_id]', form).val();
                var cpUserId = $('input[name=cp_user_id]', form).val();
                var csrfToken = $('input[name=csrf_token]', form).val();

                var param = {
                    data: {
                        csrf_token: csrfToken,
                        cp_action_id: cpActionId,
                        cp_user_id: cpUserId
                    },

                    url: url,

                    beforeSend: function () {
                        Brandco.helper.showLoading(section);
                    },

                    success: function (json) {
                        if (json.result === 'ok') {
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
                            alert('エラーが発生しました。');
                        }
                    },

                    complete: function () {
                        Brandco.helper.hideLoading();
                    }
                };
                section.hide();
                Brandco.api.callAjaxWithParam(param, false, false);
            }
        };
    })();
}
$(document).ready(function () {
    $('.cmd_execute_conversion_tag_action').each(function(){
        var target = this;
        if (UserActionConversionTagService.alreadyRead[$(target).data('messageid')] === undefined) {
            UserActionConversionTagService.alreadyRead[$(target).data('messageid')] = 1;
            UserActionConversionTagService.executeActionConversionTag(target);
        }
    });
});