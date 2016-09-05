var ShowMessageManualService = (function () {
    return{
        executeChangeOptinAction: function (target) {
            var form = $(target).parents().filter(".executeChangeHideManualAction");
            var hide_manual = $(target).is(':checked') ? '1' : '0';
            
            var csrf_token = document.getElementsByName("csrf_token")[0].value;
            var url = form.attr("action");

            var param = {
                data: {
                    csrf_token: csrf_token,
                    hide_manual: hide_manual
                },
                url: url,
                success: function (json) {
                }
            };
            Brandco.api.callAjaxWithParam(param);
        },
        changeAnnounceDeliveryMessageManual: function(target) {
            var form = $(target).parents().filter('.jsChangeAnnounceDeliveryManualForm');
            var hide_manual = $(target).is(':checked') ? '1' : '0';
            var csrf_token = document.getElementsByName('csrf_token')[0].value;
            var url = form.attr('action');

            var param = {
                data: {
                    csrf_token: csrf_token,
                    hide_manual: hide_manual
                },
                url: url,
                success: function(json) {
                }
            };
            Brandco.api.callAjaxWithParam(param);
        }
    };
})();

$(document).ready(function(){
    $(".cmd_execute_change_hide_manual_action").on("change", function (event) {
        event.preventDefault();
        ShowMessageManualService.executeChangeOptinAction(this);
    });

    $('.jsChangeAnnounceDeliveryManual').on('change', function(){
        ShowMessageManualService.changeAnnounceDeliveryMessageManual(this);
    });

    $('#submitReservationSchedule').click(function () {
        $(window).unbind('beforeunload');
        Brandco.helper.updateReservationStatus($(this));
    });
});
