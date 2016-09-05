$(function(){
    $('.jsEngagementLogHover').click(function() {
        var engagement_log_hover = $(this);
        var engagement_log_data = $(this).find('.jsEngagementLogData');

        if ($(this).hasClass('isOpening')) {
            $(engagement_log_data).slideUp(200, function(){
                engagement_log_hover.removeClass('isOpening');
            });
        } else {
            $('.jsEngagementLogData').not(engagement_log_data).each(function() {
                $(this).slideUp(200, function(){
                    $(this).parent('.jsEngagementLogHover').removeClass('isOpening');
                });
            });
            $(engagement_log_data).slideDown(200, function() {
                engagement_log_hover.addClass('isOpening');
            });
        }
    });
    $.datepicker.setDefaults({ dateFormat: 'yy-mm-dd' });
    $(".jsDate").datepicker( {
        minDate: new Date(2014, 9, 4)
    })
});