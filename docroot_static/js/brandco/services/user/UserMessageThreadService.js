$(document).ready(function(){
    $('#pinAction').click(function() {
        var speed = 1000;
        var href = $(this).attr("href");
        var target = $(href == "#" || href == "" ? 'html' : href);
        var sp_account_header = $('input[name="isSP"]:first').val() ? $('section.account').height() : 0;
        var position = target.offset().top - sp_account_header;

        $('body,html').animate({scrollTop: position}, speed, 'swing');
        CpIndicatorService.pinAction();

        return false;
    });
});

window.onload = function() {
    var speed = 1000;
    var href = '';
    var target = '';

    if (document.location.search) {
        var get_params_string = document.location.search.substring(1, document.location.search.length);

        if (get_params_string.length > 0) {
            var get_params = get_params_string.split('&');
        }

        for (var i = 0; i < get_params.length; i++) {
            if (/^scroll=/.test(get_params[i])) {
                var id = get_params[i].split('scroll=');
                target = $('#' + id[1]);

                break;
            }
        }
    }

    if (target == '') {
        if (document.location.hash && document.location.hash == '#_=_') {
            href = "#newMessage";
        } else {
            href = document.location.hash || "#newMessage";
        }
        target = $(href == "#" || href == "" ? 'html' : href);
    }

    var sp_account_header = $('input[name="isSP"]:first').val() ? $('section.account').height() : 0;
    var position = target.offset().top - sp_account_header;
    if (position > 0) {
        $('body,html').animate({scrollTop: position}, speed, 'swing');
        CpIndicatorService.init();
    }

    return false;
}
