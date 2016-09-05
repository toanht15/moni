var EditLineAddFriendActionService = (function(){
    return{
        changePreviewAccount: function (account_name) {
            if(account_name == '') {
                account_name = 'アカウント名';
            }
            $('#lineAccountName').text(account_name);
        },
        changePreviewComment: function (comment) {
            $('#lineAddFriendActionComment').text(comment);
        }
    };
})();

$(document).ready(function () {

    $("input[name='line_account_name']").change(function() {
        EditLineAddFriendActionService.changePreviewAccount($(this).val());
    });

    $("textarea[name='comment']").on('input', function(){
        EditLineAddFriendActionService.changePreviewComment($(this).val());
    });

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker({
        minDate: new Date()
    });
});
