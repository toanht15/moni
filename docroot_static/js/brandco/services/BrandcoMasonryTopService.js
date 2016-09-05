var BrandcoMasonryTopService = (function() {
    return {
        sortPanel: function(element, isLoadImages, first) {
            if (element && element.find('div.fb-video').length != 0 && typeof(FB) != 'undefined' && FB != null) {
                FB.XFBML.parse();
            }

            if (!isLoadImages || typeof isLoadImages === 'undefined') {
                BrandcoMasonryTopService.goMason(first);
            } else {
                var count = 0;
                element.imagesLoaded().progress(function(){
                    if (count % 5 == 0) {
                        BrandcoMasonryTopService.goMason(first);
                    }
                    count++;
                }).done(function(){
                    BrandcoMasonryTopService.goMason(first);
                }).always(function() {
                    $('.modal1').height($('body').height());
                });
            }
        },
        goMason: function(first){
            if (!first) {
                $('.jsMasonry').masonry('reloadItems');
            }
            $('.jsMasonry').masonry({
                // options
                itemSelector: '.jsPanel',
                stamp: '.jsStamp',
                gutter: 8,
                columnWidth: 234
            });
        }
    }
})();