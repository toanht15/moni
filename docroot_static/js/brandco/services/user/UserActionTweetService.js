if (typeof(UserActionTweetService) === 'undefined') {
    var UserActionTweetService = {
        alreadyClicked: false,
        initActionMessage: function() {
            $('.cmd_execute_tweet_action').each(function() {
                var form = $(this).parents().filter('.executeTweetActionForm');
                var section = $(this).parents().filter('.jsMessage');
                if (form.data('posted-tweet')) {
                    UserActionTweetService.executeActionTweet(this, 0, form.data('posted-tweet'), 1);
                    form.data('posted-tweet', '');
                }
                if ($('#' + section.attr('id') + ' .fileUpload_img').length > 0) {
                    UserActionTweetService.displayRemainingCharacter(this);
                }
            });
        },

        countingRemainingCharacter: function (target) {
            var section                 = $(target).parents().filter('.jsMessage');
            var tweetDefaultTextLength  = UserActionTweetService.getCharacterLengthFromString($('#' + section.attr('id') + ' .jsTweetDefaultText').val());
            var tweetFixedTextLength    = UserActionTweetService.getCharacterLengthFromString($('#' + section.attr('id') + ' .jsTweetFixedText').text());
            if (tweetFixedTextLength > 0) {
                tweetFixedTextLength += 1;
            }
            var photoLength = 0;
            if ($('#' + section.attr('id') + ' #fileUploadList').find('a').size() > 0) {
                photoLength = 24;
            }
            return 140 - (tweetDefaultTextLength + tweetFixedTextLength + photoLength);
        },

        getCharacterLengthFromString: function (tweet_text) {
            return twttr.txt.getTweetLength(tweet_text);
        },

        displayRemainingCharacter: function (target) {
            var remainingText = UserActionTweetService.countingRemainingCharacter(target);
            var section = $(target).parents().filter('.jsMessage');
            section.find('.jsRemainingCharacters').text(remainingText);
            if (remainingText < 0) {
                section.find('.jsRemainingCharacterInvalid').css('display', 'block');
            } else {
                section.find('.jsRemainingCharacterInvalid').css('display', 'none');
            }
        },

        preExecuteActionTweet: function (target) {
            if (!UserActionTweetService.alreadyClicked) {
                UserActionTweetService.alreadyClicked = true;
            } else {
                return false;
            }
            var form                = $(target).parents().filter('.executeTweetActionForm').get(0);
            var data                = new FormData(form);
            var section             = $(target).parents().filter('.jsMessage');
            var url                 = form.action;

            var param = {
                data                : data,
                processData         : false,
                contentType         : false,
                url                 : url,

                beforeSend: function() {
                    Brandco.helper.showLoading(section);
                },

                success: function(json) {
                    if (json.result === 'ok') {
                        if (json.data.post_tweet) {
                            if (json.data.post_tweet === 'api_error') {
                                alert('ツイートに失敗しました。');
                            } else {
                                UserActionTweetService.executeActionTweet(target, 0, json.data.post_tweet);
                            }
                        } else {
                            window.location.href = $(form).data('redirect-url');
                        }
                    } else {
                        if (json.errors.tweet_error) {
                            alert(json.errors.tweet_error);
                        } else {
                            alert('エラーが発生しました。');
                        }
                    }
                },

                complete: function() {
                    Brandco.helper.hideLoading();
                }
            };
            Brandco.api.callAjaxWithParam(param, false, false);
        },

        preExecuteActionTweetForIE: function (target) {

            if (!UserActionTweetService.alreadyClicked) {
                UserActionTweetService.alreadyClicked = true;
            } else {
                return false;
            }

            var form                = $(target).parents().filter('.executeTweetActionForm');
            var section             = $(target).parents().filter('.jsMessage');

            var param = {
                beforeSend: function() {
                    Brandco.helper.showLoading(section);
                },

                success: function(json) {
                    if (json.result === 'ok') {
                        if (json.data.post_tweet) {
                            if (json.data.post_tweet === 'api_error') {
                                alert('ツイートに失敗しました。');
                            } else {
                                UserActionTweetService.executeActionTweet(target, 0, json.data.post_tweet);
                            }
                        } else {
                            window.location.href = form.data('redirect-url');
                        }
                    } else {
                        if (json.errors.tweet_error) {
                            alert(json.errors.tweet_error);
                        } else {
                            alert('エラーが発生しました。');
                        }
                    }
                },

                complete: function() {
                    Brandco.helper.hideLoading();
                }
            };
            form.ajaxSubmit(param);
        },

        executeActionTweet: function (target, skipped, post_tweet, post_flg) {

            if (UserActionTweetService.alreadyClicked && skipped) return false;
            UserActionTweetService.alreadyClicked = true;

            // Remove the event to prevent duplicate submission.
            $(document).off('click', '.executeTweetActionForm #twSkipBtn a');

            var form            = $(target).parents().filter('.executeTweetActionForm');
            form.attr('action', form.data('execute-url'));
            var url             = $(form).attr('action');
            var section         = $(form).parents().filter(".jsMessage");
            var cpActionId      = $('input[name=cp_action_id]', form).val();
            var cpUserId        = $('input[name=cp_user_id]', form).val();
            var csrfToken       = $('input[name=csrf_token]', form).val();


            var param = {
                data: {
                    csrf_token      : csrfToken,
                    cp_action_id    : cpActionId,
                    cp_user_id      : cpUserId,
                    skipped         : skipped
                },

                url: url,

                beforeSend: function() {
                    Brandco.helper.showLoading(section);
                },

                success: function(json) {
                    try {
                        if (json.result === 'ok') {
                            if (!post_flg) {
                                UserActionTweetService.displayAfterActionDone(target, skipped, post_tweet);
                            }
                            if (json.data.next_action === true) {
                                var message = $(json.html);
                                message.hide();
                                section.after(message);

                                Brandco.helper.facebookParsing(json.data.sns_action);
                                $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                                    Brandco.unit.createAndJumpToAnchor();
                                });
                            }
                            UserActionTweetService.alreadyClicked = false;
                            $('.cmd_auto_execute_skip_tweet_action').remove();
                        } else {
                            if (json.errors.tweet_error) {
                                alert(json.errors.tweet_error);
                            } else {
                                alert('エラーが発生しました。');
                            }
                        }
                    } finally {
                        // Add the event.
                        $(document).on('click', '.executeTweetActionForm #twSkipBtn a', function() {
                            UserActionTweetService.executeActionTweet(this, 1);
                        });
                    }
                },

                complete: function() {
                    Brandco.helper.hideLoading();
                }
            };
            Brandco.api.callAjaxWithParam(param, false, false);
        },
        displayAfterActionDone: function(target, skipped, post_tweet) {
            var section = $(target).parents().filter(".jsMessage");
            if (skipped) {
                $('#' + section.attr('id') + ' .jsTweetDefaultText').attr('disabled', 'disabled');
                $('#' + section.attr('id') + ' #fileUploadList input').attr('disabled', 'disabled');
                $('#' + section.attr('id') + ' #fileUploadList').css('pointer-events', 'none');
            } else {
                $('#' + section.attr('id') + ' .uploadCont a').attr('href', post_tweet);
                $('#' + section.attr('id') + ' .uploadCont a').html(post_tweet);
                $('#' + section.attr('id') + ' .uploadCont').css('display', 'block');

                var textDefault =  $('#' + section.attr('id') + ' .jsTweetDefaultText').val();
                var tweetFixedText = $('#' + section.attr('id') + ' .jsTweetFixedText').html();
                if (tweetFixedText) {
                    tweetFixedText = '<br>' + tweetFixedText;
                } else {
                    tweetFixedText = '';
                }

                $('#' + section.attr('id') + ' .messageTweet .tweetText').html('<span class="postText">' + Brandco.helper.autoLink(Brandco.helper.escapeSpecialCharacter(textDefault)) + tweetFixedText + '</span>');
                $('#' + section.attr('id') + ' #fileUploadList img').each(function() {
                    $('#' + section.attr('id') + ' .messageTweet .tweetText').append($(this));
                });
                $('#' + section.attr('id') + ' .module').remove();
            }
            $('#' + section.attr('id') + ' .jsTweetBtnElement').html('<span class="middle1">ツイート</span>');
            $('#' + section.attr('id') + ' #twSkipBtn a').css('pointer-events', 'none');
            $('#' + section.attr('id') + ' #twSkipBtn').hide();
        }
    }
}
$(document).ready(function () {
    UserActionTweetService.initActionMessage();
    $(document).off('change', '.executeTweetActionForm input.photo_upload');
    $(document).on('change', '.executeTweetActionForm input.photo_upload', function(e1) {
        var input_file = $(this);
        var section = $(this).parents().filter('.jsMessage');
        if (this.files && this.files[0]) {
            if (window.FileReader) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var image = new Image();
                    image.src = e.target.result;
                    image.onload = function() {
                        $('#' + section.attr('id') + ' #thumb_' + input_file.attr('id')).append('<img src="' + image.src + '" width="41" height="41" alt=""><a href="javascript:void(0)" class="iconBtnDelete">削除する</a>');
                        input_file.css('display', 'none');
                        var id = parseInt(input_file.attr('id')) + 1;
                        if ($('#' + section.attr('id') + ' #fileUploadList').children().size() < 4) {
                            $('#' + section.attr('id') + ' #fileUploadList').append('<span><span class="thumb" id="thumb_' + id + '"></span><input type="file" name="tweet_photo_upload_' + id + '" class="photo_upload" id="' + id + '"></span>');
                        }
                        UserActionTweetService.displayRemainingCharacter(input_file);
                    };
                };
                reader.readAsDataURL(this.files[0]);
            }
        }
    });

    $(".cmd_auto_execute_skip_tweet_action").each(function () {
        var target = this;
        var inview = $(this).parents().filter(".inview");
        inview.on('inview', function(event, isInView, visiblePartX, visiblePartY) {
            if (isInView) {
                UserActionTweetService.executeActionTweet(target, 1);
            }
        });
    });

    //アップロード画像を削除する
    $(document).off('click', '.executeTweetActionForm .iconBtnDelete');
    $(document).on('click', '.executeTweetActionForm .iconBtnDelete', function(){
        var section = $(this).parents().filter('.jsMessage');
        var form = $(this).parents().filter('.executeTweetActionForm');
        this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);

        if ($('#' + section.attr('id') + ' #fileUploadList').children().size() == $('#' + section.attr('id') + ' #fileUploadList').find('a').size()) {
            var id = parseInt($('#' + section.attr('id') + ' #fileUploadList :last-child').find('input').attr('id')) + 1;
            $('#' + section.attr('id') + ' #fileUploadList').append('<span><span class="thumb" id="thumb_' + id + '"></span><input type="file" name="tweet_photo_upload_' + id + '" class="photo_upload" id="' + id + '"></span>');
        }

        UserActionTweetService.displayRemainingCharacter(form);
    });

    $(document).off('input', '.executeTweetActionForm .messageTweet .tweetText .jsTweetDefaultText');
    $(document).on('input', '.executeTweetActionForm .messageTweet .tweetText .jsTweetDefaultText', function(){
        UserActionTweetService.displayRemainingCharacter(this);
    });

    $(document).off('click', '.cmd_execute_tweet_action');
    $(document).on('click', '.cmd_execute_tweet_action', function(event) {
        var execute_flg = UserActionTweetService.countingRemainingCharacter(this) >= 0 && UserActionTweetService.countingRemainingCharacter(this) < 140;
        var section = $(this).parents().filter('.jsMessage');
        if ($('#' + section.attr('id') + ' .jsRequirePhoto').length > 0 && $('#' + section.attr('id') + ' #fileUploadList').find('a').size() == 0) {
            execute_flg = false;
            $('#' + section.attr('id') + ' .jsRequirePhoto').css('display', 'block');
        } else {
            $('#' + section.attr('id') + ' .jsRequirePhoto').css('display', 'none');
        }
        if (execute_flg) {
            event.preventDefault();
            if (window.FormData) {
                UserActionTweetService.preExecuteActionTweet(this);
            } else {
                UserActionTweetService.preExecuteActionTweetForIE(this);
            }
        }
    });

    $(document).off('click', '.executeTweetActionForm #twSkipBtn a');
    $(document).on('click', '.executeTweetActionForm #twSkipBtn a', function() {
        UserActionTweetService.executeActionTweet(this, 1);
    });
});