if (typeof(InquiryTemplateService) === 'undefined') {
    var InquiryTemplateService = function () {
        return {
            openModal: function(src) {
                var modal_name = $(src).attr('data-open_modal_type');
                Brandco.unit.openModal('#modal' + modal_name);
            },
        };
    }();
}

$(document).ready(function() {
    $('.jsTemplate').on('click', '.jsOpenTemplateModal', function () {
        InquiryTemplateService.openModal(this);

        return false;
    });
});
