if (typeof(InquirySectionService) === 'undefined') {
    var InquirySectionService = function() {
        var inquiry_section_ids = {
            1: 0,
            2: 0,
            3: 0
        };

        return {
            executeAddSection: function(src) {
                var form = $(src).parents().filter('form');
                var url = form.attr('action');

                var param = {
                    data: {
                        csrf_token: $('input[name=csrf_token]', form).val(),
                        level: $('select[name=level]', form).val(),
                        name: $('input[name=name]', form).val(),
                    },
                    url: url,
                    type: 'POST',
                    success: function (json) {
                        InquirySectionService.removeErrorNode('.jsSectionEditArea');
                        if (json.result === 'ok') {
                            $('.jsSection').html(json.html);

                            InquirySectionService.refreshSelect();
                            InquirySectionService.closeModal(src);

                            $('.jsSectionName').val('');
                        } else {
                            for (var index in json.errors) {
                                InquirySectionService.insertErrorNode('.jsSectionEditArea', json.errors[index]);
                                break;
                            }
                        }
                    },
                    error: function () {
                        InquirySectionService.removeErrorNode('.jsSectionEditArea');
                        InquirySectionService.insertErrorNode('.jsSectionEditArea', '保存した際にエラーが発生しました。時間を置いて再度お試しください。');
                    },
                };

                InquirySectionService.cacheSectionIds();
                Brandco.api.callAjaxWithParam(param);
            },

            executeDeleteSection: function(src) {
                var form = $(src).parents().filter('form');
                var url = form.attr('action');
                var inquiry_section_id = $('select[name=inquiry_section_id]', form).val();

                var param = {
                    data: {
                        csrf_token: $('input[name=csrf_token]', form).val(),
                        inquiry_section_id: inquiry_section_id,
                    },
                    url: url,
                    type: 'POST',
                    success: function (json) {
                        InquirySectionService.removeErrorNode('.jsSectionEditArea');
                        if (json.result === 'ok') {
                            $('.jsSection').html(json.html);

                            InquirySectionService.refreshSelect();
                            InquirySectionService.closeModal(src);
                        } else {
                            for (var index in json.errors) {
                                InquirySectionService.insertErrorNode('.jsSectionEditArea', json.errors[index]);
                                break;
                            }
                        }
                    },
                    error: function () {
                        InquirySectionService.removeErrorNode('.jsSectionEditArea');
                        InquirySectionService.insertErrorNode('.jsSectionEditArea', '保存した際にエラーが発生しました。時間を置いて再度お試しください。');
                    },
                };

                InquirySectionService.cacheSectionIds(inquiry_section_id);
                Brandco.api.callAjaxWithParam(param);
            },

            refreshSelectOptions: function(src) {
                var level = $(src).val();

                var select_src = $('select[data-section_level=' + level + ']');
                var select_dest = $(src).parents().filter('.jsModal').find('.jsSectionId');

                select_dest.html(select_src.html());
                select_dest.val(0);
            },

            refreshSelect: function() {
                $('select[name=inquiry_section_id_1]').val(inquiry_section_ids[1]);
                $('select[name=inquiry_section_id_2]').val(inquiry_section_ids[2]);
                $('select[name=inquiry_section_id_3]').val(inquiry_section_ids[3]);
            },

            //--------------------------------------------------
            // Util
            //--------------------------------------------------
            insertErrorNode: function(parent, message) {
                $(parent).prepend($('<p></p>').addClass('iconError1').text(message));
            },

            removeErrorNode: function(parent) {
                $(parent).find('.iconError1').remove();
            },

            openModal: function(src) {
                var modal_name = $(src).attr('data-open_modal_type');
                Brandco.unit.openModal('#modal' + modal_name);
            },

            closeModal: function(src) {
                var modal_name = $(src).attr('data-close_modal_type');
                Brandco.unit.closeModalFlame('#modal' + modal_name);
            },

            cacheSectionId: function(level, inquiry_section_id) {
                inquiry_section_ids[level] = inquiry_section_id;
            },

            cacheSectionIds: function(deleted_inquiry_section_id) {
                for (var level = 1; level <= 3; level++) {
                    var inquiry_section_id = parseInt($('select[name=inquiry_section_id_' + level + ']').val());

                    InquirySectionService.cacheSectionId(level, (inquiry_section_id == parseInt(deleted_inquiry_section_id)) ? 0 : inquiry_section_id);
                }
            }
        };
    }();
}

$(document).ready(function() {
    $('.jsSection').on('click', '.jsOpenSectionModal', function() {
        if ($(this).attr('data-open_modal_type') == 'SectionDelete') {
            InquirySectionService.refreshSelectOptions('.jsSectionLevel');
        }
        InquirySectionService.openModal(this);

        return false;
    })

    $('.jsModal').on('click', '.jsCloseSectionModal', function() {
        InquirySectionService.removeErrorNode('.jsSectionEditArea');
        InquirySectionService.closeModal(this);

        return false;
    })

    $('.jsModal').on('click', '.jsSectionAdd', function() {
        InquirySectionService.executeAddSection(this);

        return false;
    })

    $('.jsModal').on('click', '.jsSectionDelete', function() {
        InquirySectionService.executeDeleteSection(this);

        return false;
    })

    $('.jsModal').on('change', '.jsSectionLevel', function() {
        InquirySectionService.refreshSelectOptions(this);

        return false;
    })
});
