$(document).ready(function () {
    $.datepicker.setDefaults($.datepicker.regional['ja']);

    $(".jsDate").datepicker();

    $('#submitReservationDraft').click(function () {
        $(window).unbind('beforeunload');
        $("#status").val(1);
        document.actionForm.submit();
    });

    $('#submitReservationFix').click(function () {
        $(window).unbind('beforeunload');
        $("#status").val(2);
        document.actionForm.submit();
    });

    $('#submitReservationUnFix').click(function () {
        $(window).unbind('beforeunload');
        Brandco.helper.updateReservationStatus($(this));
    });

    $('#submitReservationSchedule').click(function () {
        $(window).unbind('beforeunload');
        Brandco.helper.updateReservationStatus($(this));
    });

});