$(document).ready(function () {
    // load
    $('#actionForm').find('#moduleCont1 :checked').each(function() {
        syncCheckedAccountLoading($(this));
    });
    // onclick
    $('#actionForm').find('#moduleCont1 :radio').change(function() {
        syncCheckedAccount($(this));
    });

    $('.jsModulePreviewSwitch').click(function(){
        if($(this).hasClass('left')){
            $('.jsFBButtonPC').hide();
            $('.jsFBButtonSP').show();
        }else if($(this).hasClass('right')){
            $('.jsFBButtonPC').show();
            $('.jsFBButtonSP').hide();
        }
        return false;
    });

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker({
        minDate: new Date()
    });
});

syncCheckedAccountLoading = function ($val) {
    var isl = $val.attr("id");
    // demo view
    $('#socialButton_0').hide();
    $('#socialButton_' + isl).show();
}

syncCheckedAccount = function ($val) {
    var socialButtons = $('#socialButtons');
    var isl = $val.attr("id");
    socialButtons.children().each(function(){
        $(this).hide();
        if ($(this).attr("id") == 'socialButton_' + isl ) {
            $('#socialButton_' + isl).show();
        }
    });
}
