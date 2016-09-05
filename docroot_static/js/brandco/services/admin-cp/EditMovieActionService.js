var newPlayer;
var player;
var current_status = -1;
var done = false;
var form = $("#actionForm");
var movie_object_id = $('input[name=movie_object_id_url]', form).val();
var yt_width = $(".message").width();

var EditMovieActionService = (function(){
    return{
        onPlayerReady: function (event){
            if (event) {
                player = event.target;
                event.data = player.getPlayerState();
                EditMovieActionService.onPlayerStateChange(event);
            }
        },
        onPlayerStateChange: function (event){
            if (event.data == YT.PlayerState.PLAYING && !done) {
                done = true;
            }
            if (event.data == YT.PlayerState.ENDED) {
                $('#state_playing').hide();
            }
            current_status = event.data;
        },
        initYoutubeAPI: function (){
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
                    onReady: EditMovieActionService.onPlayerReady,
                    onStateChange: EditMovieActionService.onPlayerStateChange
                }
            });
        },

        previewSetAttr: function(){
            if($('input[name=module_movie]:checked', form).val() == 1) {
                    $('#yt_player').show();
                    $('#upload_url').hide();
                    $('#jsButtonPreview').hide();
                    $('.jsMovieText').html('視聴後に次のステップへ進みます。');
;
                    $( "input[name='movie_object_id_url']" ).prop({disabled: true});
                    $( "input[name='video_file']" ).prop({disabled: true});
                    $( "input[name='upload_movie']" ).prop({disabled: true});
                    $( "input[name='movie_upload_url']" ).prop({disabled: true});
                    $( "#movie_select" ).prop({disabled: false});
                    $( "input[name='popup_view_flg']" ).prop({disabled: true});
                } else if ($('input[name=module_movie]:checked', form).val() == 2) {
                    $('#yt_player').show();
                    $('#upload_url').hide();
                    $('#jsButtonPreview').hide();
                    $('.jsMovieText').html('視聴後に次のステップへ進みます。');

                    $( "input[name='movie_object_id_url']" ).prop({disabled: false});
                    $( "input[name='video_file']" ).prop({disabled: true});
                    $( "input[name='upload_movie']" ).prop({disabled: true});
                    $( "input[name='movie_upload_url']" ).prop({disabled: true});
                    $( "#movie_select" ).prop({disabled: true});
                    $( "input[name='popup_view_flg']" ).prop({disabled: true});
                } else if ($('input[name=module_movie]:checked', form).val() == 3){
                    if($('.jsViewPopUp').is(':checked')){
                        $('#yt_player').hide();
                        $('#upload_url').hide();
                        $('#jsButtonPreview').show();
                        $('.jsMovieText').html('<small class="supplement1">※別ウィンドウで再生します。<br/>視聴後に次のステップへ進みます。');
                    } else {
                        $('#yt_player').hide();
                        $('#upload_url').show();
                        $('#jsButtonPreview').hide();
                        $('.jsMovieText').html('動画を再生してください。<br/>視聴後に次のステップへ進みます。');
                    }
                    var videoFile = $( "input[name='movie_upload_url']" ).val();
                    $('#upload_url').attr('src', videoFile);
                    $( "input[name='movie_object_id_url']" ).prop({disabled: true});
                    $( "#movie_select" ).prop({disabled: true});
                    $( "input[name='popup_view_flg']" ).prop({disabled: false});
                    if($( "input[name='is_status_fixed']").val() != 1){
                        $( "input[name='upload_movie']" ).prop({disabled: false});
                        if($( "input[name='upload_movie']:checked", form).val() == 0){
                            $( "input[name='video_file']" ).prop({disabled: false});
                            $( "input[name='movie_upload_url']" ).prop({disabled: true});
                        } else {
                            $( "input[name='video_file']" ).prop({disabled: true});
                            $( "input[name='movie_upload_url']" ).prop({disabled: false});
                            
                        }
                    } else {
                        $( "input[name='upload_movie']" ).prop({disabled: true});
                        $( "input[name='video_file']" ).prop({disabled: true});
                        $( "input[name='popup_view_flg']" ).prop({disabled: true});
                    }
                    
                }
        },
        initPreview: function() {
            EditMovieActionService.previewSetAttr();
        },
        handlePopupResultMovie: function(result) {
            ext = result.substr(-3);
            if(ext != 'mp4'){
                alert("must be a mp4 file");
                return;
            }
            var movie_url = $('#movie_url_uploaded'),
                movie_preview = $('#upload_url');

            $('#video_file').attr('disabled', 'disabled');
            movie_url.parents().find('.labelTitleMovieUpload').prop('checked', true);
            $('#module_movie_3').prop('checked', true);
            EditMovieActionService.previewSetAttr();
            $( "input[name='video_file']" ).prop({disabled: true});
            if (result != '') {
                movie_url.val(result);
                movie_preview.attr('src', result);
                $('#popup_video_link').attr('data-link','http://brandcotest.com/chuongdeptrai/video/watch_video?video_url='+result);
                movie_preview.parent().show();

            }
        },
        initActionPreview: function() {
            $(document).on('change', 'input[name=module_movie]', function() {
                EditMovieActionService.previewSetAttr();
                
            });
            $(document).on('change', 'input[name=upload_movie]', function() {
                EditMovieActionService.previewSetAttr();
                
            });
            $(document).on('change', '.jsViewPopUp', function() {
                if (this.checked) {
                    $('#jsButtonPreview').show();
                    $('#yt_player').hide();
                    $('#upload_url').hide();
                    $('.jsMovieText').html('<small class="supplement1">※別ウィンドウで再生します。<br/>視聴後に次のステップへ進みます。</small>');
                } else {
                    $('#jsButtonPreview').hide();
                    if($('input[name=module_movie]:checked', form).val() == 3) {
                        $('#upload_url').show();
                        $('#yt_player').hide();
                        $('.jsMovieText').html('動画を再生してください。<br/>視聴後に次のステップへ進みます。');
                    } else {
                        $('#yt_player').show();
                        $('#upload_url').hide();
                        $('.jsMovieText').html('視聴後に次のステップへ進みます。');
                    }
                    
                }
            });
        }
    };
})();



function onYouTubeIframeAPIReady() {
    EditMovieActionService.initPlayer();
}

$(document).ready(function(){


    EditMovieActionService.initPreview();
    EditMovieActionService.initActionPreview();
    EditMovieActionService.initYoutubeAPI();

    $('.jsModulePreviewSwitch').click(function(){
        yt_width = $(".message").width();
        player.setSize(yt_width, yt_width*9/16);
        return false;
    });

    $('.labelTitleWin').change(function(){
        $(this).parents('li').find('.actionMovie').removeAttr('disabled');
    });

    if (!$('#text_area').val()) {
        $("#textPreview").hide();
    };
    $('#text_area').on('input', function() {
        $("#textPreview").show();
        var temp = $(this).val().replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\n/g, "<br />");
        $("#textPreview").html(temp);
        if (!$(this).val()) {
            $("#textPreview").hide();
        }
    });

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker({
        minDate: new Date()
    });
});
