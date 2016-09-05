if (typeof(UserProfileService) === 'undefined') {
    var UserProfileService = function() {
        return {
            updateUserProfile: function(src) {
                var form = $(src).parents().filter('form');
                var section = $(src).parents().filter('.jsMessage');
                var url = form.attr('action');
                var csrf_token = $('input[name=csrf_token]', form).val();
                var name = $('input[name=name]', form).val();
                var mail_address = $('input[name=mail_address]', form).val();
                var cp_id = $('input[name=cp_id]', form).val();
                var cp_user_id = $('input[name=cp_user_id]', form).val();
                var cp_action_id = $('input[name=cp_action_id]', form).val();
                var optin = $('input[name=optin]:checked', form).val();
                var need_display_personal_form = $('input[name=need_display_personal_form]', form).val();

                var param = {
                    data: {
                        csrf_token: csrf_token,
                        name: name,
                        mail_address: mail_address,
                        optin: typeof(optin) !== 'undefined' ? optin : 0,
                        cp_id: cp_id,
                        cp_user_id: cp_user_id,
                        cp_action_id: cp_action_id,
                        need_display_personal_form: need_display_personal_form,
                    },
                    url: url,
                    beforeSend: function () {
                        if (section) {
                            Brandco.helper.showLoading(section);
                        }
                    },
                    success: function(json) {
                        if (json.result === 'ok') {
                            UserProfileService.removeErrors('.jsUserProfileForm');
                            Brandco.unit.disableForm(form);
                            $(src).replaceWith('<span class="large1">' + $(src).html() + '</span>');

                            var next_message = $('.jsMessageHidden');
                            if (next_message.length) {
                                next_message.removeClass('jsMessageHidden');
                                next_message.addClass('jsMessage');
                                next_message.stop(true, false).show(200, function () {
                                    Brandco.unit.createAndJumpToAnchor();
                                });
                            } else {
                                var message = $(json.html);
                                message.hide();
                                section.after(message);

                                Brandco.helper.facebookParsing(json.data.sns_action);

                                $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                                    Brandco.unit.createAndJumpToAnchor();
                                });
                            }
                        } else {
                            if (json.errors) {
                                UserProfileService.insertErrors('.jsUserProfileForm', json.errors);
                            } else {
                                alert('予期せぬエラーが発生しました。時間を置いて再度お試しください。');
                            }
                        }
                    },
                    error: function(json) {
                        alert('予期せぬエラーが発生しました。時間を置いて再度お試しください。');
                    },
                    complete: function () {
                        if (section) {
                            Brandco.helper.hideLoading();
                        }
                    }
                };

                Brandco.api.callAjaxWithParam(param, false, false);
            },

            insertErrors: function(parent, errors) {
                UserProfileService.removeErrors(parent);
                $.each(errors, function(index, val) {
                    var target = $('.itemEdit[data-input_name=' + index + ']');
                    $(target).prepend($('<span class="iconError1"></span>').html(val));
                });
            },

            removeErrors: function(parent) {
                $(parent + ' span.iconError1').remove();
            },

            submitForm: function(src) {
                var form = $(src).parents().filter('form');
                form.submit();
            }
        };
    }();
}

$(document).ready(function() {
    $('.jsMessage').on('click', '.jsUpdateUserProfile', function() {
        UserProfileService.updateUserProfile(this);
    });

    $('.singleWrap').on('click', '.jsUpdateUserProfile', function() {
        UserProfileService.submitForm(this);
    });
});
