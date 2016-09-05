var FanListDownloadService = (function(){
    return {
        getSearchConditionViewCol: function () {
            var cp_id = $('[name="cp_id"]').attr('value');
            var data = "cpdl_flg=" + true + "&csrf_token=" + document.getElementsByName("csrf_token")[0].value;
            if(cp_id){
                data += "&cp_id=" + cp_id;
            }
            var param = {
                data: data,
                type: 'POST',
                url : 'admin-fan/api_get_search_condition_view_col.json',
                success: function (data) {
                    $('#totalCount').html(data.data.target_count);
                }
            };
            Brandco.api.callAjaxWithParam(param, false);
        },
        pushCountFanCondition: function (input) {
            var form = input.closest('form');
            var data = form.serialize();
            data += "&csrf_token=" + document.getElementsByName("csrf_token")[0].value;
            var url = $('input[name="search_url"]').attr('value');
            var cp_id = $('input[name="cp_id"]').attr('value');
            if (cp_id && typeof cp_id != 'undefined') {
                data += "&cp_id="+cp_id;
            }
            var param = {
                data: data + '&search_no=1&nullable=true&isFanListDownload=true',
                type: 'POST',
                url: url,
                success: function(json) {
                    if (json.result === "ok") {
                        $('.customaudienceRefinement').find('.iconError1').remove();
                        FanListDownloadService.getSearchConditionViewCol();
                        FanListDownloadService.setHighlight(input);
                    } else {
                        $.unblockUI();
                        var search_err = 0;
                        $('.customaudienceRefinement').find('.iconError1').remove();
                        $.each(json.errors ,function(i, value) {
                            if(i.match(/^searchError\//)) {
                                if ($(input.closest('form').find('.settingDetail')).length > 0) {
                                    $(input.closest('form').find('.settingDetail')[0]).before('<span class="iconError1">' + value + '</span>');
                                } else if ($(input.closest('form').find('.status')).length > 0) {
                                    $(input.closest('form').find('.status')[0]).before('<span class="iconError1">' + value + '</span>');
                                }
                                search_err = 1;
                            }
                        });
                        if (!search_err) {
                            alert("操作が失敗しました");
                        }
                    }
                }
            };
            // 検索結果を返すタイミングではoverlayを止めない(GETで止める)
            Brandco.api.callAjaxWithParam(param, true, false);
        },
        setHighlight: function (input) {
            if (input == null) {
                $('#searchInputList').find('input[type=text], textarea').each(function() {
                    if ($(this).val()) {
                        $(this).parents('.jsSearchInputBlock').addClass('checked');
                    }
                });
                $('#searchInputList').find('input[type=checkbox]').each(function() {
                    if ($(this).is(':checked')) {
                        $(this).parents('.jsSearchInputBlock').addClass('checked');
                    }
                });
            } else {
                var target = $(input).parents('.jsSearchInputBlock');
                $(target).removeClass('checked');
                var checked = 0;
                $(target).find('input[type=text], textarea').each(function() {
                    if ($(this).val()) {
                        $(this).parents('.jsSearchInputBlock').addClass('checked');
                        checked = 1;
                        return false;
                    }
                });
                if (checked != 1) {
                    $(target).find('input[type=checkbox]').each(function() {
                        if ($(this).is(':checked')) {
                            $(this).parents('.jsSearchInputBlock').addClass('checked');
                            return false;
                        }
                    });
                }
            }
        },
        setGetParamsString: function () {
            var paramsStr = '';
            var isFirstParam = true;
            $('#fileListSelector').find('input').each(function() {
                if ($(this).is(':checked')) {
                    if (isFirstParam == false) {
                        paramsStr = paramsStr + '&file_ids[]=' + $(this).val();
                    } else {
                        isFirstParam = false;
                        paramsStr = paramsStr + '?file_ids[]=' + $(this).val();
                    }
                }
            });
            $('#submit_download_button').attr("data-params", paramsStr);
        },
        setOptionDownloadWinnerList: function (target) {
            var cp_action_id = $(target).data('cp_action_id');
            if (typeof cp_action_id === 'undefined') {
                return;
            }

            if ($(target).is(':checked')) {
                $('.jsWinnerListFile' + cp_action_id).show();
                $('#jsDownloadWinnerList' + cp_action_id).prop('disabled', false);
                $('#jsDownloadWinnerList' + cp_action_id).prop('checked', true);
                FanListDownloadService.setGetParamsString()
            } else {
                $('.jsWinnerListFile' + cp_action_id).hide();
                $('#jsDownloadWinnerList' + cp_action_id).prop('disabled', true);
                $('#jsDownloadWinnerList' + cp_action_id).prop('checked', false);
                FanListDownloadService.setGetParamsString();
            }
        }
    }
})();

$(document).ready(function() {

    if(!$('input[name="personal_pc"]').val()) {
        $(".jsDate").datepicker();
    }
    $('#customaudiencePreview').containedStickyScroll({
        unstick: false
    });

    FanListDownloadService.getSearchConditionViewCol();
    FanListDownloadService.setHighlight(null);
    FanListDownloadService.setGetParamsString();

    $('.jsParticipateTarget').each(function () {
        FanListDownloadService.setOptionDownloadWinnerList(this);
    });

    $('.jsParticipateTarget').on('change', function () {
        FanListDownloadService.setOptionDownloadWinnerList(this);
    });

    $('#searchInputList').find('.close').each(function() {
        $(this).removeClass('close');
    });

    if (typeof $('[name="cp_id"]').val() != "undefined" && typeof $('[name="open_cp_tab"]').val() != "undefined") {
        $('.customaudienceRefinement:last').find('.close').removeClass('close');
    }

    //$(document).on('click', 'a[data-search_type]', function () {
    //    FanListDownloadService.pushCountFanCondition($(this).attr('data-search_type'));
    //});

    $('input').not('.connect_social_class,.jsCheckToggle,#input3,#input4,[name="file_selector"],#selectFileListSelectAll').change(function() {
        FanListDownloadService.pushCountFanCondition($(this));
    });

    $('.jsReplaceLbComma').on('change', function() {
        FanListDownloadService.pushCountFanCondition($(this));
    });

    $('.jsCheckToggle').change(function() {
        if ($(this).is(':checked')) {
            $(this).closest('form').find('.jsCheckToggleTarget').find('input').each(function() {
                if ($(this).val()) {
                    FanListDownloadService.pushCountFanCondition($(this));
                    return false;
                }
            });
        } else {
            $(this).closest('form').find('.jsCheckToggleTarget').find('input').val('');
            FanListDownloadService.pushCountFanCondition($(this));
        }
        $(this).closest('form').find('.jsCheckToggleTarget').toggle();
    });

    $('.connect_social_class').change(function() {
        if ($(this).is(':checked')) {
            $(this).closest('form').find('[name^="search_friend_count_"]').removeAttr('disabled');
        } else {
            $(this).closest('form').find('[name^="search_friend_count_"]').val('');
            $(this).closest('form').find('[name^="search_friend_count_"]').attr('disabled','disabled');
        }
        FanListDownloadService.pushCountFanCondition($(this));
    });

    if ($('input[name="is_cp_data_download_mode"]').val()) {
        $('#showCampaignList').hide();
    } else {
        $('select[name="campaignList"]').change(function () {
            Brandco.helper.brandcoBlockUI();
            window.location.href = $(this).data("base-url") + '?cpId=' + $(this).val();
        });
    }

    $('.connect_social_class').each(function() {
        if ($(this).is(':checked')) {
            $(this).closest('form').find('[name^="search_friend_count_"]').removeAttr('disabled');
        }
    });

    $('.jsCheckToggle').each(function() {
        if ($(this).is(':checked')) {
            $(this).closest('form').find('.jsCheckToggleTarget').show();
        }
    });

    $('#clear_button').click(function() {
        Brandco.unit.openModal("#modal2");
    });

    $('#editFinish').click(function() {
        $(this).closest('form').find('input').removeAttr('disabled');
        $(this).closest('form').find('textArea').removeAttr('disabled');
        $('#nameDt').addClass('require1');
        $(this).hide();
        $('#editBegin').show();
    });

    $('[name="file_selector"]').change(function() {
        FanListDownloadService.setGetParamsString();
    });

    $('#selectFileListSelectAll').change(function() {
        if ($(this).is(':checked')) {
            $('#fileListSelector').find('input').each(function() {
                $(this).prop('checked', true);
            });
        } else {
            $('#fileListSelector').find('input').each(function() {
                $(this).prop('checked', false);
            });
        }
        FanListDownloadService.setGetParamsString();
    });

    $('#submit_download_button').click(function() {
        var target = $('#submit_download_button');
        window.location.href = $(this).data('url') + $(this).data('params');
        target.replaceWith('<span>ダウンロード中…</span>');
        $('#download_attention').show();
    });
});