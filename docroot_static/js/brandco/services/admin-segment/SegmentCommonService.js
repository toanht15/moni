var SegmentCommonService = (function() {
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
        is_show_sp_creator_confirm_box: true,
        initSegmentData: function() {
            var cur_sp_no = 0;
            $('.jsSPContainer').each(function() {
                SegmentCommonService.segment_data[cur_sp_no] = [];
                SegmentCommonService.initSPCComponentData(this, cur_sp_no);
                cur_sp_no += 1;
            });

            SegmentCommonService.is_show_sp_creator_confirm_box = $('input[name="segment_id"]').val().length == 0;
        },
        initSPCComponentData: function(sp_container, sp_no) {
            var cur_spc_no = 0;

            $(sp_container).find('.jsSPCComponent').not('.metricBoxAddWrap').each(function() {

                SegmentCommonService.segment_data[sp_no][cur_spc_no] = [];

                if ($(this).find('.jsAreaToggleWrap').length != 0) {
                    $(this).find('.jsAreaToggleWrap').each(function() {
                        SegmentCommonService.segment_data[sp_no][cur_spc_no].push($(this).attr('data-spsc_key'));
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
            $('.segmentMember > span').html($('#loading_img').html());
            $('.jsSPContainer').each(function() {
                var cur_sp_no =     $(this).hasClass('segmentItemNa') ? SegmentCommonService.segment_data.length - 1 : $(this).index();

                $(this).find('.jsSPCComponent').not('.metricBoxAddWrap').each(function() {
                    var cur_spc_no = $(this).index();

                    $(this).find('.jsSPCComponentValue').attr('name', 'spc[' + cur_sp_no + '][' + cur_spc_no + '][]');
                })
            });

            var conditions = $('form[name=save_segment_form]').serialize(),
                csrf_token = document.getElementsByName("csrf_token")[0].value,
                params = {
                    data: {
                        condition_value: conditions,
                        csrf_token: csrf_token
                    },
                    url: 'admin-segment/api_fetch_segment_provision_user_count.json',
                    type: 'POST',
                    success: function(response) {
                        if (response.result == 'ok') {
                            $('.jsRemainingUserCount').html(response.data.remaining_user_count);
                            $('.jsUnclassifiedUserCount').html(response.data.unclassified_user_count);
                            $('.jsUnconditionalUserCount').html(response.data.unconditional_user_count);

                            for (var i = 0; i < response.data.spc_user_count.length; i++) {
                                var target_container = $('.jsSPContainer').eq(i);
                                $(target_container).find('.jsSPUserCount').html(response.data.spc_user_count[i]);
                            }
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(params);
        },
        initSPCModalParams: function(target) {
            SegmentCommonService.cur_spc_component    = $(target).closest('.jsSPCComponent');
            SegmentCommonService.cur_sp_container     = $(target).closest('.jsSPContainer');
            SegmentCommonService.cur_condition_type   = $(target).attr('data-type');
            SegmentCommonService.is_unclassified      = SegmentCommonService.cur_sp_container.hasClass('segmentItemNa');
            SegmentCommonService.cur_sp_no            = SegmentCommonService.is_unclassified ? SegmentCommonService.segment_data.length - 1 : SegmentCommonService.cur_sp_container.index();
            SegmentCommonService.cur_spc_no           = SegmentCommonService.cur_spc_component.index();
        },
        initSPCModal: function (target) {
            var pre_condition_key = SegmentCommonService.getPreConditionKey(),
                params = {
                    data: {
                        pre_condition_key: pre_condition_key,
                        condition_type: SegmentCommonService.cur_condition_type
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
                            SegmentCommonService.updateCurSegmentConditionKey($(target_condition).closest("li"));

                            Brandco.unit.showModal(target);
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(params);
        },
        getPreConditionKey: function() {
            var pre_condition_key = -1;

            if (SegmentCommonService.is_unclassified) {
                return pre_condition_key;
            }

            if (SegmentCommonService.cur_condition_type == 'or') {
                return pre_condition_key;
            }

            if (SegmentCommonService.cur_spc_no >= SegmentCommonService.segment_data[SegmentCommonService.cur_sp_no].length) {
                return pre_condition_key;
            }

            for (var i = 0; i < SegmentCommonService.segment_data.length - 1; i++) {
                if (SegmentCommonService.segment_data[i][SegmentCommonService.cur_spc_no].length >= 1) {
                    return SegmentCommonService.segment_data[i][SegmentCommonService.cur_spc_no][0];
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
            SegmentCommonService.resetValidateMessage();

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
                            SegmentCommonService.resetSegmentConditionView();
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
                        condition_key: SegmentCommonService.cur_condition_key,
                        cur_condition_type: SegmentCommonService.cur_condition_type
                    },
                    type: 'GET',
                    url: 'admin-segment/api_load_segment_condition_component.json',
                    success: function (response) {
                        if (response.result == 'ok') {
                            var spc_component_html = response.html;

                            if (SegmentCommonService.cur_condition_type == 'or') {
                                SegmentCommonService.appendSPCComponent(spc_component_html);
                                SegmentCommonService.segment_data[SegmentCommonService.cur_sp_no][SegmentCommonService.cur_spc_no].push(SegmentCommonService.cur_condition_key);
                            } else {
                                var append_add_box_flg = SegmentCommonService.segment_data[SegmentCommonService.cur_sp_no].length == 1;

                                if (SegmentCommonService.cur_spc_no >= SegmentCommonService.segment_data[SegmentCommonService.cur_sp_no].length) {
                                    SegmentCommonService.appendSPCondition();
                                    SegmentCommonService.cur_spc_component.closest('ul').append($('#adding_spc_template').html());
                                } else if (append_add_box_flg) {
                                    if (!SegmentCommonService.is_unclassified) { // Adding add box to every segment container if add boxes is not available
                                        for (var i = 0; i < SegmentCommonService.segment_data.length - 1; i++) {
                                            if (SegmentCommonService.segment_data[i][0].length != 0) {
                                                append_add_box_flg = false;
                                                break;
                                            }
                                        }

                                        if (append_add_box_flg) {
                                            $('.jsSPContainer').not('.segmentItemNa').each(function () {
                                                $(this).find('ul.metricBoxs').append($('#adding_spc_template').html());
                                            });
                                        }
                                    } else { // 除外セグメント用
                                        SegmentCommonService.cur_spc_component.closest('ul').append($('#adding_spc_template').html());
                                    }
                                }

                                SegmentCommonService.replaceSPCondition(spc_component_html);
                            }

                            Brandco.unit.closeModalFlame(target);
                            SegmentCommonService.refreshMetricBoxs();
                            SegmentCommonService.reloadSPUserCount();
                            SegmentCommonService.setReloadAlert();
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
            SegmentCommonService.cur_spc_component.find('.jsAreaToggleWrap').last().find('.or').remove();
            SegmentCommonService.cur_spc_component.append(html);
        },
        appendSPCondition: function() {
            if (SegmentCommonService.is_unclassified) {
                SegmentCommonService.segment_data[SegmentCommonService.cur_sp_no][SegmentCommonService.cur_spc_no] = [];
            } else {
                var add_option_box = null;
                $('.jsSPContainer').not(SegmentCommonService.cur_sp_container).each(function () {
                    if (!$(this).hasClass('segmentItemNa')) {
                        add_option_box = $(this).find('.metricBoxAddWrap');
                        $($('#blank_spc_template').html()).insertBefore(add_option_box);
                    }
                });

                for (var i = 0; i < SegmentCommonService.segment_data.length - 1; i++) {
                    SegmentCommonService.segment_data[i][SegmentCommonService.cur_spc_no] = [];
                }
            }


            $('.jsSyncTarget').on('scroll', function() {
                SegmentCommonService.horizontalSync(this);
            });
        },
        replaceSPCondition: function (html) {

            SegmentCommonService.segment_data[SegmentCommonService.cur_sp_no][SegmentCommonService.cur_spc_no].push(SegmentCommonService.cur_condition_key);

            SegmentCommonService.cur_spc_component.removeClass('metricBoxOptionAdd');
            SegmentCommonService.cur_spc_component.removeClass('metricBoxAddWrap');
            SegmentCommonService.cur_spc_component.html(html);
        },
        resetSPCComponent: function(target) {
            if ($(target).closest('.jsSPContainer').hasClass('segmentItemNa')) {
                SegmentCommonService.resetUnclassifiedSPCComponent(target);
            } else {
                SegmentCommonService.resetClassifiedSPCComponent(target);
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
                cur_sp_no = SegmentCommonService.segment_data.length - 1;

            SegmentCommonService.segment_data[cur_sp_no][cur_spc_no].splice(component_index, 1);

            if (component_length <= 1) {
                if (SegmentCommonService.segment_data[cur_sp_no].length <= 1) {
                    $(target_component).next().remove();
                    $(target_component).replaceWith($('#blank_spc_template').html());
                } else {
                    $(target_component).remove();
                }

                SegmentCommonService.segment_data[cur_sp_no].splice(cur_spc_no, 1);
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

            SegmentCommonService.refreshMetricBoxs();
            SegmentCommonService.reloadSPUserCount();
            SegmentCommonService.setReloadAlert();
        },
        resetClassifiedSPCComponent: function(target) {
            var target_component = $(target).closest('.jsSPCComponent'),
                target_container = $(target).closest('.jsSPContainer'),
                component_length = $(target_component).find('.jsAreaToggleWrap').length,
                component_index = $(target).closest('.jsAreaToggleWrap').index(),
                cur_spc_no = $(target_component).index(),
                cur_sp_no = $(target_container).index();

            if (component_index == 0 && component_length > 1) {
                for (var i = 0; i < SegmentCommonService.segment_data.length - 1; i++) {
                    if (i == cur_sp_no) {
                        continue;
                    }

                    if (SegmentCommonService.segment_data[i][cur_spc_no].length >= 1
                        && SegmentCommonService.segment_data[cur_sp_no][cur_spc_no][component_index + 1] != SegmentCommonService.segment_data[cur_sp_no][cur_spc_no][component_index]) {

                        alert('この条件を削除することができません！');
                        return;
                    }
                }
            }

            if (!confirm('この条件を削除しますか？')) {
                return;
            }

            SegmentCommonService.segment_data[cur_sp_no][cur_spc_no].splice(component_index, 1);

            // Remove segment components if elements in the same column are empty
            var is_removable_component = SegmentCommonService.isRemovableComponent(cur_spc_no);
            if (is_removable_component) {
                if (SegmentCommonService.segment_data[cur_sp_no].length <= 1) {
                    $('.jsSPContainer').not('.segmentItemNa').each(function() {
                        $(this).find('.jsSPCComponent').next().remove();
                    });
                } else {
                    $('.jsSPContainer').not('.segmentItemNa').each(function() {
                        $(this).find('.jsSPCComponent').eq(cur_spc_no).remove();
                    });

                    for (var k = 0; k < SegmentCommonService.segment_data.length -1; k++) {
                        SegmentCommonService.segment_data[k].splice(cur_spc_no, 1);
                    }
                }
            }

            // Replace component
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

            SegmentCommonService.refreshMetricBoxs();
            SegmentCommonService.reloadSPUserCount();
            SegmentCommonService.setReloadAlert();
        },
        isRemovableComponent: function(cur_spc_no) {
            for (var j = 0; j < SegmentCommonService.segment_data.length - 1; j++) {
                if (SegmentCommonService.segment_data[j][cur_spc_no].length >= 1) {
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
                SegmentCommonService.deleteUnclassifiedSPContainer(target);
            } else {
                SegmentCommonService.deleteClassifiedSPContainer(target);
            }

            SegmentCommonService.updateSegmentName();
            SegmentCommonService.reloadSPUserCount();
            SegmentCommonService.setReloadAlert();
        },
        deleteClassifiedSPContainer: function(target) {
            var cur_sp_container = $(target).closest('.jsSPContainer'),
                cur_sp_no = cur_sp_container.index(),
                valid_component_list = SegmentCommonService.getValidComponentList(cur_sp_no);

            if (SegmentCommonService.segment_data.length <= 2) {
                SegmentCommonService.segment_data[cur_sp_no] = [[]];

                cur_sp_container.find('.metricBoxs').html($('#blank_spc_template').html());
            } else if (valid_component_list.length == 0) {
                var unclassified_data = SegmentCommonService.segment_data.slice(-1).pop();

                SegmentCommonService.segment_data = [[[]], unclassified_data];
                cur_sp_container.find('.metricBoxs').html($('#blank_spc_template').html());

                $('.jsSPContainer').not(cur_sp_container).each(function () {
                    if (!$(this).hasClass('segmentItemNa')) {
                        $(this).remove();
                    }
                });
            } else {
                for (var i = 0; i < SegmentCommonService.segment_data[0].length; i++) {
                    if ($.inArray(i, valid_component_list) != -1) {
                        continue;
                    }

                    $('.jsSPContainer').each(function () {
                        $(this).find('.jsSPCComponent').eq(i).remove();
                    });

                    for (var j = 0; j < SegmentCommonService.segment_data.length; j++) {
                        SegmentCommonService.segment_data[j].splice(i, 1);
                    }
                }

                SegmentCommonService.segment_data.splice(cur_sp_no, 1);
                $(target).closest('.jsSPContainer').delay(10).slideUp(function() {
                    $(this).remove();
                });
            }
        },
        deleteUnclassifiedSPContainer: function(target) {
            var cur_sp_container = $(target).closest('.jsSPContainer'),
                cur_sp_no = SegmentCommonService.segment_data.length - 1;

            SegmentCommonService.segment_data[cur_sp_no] = [[]];
            cur_sp_container.find('.metricBoxs').html($('#blank_spc_template').html());
        },
        getValidComponentList: function(cur_sp_no) {
            var valid_components = [];

            for (var i = 0; i < SegmentCommonService.segment_data[cur_sp_no].length; i++) {
                if (SegmentCommonService.segment_data[cur_sp_no][i].length == 0) {
                    valid_components.push(i);
                    continue;
                }

                for (var j = 0; j < SegmentCommonService.segment_data.length - 1; j++) {
                    if (j == cur_sp_no) {
                        continue;
                    }

                    if (SegmentCommonService.segment_data[j][i].length >= 1) {
                        valid_components.push(i);
                        break;
                    }
                }
            }

            return valid_components;
        },
        checkSPCreatorTermsOfUse: function(target, action_type) {
            SegmentCommonService.cur_sp_container = $(target).closest('.jsSPContainer');

            if (!SegmentCommonService.is_show_sp_creator_confirm_box) {
                SegmentCommonService.doSPCreatorAction(action_type);
            } else {
                Brandco.unit.showModal(target);
                $('.jsConfirmSPCreatorConfirmBox').attr('data-action_type', action_type);
                SegmentCommonService.is_show_sp_creator_confirm_box = false;
            }
        },
        confirmSPCreatorTermsOfUse: function(target) {
            var action_type = $(target).attr('data-action_type');

            Brandco.unit.closeModalFlame(target);
            SegmentCommonService.doSPCreatorAction(action_type);
        },
        doSPCreatorAction: function(action_type) {
            if (action_type == 1) {
                SegmentCommonService.cloneSPContainer();
            } else if (action_type == 2) {
                SegmentCommonService.addSPContainer();
            }
        },
        cloneSPContainer: function() {
            // Do not allow clone if conditions are not set
            var cur_container = SegmentCommonService.cur_sp_container,
                target_clone = $(cur_container).clone(),
                cur_sp_no = $(cur_container).index();

            // Reset clone segment data
            $(target_clone).find('.jsSPNameInput').val("");
            $(target_clone).find('.iconError1').hide();

            $(target_clone).insertAfter($(cur_container)).hide().slideDown("fast", function() {
                SegmentCommonService.updateSegmentName();
                SegmentCommonService.reloadSPUserCount();
                SegmentCommonService.setReloadAlert()
            });

            // update segment_data
            var clone_data = SegmentCommonService.segment_data[cur_sp_no].map(function(arr) {
                return arr.slice();
            });
            SegmentCommonService.segment_data.splice(cur_sp_no + 1, 0, clone_data);

            $('.jsSyncTarget').on('scroll', function() {
                SegmentCommonService.horizontalSync(this);
            });
        },
        addSPContainer: function() {
            var cur_container = SegmentCommonService.cur_sp_container,
                new_container = $(cur_container).clone(),
                new_container_data = [],
                cur_sp_no = $(cur_container).index();

            // Reset clone segment data
            $(new_container).find('.jsSPNameInput').val("");
            $(new_container).find('.iconError1').hide();

            new_container.find('ul.metricBoxs').empty();
            for (var i = 0; i < SegmentCommonService.segment_data[cur_sp_no].length; i++) {
                new_container.find('ul.metricBoxs').append($('#blank_spc_template').html());
                new_container_data[i] = [];
            }

            if (cur_container.find('.metricBoxAddWrap').length != 0) {
                new_container.find('ul.metricBoxs').append($('#adding_spc_template').html());
            }

            SegmentCommonService.segment_data.splice(cur_sp_no + 1, 0, new_container_data);
            $(new_container).insertAfter($(cur_container)).hide().slideDown("fast", function() {
                SegmentCommonService.updateSegmentName();
                SegmentCommonService.reloadSPUserCount();
                SegmentCommonService.setReloadAlert()
            });

            $('.jsSyncTarget').on('scroll', function() {
                SegmentCommonService.horizontalSync(this);
            });
        },
        updateSPCCondition: function(target) {
            var target_spc = $(SegmentCommonService.cur_spsc_object),
                condition_value = $(target).closest('.jsCloneObject').find('input').serialize(),
                params = {
                    data: {
                        condition_key: $(target_spc).attr('data-spsc_key'),
                        condition_value: condition_value,
                        cur_condition_type: SegmentCommonService.cur_condition_type,
                        action_type: 2
                    },
                    type: 'GET',
                    url: 'admin-segment/api_load_segment_condition_component.json',
                    success: function (response) {
                        if (response.result == 'ok') {
                            SegmentCommonService.cur_spsc_object = null;

                            $(target_spc).replaceWith(response.html);
                            SegmentCommonService.reloadSPUserCount();
                            SegmentCommonService.setReloadAlert();

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

            SegmentCommonService.cur_condition_key = data_container.attr('data-target_type');

            if (target_id != '') {
                SegmentCommonService.cur_condition_key += '/' + target_id;
            }
        },
        validateSegmentConditionSelector: function (target) {
            if ($(target).closest('form').find('.jsProvisionConditionValue').is(':visible')) {
                return true;
            }

            return false;
        },
        cloneSPConditionToggle: function (target) {
            SegmentCommonService.resetValidateMessage();

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
                    SegmentCommonService.horizontalSync(this);
                });
            }
        },
        saveSegment: function(target) {
            var segment_status = $(target).data('s_status');
            if (segment_status == "1") {
                Brandco.unit.closeModalFlame(target);
            }

            if (!SegmentCommonService.validateSegmentGroupName()) {
                return;
            }

            $(window).unbind('beforeunload');
            $('.jsSPContainer').each(function() {
                var cur_sp_no = $(this).hasClass('segmentItemNa') ? SegmentCommonService.segment_data.length - 1 : $(this).index();

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
            $('.jsSStatus').val(segment_status);

            $('form[name=save_segment_form]').submit();
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
        validateSegmentGroupName: function() {
            var first_error_msg_wrap = null;

            var segment_name_error = $('.jsSNameInputError');
            if (SegmentCommonService.isEmpty($('.jsSNameInput'))) {
                first_error_msg_wrap = segment_name_error.closest('.jsErrorMsgWrap');
                SegmentCommonService.showErrorMessage(segment_name_error, 1);
            } else {
                segment_name_error.hide("fast");
            }

            var duplicate_input_values = SegmentCommonService.getDuplicateInputValue();

            $('.jsSPNameInput').each(function() {
                var error_msg_wrap = $(this).closest('.jsErrorMsgWrap'),
                    sp_name_error = error_msg_wrap.find('.jsSPNameInputError');

                if (error_msg_wrap.hasClass('segmentItemNa') && !$('input[name="unclassified_flg"]').is(':checked')) {
                    sp_name_error.hide("fast");
                    return true;
                }

                if (SegmentCommonService.isEmpty(this)) {
                    if (first_error_msg_wrap == null) {
                        first_error_msg_wrap = error_msg_wrap;
                    }

                    SegmentCommonService.showErrorMessage(sp_name_error, 1);
                } else if ($.inArray($(this).val(), duplicate_input_values) != -1) {
                    if (first_error_msg_wrap == null) {
                        first_error_msg_wrap = error_msg_wrap;
                    }

                    SegmentCommonService.showErrorMessage(sp_name_error, 2);
                } else {
                    sp_name_error.hide("fast");
                }
            });

            if (first_error_msg_wrap == null) {
                return true;
            }

            // Scroll to error message
            var position = first_error_msg_wrap.offset().top;
            $('body,html').animate({scrollTop: position}, 500, 'swing');

            return false;
        },
        showErrorMessage: function(target, err_type) {
            var err_msg = "";

            if (err_type == 1) {
                err_msg = "必ず入力して下さい";
            } else if (err_type == 2) {
                err_msg = "セグメントグループ名が重複しています！"
            }

            target.html(err_msg);
            if (!target.is(':visible')) {
                target.show("fast");
            }
        },
        getDuplicateInputValue: function() {
            // Fetching sp_name input values to array
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
                if (SegmentCommonService.isEmpty(this)) {
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

    // Init Segment Default Value
    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(document).on('focus', '.jsDate', function(){
        $(this).datepicker();
    });

    $('div.disabled').each(function() {
        $(this).find('input').attr('disabled', 'disabled');
    });

    Brandco.helper.doJsCheckToggle();
    SegmentCommonService.refreshMetricBoxs();
    SegmentCommonService.reloadSPUserCount();

    /**========Segment Condition Modal===========================**/

    //Open Modal
    $(document).on('click', '.jsOpenSegmentConditionModal', function() {
        SegmentCommonService.initSPCModalParams(this);
        SegmentCommonService.initSPCModal(this);
        return false;
    });

    //Load Modal View
    $(document).on('click', '.jsProvisionCondition li', function() {
        if ($(this).find('a').hasClass('disabled')) {
            return false;
        }

        var parent = $('.jsProvisionCategory').find('.selected'),
            parent_type = $(parent).data('target_type'),
            mode;

        if ($.inArray(parent_type, [5, 6, 7]) != -1) {
            mode = 2;
        } else {
            SegmentCommonService.updateCurSegmentConditionKey(this);
            mode = 3;
        }

        SegmentCommonService.loadSegmentConditionView(this, mode);
    });

    //Close Modal And Not Save
    $(document).on('click', '.jsCloseSPCComponent', function() {
        Brandco.unit.closeModalFlame(this);
        return false;
    });

    //Close Modal And Save
    $(document).on('click', '.jsSaveSPCComponent', function() {
        if (SegmentCommonService.validateSegmentConditionSelector(this) != false) {
            SegmentCommonService.setSPCComponent(this);
        } else {
            alert('絞り込み条件を入力してください!');
        }

        return false;
    });

    // Loading Segment Provision Condition List
    $(document).on('click', '.jsProvisionCategory li', function() {
        if ($(this).find('a').hasClass('disabled')) {
            return false;
        }

        SegmentCommonService.loadSegmentConditionView(this, 1);
    });

    // Loading Segment Provision SubCondition List
    $(document).on('click', '.jsProvisionSubCondition li', function() {
        if ($(this).find('a').hasClass('disabled')) {
            return false;
        }

        SegmentCommonService.updateCurSegmentConditionKey(this);
        SegmentCommonService.loadSegmentConditionView(this, 3);
    });

    //SNSの連携状態
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
    /**========Segment Condition Modal=======================================**/


    /**========SPCに関する処理===================================================**/

    // Clone Condition Toggle
    $(document).on('click', '.jsCloneToggle', function() {
        SegmentCommonService.cur_spsc_object = $(this).closest('.jsAreaToggleWrap');
        SegmentCommonService.cloneSPConditionToggle(this);
    });

    // Update Segment Provision Condition Component
    $(document).on('click', '.jsUpdateSPCCondition', function() {
        SegmentCommonService.updateSPCCondition(this);
    });

    // Reset n Delete Segment Provision Condition Component
    $(document).on('click', '.jsResetSPCComponent', function() {
        SegmentCommonService.resetSPCComponent(this);
        return false;
    });

    //Delete SPC
    $(document).on('click', '.jsDeleteSPContainer', function() {
        SegmentCommonService.deleteSPContainer(this);
    });

    //Clone SPC
    $(document).on('click', '.jsCloneSPContainer', function() {
        SegmentCommonService.checkSPCreatorTermsOfUse(this, 1);
        return false;
    });
    
    $(document).on('click', '.jsAddSPContainer', function() {
        SegmentCommonService.checkSPCreatorTermsOfUse(this, 2);
        return false;
    });

    //Add New SP Container Confirm Box
    $(document).on('click', '.jsConfirmSPCreatorConfirmBox', function() {
        SegmentCommonService.confirmSPCreatorTermsOfUse(this);
        return false;
    });

    /**========SPCに関する処理====================================================**/

    // Others
    $('.jsSyncTarget').on('scroll', function() {
        SegmentCommonService.horizontalSync(this);
    });

    // Common action
    $(document).on('click', '.jsAreaToggle', function () {
        $(this).parents('.jsAreaToggleWrap').find('.jsAreaToggleTarget').stop(true, true).fadeToggle(200);
        return false;
    });

    // Sort
    $(".jsSegmentProvisionList").sortable({
        handle: '.segmentMove',
        update: function(ev, ui) {
            SegmentCommonService.reloadSPUserCount();
        }
    });

    // Reload Alert
    $( ":input").each(function(){
        $(this).change(function(){
            SegmentCommonService.setReloadAlert();
        });
    });

    // Unconditional Toggle Check
    $('.jsSPCheckToggle').on('change', function(){
        SegmentCommonService.reloadSPUserCount();

        var targetWrap = $(this).parents('.jsSPCheckToggleWrap')[0];
        $(targetWrap).find('.jsSPCheckToggleTarget').slideToggle(300);
    });

    //Save segment
    $(document).on('click', '.jsSaveSegmentConfirmBtn', function() {
        SegmentCommonService.saveSegment(this);
    });

    $(document).on('click', '.jsOpenSegmentConfirmModal', function() {
        SegmentCommonService.validateSegmentGroupLimit(this);
        return false;
    });
});