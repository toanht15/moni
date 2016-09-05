$(document).ready(function(){

    if($('#menuName')[0]) {
        $(".textLimit").html(("（")+($('#menuName')[0].value.length)+("文字/35文字）"));
    }

    $('#menuName').on('input', function () {
        $(".textLimit").html(("（")+($(this)[0].value.length)+("文字/35文字）"));
    });

    Brandco.helper.initConfirmBox();

    $('#submit').click(function() {
        $(window).unbind('beforeunload');
        document.frmMenu.submit();
    });
});