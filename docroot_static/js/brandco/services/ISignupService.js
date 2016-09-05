var ISignupService = (function(){
    return {
        refreshTop: function (url){
            parent.document.location.replace(url);
        }
    }
})();

$(document).ready(function(){
    var pageHeight = $(this).height();
    $('#signupIframe1', parent.document).css('height', pageHeight);

    $('#submitEntry').click(function(){
        document.frmEntry.submit();
    });
});