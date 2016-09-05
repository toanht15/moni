var StaticHtmlStampRallyService = (function () {
    return{

        initPage: function(){

            var stampRallyParts = $('.stampRally');

            stampRallyParts.each(function(){

                var cp_ids = $(this).find("input[name=cp_ids]").val();
                var cp_count = $(this).find("input[name=cp_count]").val();
                var stamp_status_coming_soon_image = $(this).find("input[name=stamp_status_coming_soon_image]").val();

                var csrf_token = $('input[name="csrf_token"]:first').val();

                var data ="cp_ids="+cp_ids+"&cp_count="+cp_count+"&stamp_status_coming_soon_image="+stamp_status_coming_soon_image+"&csrf_token="+csrf_token;

                StaticHtmlStampRallyService.loadItems(this,data);
            });
        },
        loadItems: function(stamp_rally_part, data){

            var url = $('base').attr('href') + 'blog/' + 'api_get_stamp_rally_cp.json'

            var param = {
                data: data,
                url: url,
                beforeSend: function(){
                    Brandco.helper.brandcoBlockUI();
                },
                success: function (json) {
                    if (json.result === "ok") {
                        if (json.data && json.data.cur_active_cp_url) {
                            $('.jsCurActiveCp').show();
                            $('.jsCurActiveCp span a').attr('href', json.data.cur_active_cp_url);
                        }
                        $(stamp_rally_part).find('.stampRallyList').html(json.html);
                    }
                },
                complete: function() {
                    $.unblockUI();
                }
            };
            Brandco.api.callAjaxWithParam(param,false,false);
        }
    };
})();
$(document).ready(function() {
    StaticHtmlStampRallyService.initPage();
});
