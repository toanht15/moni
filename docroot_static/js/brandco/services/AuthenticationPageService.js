var AuthenticationPageService = (function(){
    return{
        authenticate: function(auth_value){
            var is_preview = $("input[name=is_preview]").val();
            if(!is_preview){
                if(auth_value == 1){
                    CookieService.createCookie('restrict_age','restrict_age');
                    var current_url = window.location.href;
                    var callback = current_url.split('authentication_page?callback=')[1];
                    if(callback == undefined){
                        callback = $('base').attr('href');
                    }
                    window.location.href = callback;
                }else if(auth_value == 2){
                    var no_link = $("input[name=no_link]").val();
                    if(no_link != ''){
                        window.location.href = no_link;
                    }
                }
            }
        }
    }
})();
$(document).ready(function() {

    $("a[href=##LINKYES##]").click(function(event){
        event.preventDefault();
        //アクセス条件認証場合、1を渡す
        AuthenticationPageService.authenticate(1);
    });

    $("a[href=##LINKNO##]").click(function(event){
        event.preventDefault();
        //アクセス条件拒否場合、2を渡す
        AuthenticationPageService.authenticate(2);
    });
});
