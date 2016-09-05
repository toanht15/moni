$(document).ready(function() {
    $(document).on('click', '.jsOpenIGModal', function() {
        var modalID = $(this).attr('href');
        var ig_link = $(this).data('link');
        var ig_entry_id = $(this).data('entry_id');
        var instagram_embed_modal = $(modalID).find('#instagram_embed_modal');

        var param = {
            data: 'media_url=' + ig_link + '&entry_id=' + ig_entry_id,
            url: 'instagram/api_get_instagram_embed_media.json',
            success: function(response) {
                if (response.result == 'ok') {
                    $(instagram_embed_modal).html(response.data.embed_media);
                    instgrm.Embeds.process();

                    $(modalID).height($('body').height()).fadeIn(300, function(){
                        $(this).find('.jsModalCont').css({
                            display: 'block',
                            opacity: 0,
                            top: $(window).scrollTop()
                        }).animate({
                            top: $(window).scrollTop() + 30,
                            opacity: 1
                        }, 300, function() {
                            var modal_height = $(modalID).find('.jsModalCont').position().top + $(modalID).find('.jsModalCont').outerHeight(true);
                            var body_height = $('body').outerHeight(true);

                            if (body_height < modal_height) {
                                $('body').data('prev_height', body_height);
                                $('body').height(modal_height + 10);
                                $(modalID).height($('body').height());
                            } else {
                                $('body').data('prev_height', 0);
                            }
                        });
                    });
                }
            }
        };
        Brandco.api.callAjaxWithParam(param);
        return false;
    })
})