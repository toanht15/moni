var OptinStatusService = (function () {
    return{
        executeChangeOptinAction: function (target) {
            var form = $(target).parents().filter("form");
            var optin_flg = $(target).val();
            var csrf_token = $('input[name=csrf_token]', form).val();
            var optin_token = $('input[name=optin_token]', form).val();
            var url = form.attr("action");

            var param = {
                data: {
                    csrf_token: csrf_token,
                    optin_token: optin_token,
                    optin_flg: optin_flg
                },
                url: url,
                success: function (json) {
                    if (json.result == 'ok') {
                        alert('更新しました。');
                    } else {
                        alert('エラーが発生しました。時間をおいて再度お試しください。');
                    }
                },
                error: function() {
                    alert('エラーが発生しました。時間をおいて再度お試しください。');
                }
            };
            Brandco.api.callAjaxWithParam(param);
        }
    };
})();

$(document).ready(function () {
    $(".cmd_execute_change_optin_action").on("change", function (event) {
        event.preventDefault();
        OptinStatusService.executeChangeOptinAction(this);
    });
});
