var OpeningCpActionService = (function() {
    return {
        joinCommonSite: function(target) {
            OpeningCpActionService.preCheckQuestionnaireAnswer(target, 2);
        },
        submitCpJoinForm: function(target) {
            OpeningCpActionService.preCheckQuestionnaireAnswer(target, 1);
        },
        preCheckQuestionnaireAnswer: function(target, type) {
            var form = $(target).parents().filter('.jsCampaignFormWrap').find('.openingCpActionForm');
            var mail_form = $(target).parents().filter('.jsCampaignJoinForm');
            var url = form.attr('action');
            var data = $('.openingCpActionForm').serialize() + '&' + $(mail_form).serialize();
            var param = {
                data: data,
                url: url,
                success: function(json) {
                    if (!json) {
                        alert('エラーが発生しました');
                        return false;
                    }

                    // Remove error messages
                    $(mail_form).find('.iconError1').remove();
                    $(mail_form).find('.attention1').remove();
                    $('dt[data-questionId]').next('dd').find('p.iconError1').remove();

                    if (json.result === 'ok') {
                        Brandco.unit.disableForm($(form));
                        if (type == 1) {
                            $(target).closest('.jsCampaignJoinForm').submit();
                        } else if (type == 2) {
                            location.href = $(target).data('href');
                        }
                    } else {
                        var answer_err = 0;
                        var first_err_question;

                        $.each(json.errors, function (index, value) {
                            if (index.match(/^question\//)) {
                                if (!first_err_question || answer_err > index) {
                                    answer_err = index;
                                    first_err_question = $('dt[data-questionId="' + index + '"]');
                                }
                                $('dt[data-questionId="' + index + '"]').next('dd').prepend('<p class="iconError1">' + value + '</p>');
                            } else if (index.match(/^duplicated_mail/)) {
                                $(mail_form).prepend('<p class="attention1"><small>' + value + '</small></p>');
                            } else {
                                $(mail_form).find('[name="' + index + '"]').closest('li').prepend('<span class="iconError1"><small>' + value + '</small></span>');
                            }
                        });

                        if (answer_err) {
                            var speed = 1000;
                            var sp_account_header = $('input[name="isSP"]:first').val() ? $('section.account').height() : 0;
                            var position = first_err_question.offset().top - sp_account_header;
                            $('body,html').animate({scrollTop: position}, speed, 'swing');
                        }
                    }
                }
            };
            Brandco.api.callAjaxWithParam(param);
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
                // PC版の場合
                if(!Brandco.unit.isSmartPhone) {
                    if(image.width < 520) {
                        if(image.height > image.width) {
                            if(image.width*520/image.width > 450) {
                                target_image.attr('height', 450);
                            }
                        } else {
                            target_image.attr('width', 520);
                        }
                    }
                    // スマホ版の場合
                } else {
                    if(image.width < 280) {
                        if(image.height > image.width) {
                            if(image.width*280/image.width > 240) {
                                target_image.attr('height', 240);
                            }
                        } else {
                            target_image.attr('width', 280);
                        }
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
    }
})();

$(document).ready(function() {
    OpeningCpActionService.getChoiceTitle();

    // Default Login Form (Unused part)
    $('.jsDefaultCpJoinForm').on('click', function() {
        $(target).closest('.jsCampaignJoinForm').submit();
    });

    $('.jsDefaultCommJoinForm').on('click', function() {
        location.href = $(this).data('href');
    });

    // Pre-check data before execute logging actions
    $('.jsPreCheckCpJoinForm').on('click', function() {
        OpeningCpActionService.submitCpJoinForm(this);
    });

    $('.jsPreCheckCommJoinForm').on('click', function() {
        OpeningCpActionService.joinCommonSite(this);
    });

    // campaign join address
    $('.jsJoinAddress').click(function(){
        $('.jsJoinAddressTarget').slideToggle(300);
        return false;
    });

    $('.jsMailJoinForm').on('click', function() {
        if ($(this).hasClass('isLoginForm')) {
            $(this).removeClass('isLoginForm');
            $(this).addClass('isSignupForm');
            $(this).html('アカウントをお持ちの方');

            $('.jsJoinByLoginMail').slideToggle(300, function() {
                $('.jsJoinBySignupMail').slideToggle(300);
            });
        } else {
            $(this).removeClass('isSignupForm');
            $(this).addClass('isLoginForm');
            $(this).html('アカウントをお持ちでない方');

            $('.jsJoinBySignupMail').slideToggle(300, function() {
                $('.jsJoinByLoginMail').slideToggle(300);
            });
        }
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
        OpeningCpActionService.openImageModal($(this));
    });
});