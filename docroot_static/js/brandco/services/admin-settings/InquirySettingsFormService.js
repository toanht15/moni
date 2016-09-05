if (typeof(InquirySettingsFormService) === 'undefined') {
    var InquirySettingsFormService = function() {
        return {
            executeSubmitForm: function(src) {
                var form = $('.' + $(src).attr('data-submit_form_class'));
                form.submit();
            },

            openModal: function(src) {
                var modal_name = $(src).attr('data-open_modal_type');
                Brandco.unit.openModal('#modal' + modal_name);
            },

            closeModal: function(src) {
                var modal_name = $(src).attr('data-close_modal_type');
                Brandco.unit.closeModalFlame('#modal' + modal_name);
            },

            setValue: function(src) {
                var inquiry_brand_receiver_id = $(src).attr('data-inquiry_brand_receiver_id');
                var inquiry_brand_receiver_mail_address = $(src).attr('data-inquiry_brand_receiver_mail_address');

                $('.jsMailAddress').text(inquiry_brand_receiver_mail_address);
                $('.jsInquiryBrandReceiverId').val(inquiry_brand_receiver_id);
            }
        };
    }();
}

$(document).ready(function() {
    $('.jsSettings').on('click', '.jsOpenReceiverAddModal', function() {
        InquirySettingsFormService.openModal(this);

        return false;
    });

    $('.jsSettings').on('click', '.jsOpenReceiverDeleteModal', function() {
        InquirySettingsFormService.setValue(this);
        InquirySettingsFormService.openModal(this);

        return false;
    });

    $('.jsModal').on('click', '.jsCloseReceiverAddModal', function() {
        InquirySettingsFormService.closeModal(this);

        return false;
    });

    $('.jsModal').on('click', '.jsCloseReceiverDeleteModal', function() {
        InquirySettingsFormService.closeModal(this);

        return false;
    });

    $('.jsModal').on('click', '.jsReceiverAdd', function() {
        InquirySettingsFormService.executeSubmitForm(this);

        return false;
    });

    $('.jsModal').on('click', '.jsReceiverDelete', function() {
        InquirySettingsFormService.executeSubmitForm(this);

        return false;
    });
});