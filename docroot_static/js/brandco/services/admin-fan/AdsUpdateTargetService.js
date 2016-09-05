$(document).ready(function() {

    Brandco.helper.doJsCheckToggle();

    //tooltip hover
    $(document).on({
        'mouseenter': function(e) {
            var trigger = e.currentTarget;
            var target = $(trigger).data('tooltip');
            $('.jsHoverTooltip').not(target).stop(true, true).fadeOut(200);
            $(target).css({
                top: $(trigger).position().top
            }).stop(true, true).fadeIn(200);
        }
    }, '.listItem');

    $(document).on({
        'mouseleave': function() {
            $('.jsHoverTooltip').stop(true, true).fadeOut(200);
        }
    },'.segmentPreviewWrap');
});