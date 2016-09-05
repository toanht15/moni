if (typeof(MailAuthFormService) === 'undefined') {
    var MailAuthFormService = function() {
        return {
            login: function(target) {
                var $form = $(target).parents().filter('form');
                var csrf_token = $('input[name=csrf_token]', $form).val();
                var mail_address = $('input[name=mail_address]', $form).val();
                var password = $('input[name=password]', $form).val();

                var param = {
                    url: 'auth/api_validate_mail_login.json',
                    data: {
                        csrf_token: csrf_token,
                        mail_address: mail_address,
                        password: password
                    },
                    success: function(json) {
                        if (json.result === 'ok') {
                            $form.submit();
                        } else {
                            if (json.errors) {
                                $('.jsAuthErrorWrap .jsAuthError').remove();
                                for (key in json.errors) {
                                    $('.jsAuthErrorWrap').prepend($('<span class="iconError1 jsAuthError"></span>').text(json.errors[key]));
                                }
                            } else {
                                alert('エラーが発生しました。更新後、再度お試しください。');
                            }
                        }
                    },
                    error: function() {
                        alert('エラーが発生しました。更新後、再度お試しください。');
                    }
                }

                Brandco.api.callAjaxWithParam(param, false, false);

            },

            signup: function(target) {
                var $form = $(target).parents().filter('form');
                var csrf_token = $('input[name=csrf_token]', $form).val();
                var mail_address = $('input[name=mail_address]', $form).val();
                var password = $('input[name=password]', $form).val();

                var param = {
                    url: 'auth/api_validate_mail_signup.json',
                    data: {
                        csrf_token: csrf_token,
                        mail_address: mail_address,
                        password: password
                    },
                    success: function(json) {
                        if (json.result === 'ok') {
                            $form.submit();
                        } else {
                            if (json.errors) {
                                $('.jsAuthErrorWrap .jsAuthError').remove();
                                for (key in json.errors) {
                                    $('.jsAuthErrorWrap').prepend($('<span class="iconError1 jsAuthError"></span>').text(json.errors[key]));
                                }
                            } else {
                                alert('エラーが発生しました。更新後、再度お試しください。');
                            }
                        }
                    },

                    error: function() {
                        alert('エラーが発生しました。更新後、再度お試しください。');
                    }
                }

                Brandco.api.callAjaxWithParam(param, false, false);

            },

            togglePasswordVisibility: function(target) {
                var $form = $(target).parents().filter('form');
                var $input_password = $form.find('.jsInputPassword');

                if ($(target).attr('data-visible') == '0') {
                    $(target).attr('data-visible', '1').text('非表示');
                    $input_password.attr('type','text');
                } else {
                    $(target).attr('data-visible', '0').text('表示');
                    $input_password.attr('type','password');
                }
                return false;
            },

            issuePassword: function(target) {
                var $form = $(target).parents().filter('form');
                var csrf_token = $('input[name=csrf_token]', $form).val();
                var mail_address = $('input[name=mail_address]', $form).val();

                var param = {
                    url: 'auth/api_issue_password_v2.json',
                    data: {
                        csrf_token: csrf_token,
                        mail_address: mail_address,
                    },
                    type: 'POST',
                    success: function(json) {
                        if (json.result === 'ok') {
                            MailAuthFormService.slideIn(/* parent_class */ '.jsMailAuthFormSliderScreen', /* direction */ 'left', /* new_node */ json.html);
                        } else {
                            alert('エラーが発生しました。更新後、再度お試しください。');
                        }
                    },

                    error: function() {
                        alert('エラーが発生しました。更新後、再度お試しください。');
                    }
                }

                Brandco.api.callAjaxWithParam(param, false, false);
            },

            callTemplate: function(target) {
                var $form = $(target).parents().filter('form');
                var csrf_token = $('input[name=csrf_token]', $form).val();

                var param = {
                    url: 'auth/api_call_template.json',
                    data: {
                        template_id: /* template_id: MailAuthForm */ 3,
                        csrf_token: csrf_token
                    },
                    type: 'POST',
                    success: function(json) {
                        if (json.result === 'ok') {
                            MailAuthFormService.slideIn(/* parent_class */ '.jsMailAuthFormSliderScreen', /* direction */ 'right', /* new_node */ json.html);
                        } else {
                            alert('エラーが発生しました。更新後、再度お試しください。');
                        }
                    },
                    error: function() {
                        alert('エラーが発生しました。更新後、再度お試しください。');
                    }
                }

                Brandco.api.callAjaxWithParam(param);
            },

            callTemplateWithMailAddress: function(target) {
                var $form = $(target).parents().filter('form');
                var mail_address = $('input[name=mail_address]', $form).val();
                var csrf_token = $('input[name=csrf_token]', $form).val();

                var param = {
                    url: 'auth/api_call_template_with_mail_address.json',
                    data: {
                        mail_address: mail_address,
                        csrf_token: csrf_token
                    },
                    type: 'POST',
                    success: function(json) {
                        if (json.result === 'ok') {
                            MailAuthFormService.slideIn(/* parent_class */ '.jsMailAuthFormSliderScreen', /* direction */ 'left', /* new_node */ json.html);
                        } else {
                            if (json.errors) {
                                $('.jsAuthErrorWrap .jsAuthError').remove();
                                for (key in json.errors) {
                                    $('.jsAuthErrorWrap').prepend($('<span class="iconError1 jsAuthError"></span>').text(json.errors[key]));
                                }
                            } else {
                                alert('エラーが発生しました。更新後、再度お試しください。');
                            }
                        }
                    },
                    error: function() {
                        alert('エラーが発生しました。更新後、再度お試しください。');
                    }
                }

                Brandco.api.callAjaxWithParam(param);
            },

            slideIn: function(parent_class, direction, html, callback) {
                var $parent = $(parent_class);
                if ($parent.children().size() !== 1) {
                    // 入れ替える要素が1つでない場合、divで囲む
                    var $tmp_node = $('div').html($parent.html());
                    $parent.html($tmp_node);
                }

                var $new_node = $(html).css(direction, $parent.width()).hide();
                var $old_node = $parent.children().first();
                $old_node.css({left: '', right: ''});

                var property = [];
                property[direction] = -$parent.width();

                $old_node.animate(property, 250, 'swing', function() {
                    $parent.append($new_node);

                    $old_node.slideUp(100, function() {
                        $(this).remove();
                    });

                    property[direction] = 0;

                    $new_node.slideDown(100);
                    $new_node.animate(property, 250, 'swing', callback);
                });
            },
        };
    }();
}

$(document).ready(function() {
    $(document).on('click', '.jsCallMailAuthForm', function() {
        MailAuthFormService.callTemplate(this);

        return false;
    });

    $(document).on('click', '.jsCallTemplateWithMailAddress', function() {
        MailAuthFormService.callTemplateWithMailAddress(this);

        return false;
    });

    $(document).one('click', '.jsIssuePassword', function() {
        $(document).on('click', '.jsIssuePassword', function() {
            alert('短時間の間に何度もパスワードを発行できません。時間をおいてから、お試しください。');
        });

        MailAuthFormService.issuePassword(this);

        return false;
    });

    $(document).on('click', '.jsLoginByMail', function() {
        MailAuthFormService.login(this);

        return false;
    });

    $(document).on('click', '.jsSignupByMail', function() {
        MailAuthFormService.signup(this);

        return false;
    });

    $(document).on('click', '.jsTogglePasswordVisibility', function() {
        MailAuthFormService.togglePasswordVisibility(this);

        return false;
    });
});
