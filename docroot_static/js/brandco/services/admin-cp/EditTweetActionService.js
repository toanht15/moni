var EditTweetActionService = {
    initPreview: function() {
        if($('#tweetDefaultText').val()) {
            $('.modulePreview1 .messageTweet textarea').val($('#tweetDefaultText').val());
        }

        if($('#tweetFixedText').val()) {
            var temp = Brandco.helper.escapeSpecialCharacter($('#tweetFixedText').val());
            $('.modulePreview1 .messageTweet .hashtag').html(temp);
        }

        if($('.skipFlg').attr('checked')) {
            $('.modulePreview1 .jsSkipLink').css('display', 'block');
        }
        EditTweetActionService.countingRemainingCharacter();
    },

    countingRemainingCharacter: function () {
        var tweetDefaultTextLength  = EditTweetActionService.getCharacterLengthFromString($('#tweetDefaultText').val());
        var tweetFixedTextLength    = EditTweetActionService.getCharacterLengthFromString($('#tweetFixedText').val()) !=0 ? EditTweetActionService.getCharacterLengthFromString($('#tweetFixedText').val()) + 1 : 0;
        var photoLength             = $('input[name="photo_flg"]:checked').val() == 1 ? 24 : 0;
        var remainingText           = 140 - (tweetDefaultTextLength + tweetFixedTextLength + photoLength);
        $('.moduleEdit1 .counter .attention1').text(remainingText);
        $('.modulePreview1 .counter .attention1').text(remainingText);
        if (remainingText < 0) {
            $('.jsTweetLengthError').show();
        } else {
            $('.jsTweetLengthError').hide();
        }
        if (tweetFixedTextLength > 0) {
            $('.modulePreview1 .messageTweet .supplement1').css('display', 'block');
        } else {
            $('.modulePreview1 .messageTweet .supplement1').css('display', 'none');
        }
    },

    getCharacterLengthFromString: function (tweet_text) {
        return twttr.txt.getTweetLength(tweet_text);
    },

    initActionPreview: function() {
        $(document).on('input', '#tweetDefaultText', function() {
            $('.modulePreview1 .messageTweet textarea').val($('#tweetDefaultText').val());
            EditTweetActionService.countingRemainingCharacter();
        });

        $(document).on('input', '#tweetFixedText', function() {
            var temp = Brandco.helper.escapeSpecialCharacter($('#tweetFixedText').val());
            $('.modulePreview1 .messageTweet .hashtag').html(temp);
            EditTweetActionService.countingRemainingCharacter();
        });

        $(document).on('change', '.photoFlg', function() {
            EditTweetActionService.countingRemainingCharacter();
            if ($('input[name="photo_flg"]:checked').val() == 1) {
                $('.jsImageUploadForm').css('display', 'block');
                $('span.fileUpload_img').addClass('require1');
            } else if ($('input[name="photo_flg"]:checked').val() == 2) {
                $('.jsImageUploadForm').css('display', 'none');
            } else {
                $('.jsImageUploadForm').css('display', 'block');
                $('span.fileUpload_img').removeClass('require1');
            }

        });

        $(document).on('change', '.skipFlg', function() {
            if (this.checked) {
                $('.modulePreview1 .jsSkipLink').css('display', 'block');
            } else {
                $('.modulePreview1 .jsSkipLink').css('display', 'none');
            }
        });
    }
}

$(document).ready(function(){
    EditTweetActionService.initPreview();
    EditTweetActionService.initActionPreview();

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker({
        minDate: new Date()
    });
});
