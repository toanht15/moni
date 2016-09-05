if (typeof(BrandcoLoggingFormService) === 'undefined') {
    var BrandcoLoggingFormService = function() {
        return {
            getLoginSnsHeader: function() {
                return $('.jsLoginSnsHeader');
            },

            getLoginAddressHeader: function() {
                return $('.jsLoginAddressHeader');
            },

            getLoginAddressWrap: function() {
                return $('.jsLoginAddressWrap');
            },

            getLoginAddressForm: function(property) {
                property = property || '';
                return $('.jsLoginAddressForm' + property);
            },

            hasOpeningCpQuestionnaire: function () {
                return $('#jsHasOpeningCpQuestionnaire').length !== 0;
            },

            verifyMailAddress: function(target) {
                var form = $(target).parents().filter('form');
                var url = form.attr('action');
                var csrf_token = $('input[name=csrf_token]').val();
                var page_type = $('input[name=page_type]').val();
                var mail_address = $('input[name=mail_address]').val();

                var param = {
                    data: {
                        csrf_token: csrf_token,
                        page_type: page_type,
                        mail_address: mail_address
                    },
                    url: url,
                    success: function(json) {
                        if (json.result === 'ok') {
                            var speed = 500;

                            BrandcoLoggingFormService.removeErrors('li.address');
                            BrandcoLoggingFormService.replaceTemplate(json.html, 'left', function() {
                                BrandcoLoggingFormService.scroll(BrandcoLoggingFormService.getLoginAddressHeader().offset().top - 20, speed);
                            });
                        } else {
                            if (json.errors['already_login'] !== undefined) {
                                location.href = json.data['redirect_url'];

                                return;
                            }

                            if (json.errors['mail_address'] !== undefined) {
                                BrandcoLoggingFormService.insertErrors('li.address', [json.errors['mail_address']]);
                            }
                        }
                    },
                    error: function(json) {
                        BrandcoLoggingFormService.insertErrors('li.address', ['エラーが発生しました。時間をおいて再度お試しください。']);
                    }
                };

                Brandco.api.callAjaxWithParam(param);
            },

            retypeMailAddress: function(target) {
                var url = $(target).attr('data-url');
                var page_type = $('input[name=page_type]').val();
                var mail_address = $(target).attr('data-mail_address');
                var param = {
                    data: {
                        page_type: page_type,
                        mail_address: mail_address
                    },
                    url: url,
                    success: function(json) {
                        if (json.result === 'ok') {
                            BrandcoLoggingFormService.replaceTemplate(json.html, 'right');
                        } else {
                            if (json.errors['already_login'] !== undefined) {
                                location.href = json.data['redirect_url'];

                                return;
                            }

                            alert('エラーが発生しました。画面を再読み込みしてください。');
                        }
                    },
                    error: function(json) {
                        alert('エラーが発生しました。画面を再読み込みしてください。');
                    }
                };

                Brandco.api.callAjaxWithParam(param);
            },

            issuePassword: function() {
                var form = $('#email_form');
                var url = form.attr('data-url');
                var slide_skip = form.attr('data-slide_skip');
                var csrf_token = $('input[name=csrf_token]').val();
                var page_type = $('input[name=page_type]').val();
                var mail_address = $('input[name=mail_address]').val();

                var param = {
                    data: {
                        csrf_token: csrf_token,
                        page_type: page_type,
                        mail_address: mail_address
                    },
                    url: url,
                    success: function(json) {
                        if (json.result === 'ok') {
                            var speed = 300;

                            BrandcoLoggingFormService.removeErrors('li.address');
                            BrandcoLoggingFormService.scroll(BrandcoLoggingFormService.getLoginAddressHeader().offset().top - 20, speed);
                            setTimeout(function() {
                                if (slide_skip !== '1') {
                                    BrandcoLoggingFormService.replaceTemplate(json.html, 'left');
                                }
                                $('.jsModalConfirmPasswordIssue').hide();
                                $('.jsModalCompletePasswordIssue').show();
                            }, speed);
                        } else {
                            if (json.errors['already_login'] !== undefined) {
                                location.href = json.data['redirect_url'];

                                return;
                            }

                            alert('エラーが発生しました。時間をおいて再度お試しください。');
                        }
                    },
                    error: function(json) {
                        alert('エラーが発生しました。時間をおいて再度お試しください。');
                    }
                };

                Brandco.api.callAjaxWithParam(param);
            },

            scroll: function(position, speed) {
                $('html, body').animate({scrollTop: position}, speed, 'swing');
            },

            // direction: 'left' or 'right'
            replaceTemplate: function(html, direction, callback) {
                var property = [];
                var width = BrandcoLoggingFormService.getLoginAddressForm().width();
                var node = $(html).css(direction, width).hide();

                // 位置パラメータを元に戻す
                BrandcoLoggingFormService.getLoginAddressForm(':first-child').css({
                    left: '',
                    right: ''
                });

                property[direction] = -width;
                BrandcoLoggingFormService.getLoginAddressForm(':first-child').animate(property, 250, 'swing', function() {
                    BrandcoLoggingFormService.getLoginAddressWrap().append(node);
                    BrandcoLoggingFormService.getLoginAddressForm(':first-child').slideUp(100, function() {
                        $(this).remove();
                    });
                    BrandcoLoggingFormService.getLoginAddressForm(':last-child').slideDown(100);
                    property[direction] = 0;
                    BrandcoLoggingFormService.getLoginAddressForm(':last-child').animate(property, 250, 'swing', callback);
                });
            },

            insertErrors: function(parent, errors) {
                BrandcoLoggingFormService.removeErrors(parent);
                $.each(errors, function(index, val) {
                    $(parent).prepend($('<span class="iconError1"></span>').text(val));
                });
            },

            removeErrors: function(parent) {
                $(parent + ' span.iconError1').remove();
            },

            submitForm: function(src) {
                var form = $(src).parents().filter('form');
                if (BrandcoLoggingFormService.hasOpeningCpQuestionnaire() && typeof(OpeningCpActionService) !== 'undefined') {
                    OpeningCpActionService.preCheckQuestionnaireAnswer(src, 1);
                } else {
                    form.submit();
                }
            }
        };
    }();
}

$(document).ready(function() {
    $('.jsModalConfirmPasswordIssue').on('click', '.jsIssuePassword', function() {
       BrandcoLoggingFormService.issuePassword();
        return false;
    });

    $('.jsLoginAddressWrap').on('click', '.jsPassViewBtn', function() {
        var parent = $(this).parents().filter('form');
        var target = parent.find('.jsPassView');
        var btn = parent.find('.jsPassViewBtn');

        if ($(this).attr('data-visible') == '0') {
            btn.attr('data-visible', '1').text('非表示');
            target.attr('type','text');
        } else {
            btn.attr('data-visible', '0').text('表示');
            target.attr('type','password');
        }
        return false;
    });

    $('.jsLoginAddressWrap').on('click', '.jsVerifyMailAddress', function() {
        BrandcoLoggingFormService.verifyMailAddress(this);

        return false;
    })

    $('.jsLoginAddressWrap').on('click', '.jsRetypeMailAddress', function() {
        BrandcoLoggingFormService.retypeMailAddress(this);

        return false;
    })

    $('.jsLoginAddressWrap').on('click', '.jsScrollToLoginSnsHeader', function() {
        var position = BrandcoLoggingFormService.getLoginSnsHeader().offset().top;
        $('html, body').animate({scrollTop: position}, 300, 'swing');

        return false;
    })

    $('.jsLoginAddressWrap').on('click', '.jsConfirmPasswordIssue', function() {
        $('.jsModalCompletePasswordIssue').hide();
        Brandco.unit.openModal('#modalCompletePasswordIssue');
        $('.jsModalConfirmPasswordIssue').show();

        return false;
    })

    $('.jsLoginAddressWrap').on('click', '.jsSubmitForm', function() {
        BrandcoLoggingFormService.submitForm(this);

        return false;
    })
});