var EmbedIframeControllService = (function () {
    return {
        actionType: {
            INIT: 1,
            RESIZE_BODY_HEIGHT: 2,
            LOGIN_BY_SNS: 3
        },
        sendIframeSize: function(targetUrl) {
            var iframeHeight = EmbedIframeControllService.getDocHeight();
            var message = EmbedIframeControllService.createMessage(EmbedIframeControllService.actionType.RESIZE_BODY_HEIGHT, iframeHeight);
            parent.postMessage(message,targetUrl);

        },
        snsButtonOnclickHandler: function(targetUrl){
            var snsLoginButtons = document.getElementsByClassName('jLogin');
            for(var i = 0; i < snsLoginButtons.length; i++){
                snsLoginButtons[i].onclick = function() {
                    var loginUrl = this.getAttribute('data-href');
                    var message = EmbedIframeControllService.createMessage(EmbedIframeControllService.actionType.LOGIN_BY_SNS, loginUrl);
                    parent.postMessage(message,targetUrl);
                }
            }
        },
        createMessage: function(actionType,messageContent){
            var pageId = EmbedIframeControllService.getPageId();
            var message = {
                'action': actionType,
                'message': messageContent,
                'pageId': pageId
            };
            return message;
        },
        replaceRedirectUrl: function(targetUrl){
            var loginTags = document.getElementsByClassName('jLogin');
            for(var i = 0; i < loginTags.length; i++){
                var dataHref = loginTags[i].getAttribute('data-href');
                if(dataHref.indexOf(targetUrl) == -1){
                    dataHref = dataHref + targetUrl;
                    loginTags[i].setAttribute('data-href',dataHref);
                }
            }
        },
        goBack: function(){
            var backButton = document.getElementById('back_url');
            if(backButton != null){
                backButton.onclick = function(){
                    window.history.back();
                }
            }
        },
        init: function(targetUrl){

            if(targetUrl == ''){

                var baseUrl = document.getElementsByName('base_url')[0].value;

                window.location.replace(baseUrl);

            } else {

                EmbedIframeControllService.sendIframeSize(targetUrl);

                EmbedIframeControllService.replaceRedirectUrl(targetUrl);

                EmbedIframeControllService.snsButtonOnclickHandler(targetUrl);

                EmbedIframeControllService.goBack();
            }
        },
        getDocHeight: function() {
            var D = document;
            return Math.max(
                Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
                Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
                Math.max(D.body.clientHeight, D.documentElement.clientHeight)
            )
        },
        getPageId: function() {
            //Iframeは「どのページを表示するか」をチェックするために。
            var pageId = '';
            if(document.getElementsByName('page_url')[0] != undefined){
                pageId = document.getElementsByName('page_url')[0].value;
            }
            return pageId;
        }
}
})();
// message handler
if(window.addEventListener){
    window.addEventListener('message', function (event) {
        var targetUrl = event.data['targetUrl'];
        EmbedIframeControllService.init(targetUrl);
    }, false);
} else {
//IE8 or earlier
    window.attachEvent('onmessage',function (event) {
        var targetUrl = event.data['targetUrl'];

        EmbedIframeControllService.init(targetUrl);
    });
}
