$(document).ready(function(){

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker({
        minDate: new Date()
    });

    $('#delete_code_auth').click(function(){
        var csrf_token = document.getElementsByName("csrf_token")[0].value,
            param = {
                data: {'code_auth_id': $(this).data('code_auth_id'), 'csrf_token': csrf_token},
                url: 'admin-code-auth/api_delete_code_auth.json',
                success: function(data) {
                    if (data.result == 'ok') {
                        window.location.href = $('base').attr('href') + 'admin-code-auth/code_auth_list?mid=coupon_deleted';
                    }
                }
            };
        Brandco.api.callAjaxWithParam(param);
    });

    if ($('#modal2').find('.attention1')[0]) {
        Brandco.unit.openModal('#modal2');
    }

    $('.non_expire_date').each(function(){
        if ($(this).is(':checked')) {
            $(this).closest('td').find('.inputDate').prop('disabled', true);
        } else {
            $(this).closest('td').find('.inputDate').prop('disabled', false);
        }
    });

    $('.non_expire_date').on('change', function(){
        if ($(this).is(':checked')) {
            $(this).closest('td').find('.inputDate').prop('disabled', true);
        } else {
            $(this).closest('td').find('.inputDate').prop('disabled', false);
        }
    });
});