$(document).ready(function () {

    $('#submitReservationUnSchedule').click(function () {
        $(window).unbind('beforeunload');
        Brandco.helper.updateReservationStatus($(this));
    });

});