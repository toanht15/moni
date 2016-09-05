/**
 * Created by ta_minh_ha on 2015/09/18.
 */
$(document).ready(function(){
    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker({
        minDate: new Date()
    });
});
