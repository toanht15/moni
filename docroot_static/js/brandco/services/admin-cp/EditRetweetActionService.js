var EditRetweetActionService = {

    initPreview: function() {
        if($('.jsSetupSkipFlg').attr('checked')) {
            $('.modulePreview1 .messageSkip').css('display', 'block');
        }
        if($('input[name=tweet_id]').val()) {
            $('.jsPreviewMessageRetweet').css('display', 'block');
        }
    },

    initActionPreview: function() {
        $(document).on('click', '.jsApplyTweetUrl', function() {
            var api_url         = $(this).data('api_apply_tweet_url');
            var tweet_url       = $('.jsSetupTweetUrl').val();
            if (EditRetweetActionService.validateTweetUrl(tweet_url)) {
                $('.jsRetweetErrorUrl').css('display', 'none');
                EditRetweetActionService.executeApplyTweetUrl(tweet_url, api_url);
            } else {
                $('.jsRetweetErrorUrl').css('display', 'block');
            }
        });

        $(document).on('change', '.jsSetupSkipFlg', function() {
            if (this.checked) {
                $('.modulePreview1 .messageSkip').css('display', 'block');
            } else {
                $('.modulePreview1 .messageSkip').css('display', 'none');
            }
        });
    },

    executeApplyTweetUrl: function(tweet_url, api_url) {
        var data    = {tweet_url:tweet_url};
        var url     = api_url;
        var param = {
            data: data,
            type: 'GET',
            url: url,
            success: function(json) {
                if (json.result === 'ok') {
                    if (json.data.tweet_content) {
                        EditRetweetActionService.formingTweetContent(json.data.tweet_content);
                        EditRetweetActionService.displayPreviewTweetContent(json.data.tweet_content);
                    } else {
                        $('.jsRetweetErrorUrl').css('display', 'block');
                        $('.jsPreviewMessageRetweet').css('display', 'none');
                    }
                } else {
                    if (json.errors.tweet_url_error) {
                        alert(json.errors.tweet_url_error);
                    } else {
                        alert('エラーが発生しました。');
                    }
                }
            }
        };
        Brandco.api.callAjaxWithParam(param, false);
    },

    validateTweetUrl: function(tweet_url) {
        var urlRegex = /^((http|https)(:\/\/))*(www\.)*(twitter\.com\/)([a-zA-Z0-9_]{1,15})(\/status\/)([0-9]+)$/;
        return urlRegex.test(tweet_url);
    },

    formingTweetContent: function(tweet_content) {
        $('input[name=twitter_name]').val(tweet_content.twitter_name);
        $('input[name=twitter_screen_name]').val(tweet_content.twitter_screen_name);
        $('input[name=twitter_profile_image_url]').val(tweet_content.twitter_profile_image_url);
        $('input[name=tweet_id]').val(tweet_content.tweet_id);
        $('input[name=tweet_text]').val(tweet_content.tweet_text);
        $('input[name=tweet_date]').val(tweet_content.tweet_date);
        if (tweet_content.tweet_has_photo) {
            $('input[name=tweet_has_photo]').val(tweet_content.tweet_has_photo);
            $('input[name=tweet_photos]').val(tweet_content.tweet_photos.join());
        }
    },

    displayPreviewTweetContent: function(tweet_content) {
        $('.jsPreviewTwitterProfileImageUrl').attr('src', tweet_content.twitter_profile_image_url);
        $('.jsPreviewTwitterName').text(tweet_content.twitter_name + '@' + tweet_content.twitter_screen_name);
        $('.jsPreviewTweetText').html(Brandco.helper.autoLink(Brandco.helper.escapeSpecialCharacter(tweet_content.tweet_text)));
        $('.jsPreviewTweetDate').text(tweet_content.tweet_date);
        if (tweet_content.tweet_has_photo) {
            var appendHtml = '';
            if (tweet_content.tweet_photos.length == 1) {
                appendHtml += '<li class="sizeFull"><img src="' + tweet_content.tweet_photos[0] + '" style="width: 100%"></li>';
            } else if(tweet_content.tweet_photos.length == 2) {
                appendHtml += '<li class="sizeHalf"><img src="' + tweet_content.tweet_photos[0] + '" style="height: 100%"></li>';
                appendHtml += '<li class="sizeHalf"><img src="' + tweet_content.tweet_photos[1] + '" style="height: 100%"></li>';
            } else if(tweet_content.tweet_photos.length == 3) {
                appendHtml += '<li class="sizeHalf"><img src="' + tweet_content.tweet_photos[0] + '" style="height: 100%"></li>';
                appendHtml += '<li class="sizeQuarter"><img src="' + tweet_content.tweet_photos[1] + '" style="width: 100%"></li>';
                appendHtml += '<li class="sizeQuarter"><img src="' + tweet_content.tweet_photos[2] + '" style="width: 100%"></li>';
            } else {
                appendHtml += '<li class="sizeQuarter"><img src="' + tweet_content.tweet_photos[0] + '" style="width: 100%"></li>';
                appendHtml += '<li class="sizeQuarter"><img src="' + tweet_content.tweet_photos[1] + '" style="width: 100%"></li>';
                appendHtml += '<li class="sizeQuarter"><img src="' + tweet_content.tweet_photos[2] + '" style="width: 100%"></li>';
                appendHtml += '<li class="sizeQuarter"><img src="' + tweet_content.tweet_photos[3] + '" style="width: 100%"></li>';
            }
            $('.jsPreviewPostImg').html(appendHtml);
        }
        $('.jsPreviewMessageRetweet').css('display', 'block');
    }

}


$(document).ready(function(){
    EditRetweetActionService.initPreview();
    EditRetweetActionService.initActionPreview();

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker({
        minDate: new Date()
    });
});