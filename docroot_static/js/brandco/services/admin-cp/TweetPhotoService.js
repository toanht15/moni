var TweetPhotoService = (function() {
    return {
        getTweetPhotoModal: function(photo_url, modal_id) {
            $(modal_id).find('.jsViewTweetPhotoModal').html('<img src="' + photo_url + '"/>');
            $(modal_id).height($('body').height()).fadeIn(300, function(){
                $(this).find('.jsModalCont').css({
                    display: 'block',
                    opacity: 0,
                    top: $(window).scrollTop()
                }).animate({
                    top: $(window).scrollTop() + 30,
                    opacity: 1
                }, 300, function() {
                    var modal_height = $(modal_id).find('.jsModalCont').position().top + $(modal_id).find('.jsModalCont').outerHeight(true);
                    var body_height = $('body').outerHeight(true);
                    var default_height = $('body').data('prev_height');

                    if (default_height === undefined || default_height == '') {
                        $('body').data('prev_height', body_height);
                        default_height = body_height;
                    }

                    if (body_height >= default_height && body_height < modal_height) {
                        $('body').height(modal_height + 10);
                        $(modal_id).height($('body').height());
                    }
                });
            });
        }
    }
})();

$(document).ready(function() {
    // Photo Modal
    $(document).on('click', '.jsOpenTweetPhotoModal', function() {
        TweetPhotoService.getTweetPhotoModal($(this).data('photo_url'), $(this).attr('href'));
        return false;
    });
});