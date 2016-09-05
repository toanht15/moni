var EditYoutubeChannelActionService = (function(){
    return{
        syncCheckedAccountLoading: function ($val) {
            var isl = $val.attr("id");
            // demo view
            $('#stream_select_' + isl).show();
        },

        syncCheckedAccount: function ($val) {
            var isl = $val.attr("id");
            $('.jsSelectYtEntry').hide();
            if ($('#stream_select_' + isl)[0]) {
                $('#stream_select_' + isl).show();
            } else {
                $('#getStreamBtn').show();
            }
            EditYoutubeChannelActionService.changePreviewAccount($val);
        },

        changePreviewAccount: function ($val) {
            $('#ytChPreviewTitle').html('「' + $val.data('name') + '」のYouTubeチャンネルを登録しよう！');
            $('#ytChPreviewImg').attr('src', $val.data('picture_url'));
            $('#ytChPreviewImg').attr('alt', $val.data('name'));
            $('#ytChPreviewName').html($val.data('name'));
            $('#ytChPreviewScreenName').html($val.data('screen_name'));
        },

        changePreviewMovie: function ($val) {
            var term = $val.val().split(',');
            var ifs = 'https://www.youtube.com/embed/' + term[1] + '?rel=0';
            $('#ytChPreviewMovieIframe').attr('src', ifs);
        }
    };
})();

$(document).ready(function () {

    // account load
    $("#ytAccountSetting :checked").each(function () {
        EditYoutubeChannelActionService.syncCheckedAccountLoading($(this));
    });
    // account onclick
    $("#ytAccountSetting :radio").change(function () {
        EditYoutubeChannelActionService.syncCheckedAccount($(this));
    });
    // movie onclick
    $(".jsSelectYtEntry").change(function () {
        EditYoutubeChannelActionService.changePreviewMovie($(this));
    });

    $("input[name='intro_flg']").change(function() {
        if ($(this).is(':checked')) {
            $('.jsSelectYtEntry').removeAttr('disabled');
            $('#ytChPreviewMovie').show();
        } else {
            $('.jsSelectYtEntry').attr('disabled', 'disabled');
            $('#ytChPreviewMovie').hide();
        }
    });

    $("#getStreamBtn").off("click");
    $("#getStreamBtn").on("click", function (event) {
        event.preventDefault();
        // formのactionを書き換える
        $(window).unbind('beforeunload');
        $('#save_type').val(1);
        document.actionForm.submit();
        // POST処理を行う
        return false;
    });

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker({
        minDate: new Date()
    });
});
