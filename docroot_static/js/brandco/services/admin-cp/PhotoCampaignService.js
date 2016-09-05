var PhotoCampaignService = (function() {
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
        changeActionPanelHiddenFlg: function() {
            var change_hidden_flg_url = $('input[name="action_panel_hidden_url"]').val(),
                photo_panel_hidden_flg = $('.jsPhotoPanelHiddenFlg:checked').val(),
                action_id = $('input[name="action_id"]').val(),
                param = {
                    url: change_hidden_flg_url,
                    data: {
                        action_id: action_id,
                        panel_hidden_flg: photo_panel_hidden_flg
                    },
                    success: function(response) {
                        if (response.result == 'ok') {
                            // success
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(param);
        },
        getPhotoList: function(page) {
            if (page == null) page = cur_page;
            else cur_page = page;

            var get_photo_list_url = $('input[name="photo_list_url"]').val(),
                cp_id = $('input[name="cp_id"]').val(),
                action_id = $('input[name="action_id"]').val(),
                approval_status= $('.jsPhotoApprovalStatus:checked').val(),
                order_kind = $('.jsPhotoOrderKind').val(),
                order_type = $('.jsPhotoOrderType:checked').val(),
                limit = $('.jsPhotoLimit').val(),
                param = {
                    url: get_photo_list_url,
                    data: {
                        page: page,
                        cp_id: cp_id,
                        action_id: action_id,
                        approval_status: approval_status,
                        order_kind: order_kind,
                        order_type: order_type,
                        limit: limit
                    },
                    type: 'GET',
                    success: function(response) {
                        if (response.result == 'ok') {
                            $('.jsCampaignPhotoList').html(response.html);
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(param);
        },
        getPhotoEditModal: function(photo_user_id, modal_id, page_type) {
            var get_photo_edit_modal_url = $('input[name="photo_edit_modal_url"]').val(),
                photo_action_id = $('input[name="action_id"]').val(),
                param = {
                    url: get_photo_edit_modal_url,
                    data: {
                        photo_action_id: photo_action_id,
                        photo_user_id: photo_user_id,
                        page_type: page_type
                    },
                    type: 'GET',
                    success: function(response) {
                        if (response.result == 'ok') {
                            $('.jsPhotoEditModal').html(response.html);

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
            $('.jsPhotoCheck').each(function() {
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
    /**
     * Multi photo
     */

    // Panel hidden checking
    $(document).on('click', '.jsPhotoPanelHiddenConfirm', function() {
        PhotoCampaignService.changeActionPanelHiddenFlg();
    });

    // Search n Pager
    $(document).on('click', '.jsCpDataListPager', function() {
        PhotoCampaignService.getPhotoList($(this).data('page'));
    });

    $(document).on('click', '.jsPhotoSearchBtn', function() {
        PhotoCampaignService.getPhotoList();
    });

    $(document).on('click', '.jsPhotoSearchReset', function() {
        $('.jsPhotoApprovalStatus[value="1"]').prop('checked', true);
        $('.jsPhotoOrderType[value="1"]').prop('checked', true);
        $('.jsPhotoOrderKind').val(1);

        PhotoCampaignService.getPhotoList();
    });

    // Export Content API Url
    $(document).on('click', '.jsExportAPI', function() {
        PhotoCampaignService.exportAPIUrl();
    });

    // Photo Checkbox
    $(document).on('change', '.jsPhotoCheckAll', function() {
        $('.' + $(this).data('photo_check_class')).prop('checked', this.checked);
    });

    $(document).on('change', '.jsPhotoCheck', function() {
        if (!this.checked && $('.jsPhotoCheckAll').is(':checked')) {
            $('.jsPhotoCheckAll').prop('checked', false);
        }
    });

    // Photo status checking
    $(document).on('change', '.jsMultiPhotoApprovalStatus', function() {
        var multi_photo_top_status = $(this).siblings('.jsMultiPhotoTopStatus');
        $('input[name="multi_photo_approval_status"]').val($(this).val());

        switch ($(this).val()) {
            case '2':
                multi_photo_top_status.prop('disabled', true);
                multi_photo_top_status.prop('checked', false);
                $('input[name="multi_photo_top_status"]').val('1');
                break;
            default:
                multi_photo_top_status.prop('disabled', false);
                break;
        }
    });

    $(document).on('change', '.jsMultiPhotoTopStatus', function() {
        var multi_photo_top_status = $(this).is(':checked') ? '0' : '1';
        $('input[name="multi_photo_top_status"]').val(multi_photo_top_status);
    });

    // Form submit
    $(document).on('click', ('.jsPhotoActionFormSubmit1'), function() {
        if (PhotoCampaignService.isChecked() == false) return false;

        if($('input[name="multi_photo_approval_status_1"]:checked').val() == '1') {
            var submit_msg = '承認';
        } else if($('input[name="multi_photo_approval_status_1"]:checked').val() == '2') {
            var submit_msg = '非承認に';
        } else {
            var submit_msg = '未承認に';
        }

        if (confirm('チェック済みの画像を' + submit_msg + 'しますか？')) {
            document.photo_action_form.submit();
        }
    });

    // Form submit
    $(document).on('click', ('.jsPhotoActionFormSubmit2'), function() {
        if (PhotoCampaignService.isChecked() == false) return false;

        if($('input[name="multi_photo_approval_status_2"]:checked').val() == '1') {
            var submit_msg = '承認';
        } else if($('input[name="multi_photo_approval_status_2"]:checked').val() == '2') {
            var submit_msg = '非承認に';
        } else {
            var submit_msg = '未承認に';
        }

        if (confirm('チェック済みの画像を' + submit_msg + 'しますか？')) {
            document.photo_action_form.submit();
        }
    });

    /**
     * Detail Photo
     */

    // Photo Modal
    $(document).on('click', '.jsOpenPhotoModal', function() {
        PhotoCampaignService.getPhotoEditModal($(this).data('photo_user_id'), $(this).attr('href'), $(this).data('page_type'));
        return false;
    });

    $(document).on('click', '.jsPrevPhotoEditModal', function() {
        PhotoCampaignService.getPhotoEditModal($(this).data('photo_user_id'), $(this).attr('href'));
        return false;
    });

    $(document).on('click', '.jsNextPhotoEditModal', function() {
        PhotoCampaignService.getPhotoEditModal($(this).data('photo_user_id'), $(this).attr('href'));
        return false;
    });

    // Photo status checking
    $(document).on('change', '.jsDtlPhotoApprovalStt', function() {
        switch ($(this).val()) {
            case '2':
                $('.jsDtlPhotoTopStt').prop('disabled', true);
                $('.jsDtlPhotoTopStt').prop('checked', false);
                break;
            default:
                $('.jsDtlPhotoTopStt').prop('disabled', false);
                if ($('.jsDtlPhotoTopStt').data('default_value') == '0') {
                    $('.jsDtlPhotoTopStt').prop('checked', true);
                }
                break;
        }
    });
});