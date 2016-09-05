var TweetCampaignService = (function() {
    var cur_page = 1;
    return {
        changeActionPanelHiddenFlg: function() {
            var change_hidden_flg_url = $('input[name="tweet_action_panel_hidden_url"]').val(),
                tweet_panel_hidden_flg = $('.jsTweetPanelHiddenFlg:checked').val(),
                action_id = $('input[name="action_id"]').val(),
                param = {
                    url: change_hidden_flg_url,
                    data: {
                        action_id: action_id,
                        panel_hidden_flg: tweet_panel_hidden_flg
                    },
                    success: function(response) {
                        if (response && response.result == 'ok') {
                            // success
                        } else {
                            alert('エラーが発生しました、もう一度やり直してください')
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(param);
        },
        getTweetList: function(page) {
            if (page == null) page = cur_page;
            else cur_page = page;

            var tweet_status = new Array(),
                approval_status = new Array();

            $('.jsTweetStatus:checked').each(function() {
                tweet_status.push($(this).val());
            });
            $('.jsTweetApprovalStatus:checked').each(function() {
                approval_status.push($(this).val());
            });

            var get_tweet_list_url = $('input[name="tweet_list_url"]').val(),
                cp_id = $('input[name="cp_id"]').val(),
                action_id = $('input[name="action_id"]').val(),
                order_kind = $('.jsTweetOrderKind').val(),
                order_type = $('.jsTweetOrderType:checked').val(),
                param = {
                    url: get_tweet_list_url,
                    data: {
                        page: page,
                        cp_id: cp_id,
                        action_id: action_id,
                        tweet_status: tweet_status,
                        approval_status: approval_status,
                        order_kind: order_kind,
                        order_type: order_type
                    },
                    type: 'GET',
                    success: function(response) {
                        if (response && response.result == 'ok') {
                            $('.jsCampaignTweetList').html(response.html);
                        } else {
                            alert('エラーが発生しました、もう一度やり直してください')
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(param);
        },
        exportAPIUrl: function() {
            var cp_id = $('input[name="cp_id"]').val(),
                csrf_token = document.getElementsByName('csrf_token')[0].value,
                cp_action_type = $('input[name="cp_action_type"]').val(),
                params = {
                    url: 'admin-cp/api_export_api_url.json',
                    data: {
                        cp_id: cp_id,
                        csrf_token: csrf_token,
                        cp_action_type: cp_action_type
                    },
                    success: function(response) {
                        if (response && response.result == 'ok') {
                            $('.jsExportAPIBtn').html('<span class="large2">外部出力APIのURL作成</span>');
                            $('.jsExportAPIUrl').html('URL：' + response.data.api_url);
                        } else {
                            alert('エラーが発生しました、もう一度やり直してください');
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(params);
        },
        isChecked: function() {
            var checked_flg = false;
            $('.jsTweetCheck').each(function() {
                if ($(this).prop('checked') == true) {
                    checked_flg = true;
                }
            });

            if (checked_flg == false) {
                alert('チェックしてください。');
            }
            return checked_flg;
        }
    }
})();

$(document).ready(function() {
    // Panel hidden checking
    $(document).on('click', '.jsTweetPanelHiddenConfirm', function() {
        TweetCampaignService.changeActionPanelHiddenFlg();
    });

    // Search n Pager
    $(document).on('click', '.jsCpDataListPager', function() {
        TweetCampaignService.getTweetList($(this).data('page'));
    });

    $(document).on('click', '.jsTweetSearchBtn', function() {
        TweetCampaignService.getTweetList();
    });

    $(document).on('click', '.jsTweetSearchReset', function() {
        $('.jsTweetApprovalStatus[value="1"]').prop('checked', true);
        $('.jsTweetOrderType[value="1"]').prop('checked', true);
        $('.jsTweetOrderKind').val(1);

        TweetCampaignService.getTweetList();
    });

    // Export Content API Url
    $(document).on('click', '.jsExportAPI', function() {
        TweetCampaignService.exportAPIUrl();
    });

    // Tweet Checkbox
    $(document).on('change', '.jsTweetCheckAll', function() {
        $('.' + $(this).data('tweet_check_class')).prop('checked', this.checked);
    });

    $(document).on('change', '.jsTweetCheck', function() {
        if (!this.checked && $('.jsTweetCheckAll').is(':checked')) {
            $('.jsTweetCheckAll').prop('checked', false);
        }
    });

    // Tweet status checking
    $(document).on('change', '.jsMultiTweetApprovalStatus', function() {
        $('input[name="multi_tweet_approval_status"]').val($(this).val());
    });

    // Form submit
    $(document).on('click', ('.jsTweetActionFormSubmit1'), function() {
        if (TweetCampaignService.isChecked() == false) return false;

        var submit_msg = $('input[name="multi_tweet_approval_status_1"]:checked').val() == '1' ? '出力' : '非出力';
        if (confirm('チェック済みの投稿を' + submit_msg + 'にしますか？')) {
            document.tweet_action_form.submit();
        }
    });

    $(document).on('click', ('.jsTweetActionFormSubmit2'), function() {
        if (TweetCampaignService.isChecked() == false) return false;

        var submit_msg = $('input[name="multi_tweet_approval_status_2"]:checked').val() == '1' ? '出力' : '非出力';
        if (confirm('チェック済みの投稿を' + submit_msg + 'にしますか？')) {
            document.tweet_action_form.submit();
        }
    });

    // commone check toggle area
    $(document).on('change', '.jsCheckToggle', function() {
        var targetWrap = $(this).parents('.jsCheckToggleWrap')[0];
        $(targetWrap).find('.jsCheckToggleTarget').slideToggle(300);
    });
});