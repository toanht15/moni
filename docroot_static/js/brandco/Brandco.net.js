Brandco.net = (function (){
    var timeout = 120000; //120s
    return {
        callAjax: function(paramDict,overlay, closeOverlay){
            overlay = (typeof overlay === 'undefined') ? true : overlay;
            closeOverlay = (typeof closeOverlay === 'undefined') ? true : closeOverlay;

            if (overlay) {
                paramDict.beforeSend = function(jqXHR, PlainObject){
                    Brandco.helper.brandcoBlockUI();
                };
            }
            if (closeOverlay) {
                paramDict.complete = function(jqXHR, textStatus) {
                    Brandco.helper.brandcoUnblockUI();
                }
            }

            return $.ajax(paramDict);
        },
        defaultSuccessFunction: function(){
            return function(data, textStatus, jqXHR){

            }
        },
        defaultErrorFunction: function(){
            var statusErrorMap = {
                '400' : "Server understood the request but request content was invalid.",
                '401' : "Unauthorised access.",
                '403' : "Forbidden resouce can't be accessed",
                '500' : "Internal Server Error.",
                '503' : "Service Unavailable"
            };
            return function(jqXHR, textStatus, errorThrown){
                var message = "Unknow Error.";
                if(jqXHR.status){
                    message = statusErrorMap[jqXHR.status];
                    if(!message){
                        message="Unknow Error.";
                    }
                }else if(textStatus =='parsererror'){
                    message="Error.\nParsing JSON Request failed.";
                }else if(textStatus =='timeout'){
                    message="Request Time out.";
                }else if(textStatus =='abort'){
                    message="Request was aborted by the server";
                }else{
                    message="Unknow Error.";
                }
                console.log(message);
            }
        },
        ajaxSetup: function () {
            $.ajaxSetup({
                async: true,
                cache: false,
                timeout: timeout,
                type: "POST",
                dataType: 'json',
                beforeSend: function () {},
                success: Brandco.net.defaultSuccessFunction(),
                error: Brandco.net.defaultErrorFunction()
            });
        }
    };
})();