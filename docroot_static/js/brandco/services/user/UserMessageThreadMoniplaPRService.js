if(typeof(UserMessageThreadMoniplaPRService) === 'undefined') {
    var UserMessageThreadMoniplaPRService = (function () {
        return {
            showMoniplaPR: function () {
                var url     = $('input[name="base_url"]').val() + 'messages/api_show_monipla_pr.json';
                var cp_id   = $('input[name="cp_id"]').val();

                var param = {
                    data: {
                        cp_id: cp_id,
                    },
                    url: url,
                    type: 'GET',
                    beforeSend: function () {
                        Brandco.helper.showLoading();
                    },

                    success: function (json) {
                        if (json.result == 'ok') {
                            $('#jsShowMoniplaPR').replaceWith(json.html);
                            $('#jsShowMoniplaPR').remove();
                        } else if (json.result == 'ng') {
                            $('#jsShowMoniplaPR').remove();
                        }
                    },

                    error: function () {
                        $('#jsShowMoniplaPR').remove();
                        Brandco.helper.hideLoading();
                    },

                    complete: function () {
                        $('#jsShowMoniplaPR').remove();
                        Brandco.helper.hideLoading();
                    }
                };

                Brandco.api.callAjaxWithParam(param, true, true);
            }
        };
    })();
}

$(document).ready(function () {
    if ($("#jsShowMoniplaPR").length){
        UserMessageThreadMoniplaPRService.showMoniplaPR();
    }
});