if(typeof(UserOptinService) === 'undefined') {
    var UserOptinService = (function () {
        return {
            updateOptin: function (target) {
                var form = $('.jsUpdateOptin');
                var section = $(target).parents().closest('section');
                var csrf_token = $('input[name=csrf_token]', form).val();
                var url = form.attr('action');
                var new_optin_flg = $(target).hasClass("switch_large off") ? 1 : 0;
                var cp_id = $('input[name=cp_id]', form).val();

                var param = {
                    type: 'POST',
                    data: {
                        csrf_token: csrf_token,
                        new_optin_flg: new_optin_flg,
                        cp_id: cp_id
                    },
                    url: url,
                    timeout: 10000,

                    beforeSend: function () {
                        Brandco.helper.showLoading();
                    },

                    success: function (json) {
                        if (json.result == 'ng') {
                            if ($(target).hasClass("switch_large off")) {
                                $(target).removeClass("switch_large off");
                                $(target).addClass("switch_large on");
                            } else {
                                $(target).removeClass("switch_large on");
                                $(target).addClass("switch_large off");
                            }
                        }
                    },

                    error: function () {
                        if ($(target).hasClass("switch_large off")) {
                            $(target).removeClass("switch_large off");
                            $(target).addClass("switch_large on");
                        } else {
                            $(target).removeClass("switch_large on");
                            $(target).addClass("switch_large off");
                        }
                        Brandco.helper.hideLoading();
                    },

                    complete: function () {
                        Brandco.helper.hideLoading();
                    }
                };

                Brandco.api.callAjaxWithParam(param, true, true);
            }
        };
    })();
}

$(document).ready(function () {
    $('.jsSwitchStatus').off('click');
    $('.jsSwitchStatus').on('click', function () {
        UserOptinService.updateOptin(this);
    });
});