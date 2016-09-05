var BrandcoSnsPageService = (function() {
    var curPage = 1;

    return {
        loadNextPage: function() {
            $("#more_page_loading").show();
            $("#more_page_btn").hide();

            var moreUrl = $('input[name="more_page_url"]').val(),
                param = {
                    url: moreUrl + (curPage + 1),
                    dataType: 'text',
                    success: function(response) {
                        curPage++;
                        $('#more_page_loading').hide();
                        $('#more_page_loading').before(response);

                        if (curPage * $('input[name="sp_panel_per_page"]').val() < $('input[name="total_count"]').val()) {
                            $('#more_page_btn').show();
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(param, false);
        },
        recordPanelClick: function (link, entry, type, entry_id){
            var csrf_token = $('[name=csrf_token]')[0].value;
            var data = {csrf_token:csrf_token, link:link, entry:entry, type: type, entry_id: entry_id};
            var url = 'admin-top/api_record_panel_click.json';
            var param = {
                data: data,
                url: url
            };
            Brandco.api.callAjaxWithParam(param, false);
        }
    };
})();

$(document).ready(function(){
    if (!Brandco.unit.isSmartPhone) {
        BrandcoMasonryCategoryService.sortPanel($('#sortable'), true, true);
    }

    $(document).on('mousedown', '.panelClick', function() {
        BrandcoSnsPageService.recordPanelClick($(this).data('link'), $(this).data('entry'), $(this).data('type'), $(this).data('entry_id'));
    });

    $('#more_panel').on('click', function() {
        BrandcoSnsPageService.loadNextPage();
    });
});