$(document).ready(function () {
    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker({
        minDate: new Date()
    });
});
