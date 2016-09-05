var EditActionQuestionnaireService = (function(){
    return{
        init: function (action_id, questionnaire_action_id, url, status) {
            var action_status = status == 'disabled' ? '1' : '0';
            var data = {action_id:action_id, cp_questionnaire_action_id:questionnaire_action_id, status:action_status};
            var url = url;
            var param = {
                data: data,
                type: 'GET',
                url: url,
                success: function(data) {
                    $('#moduleEnqueteList').append(data.html);
                }
            };
            Brandco.api.callAjaxWithParam(param);
        }
    };
})();

$(document).ready(function(){

    $('#editButton').on('click',function(){
        Brandco.helper.edit_cp($(this));
    });

    $('.labelTitle').change(function(){
        $('.actionImage').attr('disabled', 'disabled');
        $(this).parents('li').find('.actionImage').removeAttr('disabled');
    });

    $( ":input").each(function(){
        $(this).change(function(){
            $(window).unbind('beforeunload');
            Brandco.helper.set_reload_alert(Brandco.message.reloadMessage);
        });
    });

    EditActionQuestionnaireService.init($('*[name=action_id]').attr('value'), $('.moduleEnqueteList').data('cp_questionnaire_action_id'), $('.moduleEnqueteList').data('url'), $('.moduleEnqueteList').data('disable'));

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker({
        minDate: new Date()
    });
});
