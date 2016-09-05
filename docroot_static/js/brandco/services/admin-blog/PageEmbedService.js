var PageEmbedService = (function () {
    return {
        actionType: {
            INIT: 1,
            RESIZE_BODY_HEIGHT: 2,
            LOGIN_BY_SNS: 3
        },
        eventHandler: function(targerIframe,actionType, message){
            switch (actionType){
                case PageEmbedService.actionType.RESIZE_BODY_HEIGHT:
                    PageEmbedService.resizeCrossDomainIframe(targerIframe, message);
                    break;
                case PageEmbedService.actionType.LOGIN_BY_SNS:
                    PageEmbedService.loginBySnS(message);
                    break;
            }
        },
        resizeCrossDomainIframe: function(iframe,height){
            if (isNaN(height)) {
                return;
            }

            iframe.height = parseInt(height)+ "px";
        },
        loginBySnS: function(loginUrl){
            window.top.location.href = loginUrl;
        },
        createMessage: function(actionType,targetUrl){
            var message = {
                'action': actionType,
                'targetUrl': targetUrl
            };
            return message;
        },
        isHandlerIframe: function(event, iframe){
            var iframeSrc = iframe.getAttribute('src');
            var pageId = event.data['pageId'];
            if (pageId != '' && iframeSrc.indexOf(event.origin) > -1 && iframeSrc.indexOf(pageId) > -1) {
                return true;
            }
            return false;
        },
        sendRequestInitIframe: function(){

            var targetIframes = document.getElementsByClassName('jsMoniplaEmbedPage');

            var targetUrl = window.location.href;
            var message = PageEmbedService.createMessage(PageEmbedService.actionType.INIT,targetUrl);

            for(var i = 0; i < targetIframes.length; i++){
                var targetIframe = targetIframes[i];
                targetIframe.contentWindow.postMessage(message,'*');
            }

        },
        messageHandler: function(event){

            var targetIframes = document.getElementsByClassName('jsMoniplaEmbedPage');

            if(targetIframes.length > 0){

                for(var i = 0; i < targetIframes.length; i++){

                    var targetIframe = targetIframes[i];

                    if (PageEmbedService.isHandlerIframe(event, targetIframe)) {

                        var actionType = event.data['action'];
                        var message = event.data['message'];

                        PageEmbedService.eventHandler(targetIframe,actionType,message);
                    }
                }
            }
        }
    }
})();

if ( window.addEventListener ) {
    window.addEventListener('load',function() {
        PageEmbedService.sendRequestInitIframe();
    },false);

} else if( window.attachEvent ) {
    //IE
    window.attachEvent( 'onload', function() {
        PageEmbedService.sendRequestInitIframe();
    });
} else {
    window.onload = function(){
        PageEmbedService.sendRequestInitIframe();
    }
}

// message handler
if(window.addEventListener){
    window.addEventListener('message', function (event) {
        PageEmbedService.messageHandler(event);
    }, false);
} else {
//IE8 or earlier
    window.attachEvent('onmessage',function (event) {
        PageEmbedService.messageHandler(event);
    });
}

