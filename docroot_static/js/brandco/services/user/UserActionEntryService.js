if(typeof(UserActionEntryService) === 'undefined') {
    var UserActionEntryService = (function () {
        var alreadyRead = [];
        var countkey = [];

        return{
            alreadyRead: alreadyRead,
            countkey: countkey,
            executeAction: function (target) {
                // Remove the event to prevent duplicate submission.
                $("#submitEntry").off("click");

                var form = $(".executeEntryActionForm");
                var section = $(target).parents().filter(".jsMessage");
                var csrf_token = document.getElementsByName("csrf_token")[0].value;
                var url = form.attr("action");
                var cp_action_id = $('input[name=cp_action_id]', form).val();
                var cp_user_id = $('input[name=cp_user_id]', form).val();
                var personal_data = $('#frmEntry').serializeArray();
                var data = {
                    csrf_token: csrf_token,
                    cp_action_id: cp_action_id,
                    cp_user_id: cp_user_id
                };
                UserActionEntryService.countkey = [];

                $.each(personal_data, function() {
                    inputValue = this;
                    if (UserActionEntryService.countkey[inputValue['name']]) {
                        UserActionEntryService.countkey[inputValue['name']] ++;
                    } else {
                        UserActionEntryService.countkey[inputValue['name']] = 1;
                    }
                });

                $.each(personal_data, function() {
                    inputValue = this;
                    if (UserActionEntryService.countkey[inputValue['name']] > 1) {
                        if (data[inputValue['name']] instanceof Array && data[inputValue['name']].length >= 1) {
                            data[inputValue['name']].push(inputValue['value']);
                        } else {
                            data[inputValue['name']] = [inputValue['value']];
                        }
                    } else {
                        data[inputValue['name']] = inputValue['value'];
                    }
                });
                var param = {

                    data: data,
                    url: url,
                    beforeSend: function () {
                        Brandco.helper.showLoading(section);
                    },

                    success: function (json) {
                        try {
                            $('.iconError1').not('.fixedError').remove();

                            if (json.result && json.result === "ok") {
                                var targetClass = $(target).attr("class");
                                $(target).replaceWith('<span class="' + targetClass + '">' + $(target).html() + '</span>');

                                if (json.data.next_action === true) {
                                    Brandco.unit.disableForm($('#frmEntry'));
                                    Brandco.unit.changeInputNameOfForm($('#frmEntry'));
                                    var submitEntry = $('#submitEntry');
                                    submitEntry.replaceWith('<span>' + submitEntry.html() + '</span>');
                                    var message = $(json.html);
                                    message.hide();
                                    section.after(message);

                                    Brandco.helper.facebookParsing(json.data.sns_action);

                                    $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                                        Brandco.unit.createAndJumpToAnchor();
                                    });
                                }
                                Brandco.unit.createAndJumpToAnchor();
                            } else {
                                if (!json.errors) {
                                    alert("エラーが発生しました");
                                    return false;
                                } else if (json.data.illegalDemography == 'true') {
                                    var btn_set = $(target).parents('.btnSet');

                                    for (var key in json.errors) {
                                        $('<p class="joinError">' + json.errors[key] + '</p>').insertAfter(btn_set);
                                    }

                                    $(target).replaceWith('<span>' + $(target).html() + '</span>');
                                    Brandco.unit.disableForm($('#frmEntry'));

                                    return false;
                                }
                                var first_err_question;
                                for (var key in json.errors) {
                                    var parent;
                                    if (key == 'birthDay' || key == 'restrictedAge') {
                                        parent = $('[name="birthDay_y"]').closest('.itemEdit');
                                    } else if (key == 'zipCode') {
                                        parent = $('[name="zipCode1"]').closest('.itemEdit');
                                    } else if (key == 'telNo') {
                                        parent = $('[name="telNo1"]').closest('.itemEdit');
                                    } else if(key == 'agree_agreement') { //利用規約チェックボックスのエラー場合
                                        parent = $('[name="' + key + '"]').closest('.ruleReadCheck');
                                    } else {
                                        parent = $('[name="' + key + '"]').closest('.itemEdit');

                                        //checkbox
                                        if (!parent[0]) {
                                            parent = $('[name="' + key + '[]"]').closest('.itemEdit');
                                        }
                                        if (!first_err_question) {
                                            first_err_question = parent;
                                        }
                                    }

                                    if (!parent.find('.iconError1')[0]) {
                                        parent.prepend('<span class="iconError1">' + json.errors[key] + '</span>');
                                    }
                                }
                                var first_err_account_info;
                                var agreement_checkbox_error;
                                if ('lastName' in json.errors || 'firstName' in json.errors || 'lastNameKana' in json.errors || 'firstNameKana' in json.errors) {
                                    first_err_account_info = $('[name="lastName"]').closest('.itemEdit');
                                } else if ('sex' in json.errors) {
                                    first_err_account_info = $('[name="sex"]').closest('.itemEdit');
                                } else if ('birthDay' in json.errors || 'restrictedAge' in json.errors) {
                                    first_err_account_info = $('[name="birthDay_y"]').closest('.itemEdit');
                                } else if ('zipCode' in json.errors) {
                                    first_err_account_info = $('[name="zipCode1"]').closest('.itemEdit');
                                } else if ('address1' in json.errors) {
                                    first_err_account_info = $('[name="address1"]').closest('.itemEdit');
                                } else if ('address2' in json.errors) {
                                    first_err_account_info = $('[name="address2"]').closest('.itemEdit');
                                } else if ('telNo' in json.errors) {
                                    first_err_account_info = $('[name="telNo1"]').closest('.itemEdit');
                                } else if ('mailAddress' in json.errors) {
                                    first_err_account_info = $('[name="mailAddress"]').closest('.itemEdit');
                                } else if ('agree_agreement' in json.errors) {
                                    agreement_checkbox_error = $('[name="agree_agreement"]').closest('.ruleReadCheck');
                                }

                                var speed = 1000;
                                if ($('input[name="isSP"]:first').val()) {
                                    var sp_account_header = $('section.account').height();
                                    if ($('.iconError1').length) {
                                        sp_account_header += $('.iconError1:first').outerHeight(true);
                                    }
                                } else {
                                    var sp_account_header = 0;
                                }
                                var position;
                                if (first_err_account_info) {
                                    position = first_err_account_info.offset().top - sp_account_header;
                                } else if (first_err_question) {
                                    position = first_err_question.offset().top - sp_account_header;
                                } else if (agreement_checkbox_error){
                                    position = agreement_checkbox_error.offset().top - sp_account_header;
                                }
                                $('body,html').animate({scrollTop: position}, speed, 'swing');
                            }
                        } finally {
                            // register the event.
                            $('#submitEntry').click(function() {
                                UserActionEntryService.executeAction(this);
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
    Brandco.unit.createAndJumpToAnchor();

    $('#submitEntry').click(function() {
        UserActionEntryService.executeAction(this);
    });
});