var UserActionAccountSettingService = (function () {
    return{
        executeChangeOptinAction: function (target) {
            var form = $(target).parents().filter(".executeChangeOptinAction");
            var optin_flg = $(target).val();
            var csrf_token = document.getElementsByName("csrf_token")[0].value;
            var url = form.attr("action");

            var param = {
                data: {
                    csrf_token: csrf_token,
                    optin_flg: optin_flg
                },
                url: url,
                success: function (json) {
                }
            };
            Brandco.api.callAjaxWithParam(param);
        }
    };
})();

$(document).ready(function () {
    $(".cmd_execute_change_optin_action").on("change", function (event) {
        event.preventDefault();
        UserActionAccountSettingService.executeChangeOptinAction(this);
    });

    // message setting toggle
    $('.jsMypageSetteing').click(function(){
        $('.jsMypageSetteingTarget').slideToggle(300);
        return false;
    });

});