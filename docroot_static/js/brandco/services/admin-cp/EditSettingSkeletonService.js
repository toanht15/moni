var EditSettingSkeletonService = {
    initDeleteButton: function () {
        $(".submitDeleteCpForm").click(function() {
            $('#delete_cp').attr('data-cpid',$(this).closest("section").attr('data-cp-id'));
            Brandco.unit.openModal('#modal1');
        });
    },
    initCopyButton: function () {
        $('.copyCP').click(function(event) {
            event.preventDefault();
            $('#deleteCpForm')[0].cp_id.value = $(this).closest("section").attr('data-cp-id');
            var csrf_token = document.getElementsByName("csrf_token")[0].value;
            $('#deleteCpForm').append('<input type="hidden" name="csrf_token" value="'+csrf_token+'">');
            $('#deleteCpForm')[0].action = $('#deleteCpForm').data('create-url');
            $('#deleteCpForm').submit();
        });
    },
    setReloadMessage: function() {
        $(window).unbind('beforeunload');
        Brandco.helper.set_reload_alert(Brandco.message.reloadMessage);
    },
    initAnnounceType: function(radioButton) {
        var radioButton = $("input[name='announce_type']:radio:checked"),
            opposite_set = {1: 'coupon', 2: 'instantWin'};


        $('#skeleton_url').attr('href',$('#skeleton_url').attr('href').replace(/&announce=[^&]*/, '&announce='+radioButton.val()));

        for (var value in opposite_set) {
            if (radioButton.val() == value) {
                $('#'+opposite_set[value]).removeAttr('disabled');
                $('#text_'+opposite_set[value]).remove();
                $('#'+opposite_set[value]).prop('checked', true);
                $('#skeleton_url').attr('href',$('#skeleton_url').attr('href').replace(/&basic_type=[^&]*/, '&basic_type='+$("input[name='basic_type']:radio:checked").val()));
            } else {
                if ($('#'+opposite_set[value]).is(':checked')) {
                    $('#'+opposite_set[value]).prop('checked', false);
                    $('#present').prop('checked', true);
                    $('#skeleton_url').attr('href',$('#skeleton_url').attr('href').replace(/&basic_type=[^&]*/, '&basic_type=1'));
                }
                $('#'+opposite_set[value]).prop('disabled', true);
                if (!$('#text_'+opposite_set[value])[0]) {
                    $('label[for="'+opposite_set[value]+'"]').append('<span class="textBalloon1" id="text_'+opposite_set[value]+'"><span>この条件では開催できません</span></span>');
                }
            }
        }
        if (radioButton.val() == 1) {
            $('#gift').removeAttr('disabled');
            $('#text_gift').remove();
        } else {
            $('#gift').prop('disabled', true);
            if (!$('#text_gift')[0]) {
                $('label[for="gift"]').append('<span class="textBalloon1" id="text_gift"><span>この条件では開催できません</span></span>');
            }
        }

        if (radioButton.val() == 2) {
            var package_set = ['present', 'photo', 'movie', 'questionnaire'];
            package_set.forEach(function(basic_type){
                if ($('#'+basic_type).is(':checked')) {
                    $('#'+basic_type).prop('checked', false);
                }
                $('#'+basic_type).prop('disabled', true);
                if (!$('#text_'+basic_type)[0]) {
                    $('label[for="'+basic_type+'"]').append('<span class="textBalloon1" id="text_'+basic_type+'"><span>この条件では開催できません</span></span>');
                }
            });
        } else {
            var package_set = ['present', 'photo', 'movie', 'questionnaire'];
            package_set.forEach(function(basic_type){
                if ($('#'+basic_type).is(':disabled')) {
                    $('#'+basic_type).prop('disabled', false);
                }
                if ($('#text_'+basic_type)[0]) {
                    $('#text_'+basic_type).remove();
                }
            });
        }

        if (radioButton.val() == 3) {
            var shipping_addresses = ['none', 'elected', 'all'];
            $.each(shipping_addresses, function(){
                if (this == 'all') {
                    $('#shipping_address_'+this).prop('checked', true);
                    $('#shipping_address_'+this).prop('disabled', false);
                    $('#skeleton_url').attr('href',$('#skeleton_url').attr('href').replace(/&shipping=[^&]*/, '&shipping='+this));
                } else {
                    $('#shipping_address_'+this).prop('checked', false);
                    $('#shipping_address_'+this).prop('disabled', true);
                }
            });
        } else {
            $('#shipping_address_none').prop('disabled', false);
            $('#shipping_address_elected').prop('disabled', false);
        }
    },
    onChangeCpTemplate: function(target) {
        if (!$(target).is('.current')) {
            var temp_data = '';
            var temp_action = '';
            var temp_url = $(target).data('temp_url');
            var cur_temp = $('.current');

            temp_data = cur_temp.html();
            temp_action = cur_temp.data('temp_type');
            cur_temp.children().detach();
            cur_temp.append('<a href="javascript:void(0)">' + temp_data + '</a>');
            cur_temp.removeClass('current');
            $('#' + temp_action).hide();

            temp_data = $(target).html();
            temp_action = $(target).data('temp_type');
            $(target).children().detach();
            $(target).append(temp_data);
            $(target).addClass('current');
            $('#' + temp_action).show();

            $('.jsCpTempSubmitBtn').attr('href', temp_url);
        }
    }
};

$(document).ready(function(){

    //init draft pager
    var draftPager = new Brandco.paging(1, 1, 5, 'draftPage', '#DraftCpContainer', '.draftCp');
    draftPager.initPageClickEvent(draftPager, EditSettingSkeletonService.initDeleteButton);

    //init copy pager
    var copyPager = new Brandco.paging(1, 1, 5, 'copyCpPage', '#CopyCpContainer', '.copyCp');
    copyPager.initPageClickEvent(copyPager, EditSettingSkeletonService.initCopyButton);

    //最初状態作る
    EditSettingSkeletonService.initAnnounceType();
    $('#skeleton_url').attr('href',$('#skeleton_url').attr('href').replace(/&shipping=[^&]*/, '&shipping='+$("input[name='shipping_address']:radio:checked").val()));
    $('#skeleton_url').attr('href',$('#skeleton_url').attr('href').replace(/&basic_type=[^&]*/, '&basic_type='+$("input[name='basic_type']:radio:checked").val()));
    $('#skeleton_url').attr('href',$('#skeleton_url').attr('href').replace(/&join_limit_flg=[^&]*/, '&join_limit_flg='+$("input[name='join_limit_flg']:radio:checked").val()));

    $("input[name='shipping_address']:radio").on('change', function() {
        $('#skeleton_url').attr('href',$('#skeleton_url').attr('href').replace(/&shipping=[^&]*/, '&shipping='+$(this).val()));
    });

    $("input[name='join_limit_flg']:radio").on('change', function() {
        $('#skeleton_url').attr('href',$('#skeleton_url').attr('href').replace(/&join_limit_flg=[^&]*/, '&join_limit_flg='+$(this).val()));
    });

    $("input[name='announce_type']:radio").on('change', function() {
        EditSettingSkeletonService.initAnnounceType();
    });

    $("input[name='basic_type']:radio").on('change', function() {
        $('#skeleton_url').attr('href',$('#skeleton_url').attr('href').replace(/&basic_type=[^&]*/, '&basic_type='+$(this).val()));
    });

    EditSettingSkeletonService.initDeleteButton();
    EditSettingSkeletonService.initCopyButton();

    $('#delete_cp').click(function() {
        var csrf_token = document.getElementsByName("csrf_token")[0].value;
        $('#deleteCpForm').append('<input type="hidden" name="csrf_token" value="'+csrf_token+'">');
        $('#deleteCpForm')[0].action = $('#deleteCpForm').data('delete-url');
        $('#deleteCpForm')[0].cp_id.value = $(this).attr('data-cpid');
        $('#deleteCpForm').submit();
    });

    $('.jsCpTemplateToggle').on('click', function() {
        EditSettingSkeletonService.onChangeCpTemplate(this);
    });
});
