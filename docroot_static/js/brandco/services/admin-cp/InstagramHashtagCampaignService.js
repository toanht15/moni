var InstagramHashtagCampaignService = (function() {
    var cur_page = 1;
    return {
        exportAPIUrl: function() {
            var cp_id = $('input[name="cp_id"]').val(),
                csrf_token = document.getElementsByName('csrf_token')[0].value,
                cp_action_type = $('input[name="cp_action_type"]').val(),
                params = {
                    url: 'admin-cp/api_export_api_url.json',
                    data: {
                        cp_id: cp_id,
                        cp_action_type: cp_action_type,
                        csrf_token: csrf_token
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
        changeActionApprovalFlg: function() {
            var instagram_hashtag_change_approval_url = $('input[name="instagram_hashtag_change_approval_url"]').val(),
                instagram_hashtag_approval_flg = $('.jsInstagramHashtagApprovalFlg:checked').val(),
                action_id = $('input[name="action_id"]').val(),
                param = {
                    url: instagram_hashtag_change_approval_url,
                    data: {
                        action_id: action_id,
                        approval_flg: instagram_hashtag_approval_flg
                    },
                    success: function(response) {
                        if (response.result == 'ok') {
                            // success
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(param);
        },
        getInstagramHashtagList: function(page) {
            if (page == null) page = cur_page;
            else cur_page = page;

            var get_instagram_hashtag_list_url = $('input[name="instagram_hashtag_list_url"]').val(),
                cp_id = $('input[name="cp_id"]').val(),
                action_id = $('input[name="action_id"]').val(),
                approval_status= $('.jsInstagramHashtagApprovalStatus:checked').val(),
                duplicate_flg = $('.jsInstagramHashtagDuplicateFlg:checked').val(),
                reverse_post_time_flg = $('.jsInstagramHashtagReversePostTimeFlg:checked').val(),
                order_kind = $('.jsInstagramHashtagOrderKind').val(),
                order_type = $('.jsInstagramHashtagOrderType:checked').val(),
                param = {
                    url: get_instagram_hashtag_list_url,
                    data: {
                        page: page,
                        cp_id: cp_id,
                        action_id: action_id,
                        approval_status: approval_status,
                        duplicate_flg: duplicate_flg,
                        reverse_post_time_flg: reverse_post_time_flg,
                        order_kind: order_kind,
                        order_type: order_type
                    },
                    type: 'GET',
                    success: function(response) {
                        if (response.result == 'ok') {
                            $('.jsCampaignInstagramHashtagList').html(response.html);
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(param);
        },
        getInstagramHashtagEditModal: function(instagram_hashtag_user_post_id, modal_id, page_type) {
            var get_instagram_hashtag_edit_modal_url = $('input[name="instagram_hashtag_edit_modal_url"]').val(),
                instagram_hashtag_action_id = $('input[name="action_id"]').val(),
                param = {
                    url: get_instagram_hashtag_edit_modal_url,
                    data: {
                        instagram_hashtag_action_id: instagram_hashtag_action_id,
                        instagram_hashtag_user_post_id: instagram_hashtag_user_post_id,
                        page_type: page_type
                    },
                    type: 'GET',
                    success: function(response) {
                        if (response.result == 'ok') {
                            $('.jsInstagramHashtagEditModal').html(response.html);

                            $(modal_id).height($('body').height()).fadeIn(300, function(){
                                $(this).find('.jsModalCont').css({
                                    display: 'block',
                                    opacity: 0,
                                    top: $(window).scrollTop()
                                }).animate({
                                        top: $(window).scrollTop() + 30,
                                        opacity: 1
                                    }, 300, function() {
                                        var modal_height = $(modal_id).find('.jsModalCont').position().top + $(modal_id).find('.jsModalCont').outerHeight(true);
                                        var body_height = $('body').outerHeight(true);
                                        var default_height = $('body').data('prev_height');

                                        if (default_height === undefined || default_height == '') {
                                            $('body').data('prev_height', body_height);
                                            default_height = body_height;
                                        }

                                        if (body_height >= default_height && body_height < modal_height) {
                                            $('body').height(modal_height + 10);
                                            $(modal_id).height($('body').height());
                                        }
                                    });
                            });
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(param);
        },
        isChecked: function() {
            var checked_flg = false;
            $('.jsInstagramHashtagCheck').each(function() {
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

    $(document).on('click', '.jsInstagramHashtagApprovalFlg', function() {
        InstagramHashtagCampaignService.changeActionApprovalFlg();
    });

    $(document).on('click', '.jsCpDataListPager', function() {
        InstagramHashtagCampaignService.getInstagramHashtagList($(this).data('page'));
    });

    $(document).on('click', '.jsInstagramHashtagSearchBtn', function() {
        InstagramHashtagCampaignService.getInstagramHashtagList();
    });

    $(document).on('click', '.jsInstagramHashtagSearchReset', function() {
        $('.jsInstagramHashtagApprovalStatus[value="1"]').prop('checked', true);
        $('.jsInstagramHashtagOrderType[value="1"]').prop('checked', true);
        $('.jsInstagramHashtagOrderKind').val(1);
        $('.jsInstagramHashtagDuplicateFlg').val(1);
        $('.jsInstagramHashtagReversePostTimeFlg').val(1);

        InstagramHashtagCampaignService.getInstagramHashtagList();
    });

    $(document).on('change', '.jsInstagramHashtagCheckAll', function() {
        $('.' + $(this).data('instagram_hashtag_check_class')).prop('checked', this.checked);
    });

    $(document).on('change', '.jsInstagramHashtagCheck', function() {
        if (!this.checked && $('.jsInstagramHashtagCheckAll').is(':checked')) {
            $('.jsInstagramHashtagCheckAll').prop('checked', false);
        }
    });

    $(document).on('change', '.jsMultiInstagramHashtagApprovalStatus', function() {
        var multi_instagram_hashtag_top_status = $(this).siblings('.jsMultiInstagramHashtagTopStatus');
        $('input[name="multi_instagram_hashtag_approval_status"]').val($(this).val());

        switch ($(this).val()) {
            case '2':
                multi_instagram_hashtag_top_status.prop('disabled', true);
                multi_instagram_hashtag_top_status.prop('checked', false);
                $('input[name="multi_instagram_hashtag_top_status"]').val('1');
                break;
            default:
                multi_instagram_hashtag_top_status.prop('disabled', false);
                break;
        }
    });

    $(document).on('change', '.jsMultiInstagramHashtagTopStatus', function() {
        var multi_instagram_hashtag_top_status = $(this).is(':checked') ? '0' : '1';
        $('input[name="multi_instagram_hashtag_top_status"]').val(multi_instagram_hashtag_top_status);
    });

    // Form submit
    $(document).on('click', ('.jsInstagramHashtagActionFormSubmit1'), function() {
        if (InstagramHashtagCampaignService.isChecked() == false) return false;

        if($('input[name="multi_instagram_hashtag_approval_status_1"]:checked').val() == '1') {
            var submit_msg = '承認';
        } else if($('input[name="multi_instagram_hashtag_approval_status_1"]:checked').val() == '2') {
            var submit_msg = '非承認に';
        } else {
            var submit_msg = '未承認に';
        }

        if (confirm('チェック済みの画像を' + submit_msg + 'しますか？')) {
            document.instagram_hashtag_action_form.submit();
        }
    });

    // Form submit
    $(document).on('click', ('.jsInstagramHashtagActionFormSubmit2'), function() {
        if (InstagramHashtagCampaignService.isChecked() == false) return false;

        if($('input[name="multi_instagram_hashtag_approval_status_2"]:checked').val() == '1') {
            var submit_msg = '承認';
        } else if($('input[name="multi_instagram_hashtag_approval_status_2"]:checked').val() == '2') {
            var submit_msg = '非承認に';
        } else {
            var submit_msg = '未承認に';
        }

        if (confirm('チェック済みの画像を' + submit_msg + 'しますか？')) {
            document.instagram_hashtag_action_form.submit();
        }
    });

    /**
     * Detail InstagramHashtag
     */

    $(document).on('click', '.jsOpenInstagramHashtagModal', function() {
        InstagramHashtagCampaignService.getInstagramHashtagEditModal($(this).data('instagram_hashtag_user_post_id'), $(this).attr('href'), $(this).data('page_type'));
        return false;
    });

    $(document).on('click', '.jsPrevInstagramHashtagEditModal', function() {
        InstagramHashtagCampaignService.getInstagramHashtagEditModal($(this).data('instagram_hashtag_user_post_id'), $(this).attr('href'));
        return false;
    });

    $(document).on('click', '.jsNextInstagramHashtagEditModal', function() {
        InstagramHashtagCampaignService.getInstagramHashtagEditModal($(this).data('instagram_hashtag_user_post_id'), $(this).attr('href'));
        return false;
    });

    $(document).on('change', '.jsDtlInstagramHashtagApprovalStt', function() {
        switch ($(this).val()) {
            case '2':
                $('.jsDtlInstagramHashtagTopStt').prop('disabled', true);
                $('.jsDtlInstagramHashtagTopStt').prop('checked', false);
                break;
            default:
                $('.jsDtlInstagramHashtagTopStt').prop('disabled', false);
                if ($('.jsDtlInstagramHashtagTopStt').data('default_value') == '0') {
                    $('.jsDtlInstagramhashtagTopStt').prop('checked', true);
                }
                break;
        }
    });

    // Export Content API Url
    $(document).on('click', '.jsExportAPI', function() {
        InstagramHashtagCampaignService.exportAPIUrl();
    });

});
