if(typeof(UserActionQuestionnaireService) === 'undefined') {
    var UserActionQuestionnaireService = (function () {
        var countkey = [];
        return{
            countkey: countkey,
            executeAction: function (target) {
                // Remove the event to prevent duplicate submission.
                $('#submitEntry').off('click');

                // Execute the first questionnaire action
                var form = $(".executeQuestionnaireActionForm");
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
                UserActionQuestionnaireService.countkey = [];

                $.each(personal_data, function() {
                    inputValue = this;
                    if (UserActionQuestionnaireService.countkey[inputValue['name']]) {
                        UserActionQuestionnaireService.countkey[inputValue['name']] ++;
                    } else {
                        UserActionQuestionnaireService.countkey[inputValue['name']] = 1;
                    }
                });

                $.each(personal_data, function() {
                    inputValue = this;
                    if (UserActionQuestionnaireService.countkey[inputValue['name']] > 1) {
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
                            // Add the event.
                            $('#submitEntry').on('click', function(event) {
                                UserActionQuestionnaireService.executeAction(this);
                            });
                        }
                    },

                    complete: function () {
                        Brandco.helper.hideLoading();
                    }
                };
                Brandco.api.callAjaxWithParam(param, false, false);
            },
            executeQuestionnaireAction: function (target) {
                // Remove the event to prevent duplicate submission.
                $(".cmd_execute_questionnaire_action").off("click");

                var form = $(target).parents().filter(".executeQuestionnaireActionForm");
                var section = $(target).parents().filter(".jsMessage");
                var url = form.attr("action");
                var data = $(form).serialize();
                var param = {
                    data: data,
                    url: url,
                    beforeSend: function () {
                        Brandco.helper.showLoading(section);
                    },
                    success: function (json) {
                        try {
                            $('dt[data-questionId]').next('dd').find('p.iconError1').remove();
                            if (json.result === "ok") {
                                Brandco.unit.disableForm($(target).closest('form'));
                                if (json.data.next_action === true) {
                                    var message = $(json.html);
                                    message.hide();
                                    section.after(message);

                                    Brandco.helper.facebookParsing(json.data.sns_action);

                                    $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                                        Brandco.unit.createAndJumpToAnchor();
                                        $(target).replaceWith('<span class="large1">' + $(target).html() + '</span>');
                                    });
                                } else {
                                    $(target).replaceWith('<span class="large1">' + $(target).html() + '</span>');
                                }
                            } else {
                                var answer_err = 0;
                                var first_err_question;
                                $.each(json.errors, function (i, value) {
                                    if (i.match(/^question\//)) {
                                        if (!first_err_question) {
                                            first_err_question = $('dt[data-questionId="' + i + '"]');
                                        }
                                        $('dt[data-questionId="' + i + '"]').next('dd').prepend('<p class="iconError1">' + value + '</p>');
                                        answer_err = 1;
                                    }
                                });

                                if (!answer_err) {
                                    alert("エラーが発生しました");
                                } else {
                                    var speed = 1000;
                                    var sp_account_header = $('input[name="isSP"]:first').val() ? $('section.account').height() : 0;
                                    var position = first_err_question.offset().top - sp_account_header;
                                    $('body,html').animate({scrollTop: position}, speed, 'swing');
                                }
                            }
                        } finally {
                            // Register the event.
                            $('.cmd_execute_questionnaire_action').click(function() {
                                UserActionQuestionnaireService.executeQuestionnaireAction(this);
                            });
                        }
                    },
                    complete: function () {
                        Brandco.helper.hideLoading();
                    }
                };
                Brandco.api.callAjaxWithParam(param, false, false);
            },
            openImageModal: function(target) {
                var image_src = target.prev('label').find('img').attr('src');
                var image_title = target.prev('label').find('img').attr('alt');
                var target_image = $('.modalImgPreview').children('img');
                var image = new Image();
                image.src = image_src;
                image.onload = function() {
                    $('.modalImgPreview').children('figcaption.title').html(image_title);
                    target_image.attr('src', image_src);
                    target_image.attr('alt', image_title);
                    target_image.removeAttr('width');
                    target_image.removeAttr('height');

                    var max_size = Brandco.unit.isSmartPhone ? 280 : 520;
                    if (image.width < max_size || image.height < max_size) {
                        if(image.width >= image.height) {
                            target_image.attr('width', max_size);
                        } else {
                            target_image.attr('height', max_size);
                        }
                    }
                    Brandco.unit.openModal("#modal5");
                };
            },
            getChoiceTitle: function() {
                $('figcaption[data-action_type="questionnaire"]').each(function() {
                    $(this).html(Brandco.helper.cutLongText($(this).next('span').children('img').attr('alt'), 120));
                });
            }
        };
    })();
}

$(document).ready(function () {
    Brandco.unit.createAndJumpToAnchor();
    UserActionQuestionnaireService.getChoiceTitle();

    $(".cmd_execute_questionnaire_action").off("click");
    $(".cmd_execute_questionnaire_action").on("click", function (event) {
        event.preventDefault();
        UserActionQuestionnaireService.executeQuestionnaireAction(this);
    });

    $('#submitEntry').off('click');
    $('#submitEntry').on('click', function(event) {
        UserActionQuestionnaireService.executeAction(this);
    });

    $("a[data-close_modal_type]").off("click");
    $("a[data-close_modal_type]").on("click", function(){
        Brandco.unit.closeModal($(this).attr('data-close_modal_type'));
        if($(this).attr('data-close_modal_type') == 5) {
            setTimeout(function() {
                $('#modal1').find('img').removeAttr('width height');
            }, 300);
        }
    });

    $('a.previwe').off("click");
    $('a.previwe').on("click", function() {
        UserActionQuestionnaireService.openImageModal($(this));
    });
});