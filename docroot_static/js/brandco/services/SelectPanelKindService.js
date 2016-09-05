var SelectPanelKindService = (function(){
    return {
        submitClick: function (rssCheckUrl, rssAddUrl, callbackUrl){
            var url = document.getElementsByName("link")[0].value;
            if(url == ""){
                alert("リンクを入力してください。");
                return false;
            }
            var csrf_token = document.getElementsByName("csrf_token")[0].value;
            data = "url="+url+"&csrf_token="+csrf_token;
          Brandco.unit.closeModal(2);
            var param = {
                data: data,
                url: rssCheckUrl,
                success: function(data){
                    if(data.result != "ng"){
                        postData = {"url":data.data.url, "csrf_token":csrf_token};
                        var saveParam = {
                            data: postData,
                            url: rssAddUrl,
                            success: function (data) {
                                window.location = callbackUrl;
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                alert("保存中にエラーが発生しました。");
                            }
                        };
                        Brandco.api.callAjaxWithParam(saveParam);
                    }else{
                        alert("RSS URLを入力してください。");
                        Brandco.unit.openModal("#modal2");
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert("チェック中にエラーが発生しました。");
                }
            };
            Brandco.api.callAjaxWithParam(param);
        }
    }
})();

$(document).ready(function(){
    $("#rssButton").click(function(event){
        event.preventDefault();
        Brandco.unit.openModal("#modal2");
        Brandco.unit.closeModal(1);
    }) ;

    $('a[href="#openModal1"]').click(function(){
        Brandco.unit.openModal("#modal1");
        Brandco.unit.closeModal(2);
    });

    $('#aSubmit').click(function(){
        SelectPanelKindService.submitClick($(this).data('checkurl'), $(this).data('addurl'), $(this).data('callbackurl'));
    });
});