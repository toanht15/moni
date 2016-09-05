var AdsListService = (function() {
    return {
        loadAdsAudienceList: function(page) {

            var url = $('input[name=load_audience_url]').val();
            var target_account_ids = $('input[name^=ads_account]:checked').serialize();

            params = {
                data: {
                    page_no: page,
                    target_account_ids: target_account_ids
                },
                type: 'GET',
                url: url,
                success: function(response) {
                    if (response.result == 'ok') {
                        $('.jsListAudience').html(response.html);
                    }
                }
            };
            Brandco.api.callAjaxWithParam(params);
        },
        toggleAnimation: function (target) {
            $('.jsAreaToggleTarget').not(target).fadeOut(200, function() {
                setTimeout(function(){

                },300)
            });
            if(target.is(':hidden')) {
                target.fadeIn(200);
            } else {
                target.fadeOut(200);
            }
        },
        deleteToggle: function (target) {
            target.stop(true, true).fadeToggle(200,function() {
                setTimeout(function(){
                    AdsListService.removeCheckbox();
                },300)
            });
        },
        removeCheckbox: function() {
            var target_account_ids = jQuery.parseJSON($('input[name=target_account_ids]').val());
            $('input[name^=ads_account]:checked').each(function(){
                if(jQuery.inArray( $(this).val(), target_account_ids ) == -1) {
                    $(this).removeAttr('checked');
                }
            });
        },
        updateAutoSendTarget: function(target) {

            var url = $('input[name=update_send_target_url]').val();

            var send_target_flg = 0;

            if ($(target).closest('a').hasClass('switch off')) {
                send_target_flg = 1;
            }

            var relation_id = $(target).closest('a').data('relation_id');
            var csrf_token = document.getElementsByName("csrf_token")[0].value;

            params = {
                data: {
                    send_target_flg: send_target_flg,
                    relation_id: relation_id,
                    csrf_token: csrf_token
                },
                type: 'POST',
                url: url,
                success: function(response) {

                    if (response.result == 'ng') {
                        if($(target).closest('a').hasClass('switch off')) {
                            $(target).closest('a').attr('class','switch on');
                        } else {
                            $(target).closest('a').attr('class','switch off');
                        }

                        alert('操作が失敗しました！');
                    }

                },
                error: function ()  {

                    if($(target).closest('a').hasClass('switch off')) {
                        $(target).closest('a').attr('class','switch on');
                    } else {
                        $(target).closest('a').attr('class','switch off');
                    }

                    alert('操作が失敗しました！');
                }
            };
            Brandco.api.callAjaxWithParam(params);
        },
        sendTargetUser: function() {

            var url = $('input[name=send_target_url]').val();
            var cur_page = $('.jsListPager span').text();
            var target_account_ids = $('input[name^=ads_account]:checked').serialize();
            var target_relation_ids = $('input[name^=target_relation_ids]:checked').serialize();
            var csrf_token = document.getElementsByName("csrf_token")[0].value;

            params = {
                data: {
                    page_no: cur_page,
                    target_account_ids: target_account_ids,
                    target_relation_ids: target_relation_ids,
                    csrf_token: csrf_token
                },
                type: 'POST',
                url: url,
                success: function(response) {
                    if (response.result == 'ok') {
                        $('.jsListAudience').html(response.html);
                        Brandco.unit.showNoticeBar($('#send_success_target'));
                    } else {
                        alert('操作が失敗しました！');
                    }
                },
                error: function (r)  {
                    alert('操作が失敗しました！');
                }
            };

            Brandco.api.callAjaxWithParam(params);
        },
        deleteAttention: function () {
            $('p.attention1').remove();
        }
    }
})();
$(document).ready(function() {

    //Load Audience List
    AdsListService.loadAdsAudienceList(1);

    $('.jsSendTarget').click(function(){
        if ($('input[name^=target_relation_ids]:checked').length == 0) {
            alert('オーディエンスを選択してください!');
        } else {
            AdsListService.sendTargetUser();
        }
        return false;
    });

    $(document).on('click', '.jsAreaToggle', function () {
        if ($(this).hasClass('iconBtnSort') || $(this).hasClass('btnArrowB1')) {
            AdsListService.deleteAttention();
            AdsListService.toggleAnimation($(this).parents('.jsAreaToggleWrap').find('.jsAreaToggleTarget'));
        }
    });

    $(document).on('click', '.boxCloseBtn', function () {
        AdsListService.deleteAttention();
        AdsListService.deleteToggle($(this).parents('.jsAreaToggleWrap').find('.jsAreaToggleTarget'));
    });

    $(document).on('click', '.jsClearAdsAccountFilter', function () {
        $('input[name^=ads_account]').removeAttr('checked');
        AdsListService.deleteAttention();
        AdsListService.loadAdsAudienceList(1);
    });

    $(document).on('click', '.jsSearchAdsAccountFilter', function () {

        if($('input[name^=ads_account]:checked').length == 0 ) {
            $(this).parents('.jsAreaToggleTarget').find('.boxCloseBtn').after('<p class="attention1">1つ以上選択してください。</p>');
        } else {
            AdsListService.deleteAttention();
            AdsListService.loadAdsAudienceList(1);
        }
    });

    $(document).on('click', 'a[data-page]', function () {
        AdsListService.loadAdsAudienceList($(this).attr('data-page'));
    });

    $(document).on('click','.jsCopyCondition', function () {
        $('#copy_message').html($(this).data('name') + 'をコピーしますか？');
        $('#copy_confirm').attr('data-url',$(this).data('url'));
        Brandco.unit.openModal("#ConfirmCopyModal");
    });

    $(document).on('click','#copy_confirm', function() {
        if ($(this).attr('data-submitted') != '1') {
            $(this).attr('data-submitted', '1');
            window.location.href = $(this).attr('data-url');
        }
    });

    $(document).on('click','.switchInner', function() {
        AdsListService.updateAutoSendTarget(this);
    });
});