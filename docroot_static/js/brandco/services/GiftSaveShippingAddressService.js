var GiftSaveShippingAddressService = {
    alreadyClicked: false,
    saveShippingAddressAction: function (target) {
        if (!GiftSaveShippingAddressService.alreadyClicked) {
            GiftSaveShippingAddressService.alreadyClicked = true;
        } else {
            return false;
        }

        var form = $(target).parents().filter(".saveShippingAddressActionForm");
        var section = $(target).parents().filter(".jsMessageShippingAddress");
        var url = form.attr("action");
        var data = $('.saveShippingAddressActionForm').serialize();
        var param = {
            data: data,
            url: url,
            beforeSend: function () {
                Brandco.helper.showLoading(section);
            },
            success: function (json) {
                $('.iconError1[id^=error_shipping_address_]').hide();
                if (json.result === "ok") {
                    Brandco.unit.disableForm($(target).closest('form'));
                    $('.jsSaveShippingAddress').html('<span>送信完了</span>');
                } else {
                    $('.iconError1[id^=error_shipping_address_]').hide();
                    $.each(json.errors, function (i, value) {
                        if ($('#error_shipping_address_' + i)) {
                            $('#error_shipping_address_' + i).html(value);
                            $('#error_shipping_address_' + i).show();
                        }
                    });
                    alert("エラーが発生しました");
                }
                GiftSaveShippingAddressService.alreadyClicked = false;
            }
        };
        Brandco.api.callAjaxWithParam(param);
    }
};

$(document).ready(function () {
    $("#saveShippingAddressBtn").off("click");
    $("#saveShippingAddressBtn").on("click", function (event) {
        event.preventDefault();
        GiftSaveShippingAddressService.saveShippingAddressAction(this);
    });
});