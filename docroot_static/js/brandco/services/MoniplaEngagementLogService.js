if(typeof(MoniplaEngagementLogService) === 'undefined') {
    var MoniplaEngagementLogService = (function () {
        return {
                MoniplaEngagementLog: function(social_media_id,locate_id,value){
            var url = $('input[name="base_url"]').val() + 'messages/api_monipla_engagement_log.json';
            var user_id = $('input[name="user_id"]').val();
                    
        var param = {
            type: 'POST',
            data: {
                social_media_id:social_media_id,
                locate_id:locate_id,
                value:value, 
                user_id: user_id,
            },
            url: url,

            success: function () {
            }
        };

        Brandco.api.callAjaxWithParam(param, false, false);
    }}
    })();
}
    FB.Event.subscribe('edge.create', function(){
        MoniplaEngagementLogService.MoniplaEngagementLog(1,"fb-like",1);
    });

    FB.Event.subscribe('edge.remove', function(){
        MoniplaEngagementLogService.MoniplaEngagementLog(1,"fb-unlike",-1)
    });

    twttr.ready(function (twttr) {
        twttr.events.bind('follow', function(){
            MoniplaEngagementLogService.MoniplaEngagementLog(3,"twitter-follow",0)
        })
    });