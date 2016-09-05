var BrandcoTopMainColUserService = (function(){
    return{
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
    $(document).on('mousedown', '.panelClick', function() {
        BrandcoTopMainColUserService.recordPanelClick($(this).data('link'), $(this).data('entry'), $(this).data('type'), $(this).data('entry_id'));
    });

    if (!Brandco.unit.isSmartPhone) {
        BrandcoMasonryTopService.sortPanel($('#sortable'), true, true);
    }
});