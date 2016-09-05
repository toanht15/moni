jQuery(function(){

    // message setting toggle
    $('.jsMessageSetting').click(function(){
        $('.jsMessageSettingTarget').slideToggle(300);
        return false;
    });

    $("[data-toggle=tooltip]").tooltip();

    $.datepicker.setDefaults({ dateFormat: 'yy-mm-dd' });
    $(".jsDate").datepicker({
    });

});

$(document).ready(function() {
    $("#btn_csv_campaign_list").click(function() {
        $("#csv_campaign_list").submit();
    });

    $('.testPage').on('change', function() {
        if ($(this).val() == "1") {
            $('#basicAuthDiv').slideDown(300);
        } else {
            $('#basicAuthDiv').slideUp(300);
        }
    });

    $('.jsSegmentLimitCheckBox').on('change', function() {
        if ($(this).is(':checked')) {
            $('#segment_limit_form').slideDown("fast");
        } else {
            $('#segment_limit_form').slideUp("fast");
        }
    });
});