var EditSettingBasicService = (function(){
    return {
        init: function(){
            if ($('.jsAnnounceDisplayLabelUseFlg:checked').val() == 0) {
                $('.jsAnnounceDisplayLabel').attr('disabled', true);
            } else {
                console.log($('.jsAnnounceDisplayLabelUseFlg:checked').val());
                $('.jsAnnounceDisplayLabel').attr('disabled', false);
            }
        }
    }
})();

$(document).ready(function(){
    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker();

    $('#submit').click(function(){
        $('.jsAnnounceDate').attr('disabled', false);
        $(window).unbind('beforeunload');
        $('#save_type').val(1);
        document.actionForm.submit();
    });

    //スピードくじの応募終了日時を変えた時に、当選発表日をその次の日に設定する
    $('.jsPublicDate').change(function(){
        var date = new Date($(this).val());
        date.setDate(date.getDate()+1);
        var public_date = date.getFullYear() + '/' + ('0'+(date.getMonth()+1)).slice(-2) + '/' + ('0'+(date.getDate())).slice(-2);
        $('.jsAnnounceDate').val(public_date);
    });

    $('#cancelSchedule').click(function(){
        $("#actionForm").attr("action", $(this).data("action"));
        document.actionForm.submit();
    });

    $('#editButton').click(function(){
        Brandco.helper.edit_cp($(this));
    });

    $('#submitDraft').click(function(){
        $('.jsAnnounceDate').attr('disabled', false);
        $(window).unbind('beforeunload');
        $('#save_type').val(0);
        document.actionForm.submit();
    });

    $('.actionImage0').on('change', function(){
        if ($(this)[0].files && $(this)[0].files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cpImage').attr('src', e.target.result);
                $('#cpImage').show();
            }
            reader.readAsDataURL($(this)[0].files[0]);
        } else {
            $('#cpImage').attr('src', '');
        }
    });

    $('.actionImage1').on('change', function(){
        if ($(this)[0].files && $(this)[0].files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cpRecImage').attr('src', e.target.result);
                $('#cpRecImage').show();
            }
            reader.readAsDataURL($(this)[0].files[0]);
        } else {
            $('#cpRecImage').attr('src', '');
        }
    });

    $( ":input").each(function(){
        $(this).change(function(){
            $(window).unbind('beforeunload');
            Brandco.helper.set_reload_alert(Brandco.message.reloadMessage);
        });
    });

    $('.settingClose').click(function(){
        if ($('#editButton')[0]) {
            return false;
        }
        $('input[name="closeTimeDate"]').removeAttr('checked');
        $('.closeTimeDate').hide();
        $('.endTime').show();
    });

    $('.iconBtnDelete').click(function(){
        $('input[name="closeTimeDate"]').attr('checked','checked');
        $('.endTime').hide();
        $('.closeTimeDate').show();
    });

    // commone check toggle area
    Brandco.helper.doJsCheckToggle();

    $('.jsCheckToggle').each(function(){
       if ($(this).is(':checked')) {
           var targetWrap = $(this).parents('.jsCheckToggleWrap')[0];
           $(targetWrap).find('.jsCheckToggleTarget').slideToggle(300);
       }
    });

    Brandco.admin.adminCpInit();

    $('.jsAllCheck').each(function() {
        var jsSingleCheck = $(this).parents('dt').next('dd').find('.jsSingleCheck');

        if ($(jsSingleCheck).length == $(jsSingleCheck).filter(':checked').length) {
            $(this).prop('checked', 'checked');
        }
    });

    $('.jsSingleCheck').on('change', function() {
        var jsSingleCheck = $(this).parent('dd').find('.jsSingleCheck');
        var jsAllCheck = $(this).parent('dd').prev('dt').find('.jsAllCheck');

        if ($(jsSingleCheck).length == $(jsSingleCheck).filter(':checked').length) {
            $(jsAllCheck).prop('checked', 'checked');
        } else {
            $(jsAllCheck).prop('checked', null);
        }
    });

    $('.jsAnnounceDisplayLabelUseFlg').on('change', function(){
        if ($(this).val() == 0) {
            $('.jsAnnounceDisplayLabel').attr('disabled', true);
            $('.jsAnnounceDisplayLabel').val('');
        } else {
            $('.jsAnnounceDisplayLabel').attr('disabled', false);
        }
    });

    if ($('.jsIsDisableWhenFixAction').data('disabled') != 'disabled') {
        EditSettingBasicService.init();
    }

    $('.jsRefUrlTypeToggle').on('click', function() {
        var targetWrap = $(this).parent('.jsRefUrlTypeToggleWrap').find('.jsRefUrlTypeToggleTarget');

        if ($(this).val() == 1) {
            $(targetWrap).prop('disabled', "");
        } else {
            $(targetWrap).prop('disabled', true);
        }
    })

    // Checking campaign type
    $('.jsPermanentToggle').on('change', function() {
        var targetWrap = $(this).parents('.jsPermanentToggleWrap')[0];

        if ($(this).val() == '1') {
            $(targetWrap).find('.jsPermanentToggleTarget').hide();
        } else {
            $(targetWrap).find('.jsPermanentToggleTarget').show();
        }
    });

    $('.jsToggleSettingPublicDate').on('change', function() {
        var span_setting_public_date = $('.jsSettingPublicDate');

        if ($(this).is(':checked')) {
            span_setting_public_date.show();
        } else {
            span_setting_public_date.hide();
        }
    })

});

function useCloseMode() {
    if(document.actionForm.use_cp_page_close_flg.checked) {
        document.actionForm.cp_page_close_date.disabled = "";
        document.actionForm.cpPageCloseTimeHH.disabled  = "";
        document.actionForm.cpPageCloseTimeMM.disabled  = "";
    } else {
        document.actionForm.cp_page_close_date.disabled = "true";
        document.actionForm.cpPageCloseTimeHH.disabled  = "true";
        document.actionForm.cpPageCloseTimeMM.disabled  = "true";
    }
}
