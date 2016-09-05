if (typeof(UserActionPaymentService) === 'undefined') {
    var UserActionPaymentService = (function() {
        return {
            executePaymentSkip: function(target, isAutoLoad) {
                $('.cmd_execute_payment_skip_action').off('click');
                event.preventDefault();
                section = $(target).parents().filter(".jsMessage");

                var param = {
                    data: {
                        'csrf_token':$(':hidden[name="csrf_token"]').val() ,
                        'cp_action_id': target.getAttribute('data-cp_action_id'),
                        'cp_user_id':target.getAttribute('data-cp_user_id')
                    } ,
                    url: target.getAttribute('data-url'),
                    beforeSend: function () {
                        Brandco.helper.showLoading(section);
                    },
                    success: function (json) {
                        if (json.result === "ok") {
                            if (json.data.next_action === true) {
                                var message = $(json.html);
                                section.after(message);
                                $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                                    Brandco.unit.createAndJumpToAnchor();
                                });
                            }
                        } else {
                            alert("エラーが発生しました");
                        }
                    },
                    complete: function () {
                        Brandco.helper.hideLoading();
                    }
                };
                Brandco.api.callAjaxWithParam(param, false, false);
            },
        };
    })();
}

$(document).ready(function() {
    $('.cmd_execute_payment_skip_action').off('click');
    $('.cmd_execute_payment_skip_action').on('click', function(event) {
        event.preventDefault();
        UserActionPaymentService.executePaymentSkip(this);
    });
    $('.jsModuleContTile').click(function(){
        var trigger = $(this);
        var target = trigger.next('.jsModuleContTarget');
        if(trigger.hasClass('close')) {
            target.slideDown(200, function() {
                trigger.removeClass('close');
            });
        }else{
            target.slideUp(200, function() {
                trigger.addClass('close');
            });
        }
        // return false;
    });
    $('.jsModuleContText').click(function(){
        var trigger = $(this).parents('.jsModuleContWrap');
        var target = trigger.find('.jsModuleContTarget');

        if(trigger.hasClass('close')) {
            target.slideDown(200, function() {
                trigger.removeClass('close');
            });
        }else{
            target.slideUp(200, function() {
                trigger.addClass('close');
            });
        }
        return false;
    });


});
