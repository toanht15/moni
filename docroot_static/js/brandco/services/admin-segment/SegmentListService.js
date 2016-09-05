var SegmentListService = (function () {
    return {
        dojsSegmentToggle: function (trigger) {
            var target = $(trigger).parents('.jsSegmentToggleWrap').find('.jsSegmentToggleTarget');

            if ($(trigger).hasClass('close')) {
                target.slideDown(200, function () {
                    $(trigger).removeClass('close');
                });
            } else {
                target.slideUp(200, function () {
                    $(trigger).addClass('close');
                });
            }
        },
        dojsSegmentCheck: function(target) {
            var anchor = $(target).parents('.jsSegmentToggleWrap');
            anchor.find('input').prop('checked', $(target).prop('checked'));
            anchor.find('.jsSegmentToggleTarget').stop(true, true).slideDown(200);
            anchor.find('.jsSegmentToggle').removeClass('close').slideDown(200);
        },
        isCurrentSelector: function(target) {
            if ($(target).find('span.current').length != 0) {
                return true;
            }

            return false;
        },
        fadeToggleTarget: function(target) {
            $(target).closest('.jsAreaToggleTarget').stop(true, true).fadeToggle(200);
        },
        loadSegmentContainerList: function (target) {
            var s_type = $(target).attr('data-segment_type'),
                params = {
                    data: {
                        s_type: s_type
                    },
                    url: 'admin-segment/api_load_segment_container_list.json',
                    type: 'GET',
                    success: function (response) {
                        if (response.result == 'ok') {
                            $('.jsSSelector').each(function () {
                                var content_html = $(this).children().html();

                                if ($(this).is(target)) {
                                    $(this).html('<span class="current">' + content_html + '</span>');
                                } else {
                                    $(this).html('<a>' + content_html + '</a>');
                                }
                            });

                            $('.jsSContainerList').replaceWith(response.html);
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(params);

        },
        archiveSegment: function (target) {
            var target_sid = $(target).attr('data-segment_id'),
                segment_status = $(target).attr('data-segment_status'),
                csrf_token = document.getElementsByName("csrf_token")[0].value,
                params = {
                    data: {
                        csrf_token: csrf_token,
                        target_sid: target_sid
                    },
                    url: 'admin-segment/api_archive_segment.json',
                    type: 'POST',
                    success: function (response) {
                        if (response.result == 'ok') {
                            Brandco.unit.closeModalFlame(target);
                            $('#segment_' + target_sid).hide('slow', function () {
                                $(this).remove();
                            });

                            if (segment_status == "1") {
                                SegmentListService.updateSegmentCounter($('#total_segment_counter'));
                                SegmentListService.updateSegmentCounter($('#segment_counter_' + response.data.s_type));
                            }
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(params);
        },
        updateSegmentCounter: function (target) {
            var cur_count = parseInt($(target).html());

            $(target).prop('Counter', $(target).html())
                .animate({
                    Counter: cur_count - 1
                }, {
                    duration: 100,
                    easing: 'swing',
                    step: function (now) {
                        $(this).text(Math.ceil(now));
                    }
                });
        },
        duplicateSegment: function (target) {
            var target_sid = $(target).closest('.jsSegment').attr('data-segment_id'),
                csrf_token = document.getElementsByName("csrf_token")[0].value,
                params = {
                    data: {
                        csrf_token: csrf_token,
                        target_sid: target_sid
                    },
                    'url': 'admin-segment/api_duplicate_segment.json',
                    'type': 'POST',
                    success: function (response) {
                        if (response.result == 'ok') {
                            window.location.href = response.data.redirect_url;
                        } else if (response.result == 'ng') {
                            alert('エラーが発生しました！')
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(params);
        },
        isContainDraftProvision: function() {
            var target_provisions = $('.jsSContainerList input:checked');
            var isContainDraftProvision = false;

            target_provisions.each(function(){
                var is_active = $(this).data('is_active');
                if($(this).val() > 0 && is_active == '') {
                    isContainDraftProvision = true;
                    return false;
                }
            });

            return isContainDraftProvision;
        },
        executeSegmentAction: function (target) {
            var action_url = $(target).data('action_url');
            var target_data = $('.jsSContainerList input').serialize();
            var action_type = $(target).data('action_type');

            SegmentListService.closeSegmentActionModal($(this).parent('.jsModal'));

            switch (action_type) {
                case 1:
                case 4:
                    Brandco.unit.closeModalFlame(this);
                    window.location.href = action_url + '?' + target_data;
                    break;
                case 3:
                    SegmentListService.loadSegmentAdsAction(target_data);
                    Brandco.unit.openModal('#segmentAdsActionModal');
                    break;
                default:
                    break;
            }
        },
        closeSegmentActionModal: function (modal) {
            $('.jsModalCont').animate({
                top: -150,
                opacity: 0
            }, 200, function(){
                $(modal).css('display', 'none');
                $(modal).parents('.jsModal').fadeOut(200);
                var prev_height = $('body').data('prev_height');
                if (prev_height && prev_height > 0) {
                    $('body').height(prev_height);
                }
            });
        },
        loadSegmentAdsAction: function(target_sps) {
            var url = $("input[name=get_segment_ads_action_url]").val();
            var data = {
                'target_sps' : target_sps
            };
            var param = {
                data: data,
                type: 'GET',
                url: url,
                beforeSend: function(){
                    Brandco.helper.brandcoBlockUI();
                },
                success: function(response) {
                    $('.jsLoadSegmentAdsAction').html(response.html);
                },
                complete: function() {
                    $.unblockUI();
                    var modalID = '#segmentAdsActionModal';
                    $(modalID).height($('body').height()).fadeIn(300, function(){
                        $(this).find('.jsModalCont').css({
                            display: 'block',
                            opacity: 0,
                            top: $(window).scrollTop()
                        }).animate({
                            top: $(window).scrollTop() + 30,
                            opacity: 1
                        }, 300, function() {
                            var modal_height = $(modalID).find('.jsModalCont').position().top + $(modalID).find('.jsModalCont').outerHeight(true);
                            var body_height = $('body').outerHeight(true);
                            var default_height = $('body').data('prev_height');

                            if (default_height === undefined || default_height == '') {
                                $('body').data('prev_height', body_height);
                                default_height = body_height;
                            }

                            if (body_height >= default_height && body_height < modal_height) {
                                $('body').height(modal_height + 10);
                                $(modalID).height($('body').height());
                            }
                        });
                    });
                }
            };
            Brandco.api.callAjaxWithParam(param,false,false);
        },
        sendAdsTargetUser: function() {

            if(SegmentListService.validateAdsAction()) {
                var target_data = $('.jsSContainerList input').serialize();
                var ads_audience_name = $('input[name=ads_audience_name]').val();
                var ads_account_ids = $('.jsLoadAdsAccount input').serialize();
                var csrf_token = document.getElementsByName("csrf_token")[0].value;
                var url = $("input[name=send_ads_target_url]").val();
                var ads_audience_description = $('input[name=ads_audience_description]').val();
                var description_flg = $('input[name=ads_description_flg]').is(":checked");

                var params = {
                        data: {
                            csrf_token: csrf_token,
                            target_data: target_data,
                            ads_audience_name: ads_audience_name,
                            ads_account_ids: ads_account_ids,
                            ads_audience_description: ads_audience_description,
                            description_flg: description_flg
                        },
                        url: url,
                        type: 'POST',
                        success: function (response) {
                            if (response.result == 'ok') {
                                $('#segmentAdsActionModal').html(response.html);
                            } else {
                                SegmentListService.clearAdsActionValidate();
                                alert('apiの送信に失敗しました。時間をおいて再度お試し下さい。');
                            }

                        }
                    };
                Brandco.api.callAjaxWithParam(params);
            }
        },
        validateAdsAction: function() {

            var is_valid = true;

            var ads_audience_name = $('input[name=ads_audience_name]').val();

            if($("input[name^=ads_account_ids]:checked").length == 0) {
                $('.jsAdsAccountError').html('一つ以上のアカウントを選択してください');
                $('.jsAdsAccountError').show();

                is_valid = false;
            } else {
                $('.jsAdsAccountError').hide('fast');
            }

            if(ads_audience_name == '') {
                $('.jsAdsAudienceNameInputError').html('必ず入力して下さい');
                $('.jsAdsAudienceNameInputError').show();

                is_valid = false;
            } else if(ads_audience_name.length > 255) {
                $('.jsAdsAudienceNameInputError').html('1文字以上255文字以下で入力して下さい');
                $('.jsAdsAudienceNameInputError').show();

                is_valid = false;
            } else {
                $('.jsAdsAudienceNameInputError').hide('fast');
            }

            var ads_description = $('input[name=ads_audience_description]').val();
            if($('input[name=ads_description_flg]').is(':checked') && ads_description.length > 255) {
                $('.jsAdsAudienceDescriptionInputError').html('255文字以下で入力して下さい');
                $('.jsAdsAudienceDescriptionInputError').show();

                is_valid = false;
            } else {
                $('.jsAdsAudienceDescriptionInputError').hide('fast');
            }
            
            return is_valid;
        },
        clearAdsActionValidate: function() {

            $('.jsAdsAccountError').html('');
            $('.jsAdsAccountError').hide();

            $('.jsAdsAudienceNameInputError').html('');
            $('.jsAdsAudienceNameInputError').hide();
        }
    }
})();
$(document).ready(function () {

    // all checked
    $(document).on('change', '.jsSegmentCheck', function () {
        SegmentListService.dojsSegmentCheck(this);
    });

    // checked decision
    $(document).on('click', '.jsSegmentToggleTarget input', function() {
        var target = $(this).parents('.jsSegmentToggleTarget').find('input');
        if (target.length == target.filter(':checked').length) {
            $(this).parents('.jsSegmentToggleWrap').find('.jsSegmentCheck').prop('checked', 'checked');
        } else {
            $(this).parents('.jsSegmentToggleWrap').find('.jsSegmentCheck').prop('checked', false);
        }
    });

    // common toggle area
    $(document).on('click', '.jsSegmentToggle', function() {
        SegmentListService.dojsSegmentToggle(this);
    });

    //tooltip hover
    $(document).on({
        'mouseenter': function(e) {
            var trigger = e.currentTarget;
            var target = $(trigger).data('tooltip');
            $('.jsHoverTooltip').not(target).stop(true, true).fadeOut(200);
            $(target).css({
                top: $(trigger).position().top
            }).stop(true, true).fadeIn(200);
        }
    }, '.segmentItemInner, .listItem');

    $(document).on({
        'mouseleave': function() {
            $('.jsHoverTooltip').stop(true, true).fadeOut(200);
        }
    },'.segmentItemList, .segmentPreviewWrap');

    // Segment Selector
    $(document).on('click', '.jsSSelector', function () {
        if (SegmentListService.isCurrentSelector(this)) {
            return false;
        }
        SegmentListService.loadSegmentContainerList(this);
    });

    // Duplicating Segment
    $(document).on('click', '.jsDuplicateSegment', function () {
        SegmentListService.fadeToggleTarget(this);
        SegmentListService.duplicateSegment(this);
    });

    // Archiving Segment
    $(document).on('click', '.jsOpenArchiveModal', function () {
        var segment_id = $(this).closest('.jsSegment').attr('data-segment_id'),
            segment_status = $(this).attr('data-segment_status'),
            target = $('.jsConfirmArchiveSegment'),
            archive_text = "アーカイブしますか？";

        SegmentListService.fadeToggleTarget(this);
        target.attr('data-segment_id', segment_id);
        target.attr('data-segment_status', segment_status);

        if (segment_status == "1") {
            archive_text = "アーカイブするとセグメントを停止します。アーカイブしますか？";
        }
        $('#archiveSet').find('.supplement1').html(archive_text);

        Brandco.unit.showModal(this);
        return false;
    });

    $(document).on('click', '.jsConfirmArchiveSegment', function () {
        SegmentListService.archiveSegment(this);
    });

    // Common action
    $(document).on('click', '.jsAreaToggle', function () {
        $(this).parents('.jsAreaToggleWrap').find('.jsAreaToggleTarget').stop(true, true).fadeToggle(200);
        return false;
    });

    $(document).on('click', '.jsOpenSegmentActionModal', function () {
        if ($('.jsSegmentCheck:checked').length == 0 && $('.jsSProvisionCheck:checked').length == 0) {
            alert('セグメントを選択してください!');
        } else if(SegmentListService.isContainDraftProvision()) {
            Brandco.unit.openModal('#segmentActionSelectorAlert');
        } else {
            Brandco.unit.showModal(this);
        }
        return false;
    });

    // Segment Action
    $(document).on('click', '.jsSegmentAction', function() {
        SegmentListService.executeSegmentAction(this);
    });

    $(document).on('change', '.jsCheckToggle', function(){
        var targetWrap = $(this).parents('.jsCheckToggleWrap')[0];
        $(targetWrap).find('.jsCheckToggleTarget').slideToggle(300);
    });

    //Close And Do Nothing ADS Modal
    $(document).on('click', '.jsCancelAdsModal', function(){
        $('.jsModalCont').animate({
            top: -150,
            opacity: 0
        }, 200, function(){
            $(this).css('display', 'none');
            $(this).parents('.jsModal').fadeOut(200);

            var prev_height = $('body').data('prev_height');
            if (prev_height && prev_height > 0) {
                $('body').height(prev_height);
            }
        });

        $('.jsLoadSegmentAdsAction').html('');

        return false;
    });

    $(document).on('click', '.jsSendTargetUser', function(){
        SegmentListService.sendAdsTargetUser();
    });

    $(document).on('click', '.jsCloseAdsConfirmModal', function(){
        var redirect_url = $(this).data('redirect_url');
        window.location = redirect_url;
    });

    $(document).on('change', 'input[name=ads_description_flg]', function(){
        if($(this).is(':checked') == false) {
            $('input[name=ads_audience_description]').val('');
        }
    });

    $(document).on('click', '.jsOpenAdsAccountModal', function(){
        SegmentListService.closeSegmentActionModal($(this).parent('.jsModal'));
        Brandco.unit.openModal('#selectAdsSnSType');
    });
});