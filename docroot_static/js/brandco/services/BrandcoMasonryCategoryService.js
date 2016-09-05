var BrandcoMasonryCategoryService = (function() {
    return {
        sortPanel: function(element, isLoadImages, first) {
            if (!isLoadImages || typeof isLoadImages === 'undefined') {
                BrandcoMasonryCategoryService.goMason(first);
            } else {
                var count = 0;
                element.imagesLoaded().progress(function() {
                    if (count % 5 == 0) {
                        BrandcoMasonryCategoryService.goMason(first);
                    }
                    count++;
                }).done(function() {
                    BrandcoMasonryCategoryService.goMason(first);
                }).always(function() {
                    $('.modal1').height($('body').height());
                });
            }
        },
        goMason: function(first) {
            if (!first) {
                $('.jsMasonry').masonry('reloadItems');
            }
            $('.jsMasonry').masonry({
                itemSelector: '.jsPanel',
                gutter: 31,
                columnWidth: 286
            });
        }
    }
})();