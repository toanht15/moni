var ManagerKpiService = (function(){
    return{
        updateKpiValue: function (obj){
            var div = obj.name.split('__');
            var csrf_token = $('[name=csrf_token]')[0].value;
            var data = {csrf_token:csrf_token, value:obj.value, column_id:div[0], summed_date:div[1]};
            var url = '/api/api_update_kpi_value.json';
            var param = {
                data: data,
                url: url,
                success: function(){
                    $('.autoSave').stop().animate({opacity: 1}, 300);
                }
            };
            Brandco.api.callAjaxWithParam(param ,false ,false);
        },
        numOnly: function() {
            if((event.keyCode >= 48 && event.keyCode <= 57)  ||
                (event.keyCode >= 96 && event.keyCode <= 105) ||
                event.keyCode == 13  || //enter
                event.keyCode == 8  || //backspace
                event.keyCode == 37  || //arrow
                event.keyCode == 38  || //arrow
                event.keyCode == 39  || //arrow
                event.keyCode == 40  || //arrow
                event.keyCode == 9   || //tab
                event.keyCode == 46  || //delete
                event.keyCode == 189 || //minus
                event.keyCode == 229  //minus
                ){
                return true;
            }
            return false;
        }
    };
})();
$(".autoSave").blur( function () {
    $(this).stop().animate({opacity: 0.5}, 300);
    ManagerKpiService.updateKpiValue(this);

});

$('.jsDate').on('click', function(){
    $('#search_mode_date').prop('checked', true);
});

$(function(){
    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker();
});