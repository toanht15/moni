if(typeof(UserActionShippingAddressService) === 'undefined') {
    var UserActionShippingAddressService = (function () {
        return{

            executeShippingAddressAction: function (target) {
                // Remove the event to prevent duplicate submission.
                $(".cmd_execute_shipping_address_action").off("click");

                var form = $(target).parents().filter(".executeShippingAddressActionForm");
                var section = $(target).parents().filter(".jsMessage");
                var url = form.attr("action");
                var data = $('.executeShippingAddressActionForm').serialize();
                var param = {
                    data: data,
                    url: url,
                    beforeSend: function () {
                        Brandco.helper.showLoading(section);
                    },
                    success: function (json) {
                        try {
                            $('.iconError1[id^=error_shipping_address_]').hide();
                            if (json.result === "ok") {
                                Brandco.unit.disableForm($(target).closest('form'));
                                if (json.data.next_action === true) {
                                    var message = $(json.html);
                                    message.hide();
                                    section.after(message);

                                    Brandco.helper.facebookParsing(json.data.sns_action);

                                    $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                                        Brandco.unit.createAndJumpToAnchor();
                                        $(target).replaceWith('<span class="large1">送信完了</span>');
                                    });
                                } else {
                                    $(target).replaceWith('<span class="large1">送信完了</span>');
                                }
                            } else {
                                var answer_err = 0;
                                $('.iconError1[id^=error_shipping_address_]').hide();
                                $.each(json.errors, function (i, value) {
                                    if (form.find('span#error_shipping_address_' + i)) {
                                        form.find('span#error_shipping_address_' + i).html(value);
                                        form.find('span#error_shipping_address_' + i).show();
                                        answer_err = 1;
                                    }
                                });
                                if (!answer_err) {
                                    alert("エラーが発生しました");
                                }
                            }
                        } finally {
                            // Add the event.
                            $(".cmd_execute_shipping_address_action").on("click", function (event) {
                                event.preventDefault();
                                UserActionShippingAddressService.executeShippingAddressAction(this);
                            });
                        }
                    }
                };
                Brandco.api.callAjaxWithParam(param);
            }
        };
    })();
}

$(document).ready(function () {
    $(".cmd_execute_shipping_address_action").off("click");
    $(".cmd_execute_shipping_address_action").on("click", function (event) {
        event.preventDefault();
        UserActionShippingAddressService.executeShippingAddressAction(this);
    });
});