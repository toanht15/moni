if(typeof(UserActionMovieService) === 'undefined') {
    var UserActionMovieService = (function () {

        var player;
        var newPlayer;
        var yt_width = $(".jsMessage").width();
        var current_status = -1;
        var done = false;
        var form = $(".executeMovieActionForm");
        var movie_object_id = $('input[name=movie_object_id]', form).val();
        var view_status = $('input[name=view_status]', form).val();

        return{

            executeAction: function () {

                var section = form.closest(".jsMessage");
                var csrf_token = document.getElementsByName("csrf_token")[0].value;
                var cp_action_id = $('input[name=cp_action_id]', form).val();
                var cp_user_id = $('input[name=cp_user_id]', form).val();
                var url = form.attr("action");
                var param = {

                    data: {
                        csrf_token: csrf_token,
                        cp_action_id: cp_action_id,
                        cp_user_id: cp_user_id
                    },
                    url: url,

                    beforeSend: function () {
                        Brandco.helper.showLoading(section);
                    },

                    success: function (json) {
                        if (json.result === "ok") {
                            if (json.data.next_action === true) {
                                var message = $(json.html);
                                message.hide();
                                section.after(message);

                                Brandco.helper.facebookParsing(json.data.sns_action);

                                $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                                    Brandco.unit.createAndJumpToAnchor();
                                });
                            }
                        } else {
                            $.each(json.errors, function (i, value) {
                                if (value) {
                                    alert(value);
                                } else {
                                    alert("エラーが発生しました");
                                }
                            });
                        }
                    },

                    complete: function () {
                        Brandco.helper.hideLoading();
                    }
                };
                Brandco.api.callAjaxWithParam(param, true);
            },

            initYoutubeAPI: function () {
                var tag = document.createElement('script');
                tag.src = "https://www.youtube.com/iframe_api";
                var firstScriptTag = document.getElementsByTagName('script')[0];
                firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
            },

            initPlayer: function () {
                newPlayer = new YT.Player('yt_player', {
                    videoId: movie_object_id,
                    width: yt_width,
                    height: yt_width*9/16,
                    playerVars: {
                        rel: 0,
                        enablejsapi: 1,
                        showinfo: 0
                    },
                    events: {
                        onReady: UserActionMovieService.onPlayerReady,
                        onStateChange: UserActionMovieService.onPlayerStateChange
                    }
                });
            },

            onPlayerReady: function (event) {
                if (event) {
                    player = event.target;
                    event.data = player.getPlayerState();
                    UserActionMovieService.onPlayerStateChange(event);
                }
            },

            onPlayerStateChange: function (event) {
                if (event.data == YT.PlayerState.PLAYING && !done) {
                    done = true;
                }
                if (event.data == YT.PlayerState.ENDED) {
                    UserActionMovieService.next();
                }
                current_status = event.data;
            },

            next: function () {
                $('#state_playing').hide();
                if (view_status == 0) {
                    UserActionMovieService.executeAction();
                }
                view_status = 1;
            },
            closeWindow: function (msg_id){
                if(msg_id != null){
                    form = $('#message_' + msg_id).find(".executeMovieActionForm");
                    view_status = $('input[name=view_status]', form).val();

                    if (view_status == 0) {
                        UserActionMovieService.executeAction();
                    }
                    $('input[name=view_status]', form).val(1);
                }
            },
            onEndedVideoStream: function(target) {
                form = $(target).closest(".executeMovieActionForm");
                view_status = $('input[name=view_status]', form).val();

                if (view_status == 0) {
                    UserActionMovieService.executeAction();
                }
                $('input[name=view_status]', form).val(1);
            }
        };
    })();
}

function onYouTubeIframeAPIReady() {
    $('.moveiReload').hide();
    UserActionMovieService.initPlayer();
    if (!$('iframe#yt_player').length) {
        $('.moveiReload').show();
    }
}

$(document).ready(function () {
    UserActionMovieService.initYoutubeAPI();

    $(document).on('click', '#movieReload', function (event) {
        location.reload();
    });

    $(".jsVideoStream").bind('ended', function(){
        UserActionMovieService.onEndedVideoStream(this);
    });

    $(document).on('click', '.jsWatchVideoPopup', function() {
        var popuplink = $(this).data('link');
        Brandco.unit.windowOpenWrap(popuplink, '動画視聴', "960", "640");

    });
});
