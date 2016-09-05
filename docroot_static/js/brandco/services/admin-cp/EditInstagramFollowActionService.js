var EditInstagramFollowActionService = (function(){
    return{
        syncCheckedAccount: function ($val) {
            var isl = $val.attr("id");
            var cp_action_id = $('#igAccountList_'+isl).val();
            $("#igAccNamePreview").replaceWith('<h1 class="messageHd1">「アカウント名」のInstagramアカウントをフォローしよう！</h1>');
            $("#igAccEntryPreview").replaceWith('<div class="engagementInner followIg jsIgSelectEntry"><div class="engagementIg"><p class="postDummy_ig">post dummy</p></div></div>');
            $("#selectInstagramEntry").attr('data-option', '?tgt_act_id=' + isl + '&action_id=' + cp_action_id);
            $("#currentEntryImg").hide();
            $("#currentEntryId").attr('value', -1);
        }
    };
})();

$(document).ready(function () {
    // アカウント設定変更時
    $("#igAccountSetting :radio").change(function () {
        EditInstagramFollowActionService.syncCheckedAccount($(this));
    });

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker({
        minDate: new Date()
    });
});
