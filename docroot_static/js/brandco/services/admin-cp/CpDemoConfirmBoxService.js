$('.resetDemoButton').click(function() {
    $(".demoResetConfirmed").attr('data-cp-id', $(this).data('cp-id'));
    Brandco.unit.openModal("#modal_demo_reset_confirm");
});

$('.resetOneDemoButton').click(function() {
    $(".demoResetConfirmed").attr('data-cp-id', $(this).data('cp-id'));
    Brandco.unit.openModal("#modal_demo_reset_one_confirm");
});

$('.cancelDemoButton').click(function() {
    $("#demoCancelConfirmed").attr('data-cp-id', $(this).data('cp-id'));
    Brandco.unit.openModal("#modal_demo_cancel");
});

$(".demoResetConfirmed").click(function() {
    var reset_one = $(this).attr('data-reset-one');
    if(typeof reset_one == "undefined") {
        reset_one = 0;
    }
    var param = {
        data: "cp_id="+$(this).attr('data-cp-id')+'&reset_one_flg='+reset_one+'&csrf_token='+document.getElementsByName("csrf_token")[0].value,
        url: $(this).data('url'),
        success: function(data){
            if(data.result == 'ok'){
                if (data.data.call_back_url) {
                    location.href = data.data.call_back_url;
                } else {
                    // open message
                    Brandco.helper.reloadWithMIDMessage('reset-demo-data');
                }
            } else {
                Brandco.unit.closeModal("_demo_reset_one_confirm");
                if (data.errors.message) {
                    $('#modal_demo_reset_error').find('.attention1').html(data.errors.message);
                    Brandco.unit.openModal("#modal_demo_reset_error");
                }
            }
        }
    }
    Brandco.api.callAjaxWithParam(param);
});

$("#demoCancelConfirmed").click(function() {
    var param = {
        data: "cp_id="+$(this).attr('data-cp-id')+'&csrf_token='+document.getElementsByName("csrf_token")[0].value,
        url: $(this).data('url'),
        success: function(data){
            if(data.result == 'ok'){
                // open message
                location.href = 'admin-cp/edit_setting_basic/'+data.data.cp_id+'?mid=canceled_demo';
            }
        }
    }
    Brandco.api.callAjaxWithParam(param);
});