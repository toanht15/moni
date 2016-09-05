var SegmentMessageActionService = (function() {

    return {
        toggleSegmentConditionContainer: function(trigger) {
            var target = $(trigger).parents('.jsSegmentToggleWrap').find('.jsSegmentToggleTarget');
            if (target.is(':hidden')) {
                target.fadeIn(200);
            } else {
                target.fadeOut(200);
                SegmentMessageActionService.synchSessionCondition();
            }
        },
        dojsSegmentCheck: function(target) {
            var anchor = $(target).closest('.jsSegmentContainerToggleWrap');
            anchor.find('input').prop('checked', $(target).prop('checked'));
        },
        synchSessionCondition: function() {

            var provision_id_array = jQuery.parseJSON($('input[name=provision_id_array]').val());

            $('.jsSegmentCheckbox').prop('checked', false);

            $('.jsSegmentCheckbox').each(function(){

                if(jQuery.inArray($(this).val(), provision_id_array) >= 0) {
                    $(this).prop('checked', 'checked');
                    var target = $(this).parents('.jsSegmentContainerToggleTarget').find('input');

                    if (target.length == target.filter(':checked').length) {
                        $(this).parents('.jsSegmentContainerToggleWrap').find('.jsSegmentCheck').prop('checked', 'checked');
                    }
                }
            });
        },
        deleteSegmentSession: function(segment_id, sp_id) {

            var condition = jQuery.parseJSON($('input[name=segment_condition_session]').val());

            var target_condition = condition[segment_id];

            target_condition.splice($.inArray(sp_id, target_condition),1);

            if(target_condition.length == 0) {

                delete condition[segment_id];

                if(jQuery.isEmptyObject(condition)) {
                    $('.jsSegmentPresetInfo').hide();
                }

            } else {
                condition[segment_id] = target_condition;
            }

            $('input[name=segment_condition_session]').val(JSON.stringify(condition));

            var page_info = $('[name="page_info"]').attr('value').split("/");
            ShowCpUserListService.get_fan(1, page_info[1], page_info[2], null, null, $('select[name="fan_limit"]').val(), null);
        },
        applySegmentCondition: function() {

            var condition = {};

            $('.jsSegmentCheckbox:checked').each(function(){

                if($(this).val() == 'on') {
                    return true;
                }

                var sp_id = $(this).val();
                var segment_id  = $(this).closest('.jsSegment').data('segment_id');

                var provision_ids = condition[segment_id];
                if(provision_ids == undefined){
                    condition[segment_id] = [sp_id];
                } else {
                    provision_ids.push(sp_id);
                    condition[segment_id] = provision_ids;
                }
            });

            $('input[name=segment_condition_session]').val(JSON.stringify(condition));

            var page_info = $('[name="page_info"]').attr('value').split("/");
            ShowCpUserListService.get_fan(1, page_info[1], page_info[2], null, null, $('select[name="fan_limit"]').val(), null);
        }
    }
})();

$(document).ready(function () {
    $(document).on('click', '.jsSegmentToggle' , function() {
        SegmentMessageActionService.toggleSegmentConditionContainer(this);
    });

    $(document).on('click', '.jsApplySegmentCondition' , function() {
        SegmentMessageActionService.applySegmentCondition(this);
    });

    // checked decision
    $(document).on('click', '.jsSegmentContainerToggleTarget input', function() {
        var target = $(this).parents('.jsSegmentContainerToggleTarget').find('input');
        if (target.length == target.filter(':checked').length) {
            $(this).parents('.jsSegmentContainerToggleWrap').find('.jsSegmentCheck').prop('checked', 'checked');
        } else {
            $(this).parents('.jsSegmentContainerToggleWrap').find('.jsSegmentCheck').prop('checked', false);
        }
    });

    $(document).on('click', '.jsDeleteSegmentConditionSession' , function() {

        var sp_name = $(this).data('provision_name');
        var sp_id = $(this).data('provision_id');
        var segment_id = $(this).data('segment_id');

        $('#modal11').find('.jsSPName').text(sp_name);

        Brandco.unit.openModal("#modal11");

        $("#modal11").find('.btn4').off('click');

        $("#modal11").find('.btn4').click(function(){
            Brandco.unit.closeModal(11);
            SegmentMessageActionService.deleteSegmentSession(segment_id, sp_id);
        });
    });

    // all checked
    $(document).on('change', '.jsSegmentCheck', function () {
        SegmentMessageActionService.dojsSegmentCheck(this);
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
    }, '.segmentItemInner');

    $(document).on({
        'mouseleave': function() {
            $('.jsHoverTooltip').stop(true, true).fadeOut(200);
        }
    },'.segmentItemList');
});

