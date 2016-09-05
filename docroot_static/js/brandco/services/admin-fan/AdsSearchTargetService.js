var AdsSearchTargetService = (function(){
    return {
        segment_data: [],
        cur_spsc_object: null,
        cur_sp_container: null,
        cur_spc_component: null,
        cur_sp_no: 0,
        cur_spc_no: 0,
        is_unclassified: false,
        cur_condition_type: '',
        cur_condition_key: '',
        initSegmentData: function() {
            var cur_sp_no = 0;
            $('.jsSPContainer').each(function() {
                AdsSearchTargetService.segment_data[cur_sp_no] = [];
                AdsSearchTargetService.initSPCComponentData(this, cur_sp_no);
                cur_sp_no += 1;
            });

            AdsSearchTargetService.segment_data[cur_sp_no] = [];
        },
        initSPCComponentData: function(sp_container, sp_no) {
            var cur_spc_no = 0;
            $(sp_container).find('.jsSPCComponent').not('.metricBoxAddWrap').each(function() {
                AdsSearchTargetService.segment_data[sp_no][cur_spc_no] = [];
                if ($(this).find('.jsAreaToggleWrap').length != 0) {
                    $(this).find('.jsAreaToggleWrap').each(function() {
                        AdsSearchTargetService.segment_data[sp_no][cur_spc_no].push($(this).attr('data-spsc_key'));
                    });
                }
                cur_spc_no += 1;
            });
        },
        refreshMetricBoxs: function () {
            var skeleton = $('.metricBoxs'),
                is_fixed = false;

            if (skeleton.length == 0) {
                return false;
            }

            if (!$(skeleton[0]).children('li').last().hasClass('metricBoxAddWrap')) {
                is_fixed = true;
            }

            for (var i = skeleton.length - 1; i >= 0; i--) {
                var targetWidth = 0,
                    target = $(skeleton[i]),
                    children_length = target.children('li').length;

                if (is_fixed) {
                    targetWidth += 170 * (children_length);
                } else {
                    targetWidth += 170 * (children_length - 1);
                    targetWidth += 73;
                }
                targetWidth += 10 * (children_length);

                if (targetWidth > target.parent().width()) {
                    target.width(targetWidth);
                }
            }
        },
        reloadSPUserCount: function() {

            $('.jsSPContainer').each(function() {
                var cur_sp_no =     $(this).hasClass('segmentItemNa') ? AdsSearchTargetService.segment_data.length - 1 : $(this).index();

                $(this).find('.jsSPCComponent').not('.metricBoxAddWrap').each(function() {
                    var cur_spc_no = $(this).index();

                    $(this).find('.jsSPCComponentValue').attr('name', 'spc[' + cur_sp_no + '][' + cur_spc_no + '][]');
                })
            });

            var conditions = $('form[name=save_audience_form]').serialize(),
                csrf_token = document.getElementsByName("csrf_token")[0].value,
                params = {
                    data: {
                        condition_value: conditions,
                        csrf_token: csrf_token
                    },
                    url: 'admin-fan/api_get_ads_target_user_count.json',
                    type: 'POST',
                    success: function(response) {
                        if (response.result == 'ok') {
                            $('.jsTargetCount').html(response.data.spc_user_count);
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(params);
        },
        initSPCModalParams: function(target) {
            AdsSearchTargetService.cur_spc_component    = $(target).closest('.jsSPCComponent');
            AdsSearchTargetService.cur_sp_container     = $(target).closest('.jsSPContainer');
            AdsSearchTargetService.cur_condition_type   = $(target).attr('data-type');
            AdsSearchTargetService.is_unclassified      = AdsSearchTargetService.cur_sp_container.hasClass('segmentItemNa');
            AdsSearchTargetService.cur_sp_no            = AdsSearchTargetService.is_unclassified ? AdsSearchTargetService.segment_data.length - 1 : AdsSearchTargetService.cur_sp_container.index();
            AdsSearchTargetService.cur_spc_no           = AdsSearchTargetService.cur_spc_component.index();
        },
        initSPCModal: function (target) {
            var pre_condition_key = AdsSearchTargetService.getPreConditionKey(),
                params = {
                    data: {
                        pre_condition_key: pre_condition_key,
                        condition_type: AdsSearchTargetService.cur_condition_type
                    },
                    type: 'GET',
                    url: 'admin-segment/api_init_segment_condition_view.json',
                    success: function(response) {
                        if (response.result == 'ok') {
                            $('#segmentProvisionConditionSelector').replaceWith(response.html);

                            var target_condition = null;
                            if ($('.jsProvisionSubCondition li').length == 0) {
                                target_condition = $('.jsProvisionCondition').find('.selected')
                            } else {
                                target_condition = $('.jsProvisionSubCondition').find('.selected')
                            }
                            AdsSearchTargetService.updateCurSegmentConditionKey($(target_condition).closest("li"));

                            Brandco.unit.showModal(target);
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(params);
        },
        getPreConditionKey: function() {
            var pre_condition_key = -1;

            if (AdsSearchTargetService.is_unclassified) {
                return pre_condition_key;
            }

            if (AdsSearchTargetService.cur_condition_type == 'or') {
                return pre_condition_key;
            }

            if (AdsSearchTargetService.cur_spc_no >= AdsSearchTargetService.segment_data[AdsSearchTargetService.cur_sp_no].length) {
                return pre_condition_key;
            }

            for (var i = 0; i < AdsSearchTargetService.segment_data.length - 1; i++) {
                if (AdsSearchTargetService.segment_data[i][AdsSearchTargetService.cur_spc_no].length >= 1) {
                    return AdsSearchTargetService.segment_data[i][AdsSearchTargetService.cur_spc_no][0];
                }
            }

            return pre_condition_key;
        },
        loadSegmentConditionView: function (target, category_mode) {
            var target_data = $(target).find('a'),
                target_type = $(target_data).data('target_type'),
                target_id = $(target_data).data('target_id');

            if ($(target_data).hasClass('selected')) {
                return;
            }

            $(target_data).toggleClass('selected');
            $(target).siblings().find('a').removeClass('selected');
            AdsSearchTargetService.resetValidateMessage();

            var param = {
                data: {
                    category_mode: category_mode,
                    target_type: target_type,
                    target_id: target_id
                },
                type: 'GET',
                url: 'admin-segment/api_load_segment_condition_view.json',
                success: function (response) {
                    if (response.result == 'ok') {
                        if (category_mode == 3) {
                            $('.jsProvisionConditionValue').show();
                            $('.jsSPConditionTitle').html(response.data.title);
                        } else {
                            AdsSearchTargetService.resetSegmentConditionView();
                        }

                        $('.' + response.data.target_class).html(response.html);
                    }
                }
            };
            Brandco.api.callAjaxWithParam(param);
        },
        setSPCComponent: function (target) {
            var condition_value = $(target).closest('form').serialize(),
                params = {
                    data: {
                        action_type: 1,
                        condition_value: condition_value,
                        condition_key: AdsSearchTargetService.cur_condition_key,
                        cur_condition_type: AdsSearchTargetService.cur_condition_type
                    },
                    type: 'GET',
                    url: 'admin-segment/api_load_segment_condition_component.json',
                    success: function (response) {
                        if (response.result == 'ok') {
                            var spc_component_html = response.html;

                            if (AdsSearchTargetService.cur_condition_type == 'or') {
                                AdsSearchTargetService.appendSPCComponent(spc_component_html);
                                AdsSearchTargetService.segment_data[AdsSearchTargetService.cur_sp_no][AdsSearchTargetService.cur_spc_no].push(AdsSearchTargetService.cur_condition_key);
                            } else {
                                var append_add_box_flg = AdsSearchTargetService.segment_data[AdsSearchTargetService.cur_sp_no].length == 1;

                                if (AdsSearchTargetService.cur_spc_no >= AdsSearchTargetService.segment_data[AdsSearchTargetService.cur_sp_no].length) {
                                    AdsSearchTargetService.appendSPCondition();
                                    AdsSearchTargetService.cur_spc_component.closest('ul').append($('#adding_spc_template').html());
                                } else if (append_add_box_flg) {
                                    if (!AdsSearchTargetService.is_unclassified) {
                                        for (var i = 0; i < AdsSearchTargetService.segment_data.length - 1; i++) {
                                            if (AdsSearchTargetService.segment_data[i][0].length != 0) {
                                                append_add_box_flg = false;
                                                break;
                                            }
                                        }

                                        if (append_add_box_flg) {
                                            $('.jsSPContainer').not('.segmentItemNa').each(function () {
                                                $(this).find('ul.metricBoxs').append($('#adding_spc_template').html());
                                            });
                                        }
                                    } else {
                                        AdsSearchTargetService.cur_spc_component.closest('ul').append($('#adding_spc_template').html());
                                    }
                                }

                                AdsSearchTargetService.replaceSPCondition(spc_component_html);
                            }

                            Brandco.unit.closeModalFlame(target);
                            AdsSearchTargetService.refreshMetricBoxs();
                            AdsSearchTargetService.reloadSPUserCount();
                            AdsSearchTargetService.setReloadAlert();
                        } else if (response.result == 'ng') {
                            var spc_selector_error = $(target).closest('form').find('.jsSPCSelectorError');
                            $(spc_selector_error).html(response.errors.error_msg);
                            $(spc_selector_error).show();
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(params);
        },
        appendSPCComponent: function(html) {
            AdsSearchTargetService.cur_spc_component.find('.jsAreaToggleWrap').last().find('.or').remove();
            AdsSearchTargetService.cur_spc_component.append(html);
        },
        appendSPCondition: function() {
            if (AdsSearchTargetService.is_unclassified) {
                AdsSearchTargetService.segment_data[AdsSearchTargetService.cur_sp_no][AdsSearchTargetService.cur_spc_no] = [];
            } else {
                var add_option_box = null;
                $('.jsSPContainer').not(AdsSearchTargetService.cur_sp_container).each(function () {
                    if (!$(this).hasClass('segmentItemNa')) {
                        add_option_box = $(this).find('.metricBoxAddWrap');
                        $($('#blank_spc_template').html()).insertBefore(add_option_box);
                    }
                });

                for (var i = 0; i < AdsSearchTargetService.segment_data.length - 1; i++) {
                    AdsSearchTargetService.segment_data[i][AdsSearchTargetService.cur_spc_no] = [];
                }
            }


            $('.jsSyncTarget').on('scroll', function() {
                AdsSearchTargetService.horizontalSync(this);
            });
        },
        replaceSPCondition: function (html) {

            AdsSearchTargetService.segment_data[AdsSearchTargetService.cur_sp_no][AdsSearchTargetService.cur_spc_no].push(AdsSearchTargetService.cur_condition_key);

            AdsSearchTargetService.cur_spc_component.removeClass('metricBoxOptionAdd');
            AdsSearchTargetService.cur_spc_component.removeClass('metricBoxAddWrap');
            AdsSearchTargetService.cur_spc_component.html(html);
        },
        resetSPCComponent: function(target) {
            if ($(target).closest('.jsSPContainer').hasClass('segmentItemNa')) {
                AdsSearchTargetService.resetUnclassifiedSPCComponent(target);
            } else {
                AdsSearchTargetService.resetClassifiedSPCComponent(target);
            }
        },
        resetUnclassifiedSPCComponent: function(target) {
            if (!confirm('この条件を削除しますか？')) {
                return;
            }

            var target_component = $(target).closest('.jsSPCComponent'),
                component_index = $(target).closest('.jsAreaToggleWrap').index(),
                component_length = $(target_component).find('.jsAreaToggleWrap').length,
                cur_spc_no = $(target_component).index(),
                cur_sp_no = AdsSearchTargetService.segment_data.length - 1;

            AdsSearchTargetService.segment_data[cur_sp_no][cur_spc_no].splice(component_index, 1);

            if (component_length <= 1) {
                if (AdsSearchTargetService.segment_data[cur_sp_no].length <= 1) {
                    $(target_component).next().remove();
                    $(target_component).replaceWith($('#blank_spc_template').html());
                } else {
                    $(target_component).remove();
                }

                AdsSearchTargetService.segment_data[cur_sp_no].splice(cur_spc_no, 1);
            } else if (component_index == 0) {
                var next_component_condition = $(target_component).find('.jsAreaToggleWrap').eq(1);
                $(next_component_condition).find('input[name="or_label_flg"]').val('');
                $(next_component_condition).find('.labelOr').remove();
                $(target).closest('.jsAreaToggleWrap').remove();
            } else if (component_index == (component_length - 1)) {
                var prev_component_condition = $(target_component).find('.jsAreaToggleWrap').eq(component_index - 1);
                $(prev_component_condition).append('<p class="or"><a href="#segmentProvisionConditionSelector" data-type="or" class="jsOpenSegmentConditionModal">or</a></p>');
                $(target).closest('.jsAreaToggleWrap').remove();
            } else if(component_index > 0 && component_index < (component_length - 1)) {
                $(target).closest('.jsAreaToggleWrap').remove();
            }

            AdsSearchTargetService.refreshMetricBoxs();
            AdsSearchTargetService.reloadSPUserCount();
            AdsSearchTargetService.setReloadAlert();
        },
        resetClassifiedSPCComponent: function(target) {
            var target_component = $(target).closest('.jsSPCComponent'),
                target_container = $(target).closest('.jsSPContainer'),
                component_length = $(target_component).find('.jsAreaToggleWrap').length,
                component_index = $(target).closest('.jsAreaToggleWrap').index(),
                cur_spc_no = $(target_component).index(),
                cur_sp_no = $(target_container).index();

            if (component_index == 0 && component_length > 1) {
                for (var i = 0; i < AdsSearchTargetService.segment_data.length - 1; i++) {
                    if (i == cur_sp_no) {
                        continue;
                    }

                    if (AdsSearchTargetService.segment_data[i][cur_spc_no].length >= 1
                        && AdsSearchTargetService.segment_data[cur_sp_no][cur_spc_no][component_index + 1] != AdsSearchTargetService.segment_data[cur_sp_no][cur_spc_no][component_index]) {

                        alert('この条件を削除することができません！');
                        return;
                    }
                }
            }

            if (!confirm('この条件を削除しますか？')) {
                return;
            }

            AdsSearchTargetService.segment_data[cur_sp_no][cur_spc_no].splice(component_index, 1);

            var is_removable_component = AdsSearchTargetService.isRemovableComponent(cur_spc_no);
            if (is_removable_component) {
                if (AdsSearchTargetService.segment_data[cur_sp_no].length <= 1) {
                    $('.jsSPContainer').not('.segmentItemNa').each(function() {
                        $(this).find('.jsSPCComponent').next().remove();
                    });
                } else {
                    $('.jsSPContainer').not('.segmentItemNa').each(function() {
                        $(this).find('.jsSPCComponent').eq(cur_spc_no).remove();
                    });

                    for (var k = 0; k < AdsSearchTargetService.segment_data.length -1; k++) {
                        AdsSearchTargetService.segment_data[k].splice(cur_spc_no, 1);
                    }
                }
            }

            if (component_length <= 1) {
                $(target_component).replaceWith($('#blank_spc_template').html());
            } else if (component_index == 0) {
                var next_component_condition = $(target_component).find('.jsAreaToggleWrap').eq(1);
                $(next_component_condition).find('input[name="or_label_flg"]').val('');
                $(next_component_condition).find('.labelOr').remove();
                $(target).closest('.jsAreaToggleWrap').remove();
            } else if (component_index == (component_length - 1)) {
                var prev_component_condition = $(target_component).find('.jsAreaToggleWrap').eq(component_index - 1);
                $(prev_component_condition).append('<p class="or"><a href="#segmentProvisionConditionSelector" data-type="or" class="jsOpenSegmentConditionModal">or</a></p>');
                $(target).closest('.jsAreaToggleWrap').remove();
            } else if(component_index > 0 && component_index < (component_length - 1)) {
                $(target).closest('.jsAreaToggleWrap').remove();
            }

            AdsSearchTargetService.refreshMetricBoxs();
            AdsSearchTargetService.reloadSPUserCount();
            AdsSearchTargetService.setReloadAlert();
        },
        isRemovableComponent: function(cur_spc_no) {
            for (var j = 0; j < AdsSearchTargetService.segment_data.length - 1; j++) {
                if (AdsSearchTargetService.segment_data[j][cur_spc_no].length >= 1) {
                    return false;
                }
            }

            return true;
        },
        deleteSPContainer: function(target) {
            if(!confirm('このセグメントを削除しますか?')) {
                return;
            }

            if ($(target).closest('.jsSPContainer').hasClass('segmentItemNa')) {
                AdsSearchTargetService.deleteUnclassifiedSPContainer(target);
            } else {
                AdsSearchTargetService.deleteClassifiedSPContainer(target);
            }

            AdsSearchTargetService.updateSegmentName();
            AdsSearchTargetService.reloadSPUserCount();
            AdsSearchTargetService.setReloadAlert();
        },
        deleteClassifiedSPContainer: function(target) {
            var cur_sp_container = $(target).closest('.jsSPContainer'),
                cur_sp_no = cur_sp_container.index(),
                valid_component_list = AdsSearchTargetService.getValidComponentList(cur_sp_no);

            if (AdsSearchTargetService.segment_data.length <= 2) {
                AdsSearchTargetService.segment_data[cur_sp_no] = [[]];

                cur_sp_container.find('.metricBoxs').html($('#blank_spc_template').html());
            } else if (valid_component_list.length == 0) {
                var unclassified_data = AdsSearchTargetService.segment_data.slice(-1).pop();

                AdsSearchTargetService.segment_data = [[[]], unclassified_data];
                cur_sp_container.find('.metricBoxs').html($('#blank_spc_template').html());

                $('.jsSPContainer').not(cur_sp_container).each(function () {
                    if (!$(this).hasClass('segmentItemNa')) {
                        $(this).remove();
                    }
                });
            } else {
                for (var i = 0; i < AdsSearchTargetService.segment_data[0].length; i++) {
                    if ($.inArray(i, valid_component_list) != -1) {
                        continue;
                    }

                    $('.jsSPContainer').each(function () {
                        $(this).find('.jsSPCComponent').eq(i).remove();
                    });

                    for (var j = 0; j < AdsSearchTargetService.segment_data.length; j++) {
                        AdsSearchTargetService.segment_data[j].splice(i, 1);
                    }
                }

                AdsSearchTargetService.segment_data.splice(cur_sp_no, 1);
                $(target).closest('.jsSPContainer').delay(10).slideUp(function() {
                    $(this).remove();
                });
            }
        },
        deleteUnclassifiedSPContainer: function(target) {
            var cur_sp_container = $(target).closest('.jsSPContainer'),
                cur_sp_no = AdsSearchTargetService.segment_data.length - 1;

            AdsSearchTargetService.segment_data[cur_sp_no] = [[]];
            cur_sp_container.find('.metricBoxs').html($('#blank_spc_template').html());
        },
        getValidComponentList: function(cur_sp_no) {
            var valid_components = [];

            for (var i = 0; i < AdsSearchTargetService.segment_data[cur_sp_no].length; i++) {
                if (AdsSearchTargetService.segment_data[cur_sp_no][i].length == 0) {
                    valid_components.push(i);
                    continue;
                }

                for (var j = 0; j < AdsSearchTargetService.segment_data.length - 1; j++) {
                    if (j == cur_sp_no) {
                        continue;
                    }

                    if (AdsSearchTargetService.segment_data[j][i].length >= 1) {
                        valid_components.push(i);
                        break;
                    }
                }
            }

            return valid_components;
        },
        doSPCreatorAction: function(action_type) {
            if (action_type == 1) {
                AdsSearchTargetService.cloneSPContainer();
            } else if (action_type == 2) {
                AdsSearchTargetService.addSPContainer();
            }
        },
        cloneSPContainer: function() {

            var cur_container = AdsSearchTargetService.cur_sp_container,
                target_clone = $(cur_container).clone(),
                cur_sp_no = $(cur_container).index();


            $(target_clone).find('.jsSPNameInput').val("");
            $(target_clone).find('.iconError1').hide();

            $(target_clone).insertAfter($(cur_container)).hide().slideDown("fast", function() {
                AdsSearchTargetService.updateSegmentName();
                AdsSearchTargetService.reloadSPUserCount();
                AdsSearchTargetService.setReloadAlert()
            });

            var clone_data = AdsSearchTargetService.segment_data[cur_sp_no].map(function(arr) {
                return arr.slice();
            });
            AdsSearchTargetService.segment_data.splice(cur_sp_no + 1, 0, clone_data);

            $('.jsSyncTarget').on('scroll', function() {
                AdsSearchTargetService.horizontalSync(this);
            });
        },
        addSPContainer: function() {
            var cur_container = AdsSearchTargetService.cur_sp_container,
                new_container = $(cur_container).clone(),
                new_container_data = [],
                cur_sp_no = $(cur_container).index();

            $(new_container).find('.jsSPNameInput').val("");
            $(new_container).find('.iconError1').hide();

            new_container.find('ul.metricBoxs').empty();
            for (var i = 0; i < AdsSearchTargetService.segment_data[cur_sp_no].length; i++) {
                new_container.find('ul.metricBoxs').append($('#blank_spc_template').html());
                new_container_data[i] = [];
            }

            if (cur_container.find('.metricBoxAddWrap').length != 0) {
                new_container.find('ul.metricBoxs').append($('#adding_spc_template').html());
            }

            AdsSearchTargetService.segment_data.splice(cur_sp_no + 1, 0, new_container_data);
            $(new_container).insertAfter($(cur_container)).hide().slideDown("fast", function() {
                AdsSearchTargetService.updateSegmentName();
                AdsSearchTargetService.reloadSPUserCount();
                AdsSearchTargetService.setReloadAlert()
            });

            $('.jsSyncTarget').on('scroll', function() {
                AdsSearchTargetService.horizontalSync(this);
            });
        },
        updateSPCCondition: function(target) {
            var target_spc = $(AdsSearchTargetService.cur_spsc_object),
                condition_value = $(target).closest('.jsCloneObject').find('input').serialize(),
                params = {
                    data: {
                        condition_key: $(target_spc).attr('data-spsc_key'),
                        condition_value: condition_value,
                        cur_condition_type: AdsSearchTargetService.cur_condition_type,
                        action_type: 2
                    },
                    type: 'GET',
                    url: 'admin-segment/api_load_segment_condition_component.json',
                    success: function (response) {
                        if (response.result == 'ok') {
                            AdsSearchTargetService.cur_spsc_object = null;

                            $(target_spc).replaceWith(response.html);
                            AdsSearchTargetService.reloadSPUserCount();
                            AdsSearchTargetService.setReloadAlert();

                            $(target).closest('.jsAreaToggleTarget').stop(true, true).fadeToggle(200);
                        } else if (response.result == 'ng') {
                            var spc_error = $(target).closest('.jsAreaToggleTarget').find('.jsSPConditionError');
                            spc_error.html(response.errors.error_msg);
                            spc_error.show();
                        }
                    }
                };

            Brandco.api.callAjaxWithParam(params);
        },
        resetSegmentConditionView: function () {
            $('.jsProvisionSubCondition').empty();
            $('.jsProvisionConditionDetail').empty();
            $('.jsProvisionConditionValue').hide();
        },
        updateCurSegmentConditionKey: function (target) {
            var data_container = $(target).find('a'),
                target_id = data_container.attr('data-target_id');

            AdsSearchTargetService.cur_condition_key = data_container.attr('data-target_type');

            if (target_id != '') {
                AdsSearchTargetService.cur_condition_key += '/' + target_id;
            }
        },
        validateSegmentConditionSelector: function (target) {
            if ($(target).closest('form').find('.jsProvisionConditionValue').is(':visible')) {
                return true;
            }

            return false;
        },
        cloneSPConditionToggle: function (target) {
            AdsSearchTargetService.resetValidateMessage();

            var clone_target = $(target).next();

            if ($(target).closest('.jsAreaToggleWrap').find('.or').length == 0) {
                $(clone_target).find('input[name="or_condition_flg"]').val("off");
            }

            $('.jsCloneObject').fadeOut().queue(function () {
                $(this).remove();
            });

            var clone = $(clone_target).clone()
                .css({top: $(target).offset().top + 15, left: $(target).offset().left - 98})
                .stop(true, true).fadeToggle(200)
                .addClass('jsCloneObject');

            clone.on('click', '.boxCloseBtn', function () {
                clone.fadeOut().queue(function () {
                    clone.remove();
                });
            });

            $('body').append(clone);
            return false;
        },
        horizontalSync: function (container) {
            var self = $('.jsSyncTarget').not(container).off('scroll');

            for (i = 0; i < self.length; i++) {
                self[i].scrollLeft = container.scrollLeft;
                self.on('scroll', function() {
                    AdsSearchTargetService.horizontalSync(this);
                });
            }
        },
        saveAudience: function() {

            if(!AdsSearchTargetService.validateAdsAccount() || !AdsSearchTargetService.validateSegmentGroupName() || !AdsSearchTargetService.validateAudienceDescription()) {
                return;
            }

            $(window).unbind('beforeunload');

            $('.jsSPContainer').each(function() {
                var cur_sp_no = $(this).hasClass('segmentItemNa') ? AdsSearchTargetService.segment_data.length - 1 : $(this).index();

                if ($(this).find('.jsSPNameInput').length != 0) {
                    $(this).find('.jsSPNameInput').attr('name', 'spc_name[' + cur_sp_no + ']');
                } else {
                    var cur_sp_name = $(this).find('.jsSPName').html();
                    $(this).append('<input type="hidden" name="spc_name[' + cur_sp_no + ']" value="' + cur_sp_name + '" />');
                }
                $(this).find('.jsSPCComponent').each(function() {
                    var cur_spc_no = $(this).index();

                    if (cur_spc_no != 0 || cur_sp_no != 0) {
                        $(this).find('.jsSPCComponentValue').attr('name', 'spc[' + cur_sp_no + '][' + cur_spc_no + '][]');
                    }
                })
            });

            $('form[name=save_audience_form]').submit();
        },
        validateSegmentGroupLimit: function(target) {
            var csrf_token = document.getElementsByName("csrf_token")[0].value,
                params = {
                    data: {
                        csrf_token: csrf_token,
                        segment_type: $('input[name="segment_type"]').val()
                    },
                    type: 'POST',
                    url: 'admin-segment/api_validate_segment_provision_limit.json',
                    success: function(response) {
                        if (response.result == 'ok') {
                            $(target).attr('data-modal_id', "#" + response.data.modal_id);
                            Brandco.unit.showModal(target);
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(params);
        },
        validateAdsAccount: function() {
            var account_error = $('.jsAdsAccountError');

            if ($('input[name=save_type]').val() == 1 && $('input[name^=ads_account_ids]:checked').length == 0) {
                AdsSearchTargetService.showErrorMessage(account_error, 2);
                return false;
            } else {
                account_error.hide("fast");
                return true;
            }
        },
        validateAudienceDescription: function() {

            var is_valid = true;

            var ads_description = $('input[name=audience_description]').val();
            if($('input[name=description_flg]').is(':checked') && ads_description.length > 255) {
                $('.jsDescriptionInputError').html('255文字以下で入力して下さい');
                $('.jsDescriptionInputError').show();

                is_valid = false;
            } else {
                $('.jsDescriptionInputError').hide('fast');
            }

            return is_valid;
        },
        validateSegmentGroupName: function() {
            var name_error = $('.jsNameInputError');

            var is_valid = true;

            var ads_audience_name = $('input[name=audience_name]').val();

            if(ads_audience_name == '') {
                name_error.html('必ず入力して下さい');
                name_error.show();

                is_valid = false;
            } else if(ads_audience_name.length > 255) {
                name_error.html('1文字以上255文字以下で入力して下さい');
                name_error.show();

                is_valid = false;
            } else {
                name_error.hide('fast');
            }

            return is_valid;
        },
        showErrorMessage: function(target, err_type) {
            var err_msg = "";

            if (err_type == 1) {
                err_msg = "必ず入力して下さい";
            } else if (err_type == 2) {
                err_msg = "一つ以上のアカウントを選択してください";
            }

            target.html(err_msg);
            if (!target.is(':visible')) {
                target.show("fast");
            }
        },
        getDuplicateInputValue: function() {

            var sp_name_input = $('.jsSPNameInput'),
                unique_input_values = [],
                duplicate_input_values = [],
                sp_name_input_values = $.map(sp_name_input, function(input) {
                    var input_value = $(input).val();
                    return input_value != "" ? input_value : null;
                });

            for (var i = 0; i < sp_name_input_values.length; i++) {
                if ($.inArray(sp_name_input_values[i], unique_input_values) == -1) {
                    unique_input_values.push(sp_name_input_values[i]);
                } else if ($.inArray(sp_name_input_values[i], duplicate_input_values) == -1) {
                    duplicate_input_values.push(sp_name_input_values[i]);
                }
            }

            return duplicate_input_values;
        },
        resetValidateMessage: function () {
            $('.jsSPCSelectorError').hide();
            $('.jsSPConditionError').hide();
        },
        updateSegmentName: function() {
            var cur_sp_no = 0;
            $('.jsSPNameInput').each(function() {
                if (AdsSearchTargetService.isEmpty(this)) {
                    if ($(this).closest('.jsSPContainer').hasClass('segmentItemNa')) {
                        $(this).attr('placeholder', '除外セグメント');
                    } else {
                        cur_sp_no = $(this).closest('.jsSPContainer').index();
                        $(this).attr('placeholder', 'セグメント' + (cur_sp_no + 1));
                    }
                }
            });
        },
        setReloadAlert: function() {
            $(window).unbind('beforeunload');
            Brandco.helper.set_reload_alert(Brandco.message.reloadMessage);
        },
        isEmpty: function(target) {
            return $.trim($(target).val()).length == 0;
        }
    }
})();

$(document).ready(function() {

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(document).on('focus', '.jsDate', function(){
        $(this).datepicker();
    });

    AdsSearchTargetService.refreshMetricBoxs();
    AdsSearchTargetService.reloadSPUserCount();
    AdsSearchTargetService.initSegmentData();


    $(document).on('click', '.jsOpenSegmentConditionModal', function() {
        AdsSearchTargetService.initSPCModalParams(this);
        AdsSearchTargetService.initSPCModal(this);
        return false;
    });


    $(document).on('click', '.jsCloseSPCComponent', function() {
        Brandco.unit.closeModalFlame(this);
        return false;
    });

    $(document).on('click', '.jsSaveSPCComponent', function() {
        if (AdsSearchTargetService.validateSegmentConditionSelector(this) != false) {
            AdsSearchTargetService.setSPCComponent(this);
        } else {
            alert('絞り込み条件を入力してください!');
        }
        return false;
    });

    $(document).on('click', '.jsProvisionCategory li', function() {
        if ($(this).find('a').hasClass('disabled')) {
            return false;
        }
        AdsSearchTargetService.loadSegmentConditionView(this, 1);
    });

    $(document).on('click', '.jsProvisionCondition li', function() {
        if ($(this).find('a').hasClass('disabled')) {
            return false;
        }

        var parent = $('.jsProvisionCategory').find('.selected');
        var parent_type = $(parent).data('target_type');
        var mode;

        if ($.inArray(parent_type, [5, 6, 7]) != -1) {
            mode = 2;
        } else {
            AdsSearchTargetService.updateCurSegmentConditionKey(this);
            mode = 3;
        }
        AdsSearchTargetService.loadSegmentConditionView(this, mode);
    });

    $(document).on('click', '.jsProvisionSubCondition li', function() {
        if ($(this).find('a').hasClass('disabled')) {
            return false;
        }
        AdsSearchTargetService.updateCurSegmentConditionKey(this);
        AdsSearchTargetService.loadSegmentConditionView(this, 3);
    });

    $(document).on('change', '.jsSocialAccountConnect', function() {
        var target_condition = $(this).closest('.jsProfileSocialAccountCondition');
        if ($(this).is(':checked')) {
            $(target_condition).find('[name^="search_friend_count_"]').removeAttr('disabled');
        } else {
            $(target_condition).find('[name^="search_friend_count_"]').val('');
            $(target_condition).find('[name^="search_friend_count_"]').attr('disabled','disabled');
        }
    });

    $('.jsSocialAccountConnect').each(function() {
        if ($(this).is(':checked') && !$(this).closest('div').hasClass('disabled')) {
            $(this).closest('.jsProfileSocialAccountCondition').find('[name^="search_friend_count_"]').removeAttr('disabled');
        }
    });

    $(document).on('click', '.jsCloneToggle', function() {
        AdsSearchTargetService.cur_spsc_object = $(this).closest('.jsAreaToggleWrap');
        AdsSearchTargetService.cloneSPConditionToggle(this);
    });

    $(document).on('click', '.jsUpdateSPCCondition', function() {
        AdsSearchTargetService.updateSPCCondition(this);
    });

    $(document).on('click', '.jsResetSPCComponent', function() {
        AdsSearchTargetService.resetSPCComponent(this);
        return false;
    });

    $(document).on('click', '.jsCreateDraftAudience', function() {
        $('input[name=save_type]').val(0);
        AdsSearchTargetService.saveAudience(this);
    });

    $(document).on('click', '.jsCreateAudienceAndSendTarget', function() {
        $('input[name=save_type]').val(1);
        AdsSearchTargetService.saveAudience(this);
    });
});