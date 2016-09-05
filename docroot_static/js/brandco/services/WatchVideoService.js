var WatchVideoService = (function(){
    return{
        checkCloseButtonStatus: function (){
            var video = document.getElementById('upload_url'),
                current_time = video.currentTime,
                duration     = video.duration;
            if(current_time >= (duration*70/100)){
                $('.jsCloseWindow').html("<a>閉じる</a>");
            }else{
                $('.jsCloseWindow').html("<span>閉じる</span>");
            }
        }
    };
})();
$(document).ready(function() {
    var vid = document.getElementById('upload_url');
    vid.addEventListener( "loadedmetadata", function (e) {
        var cur_width = this.videoWidth,
            cur_height = this.videoHeight;

        if (cur_width/cur_height < 16/9) {
            var resize_width = 960,
                resize_height = 540 * (cur_width / cur_height) + 100 + 50; // close button: 80px, window title bar: 50 px

            window.resizeTo(resize_width.toString(), resize_height.toString());
        }
    });

    $(document).on('click touchstart', '.jsCloseWindow a', function() {
        if(window.opener.UserActionMovieService){
            var msg_id = $('input[name="msg_id"]').val();

            window.opener.UserActionMovieService.closeWindow(msg_id);
        }

        window.close();
    });

    $(document).on('click touchstart', '.jsVideoSpeedSelector', function() {
        var video = document.getElementById('upload_url'),
            video_speed = $('.jsVideoSpeedSelector:checked').val();
        video.playbackRate = +video_speed;
    });

    $("#upload_url").bind('ended', function(){
        WatchVideoService.checkCloseButtonStatus();
    });
    $("#upload_url").bind('seeked', function(){
        WatchVideoService.checkCloseButtonStatus();
    });
    $("#upload_url").bind('timeupdate', function(){
        WatchVideoService.checkCloseButtonStatus();
    });
});

