var EditCustomizeSkeletonService = {
    dragItem_zIndex: 1,
    //flag to control the place holder when over the adding button
    //when over the action and the adding button at the same time, this flag will control when the new group place holder is appeared
    dragToAddModuleFlg: false,
    updateSkeletonFlg: $('.jsUpdateSkeleton').length > 0 ? true : false,
    balloon_element: null,
    changeFlg: false,

    canDragOneActions: function(ui_draggable) {
        if($.inArray(ui_draggable.data('action-type'),[12,13,16]) < 0 || ui_draggable.hasClass('moduleDetail1')) {
            return true;
        }
        var canDrag = true;
        $('.moduleDetail1').each(function() {
            if ($(this).data('action-type') == ui_draggable.data('action-type')) {
                canDrag = false;
            }
        });
        return canDrag;
    },
    canSetActionInGroup: function(ui_droppable, action_type) {
        var stepGroup = ui_droppable.parent();
        var canDrag = true;
        stepGroup.find('li.moduleDetail1').each(function() {
            if ($(this).data('action-type') == action_type) {
                canDrag = false;
                return false;
            }
        });
        return canDrag;
    },

    isDisableDragActions: function(action_li, drag_action_type, draggableHelper) {
        // data-disable-actions属性(挿入できないアクションタイプがある場合にできる)がある場合
        var disable_actions = action_li.parent().data('disable-actions');
        if (disable_actions) {
            var action_elements = disable_actions.split(',');
            for (var i = 0; i < action_elements.length; i++) {
                if (action_elements[i] == 'all') {
                    return false;
                } else if (drag_action_type == action_elements[i]) {
                    // disable_beforeの前までは挿入できない
                    var disable_before = action_li.parent().data('disable-before');
                    if (disable_before) {
                        var canDrag = true;
                        $("."+disable_before).each(function(){
                            var leftPosition = $(this).position().left + $(this).parent().parent().position().left;
                            if(draggableHelper.position().left <= leftPosition ) {
                                canDrag = false;
                            }
                        });
                        return canDrag;
                    } else {
                        return false;
                    }
                }
            }
        }
        return true;
    },
    hideShowAdd: function(target) {
        if(!EditCustomizeSkeletonService.canDragOneActions(target)) {
            $('.moduleDetail1').addClass('notShowAddR notShowAddL');
        }
        var first_group = $('.moduleList')[0];
        var disable_actions = $(first_group).data('disable-actions');
        if (disable_actions) {
            var action_elements = disable_actions.split(',');
            for (var i = 0; i < action_elements.length; i++) {
                if (action_elements[i] == 'all') {
                    $(first_group).children('.moduleDetail1').each(function() {
                        $(this).addClass('notShowAddR notShowAddL');
                    });
                } else if (target.data('action-type') == action_elements[i]) {
                    var disable_before = $(first_group).data('disable-before');
                    if (disable_before) {
                        $('.moduleDetail1').each(function(){
                            if($(this).hasClass(disable_before)) {
                                $(this).addClass('notShowAddL');
                                return false;
                            } else {
                                $(this).addClass('notShowAddR notShowAddL');
                            }
                        });
                    } else {
                        $(first_group).children('.moduleDetail1').each(function() {
                            $(this).addClass('notShowAddR notShowAddL');
                        });
                    }
                }
            }
        }
        // クーポンの場合は各グループの先頭には移動不可
        if(target.data('action-type') == 11) {
            $('.newSkeletonGroup').each(function() {
                $($(this).find('.moduleDetail1')[0]).addClass('notShowAddL');
            })
        }
        // 配送先情報は1グループ1つまで
        if(target.data('action-type') == 4) {
            target.addClass('tmpAddressLock');
            $('.moduleDetail1[data-action-type="4"]').each(function(){
                if(!$(this).hasClass('tmpAddressLock')) {
                    $(this).parent().children('.moduleDetail1').addClass('notShowAddR notShowAddL');
                }
            });
            target.removeClass('tmpAddressLock');
        }

        var prev_action = target.prev('.moduleDetail1');
        // sortableの場合はhidden要素が1つ間に入る
        var next_all_action = target.nextAll('.moduleDetail1');
        if(prev_action.length == 0 && next_all_action.length > 0) {
            var order_no = 0;
            next_all_action.each(function() {
                order_no += 1;
                if(order_no > 2) {
                    return false;
                }
                if($(this).is(':hidden')) {
                    return true;
                } else if($(this).data('action-type') == 11) {
                    $(this).addClass('notShowAddR');
                    $(this).nextAll('.moduleDetail1').addClass('notShowAddR notShowAddL');
                    return false;
                }
            });
        }

    },
    removePlaceHolderElement: function() {
        if ($('.dropPlaceHolder')[0]) {
            $('.dropPlaceHolder').hide('200', function(){
                $(this).remove();
                EditCustomizeSkeletonService.updateStepAttribute();
            });
        }
        if ($('.overAction')[0]) {
            $('.overAction').each(function(){
                $(this).removeClass('overAction');
            });
        }
    },
    initDroppable: function () {
        $('.moduleDetail1').not('.dummyAction').droppable({
            accept: '.moduleDetail2',
            tolerance: "touch",
            over: function( event, ui ) {
                if ($(ui.draggable).data('action-type') == 25) {
                    return false;
                }
                if(!EditCustomizeSkeletonService.canDragOneActions($(ui.draggable))) {
                    return false;
                }
                if ($(ui.draggable).data('action-type') == 4 &&
                    !EditCustomizeSkeletonService.canSetActionInGroup($(this), $(ui.draggable).data('action-type'))) {
                    return false;
                }
                //lock step1 when drag coupon action
                if (!EditCustomizeSkeletonService.isDisableDragActions($(this), $(ui.draggable).data('action-type'), $(ui.helper))) {
                    return false;
                }
                var left = $(this).position().left + $(this).parent().parent().position().left,
                    li = '<li class="moduleDetail1 moduleDetail1Drag dropPlaceHolder" data-action-type="'+$(ui.draggable).data('action-type')+'" style="display: none"><span class="addModuleL" style="display:none">追加する</span>';

                if(EditCustomizeSkeletonService.updateSkeletonFlg) {
                    li += '<span class="moduleIcon new">';
                } else {
                    li += '<span class="moduleIcon">';
                }

                li += '<img src="'+$(ui.draggable).find('img').attr('src')+'" width="33" height="33"><span class="textBalloon1">' +
                    '<span>' + $(ui.draggable).find('.moduleName').text() + '</span></span></span><span class="addModuleR" style="display:none">追加する</span></li>';

                if($(ui.helper).position().left > left ) {
                    //右にオーバーする
                    if ($('.stepPlaceHolder')[0]) {
                        $('.stepPlaceHolder').hide(200, function(){
                            $(this).remove();
                        });
                    }
                    //右側にドラッグできるかどうかの制御
                    if ($.inArray($(this).data('action-type'),[25]) < 0) {
                        //not message action
                        if ($(this).next()) {
                            if (!$(this).next().hasClass('dropPlaceHolder')) {
                                EditCustomizeSkeletonService.removePlaceHolderElement();
                                $(li).insertAfter($(this));
                                $(this).addClass('overAction');
                            }
                        } else {
                            EditCustomizeSkeletonService.removePlaceHolderElement();
                            $(li).insertAfter($(this));
                            $(this).addClass('overAction');
                        }
                    }
                } else {
                    //左にオーバーする
                    if ($(this).data('opening_flg') != 1 && $.inArray($(this).data('action-type'), [0, 25]) < 0 && $(ui.draggable).data('action-type') != 11) {
                        //not entry action
                        if ($(this).prev()) {
                            if (!$(this).prev().hasClass('dropPlaceHolder')) {
                                EditCustomizeSkeletonService.removePlaceHolderElement();
                                $(li).insertBefore($(this));
                                $(this).addClass('overAction');
                            }
                        } else {
                            EditCustomizeSkeletonService.removePlaceHolderElement();
                            $(li).insertBefore($(this));
                            $(this).addClass('overAction');
                        }
                    }
                }

                $('.dropPlaceHolder').show('200');
                EditCustomizeSkeletonService.initFunction();
            },
            out: function( event, ui ) {
                //lock step1 when drag coupon action
                if (!EditCustomizeSkeletonService.isDisableDragActions($(this), $(ui.draggable).data('action-type'), $(ui.helper))) {
                    return false;
                }

                if ($(this).hasClass('overAction')) {
                    EditCustomizeSkeletonService.removePlaceHolderElement();
                    $(this).removeClass('overAction');
                }

                if (EditCustomizeSkeletonService.dragToAddModuleFlg) {
                    EditCustomizeSkeletonService.insertStepPlaceHolder(ui);
                    //when the place holder is added, set the flag back to false
                    EditCustomizeSkeletonService.dragToAddModuleFlg = false;
                }
            },
            drop: function(event, ui) {
                //lock step1 when drag coupon action
                if (!EditCustomizeSkeletonService.isDisableDragActions($(this), $(ui.draggable).data('action-type'), $(ui.helper))) {
                    return false;
                }

                if ($('.dropPlaceHolder').length > 0) {
                    $('.dropPlaceHolder').removeClass('dropPlaceHolder');

                    //when drop into the action set the flag back to false
                    EditCustomizeSkeletonService.dragToAddModuleFlg = false;
                    EditCustomizeSkeletonService.setReloadMessage();
                }
                EditCustomizeSkeletonService.initFunction();
                EditCustomizeSkeletonService.changeFlg = true;
            }
        });
    },
    initDragAction: function() {
        $('.moduleDetail1:not(.jsLockShift,.jsTmpLockShift,.jsLockSortable)').css('cursor','move');
    },
    initDummyActionDrop: function() {
        $('.dummyAction').droppable({
            accept: '.moduleDetail1:not(.dummyAction),.moduleDetail2',
            tolerance: "pointer",
            out: function (event, ui) {
                $(this).find('img').attr('src',$('base').data('static-href')+'/img/dummy/02.jpg');
                $(this).find('.addModuleR').remove();
                $(this).find('.addModuleL').remove();
            },
            drop: function (event, ui) {
                if($(ui.draggable).parent().children('li[data-action-type]').length == 1) {
                    return false;
                }

                if ($(ui.draggable).data('action-type') == 11) {
                    return false;
                }
                if(!EditCustomizeSkeletonService.canDragOneActions($(ui.draggable))) {
                    return false;
                }
                if ($(ui.draggable).data('action-type') == 4 &&
                    !EditCustomizeSkeletonService.canSetActionInGroup($(this), $(ui.draggable).data('action-type'))) {
                    return false;
                }

                if(EditCustomizeSkeletonService.updateSkeletonFlg && $(ui.draggable).find('.moduleIcon').hasClass('new')) {
                    $(this).find('.moduleIcon').addClass('new');
                }
                if ($.inArray($(ui.draggable).data('action-type'), [25]) < 0) {
                    $('<span class="addModuleL" style="display:none">追加する</span>').insertBefore($(this).find('.moduleIcon'));
                }
                $(this).find('img').attr('src',$(ui.draggable).find('img').attr('src'));
                $(this).find('.moduleIcon').children('span').addClass('textBalloon1');
                if($(ui.draggable).hasClass('moduleDetail2')) {
                    var ballon = $(ui.draggable).find('.moduleName').text();
                    $(this).attr('data-action-type', $(ui.draggable).data('action-type'));
                } else {
                    var ballon = EditCustomizeSkeletonService.balloon_element.children('span').html();
                    if(EditCustomizeSkeletonService.updateSkeletonFlg && !$(ui.draggable).find('.moduleIcon').hasClass('new')) {
                        $(this).attr('data-action-id', $(ui.draggable).data('action-id'));
                        $(this).attr('data-action-type', $(ui.draggable).data('action-type'));
                        $(this).attr('data-type-name', $(ui.draggable).data('type-name'));
                        $(this).attr('data-created', $(ui.draggable).data('created'));
                        $(this).attr('data-title-text', $(ui.draggable).data('title-text'));
                    }
                }
                $(this).find('.textBalloon1').children('span').html(ballon);
                if ($.inArray($(ui.draggable).data('action-type'), [25]) < 0) {
                    $('<span class="addModuleR" style="display:none">追加する</span>').insertAfter($(this).find('.moduleIcon'));
                }
                if($(ui.draggable).hasClass('moduleDetail1')) {
                    $(ui.draggable).hide(200, function() {
                        $(ui.draggable).remove();
                    });
                }

                $(this).data('action-type', $(ui.draggable).data('action-type'));
                $(this).removeClass('dummyAction').addClass('moduleDetail1Drag');
                EditCustomizeSkeletonService.changeFlg = true;
                EditCustomizeSkeletonService.initFunction();
            }
        });
    },
    initActionDraggable: function() {
        $('.moduleDetail2').draggable({
            cursor: 'move',
            containment: $('.makeStepTypeCont1'),
            revert: 'invalid',
            start: function() {
                if(!EditCustomizeSkeletonService.canDragOneActions($(this))) {
                    return false;
                }
                EditCustomizeSkeletonService.showAddModuleSpan($(this));
            },
            helper: function () {
                return '<li class="moduleDetail1"><span class="moduleIcon"><img src="'+$(this).find('img').attr('src')+'" width="33" height="33"></span>' +
                    '<span class="textBalloon1"><span>' + $(this).find('moduleName').text() + '</span></span></li>';
            },
            stop: function() {
                EditCustomizeSkeletonService.hideAddModuleSpan();
            }
        });
    },
    initAddModuleDrop: function() {
        $('.addModuleDetail1').children('span').droppable({
            accept: '.moduleDetail1:not(.dummyAction),.moduleDetail2',
            tolerance: "touch",
            over: function (event, ui) {
                if($(ui.draggable).parent().children('li[data-action-type]').length == 1) {
                    return false;
                }

                if ($(ui.draggable).data('action-type') == 11) {
                    return false;
                }
                if(!EditCustomizeSkeletonService.canDragOneActions($(ui.draggable))) {
                    return false;
                }

                if ($('.dropPlaceHolder')[0]){
                    //when over the action and adding button at the same time, set the flag to true
                    EditCustomizeSkeletonService.dragToAddModuleFlg = true;
                    return;
                }
                EditCustomizeSkeletonService.insertStepPlaceHolder(ui);
                if($(ui.draggable).hasClass('moduleDetail1')) {
                    $(ui.draggable).remove();
                }
                EditCustomizeSkeletonService.initFunction();
                if ($('.dropPlaceHolder')[0]) {
                    $('.dropPlaceHolder').removeClass('dropPlaceHolder');
                }
                if ($('.stepPlaceHolder')[0]) {
                    $('.stepPlaceHolder').removeClass('stepPlaceHolder');
                }
                EditCustomizeSkeletonService.changeFlg = true;
            },
            out: function (event, ui) {
                //when move to out of the adding button, set the flag back to false
                EditCustomizeSkeletonService.dragToAddModuleFlg = false;
                if ($('.stepPlaceHolder')[0]) {
                    $('.stepPlaceHolder').hide(200, function(){
                        $(this).remove();
                    });
                }
                //update step width
                Brandco.admin.executeStepListWith();
                EditCustomizeSkeletonService.initFunction();
            }
        });
    },
    setReloadMessage: function() {
        if(!EditCustomizeSkeletonService.updateSkeletonFlg) {
            $(window).unbind('beforeunload');
            Brandco.helper.set_reload_alert(Brandco.message.reloadMessage);
        }
    },
    insertStepPlaceHolder: function(ui) {
        if(!EditCustomizeSkeletonService.canDragOneActions($(ui.draggable))) {
            return false;
        }

        var newElement = '<li class="stepDetail_require newSkeletonGroup stepPlaceHolder" style="display: none"><h1>STEP'+($('.newSkeletonTag').find('.moduleDetail1').length+1)+'</h1><ul class="moduleList">'+
        '<li class="moduleDetail1 moduleDetail1Drag dropPlaceHolder" data-action-type="'+$(ui.draggable).data('action-type')+'"';

        if(EditCustomizeSkeletonService.updateSkeletonFlg && !$(ui.draggable).hasClass('moduleDetail2') && !$(ui.draggable).find('.moduleIcon').hasClass('new')) {
            newElement += 'data-action-id="' + $(ui.draggable).data('action-id') + '" ';
            newElement += 'data-type-name="' + $(ui.draggable).data('type-name') + '" ';
            newElement += 'data-created="' + $(ui.draggable).data('created') + '" ';
            newElement += 'data-title-text="' + $(ui.draggable).data('title-text') + '" ';
        }
        newElement += '>';

        if ($.inArray($(ui.draggable).data('action-type'),[25]) < 0) {
            newElement += '<span class="addModuleL" style="display:none">追加する</span>';
        }

        if((EditCustomizeSkeletonService.updateSkeletonFlg && $(ui.draggable).hasClass('moduleDetail2')) || $(ui.draggable).find('.moduleIcon').hasClass('new')) {
            newElement += '<span class="moduleIcon new">';
        } else {
            newElement += '<span class="moduleIcon">';
        }

        if($(ui.draggable).hasClass('moduleDetail2')) {
            var ballon = $(ui.draggable).find('.moduleName').text();
        } else {
            var ballon = EditCustomizeSkeletonService.balloon_element.children('span').html();
        }
        newElement += '<img src="'+$(ui.draggable).find('img').attr('src')+'" width="33" height="33"><span class="textBalloon1"><span>' + ballon + '</span></span></span>';

        if ($.inArray($(ui.draggable).data('action-type'),[25]) < 0) {
            newElement += '<span class="addModuleR" style="display:none">追加する</span></li>';
        }
        newElement = newElement + '<!-- /.moduleList --></ul><!-- /.stepDetail_require --></li>';
        $(newElement).insertBefore($('.addModuleDetail1').parent().parent());
        $('.newSkeletonGroup').show(300);
    },
    updateStepAttribute: function() {
        //update step label
        var stepcount = 0;
        $('.newSkeletonGroup').each(function(){
            var child = $(this).find('ul').find('li[data-action-type]').length;
            if(child == 0) {
                $(this).remove();
            } else if (child > 1) {
                $(this).find('h1').html('STEP'+(stepcount+1)+'-'+(stepcount + child));
            } else {
                $(this).find('h1').html('STEP'+(stepcount+1));
            }
            stepcount += child;
        });

        //update step width
        Brandco.admin.executeStepListWith();
    },
    initActionShiftDroppable: function () {
        // jsLockShift:ドラッグ不可
        // jsTmpLockShift:一時的に移動不可
        // jsLockSortable:ドラッグ不可 + 移動不可
        $('.makeStepTypeCont1').sortable({
            items: '.moduleDetail1:not(.jsLockShift,.jsTmpLockShift)',
            scroll: false,
            tolerance: "pointer",
            cancel: '.jsLockSortable',
            containment: $('.makeStepTypeCont1'),
            start: function(event, ui) {
                // 発送をもって発表とdummyActionは捨てる以外はできない
                if($(ui.item).data('action-type') == 25 || $(ui.item).hasClass('dummyAction')) {
                    $('.moduleDetail1').addClass('jsTmpLockShift');
                }
                $('.moduleDetail1[data-action-type="25"]').addClass('jsTmpLockShift');
                if(($.inArray($(ui.item).data('action-type'), [11, 16])) >= 0) {
                    // ギフト、クーポンは第一グループへの挿入不可の場合あり
                    var first_group = $('.moduleList')[0];
                    var disable_actions = $(first_group).data('disable-actions');
                    if (disable_actions) {
                        var action_elements = disable_actions.split(',');
                        for (var i = 0; i < action_elements.length; i++) {
                            if (action_elements[i] == 'all') {
                                $(first_group).children('.moduleDetail1').each(function() {
                                    $(this).addClass('jsTmpLockShift');
                                });
                            } else if ($(ui.item).data('action-type') == action_elements[i]) {
                                var disable_before = $(first_group).data('disable-before');
                                if (disable_before) {
                                    $('.moduleDetail1').each(function(){
                                        $(this).addClass('jsTmpLockShift');
                                        if($(this).hasClass(disable_before)) {
                                            return false;
                                        }
                                    });
                                } else {
                                    $(first_group).children('.moduleDetail1').each(function() {
                                        $(this).addClass('jsTmpLockShift');
                                    });
                                }
                            }
                        }
                    }
                    // クーポンはグループへの先頭へは挿入不可
                    if($(ui.item).data('action-type') == 11) {
                        $('.newSkeletonGroup').each(function() {
                            $($(this).find('.moduleDetail1')[0]).addClass('jsTmpLockShift');
                        });
                        // 1つしかないアクションの後ろにクーポンを移動させるイベント
                        $('.jsTmpLockShift').each(function() {
                            if($(this).data('action-type') == 25) {
                                return false;
                            }
                            if($(this).closest('.moduleList').children('[data-action-type]').length == 1) {
                                $(this).droppable({
                                    accept: '.moduleDetail1:not(.dummyAction)',
                                    tolerance: "touch",
                                    over: function (event, ui) {
                                        var html = '<li class="moduleDetail1 moduleDetail1Drag" data-action-type="' + $(ui.draggable).data('action-type') + '">'
                                            + $(ui.draggable).html();
                                        +'</li>';

                                        $(html).insertAfter($(this));
                                        $(ui.draggable).hide();
                                        setTimeout(function () {
                                            $(ui.draggable).remove();
                                            EditCustomizeSkeletonService.initFunction();
                                            $('.jsLockShift').removeClass('jsTmpLockShift');
                                        }, 200);
                                    }
                                });
                            }
                        });
                    }
                }

                if($(ui.item).data('action-type') == 4) {
                    $(ui.item).addClass('tmpAddressShiftLock');
                    $('.moduleDetail1[data-action-type="4"]').each(function(){
                        if(!$(this).hasClass('tmpAddressShiftLock')) {
                            $(this).parent().children('.moduleDetail1').addClass('jsTmpLockShift');
                        }
                    });
                    $(ui.item).removeClass('tmpAddressShiftLock');
                }

                var prev_action = $(ui.item).prev('.moduleDetail1');
                // sortableの場合はhidden要素が1つ間に入る
                var next_all_action = $(ui.item).nextAll('.moduleDetail1');
                if(prev_action.length == 0 && next_all_action.length > 0) {
                    var order_no = 0;
                    next_all_action.each(function() {
                        order_no += 1;
                        if(order_no > 2) {
                            return false;
                        }
                        if($(this).is(':hidden')) {
                            return true;
                        } else if($(this).data('action-type') == 11) {
                            $(this).addClass('jsTmpLockShift');
                            $(this).nextAll('.moduleDetail1').addClass('jsTmpLockShift');
                            return false;
                        }
                    });
                }
                $('.makeStepTypeCont1').sortable('refresh');

                EditCustomizeSkeletonService.showAddModuleSpan($(ui.item));
                EditCustomizeSkeletonService.balloon_element = $(ui.item).find('.textBalloon1').detach();
                $('.deleteModule').children('p').addClass('selected');
                EditCustomizeSkeletonService.updateStepAttribute();
            },
            stop: function(event, ui) {
                EditCustomizeSkeletonService.hideAddModuleSpan();
                $(ui.item).children('.moduleIcon').append(EditCustomizeSkeletonService.balloon_element);
                EditCustomizeSkeletonService.balloon_element = null;
                $('.deleteModule').children('p').removeClass('selected');
                $('.jsTmpLockShift').removeClass('jsTmpLockShift');
                EditCustomizeSkeletonService.changeFlg = true;
                EditCustomizeSkeletonService.initFunction();
            },
        });
    },
    deleteActionModule: function(target) {
        // styleに付属しているのがwidthだけの前提でremoveする
        target.closest('.newSkeletonGroup').removeAttr('style');

        $('.makeStepTypeCont1').sortable('refresh');

        var size = target.parent().children('[data-action-type]').length;
        if(size <= 1) {
            target.closest('.newSkeletonGroup').hide(200, function() {
                target.closest('.newSkeletonGroup').remove();
                EditCustomizeSkeletonService.updateStepAttribute();
            });
            $('.addModuleDetail1').parent().parent().show(200);
        } else {
            target.hide(200, function() {
                target.remove();
                EditCustomizeSkeletonService.updateStepAttribute();
                EditCustomizeSkeletonService.lockPrevCoupon();
                EditCustomizeSkeletonService.setOneModuleLock();
            });
        }
        //メッセージ対応
        $('.selectModuleList').removeClass('selectModuleListActive');

        if (target.parent().hasClass('moduleList_message') && $('.moduleDetail1Drag').length == 2) {
            $('.moduleDetail1Drag').draggable('destroy');
        }
        EditCustomizeSkeletonService.changeFlg = true;
        EditCustomizeSkeletonService.lockShiftDroppable();
    },
    showAddModuleSpan: function(target) {
        if(target.data('action-type') == 25 || target.hasClass('dummyAction')) {
            return false;
        }
        if(target.length > 0) {
            target.addClass('notShowAddR notShowAddL');
        }

        EditCustomizeSkeletonService.hideShowAdd(target);
        $('.makeStepTypeCont1').sortable('refresh');

        $('.addModuleR').each(function() {
            if(!$(this).parent().hasClass('notShowAddR')) {
                $(this).show();
            }
        });
        $('.addModuleL').each(function() {
            if(!$(this).parent().hasClass('notShowAddL')) {
                $(this).show();
            }
        });
        $('.notShowAddR,.notShowAddL').removeClass('notShowAddR notShowAddL');
    },
    hideAddModuleSpan: function() {
        $('span.addModuleR').hide();
        $('span.addModuleL').hide();
    },
    lockShiftDroppable: function() {
        // 場所が動かせないアクションしかグループ内にない場合、droppableイベントをつける
        $('.newSkeletonGroup').each(function() {
            var action_type_li = $(this).find('.moduleDetail1[data-action-type]').length;
            var lock_shift_li = $(this).find('.jsLockShift').length;
            if(action_type_li == 0) {
                if(lock_shift_li > 0) {
                    $('.jsLockShiftDroppable').droppable('destroy');
                    $('.jsLockShift').removeClass('jsLockShiftDroppable');
                }
                return false;
            }
            if(action_type_li == lock_shift_li) {
                $($(this).find('.jsLockShift')[0]).addClass('jsLockShiftDroppable');
                $('.jsLockShiftDroppable').droppable({
                    accept: '.moduleDetail1:not(.dummyAction)',
                    tolerance: "touch",
                    over: function(event, ui) {
                        var li = '<li class="moduleDetail1 moduleDetail1Drag" data-action-type="'+$(ui.draggable).data('action-type')+'"';

                        if(EditCustomizeSkeletonService.updateSkeletonFlg && !$(ui.draggable).hasClass('moduleDetail2') && !$(ui.draggable).find('.moduleIcon').hasClass('new')) {
                            li += 'data-action-id="' + $(ui.draggable).data('action-id') + '" ';
                            li += 'data-type-name="' + $(ui.draggable).data('type-name') + '" ';
                            li += 'data-created="' + $(ui.draggable).data('created') + '" ';
                            li += 'data-title-text="' + $(ui.draggable).data('title-text') + '" ';
                        }
                        li += '><span class="addModuleL" style="display:none">追加する</span>';

                        if($(ui.draggable).find('.moduleIcon').hasClass('new')) {
                            li += '<span class="moduleIcon new">';
                        } else {
                            li += '<span class="moduleIcon">';
                        }

                        li += '<img src="'+$(ui.draggable).find('img').attr('src')+'" width="33" height="33"><span class="textBalloon1">'
                            + EditCustomizeSkeletonService.balloon_element.html() + '</span></span><span class="addModuleR" style="display:none">追加する</span></li>';

                        if(!EditCustomizeSkeletonService.canDragOneActions($(ui.draggable))) {
                            return false;
                        }

                        //lock step1 when drag coupon action
                        if (!EditCustomizeSkeletonService.isDisableDragActions($(this), $(ui.draggable).data('action-type'), $(ui.helper))) {
                            return false;
                        }
                        $(li).insertAfter($(this));
                        $(ui.draggable).hide();
                        setTimeout(function() {
                            $(ui.draggable).remove();
                            EditCustomizeSkeletonService.initFunction();
                            $('.jsLockShiftDroppable').droppable('destroy');
                            $('.jsLockShift').removeClass('jsLockShiftDroppable');
                        }, 200);
                    }
                });
            } else {
                if(lock_shift_li > 0) {
                    $('.jsLockShiftDroppable').droppable('destroy');
                    $('.jsLockShift').removeClass('jsLockShiftDroppable');
                }
                return false;
            }
        });
    },
    initFunction: function() {
        EditCustomizeSkeletonService.updateStepAttribute();
        EditCustomizeSkeletonService.initDroppable();
        EditCustomizeSkeletonService.initActionShiftDroppable();
        EditCustomizeSkeletonService.initDragAction();
        EditCustomizeSkeletonService.lockShiftDroppable();
        EditCustomizeSkeletonService.lockPrevCoupon();
        EditCustomizeSkeletonService.setAnnounceLock();
        EditCustomizeSkeletonService.setOneModuleLock();
    },
    lockPrevCoupon: function() {
        $('.moduleDetail1[data-action-type="11"]').each(function() {
            var prev_action = $(this).prevAll('.moduleDetail1[data-action-type]');
            if(prev_action.length == 1) {
                if(!prev_action.hasClass('jsLockSortable') && !prev_action.hasClass('jsLockShiftDroppable')) {
                    prev_action.addClass('jsLockSortable jsPrevCouponLock');
                    prev_action.children('.moduleIcon').addClass('lock');
                    prev_action.css('cursor','');
                }
            }
        });
        $('.jsPrevCouponLock').each(function() {
            var lock_prev_action = $(this).prevAll('.moduleDetail1[data-action-type]:not(.jsLockSortable)');
            if($(this).next('.moduleDetail1[data-action-type="11"]').length == 0 || lock_prev_action.length > 0) {
                $(this).children('.moduleIcon').removeClass('lock');
                $(this).removeClass('jsLockSortable').removeClass('jsPrevCouponLock');
            }
        });
    },
    setAnnounceLock: function() {
        $('.moduleList[data-required-announce="1"]').each(function() {
            var announce = $(this).children('[data-action-type="3"]');
            if(announce.length == 0) {
                return false;
            }
            if(announce.length == 1) {
                $(announce).addClass('jsLockSortable');
                $(announce).find('.moduleIcon').addClass('lock');
                $(announce).css('cursor','');
            } else {
                $(announce).removeClass('jsLockSortable');
                $(announce).find('.moduleIcon').removeClass('lock');
                $(announce).css('cursor','move');
            }
        });
    },
    setOneModuleLock: function() {
        // モジュールが1つしかない場合はロック
        if($('.moduleDetail1').length == 1 && !$('.moduleDetail1').find('.moduleIcon').hasClass('lock')) {
            $('.moduleDetail1').addClass('jsLockSortable jsOneLock');
            $('.moduleDetail1').find('.moduleIcon').addClass('lock');
            $('.moduleDetail1').css('cursor', '');
        } else if($('.jsOneLock')) {
            $('.jsOneLock').find('.moduleIcon').removeClass('lock').css('cursor','move');
            $('.jsOneLock').removeClass('jsLockSortable').removeClass('jsOneLock')
        }
    },
    isValidFirstActionInFirstGroup: function() {
        var firstAction = $('.moduleList:first').find('.moduleDetail1').filter(':first-child');

        if ($('input[name=cps_type]').val() === '1') {
            if (['0', '5', '27'].indexOf(firstAction.attr('data-action-type')) === -1) {
                alert('キャンペーンのSTEPグループ１は「エントリー」「アンケート」のいずれかのモジュールが最初になるようにモジュールを配置してください。');
                return false;
            }
        } else if ($('input[name=cps_type]').val() === '2') {
            if (['1'].indexOf(firstAction.attr('data-action-type')) === -1) {
                alert('メッセージを配信する際は、STEP1に「メッセージ」が来るように設定をしてください。');
                return false;
            }
        }

        return true;
    },

    isValidLastActionInGroup: function(target) {
        var nAction = $(target).find('.moduleDetail1').length;
        var lastAction = $(target).find('.moduleDetail1').filter(':last');
        if (['1', '3', '9', '11', '25'].indexOf(lastAction.attr('data-action-type')) === -1 ||
            (nAction === 1 && lastAction.attr('data-action_type') === '25')) {
            alert('キャンペーンやメッセージの各STEPグループは「参加完了」「当選通知」「メッセージ」「クーポン」のいずれかのモジュールが最後になるようにモジュールを配置してください。');
            return false;
        }

        return true;
    }
};

$(document).ready(function(){

    $('.deleteModule').droppable({
        accept: '.moduleDetail1',
        activate: function( event, ui ) {
            $('.selectModuleList').addClass('selectModuleListActive');
        },
        drop: function(even, ui) {
            if(!$(this).children('p').hasClass('selected')) {
                return false;
            }
            if ($.inArray($(ui.draggable).data('action-type'), [12, 13, 16]) >= 0) {
                //lock action
                $('.moduleDetail2[data-action-type="'+$(ui.draggable).data('action-type')+'"]').draggable('enable');
            }

            if(typeof $(ui.draggable).attr('data-action-id') != 'undefined' && $(ui.draggable).data('action-id') != '-1') {
                // 消えたように見せるために一時的にmoduleDetail1をhideする
                var module_count = $(ui.draggable).parent().children('[data-action-type]').length;
                $(ui.draggable).closest('.newSkeletonGroup').width(54 * module_count);
                $(ui.draggable).hide();
                $('#modal_confirm_action_delete').find('.middle1').on('click', function() {
                    $(ui.draggable).closest('.newSkeletonGroup').removeAttr('style');
                    $(ui.draggable).show(200);
                });

                $('.jsActionType').html($(ui.draggable).data('type-name'));
                $('.jsActionCreate').html($(ui.draggable).data('created'));
                $('.jsActionTitle').html($(ui.draggable).data('title-text'));
                Brandco.unit.openModal("#modal_confirm_action_delete");

                $('#executeActionDelete').on('click', function() {
                    $(ui.draggable).hide();
                    EditCustomizeSkeletonService.deleteActionModule($(ui.draggable));
                    Brandco.unit.closeModal("_confirm_action_delete");
                });
            } else {
                EditCustomizeSkeletonService.deleteActionModule($(ui.draggable));
            }
        }
    });

    $('.addModuleDetail1').click(function(){
        var newElement = '<li class="stepDetail_require newSkeletonGroup" style="display: none"><h1>STEP'+($('.newSkeletonTag').find('.moduleDetail1').length+1)+'</h1><ul class="moduleList">'+
        '<li class="moduleDetail1 dummyAction" data-action-type=""><span class="moduleIcon"><img src="'+$('base').data('static-href')+'/img/dummy/02.jpg" height="33" width="33" alt="dummy">' +
        '<span><span></span></span></span></li><!-- /.moduleList --></ul><!-- /.stepDetail_require --></li>';
        $(newElement).insertBefore($(this).parent().parent());
        $('.newSkeletonGroup').show(200);

        EditCustomizeSkeletonService.initFunction();
        EditCustomizeSkeletonService.initDummyActionDrop();
        EditCustomizeSkeletonService.setReloadMessage();
        EditCustomizeSkeletonService.changeFlg = true;

        //update step width
        Brandco.admin.executeStepListWith();
    });

    $('.newSkeletonSubmitButton').click(function(){
        $(window).unbind('beforeunload');
        var LOCK = false,
            i = 0,
            data = [];

        if (!EditCustomizeSkeletonService.isValidFirstActionInFirstGroup()) {
            LOCK = true;
            return false;
        }

        $('.newSkeletonGroup').each(function(){
            var breakFlag = false;
            data[i] = [];
            $(this).find('.moduleDetail1').each(function(){
                if ($(this).hasClass('dummyAction')) {
                    alert('設定していないアクションがあります。');
                    breakFlag = true;
                } else {
                    data[i].push($(this).data('action-type'));
                }
            });

            // canDrag
            if ($(this).find('.moduleList').data('disable-actions') !== 'all') {
                if (!EditCustomizeSkeletonService.isValidLastActionInGroup(this)) {
                    breakFlag = true;
                }
            }

            if (breakFlag) {
                LOCK = true;
                return false;
            } else {
                ++i;
            }
        });

        if (!LOCK) {
            var group_count = $('.newSkeletonGroup').length;
            $('#newSkeletonGroupCount').val(group_count);
            for (var i = 0;i<group_count; i++){
                $('#newSkeletonForm').append('<input type="hidden" name="group'+(i+1)+'" value="'+data[i]+'">');
            }
            document.newSkeletonForm.submit();
        } else {
            return false;
        }
    });

    EditCustomizeSkeletonService.initActionDraggable();
    EditCustomizeSkeletonService.initAddModuleDrop();
    EditCustomizeSkeletonService.initFunction();

    $("#cancelChanges").click(function() {
        if(EditCustomizeSkeletonService.changeFlg) {
            Brandco.unit.openModal('#modal_confirm_cancel');
        } else {
            Brandco.unit.closeModalFlame(this);
        }
    });

    $("#executeCancel").click(function() {
        Brandco.unit.closeModalFlame(this);
    });

    $("#confirmSave").click(function() {
        var LOCK = false,
            i = 0,
            data = [],
            groupUpdate = [],
            actionUpdate = [];

        if (!EditCustomizeSkeletonService.isValidFirstActionInFirstGroup()) {
            LOCK = true;
            return false;
        }

        $('.newSkeletonGroup').each(function(){
            var breakFlag = false;
            data[i] = [];
            actionUpdate[i] = [];
            groupUpdate.push($(this).data('group-id') > 0 ? $(this).data('group-id') : -1);
            $(this).find('.moduleDetail1').each(function(){
                if ($(this).hasClass('dummyAction')) {
                    alert('設定していないアクションがあります。');
                    breakFlag = true;
                    return false;
                } else {
                    data[i].push($(this).data('action-type'));
                    actionUpdate[i].push($(this).data('action-id') > 0 ? $(this).data('action-id') : -1 );
                }
            });

            // canDrag
            if ($(this).find('.moduleList').data('disable-actions') !== 'all') {
                if (!EditCustomizeSkeletonService.isValidLastActionInGroup(this)) {
                    breakFlag = true;
                }
            }

            if (breakFlag) {
                LOCK = true;
                return false;
            } else {
                ++i;
            }
        });
        if (!LOCK) {
            var group_count = $('.newSkeletonGroup').length;
            $('#newSkeletonGroupCount').val(group_count);
            for (var i = 0;i<group_count; i++) {
                $("#updateSkeletonForm").append('<input type="hidden" name="group'+(i+1)+'" value="'+data[i]+'">');
                $('#updateSkeletonForm').append('<input type="hidden" name="groupUpdate'+'" value="'+groupUpdate+'">');
                $('#updateSkeletonForm').append('<input type="hidden" name="actionUpdate'+(i+1)+'" value="'+actionUpdate[i]+'">');
            }
            $(window).unbind('beforeunload');
            document.updateSkeletonForm.submit();
        }
    });
});
