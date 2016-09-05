var EditCodeAuthActionService = (function() {
    return {
        previewCodeCount: function(code_count_target) {
            var code_count_input = $(code_count_target).find('.jsCodeCount');

            var code_count = $(code_count_input).val();
            var code_count_type = $(code_count_input).data('type');
            var code_count_selection = $(code_count_target).find('.jsCodeCountSelection:checked').val();
            var code_count_preview = '#' + code_count_type + '_code_count_preview';

            if (code_count_selection == 1 || code_count == 0) {
                $(code_count_preview).hide();
            } else {
                $(code_count_preview).show();
                if (code_count_type == 'min') {
                    $(code_count_preview).html('あと<span style="font-size:18px; font-weight:bold;">' + code_count + '</span>個で次に進めます');
                } else {
                    $(code_count_preview).html('/' + code_count);
                }
            }
        },
        changeCodeCountSelection: function(code_count_selection) {
            $(code_count_selection).parents('ul').find('.jsCodeCount').prop('disabled', true);
            $(code_count_selection).parents('li').find('.jsCodeCount').prop('disabled', false);

            EditCodeAuthActionService.previewCodeCount($(code_count_selection).parents('.jsCodeCountTarget'));
        },
        initEditMode: function() {
            $('.jsCodeCountTarget').each(function() {
                EditCodeAuthActionService.previewCodeCount(this);
            });

            var code_auth_selection = $('.jsCodeAuthSelection').val();
            if (code_auth_selection == 0) {
                $('#code_list_preview').hide();
            }
        }
    }
})();

$(document).ready(function() {
    $('.jsCodeCountSelection').on('change', function() {
        EditCodeAuthActionService.changeCodeCountSelection(this);
    });

    $('.jsCodeAuthSelection').on('change', function() {
        var code_auth_selection = $(this).val();

        if (code_auth_selection == 0) {
            $('#code_list_preview').hide();
            $('#min_code_count_preview').hide();
            $('#max_code_count_preview').hide();
            $('.jsCodeCountTarget input').each(function() {
                $(this).prop('disabled', true);
            });
        } else {
            $('#code_list_preview').show();
            $('#min_code_count_preview').show();
            $('#max_code_count_preview').show();
            $('.jsCodeCountSelection').prop('disabled', false);
            $('.jsCodeCountSelection:checked').each(function() {
                EditCodeAuthActionService.changeCodeCountSelection(this);
            });
        }
    });

    $('.jsCodeCount').on('input', function() {
        EditCodeAuthActionService.previewCodeCount($(this).parents('.jsCodeCountTarget'));
    });

    EditCodeAuthActionService.initEditMode();

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker({
        minDate: new Date()
    });
});
