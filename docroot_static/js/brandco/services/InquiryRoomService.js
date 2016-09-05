if (typeof(InquiryRoomService) === 'undefined') {
    var InquiryRoomService = function() {
        return {
            executeSaveRoom: function (src) {
                var form = $(src).parents().filter('form');
                var url = form.attr('action');

                var param = {
                    data: {
                        csrf_token: $('input[name=csrf_token]', form).val(),
                        inquiry_room_id: $('input[name=inquiry_room_id]', form).val(),
                        operator_name: $('input[name=operator_name]', form).val(),
                        inquiry_section_id_1: $('select[name=inquiry_section_id_1]', form).val(),
                        inquiry_section_id_2: $('select[name=inquiry_section_id_2]', form).val(),
                        inquiry_section_id_3: $('select[name=inquiry_section_id_3]', form).val(),
                        status: $('select[name=status]', form).val(),
                        remarks: $('textarea[name=remarks]', form).val()
                    },

                    url: url,
                    type: 'POST',
                    success: function(json) {
                        if (json.result == 'ok') {
                            $('.jsOperatorNameError').html('');
                        } else {
                            if (json.errors['operator_name']) {
                                $('.jsOperatorNameError').html($('<span></span>').addClass('iconError1').text(json.errors['operator_name']));
                            }
                        }
                    },
                    error: function() {
                        alert('error');
                    }
                };

                Brandco.api.callAjaxWithParam(param);
            }
        }
    }();
}

$(document).ready(function() {
    $('.jsRoom').on('click', '.jsRoomSave', function() {
        InquiryRoomService.executeSaveRoom(this);
    });
});
