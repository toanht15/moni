$(document).ready(function() {
    // load
    $("#twAccountSetting :checked").each(function () {
        syncCheckedAccountLoading($(this));
    });
    // onclick
    $("#twAccountSetting :radio").change(function () {
        syncCheckedAccount($(this));
    });

    $('.jsModulePreviewSwitch').click(function() {
        if ($(this).hasClass('left')) {
            $('.jsFBButtonPC').hide();
            $('.jsFBButtonSP').show();
        } else if ($(this).hasClass('right')) {
            $('.jsFBButtonPC').show();
            $('.jsFBButtonSP').hide();
        }

        return false;
    });

    // スキップフラグ変更時
    $("input[name='skip_flg']").change(function() {
        if ($(this).is(':checked')) {
            $(".jsSkipFlgPreview").show();
        } else {
            $(".jsSkipFlgPreview").hide();
        }
    });

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker({
        minDate: new Date()
    });
});

syncCheckedAccountLoading = function($val) {
    var isl = $val.attr("id");
    // demo view
    $('#socialButton_0').hide();
    $('#socialButton_' + isl).show();
    if ($("input[name='skip_flg_load']").val() == 0) {
        $('.jsSkipFlgPreview').hide();
    }
}

syncCheckedAccount = function($val) {
    var socialButtons = $('#socialButtons');
    var isl = $val.attr("id");
    socialButtons.children().each(function() {
        $(this).hide();
        if ($(this).attr("id") == 'socialButton_' + isl ) {
            $('#socialButton_' + isl).show();
        }
    });
}
