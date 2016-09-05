var EditShippingAddressActionService = {
        isShowOption: true,
        bindShippingAddressRequiredClick: function(){
            $('[name=name_required]:checkbox').click(function(){
                $('.element_name').each(function(){
                    $(this).toggle();
                    if($(this).css('display') == 'block') {
                        $(this).css('display', '');
                    }
                });
            })
            $('[name=address_required]:checkbox').click(function(){
                $('.element_address').each(function(){
                    $(this).toggle();
                    if($(this).css('display') == 'block') {
                        $(this).css('display', '');
                    }
                });
            })
            $('[name=tel_required]:checkbox').click(function(){
                $('.element_tel').each(function(){
                    $(this).toggle();
                    if($(this).css('display') == 'block') {
                        $(this).css('display', '');
                    }
                });
            })
        },
        initPreview: function() {
            if($('[name=name_required]:checkbox').prop('checked')) {
                $('.element_name').each(function(){
                    $(this).show();
                });
            } else{
                $('.element_name').each(function(){
                    $(this).hide();
                });
            }
            if($('[name=address_required]:checkbox').prop('checked')) {
                $('.element_address').each(function(){
                    $(this).show();
                });
            } else{
                $('.element_address').each(function(){
                    $(this).hide();
                });
            }
            if($('[name=tel_required]:checkbox').prop('checked')) {
                $('.element_tel').each(function(){
                    $(this).show();
                });
            } else{
                $('.element_tel').each(function(){
                    $(this).hide();
                });
            }
        }
};

$(document).ready(function(){
    EditShippingAddressActionService.bindShippingAddressRequiredClick();
    EditShippingAddressActionService.initPreview();

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker({
            minDate: new Date()
    });
});
