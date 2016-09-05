$(document).ready(function() {

    $('.jsConnectFbAccount').click(function() {
        if($("input[name^=account_ids]:checked").length == 0) {
            $('.jsFbAccountError').html('一つ以上のアカウントを選択してください');
            $('.jsFbAccountError').show();
        } else {
            $('#frmFacebookAdd').submit();
        }
    });
    
    $('.jsConnectTwAccount').click(function() {
        if($("input[name^=account_ids]:checked").length == 0) {
            $('.jsTwAccountError').html('一つ以上のアカウントを選択してください');
            $('.jsTwAccountError').show();
        } else {
            $('#frmTwitterAdd').submit();
        }
    });
});