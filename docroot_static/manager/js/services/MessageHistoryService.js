$(function(){
    $.datepicker.setDefaults({ dateFormat: 'yy-mm-dd' });
    $(".jsDate").datepicker( {
        maxDate: 0,
        minDate: new Date(2014, 9, 4)
    })

    $('.submitButton').click(function() {
        $("#frmMessageHistorySearch").attr('action', $(this).data('action'));
        $("#frmMessageHistorySearch").submit();
    });
});