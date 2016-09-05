var CmsCategoryService = (function () {
    var curPage = 1;
    return {
        loadNextPage: function (target) {
            $("#jsMorePageLoading").show();
            target.hide();
            var moreUrl = $('input[name="more_page_url"]').val(),
                param = {
                    url: moreUrl + (curPage + 1),
                    dataType: 'text',
                    success: function(response) {
                        curPage++;
                        $("#jsMorePageLoading").hide();
                        $('#jsCategoryInfiniteScroll li:last-child').after(response);

                        if (curPage < $('input[name="total_page"]').val()) {
                            target.show();
                        }
                    }
                };
            console.log(moreUrl + (curPage + 1));
            Brandco.api.callAjaxWithParam(param, false);
        }
    }
})();

$(document).ready(function () {
    $('.jsMoreLoad').on('click', function () {
        CmsCategoryService.loadNextPage($(this));
    });
});