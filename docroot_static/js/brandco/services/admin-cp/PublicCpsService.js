$(document).ready(function() {
    $('.trashbox').click(function() {
        $('#archive_button').attr('data-url', $(this).data('url'));
        if ($(this).data('type') == "cp") {
            $('#modal1').find(".attention1").html('このキャンペーンを削除しますか？');
        } else {
            $('#modal1').find(".attention1").html('このメッセージを削除しますか？');
        }
        Brandco.unit.openModal("#modal1");
    });

    $('#archive_button').click(function() {
        window.location.href = $(this).attr('data-url');
    });
});