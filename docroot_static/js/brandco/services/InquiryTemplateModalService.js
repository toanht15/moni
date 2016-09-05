if (typeof(InquiryTemplateModalService) === 'undefined') {
    var InquiryTemplateModalService = function () {
        return {
            openModal: function(src) {
                var modal_name = $(src).attr('data-open_modal_type');
                Brandco.unit.openModal('#modal' + modal_name);
            },

            closeModal: function(src) {
                var modal_name = $(src).attr('data-close_modal_type');
                Brandco.unit.closeModal(modal_name);
            },

            closeModalFlame: function(src) {
                var modal_name = $(src).attr('data-close_modal_type');
                Brandco.unit.closeModalFlame('#modal' + modal_name);
            },

            executeSubmitForm: function(src) {
                var form = $(src).parents().filter('form');
                form.submit();
            },

            refreshTemplateSelect: function(src) {
                var inquiry_template_category_id = $(src).val();

                $('.jsTemplateId').val(0);
                $('.jsTemplateId').find('option').each(function() {
                    if ($(this).attr('data-inquiry_template_category_id') == inquiry_template_category_id) {
                        $(this).show();
                    } else if ($(this).attr('data-inquiry_template_category_id')) {
                        $(this).hide();
                    }
                });
            },

            setTemplate: function(src) {
                var content = $(src).attr('data-content');

                $('textarea.jsContent', parent.document).val(content);
            },

            setInquiryTemplateCategoryId: function(src) {
                var inquiry_template_category_id = $(src).attr('data-inquiry_template_category_id');
                $('.jsModal input[name=inquiry_template_category_id]').val(inquiry_template_category_id);
            },

            setInquiryTemplateId: function(src) {
                var inquiry_template_id = $(src).attr('data-inquiry_template_id');
                $('.jsModal input[name=inquiry_template_id]').val(inquiry_template_id);
            },
        };
    }();
}

$(document).ready(function() {
    $('.jsTemplateSetting').on('click', '.jsCloseTemplateModal', function () {
        InquiryTemplateModalService.closeModalFlame(this);

        return false;
    });

    $('.jsTemplateSetting').on('click', '.jsOpenTemplateCategoryDeleteModal', function () {
        InquiryTemplateModalService.setInquiryTemplateCategoryId(this);
        InquiryTemplateModalService.openModal(this);

        return false;
    });

    $('.jsModal').on('click', '.jsCloseTemplateCategoryDeleteModal', function () {
        InquiryTemplateModalService.closeModal(this);

        return false;
    });

    $('.jsTemplateSetting').on('click', '.jsOpenTemplateDeleteModal', function () {
        InquiryTemplateModalService.setInquiryTemplateId(this);
        InquiryTemplateModalService.openModal(this);

        return false;
    });

    $('.jsModal').on('click', '.jsCloseTemplateDeleteModal', function () {
        InquiryTemplateModalService.closeModal(this);

        return false;
    });

    $('.jsTemplateSetting').on('click', '.jsTemplateCategoryAdd', function () {
        InquiryTemplateModalService.executeSubmitForm(this);

        return false;
    });

    $('.jsModal').on('click', '.jsTemplateCategoryDelete', function () {
        InquiryTemplateModalService.executeSubmitForm(this);
        InquiryTemplateModalService.closeModal(this);

        return false;
    });

    $('.jsTemplateSetting').on('click', '.jsTemplateAdd', function () {
        InquiryTemplateModalService.executeSubmitForm(this);

        return false;
    });

    $('.jsModal').on('click', '.jsTemplateDelete', function () {
        InquiryTemplateModalService.executeSubmitForm(this);
        InquiryTemplateModalService.closeModal(this);

        return false;
    });

    $('.jsTemplateSetting').on('change', '.jsTemplateCategoryId', function() {
        InquiryTemplateModalService.refreshTemplateSelect(this);

        return false;
    });

    $('.jsTemplateSetting').on('change', '.jsTemplateId', function() {
        var inquiry_template_id = $(this).val();
        var url = $(this).attr('data-url');

        window.location.href = url + '?inquiry_template_id=' + inquiry_template_id;
    });

    $('.jsTemplateSetting').on('click', '.jsTemplateSet', function() {
        InquiryTemplateModalService.setTemplate(this);
        InquiryTemplateModalService.closeModalFlame(this);

        return false;
    });
});
