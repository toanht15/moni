var FanRateService = (function() {
    return {
        fan_rate: function () {
            $('.starRating').raty({
                size: 20,
                path: $('base').data('static-href') + '/img/raty/',
                starOff: 'iconStar_0.png',
                starOn: 'iconStar_5.png',
                cancelOff : 'iconResetOff.png',
                cancelOn  : 'iconResetOn.png',
                cancel: true,
                cancelPlace: 'right',
                score: function() {
                    return $(this).attr('data-score');
                },
                click: function(score, evt) {
                    FanRateService.rate(score, this.id, $(this));
                }
            });
        },
        rate: function (score, brand_user_id, image_container) {
            if(score == null) {
                score = 0;
            }
            var url = $('*[name=update_rate]').attr('value');
            var csrf_token = $('input[name="csrf_token"]:first').val();
            var param = {
                data: {'csrf_token': csrf_token, 'rate': score, 'brand_user_id': brand_user_id},
                type: 'POST',
                url: url,
                success: function (json) {
                    if (json.result === "ok") {
                        image_container.closest('.userRating').html(json.html);
                        FanRateService.fan_rate();
                    } else {
                        alert('操作が失敗しました。');
                    }
                }
            };
            // 検索結果を返すタイミングではoverlayを止めない(GETで止める)
            Brandco.api.callAjaxWithParam(param);
        }
    }
})();
$(document).ready(function(){
    $(document).on('click', '.ratingBlock', function(){
        var image_container = $(this);
        var brand_user_id = image_container.attr('id');
        FanRateService.rate(-1, brand_user_id, image_container);
    });
});
