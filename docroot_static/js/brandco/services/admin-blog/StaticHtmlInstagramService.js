var StaticHtmlInstagramService = (function () {
    return{
        initPage: function(){

            var instagramParts = $('.pagePartsTemplatePostImgList');

            instagramParts.each(function(){
                var api_url = $(this).find("input[name=api_url]").val();
                var number_image_per_page = $(this).find("input[name=number_image_per_page]").val();
                var csrf_token = $('input[name="csrf_token"]:first').val();

                var data ="api_url="+api_url+"&number_image_per_page="+number_image_per_page+"&next_id=0"+"&csrf_token="+csrf_token;

                StaticHtmlInstagramService.loadItems(this,data,true);
            });
        },
        executePaging: function(instagram_part,action){

            var api_url = $(instagram_part).find("input[name=api_url]").val();
            var number_image_per_page = $(instagram_part).find("input[name=number_image_per_page]").val();
            var csrf_token = $('input[name="csrf_token"]:first').val();

            var next_id = 0;
            if(action == 1){
                next_id = $(instagram_part).find("input[name=next_id]").val();
            }else if(action == 2){
                next_id = $(instagram_part).find("input[name=previous_id]").val();
            }

            var data = "api_url="+api_url+"&number_image_per_page="+number_image_per_page+"&next_id="+next_id+"&csrf_token="+csrf_token;

            StaticHtmlInstagramService.loadItems(instagram_part,data,false);
        },
        loadItems: function(instagram_part, data, is_init_page){

            var url = $('base').attr('href') + 'blog/api_get_instagram_image_for_page.json'

            var param = {
                data: data,
                url: url,

                beforeSend: function(){

                    Brandco.helper.brandcoBlockUI();

                    if(!is_init_page){

                        if($('input[name="isSP"]:first').val()) {
                            var sp_account_header = $('section.account').height();
                        } else {
                            var sp_account_header = 0;
                        }

                        var position = instagram_part.offset().top - sp_account_header;
                        $('body,html').animate({scrollTop: position}, 1000, 'swing');
                    }
                },
                success: function (json) {
                    if (json.result === "ok") {

                        if(json.data.next_id !== undefined || json.data.previous_id !== undefined){

                            $(instagram_part).find('#pagination').show();

                            if(json.data.next_id !== undefined){
                                $(instagram_part).find('.next').show();
                                $(instagram_part).find("input[name=next_id]").val(json.data.next_id);
                            }else{
                                $(instagram_part).find('.next').hide();
                            }

                            if(json.data.previous_id !== undefined){
                                $(instagram_part).find('.prev').show();
                                $(instagram_part).find("input[name=previous_id]").val(json.data.previous_id);
                            }else{
                                $(instagram_part).find('.prev').hide();
                            }
                        }else{
                            $(instagram_part).find('#pagination').hide();
                        }

                        $(instagram_part).find('.instaOnlyList').html(json.html);
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
    StaticHtmlInstagramService.initPage();

    $(".next").click(function(){
        instagram_part = $(this).closest(".pagePartsTemplatePostImgList");

        //変数は1だったら、次のページをロードする
        StaticHtmlInstagramService.executePaging(instagram_part,1);
    });

    $(".prev").click(function(){
        instagram_part = $(this).closest(".pagePartsTemplatePostImgList");

        //変数は2だったら、前のページをロードする
        StaticHtmlInstagramService.executePaging(instagram_part,2);
    });
});
