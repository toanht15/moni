Brandco.api = (function(){
    return{
        callAjax: function(data, url, type, callbackurl){
            var param = {
                data: data,
                url: url
            };
            if(type){
                param.type = type;
            }
            if(callbackurl){
                param.success = function(data, textStatus, jqXHR){
                        document.location = callbackurl;
                }
            }
            Brandco.net.ajaxSetup();
            Brandco.net.callAjax(param);
        },
        callAjaxWithParam: function(param, overlay, closeOverlay){
            Brandco.net.ajaxSetup();

            Brandco.net.callAjax(param, overlay, closeOverlay);
        }
    };
})();