$(document).ready(function () {
    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker();

    // common check toggle area
    $('.jsCheckToggle').on('change', function () {
        var targetWrap = $(this).parents('.jsCheckToggleWrap')[0];
        var target = $(targetWrap).find('.jsCheckToggleTarget');
        target.find('.jsDate').val('');
        target.slideToggle(300);
    });
    $('.jsOrderSearch').on('click', function () {
        document.orderSearchForm.action = $(this).attr('data-url');
        document.orderSearchForm.submit();
    });

    $('.jsOrderDownLoad').on('click', function () {
        document.orderSearchForm.action = $(this).attr('data-url');
        document.orderSearchForm.submit();
    });
});
