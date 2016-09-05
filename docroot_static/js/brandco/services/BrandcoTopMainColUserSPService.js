var BrandcoTopMainColUserSPService = (function(){
    var nowPage = 1;
    return{
        recordPanelClick: function (link, entry, entry_id){
            var csrf_token = $('[name=csrf_token]')[0].value;
            var data = {csrf_token:csrf_token, link:link, entry:entry, entry_id: entry_id};
            var url = 'admin-top/api_record_panel_click.json';
            var param = {
                data: data,
                url: url
            };
            Brandco.api.callAjaxWithParam(param, false);
        },
        morePage: function() {
            $("#more_loading").show();
            $("#more_area").hide();
            var moreUrl = $("#more_ajax_url").val(),
            param = {
                url: moreUrl + (nowPage+1),
                dataType: 'text',
                success: function(data) {
                    nowPage++;
                    $("#more_loading").hide();
                    $("#more_loading").before(data);
                    if( nowPage * $('#sp_page_per_count').val() <= $('#total_count').val()){
                        //まだ続きがあるときはmoreを表示する
                        $("#more_area").show();
                    }
                }
            };
            Brandco.api.callAjaxWithParam(param, false);
        }
    };
})();

$(document).ready(function(){
    $(".panelClick").mousedown(function(){
        BrandcoTopMainColUserSPService.recordPanelClick($(this).data('link'), $(this).data('entry'), $(this).data('entry_id'));
    });
    $("#more_link").on('click', function(){
        BrandcoTopMainColUserSPService.morePage();
    });
});