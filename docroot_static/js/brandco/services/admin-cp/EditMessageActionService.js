if (typeof(EditMessageActionService) === 'undefined') {
    var EditMessageActionService = function() {
        return {
            switchBetweenTabs: function() {
                var is_login = $('.jsTab.current').attr('data-login');

                $('.jsTab[data-login=' + is_login + ']').removeClass('current');
                $('.jsTab[data-login=' + EditMessageActionService.invertBit(is_login) + ']').addClass('current');
            },

            refreshBtn: function() {
                var is_login = $('.jsTab.current').attr('data-login');

                if (parseInt(is_login) === 1) {
                    EditMessageActionService.refreshNextBtn();
                    $('.jsLoginBtn').hide();
                } else {
                    $('.jsNextBtn').hide();
                    $('.jsLoginBtn').show();
                }
            },

            refreshNextBtn: function() {
                if ($('input[name=manual_step_flg]').is(':checked')) {
                    $('.jsNextBtn').show();
                } else {
                    $('.jsNextBtn').hide();
                }
            },

            invertBit: function(bit) {
                if (parseInt(bit) === 0) {
                    return 1;
                } else {
                    return 0;
                }
            }
        };
    }();
}

$(document).ready(function(){
    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker({
        minDate: new Date()
    });

    $('section.modulePreview1').on('click', '.jsTab', function() {
        if (!$(this).hasClass('current')) {
            EditMessageActionService.switchBetweenTabs();
            EditMessageActionService.refreshBtn();
        }
    });

    $('section.moduleEdit1').on('change', '.jsManualStepFlg', function(){
        EditMessageActionService.refreshBtn();
    });
});
