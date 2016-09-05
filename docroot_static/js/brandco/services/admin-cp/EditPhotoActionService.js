    var EditPhotoActionService = {
        initPreview: function() {
            $('.jsPhotoAction').each(function() {
                if (this.checked) {
                    $('.jsCpPhotoAction' + $(this).data('require_type')).show();
                } else {
                    $('.jsCpPhotoAction' + $(this).data('require_type')).hide();
                }
            });

            var checked_flg = 0;
            $('.jsPhotoShareAction').each(function(){
                if (this.checked) {
                    checked_flg = 1;
                    $('.jsCpPhotoShareAction' + $(this).data('require_type')).show();
                } else {
                    $('.jsCpPhotoShareAction' + $(this).data('require_type')).hide();
                }

                $('.jsCpPhotoShareActionText').show();
            });

            if (checked_flg == 1) {
                $('.jsCpPhotoShareActionText').show();
            } else {
                $('.jsCpPhotoShareActionText').hide();
            }

            $('.jsCpPhotoSharePlaceholder').attr('placeholder', $('.jsPhotoSharePlaceholder').val());
        }
    }

    $(document).ready(function(){
        $('.action_image').on('change', function(){
            if ($(this)[0].files && $(this)[0].files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#cp_photo_image').attr('src', e.target.result);
                    $('#cp_photo_image').show();
                }
                reader.readAsDataURL($(this)[0].files[0]);
    } else {
        $('#cp_photo_image').attr('src', '');
    }
    });

    $('.jsPhotoAction').on('change', function() {
        if (this.checked) {
            $('.jsCpPhotoAction' + $(this).data('require_type')).show();
        } else {
            $('.jsCpPhotoAction' + $(this).data('require_type')).hide();
        }
    });

    $('.jsPhotoShareAction').on('change', function() {
        var checked_flg = 0;var checked_flg = 0;
        $('.jsPhotoShareAction').each(function() {
            if (this.checked) {
                checked_flg = 1;
                $('.jsCpPhotoShareAction' + $(this).data('require_type')).show();
            } else {
                $('.jsCpPhotoShareAction' + $(this).data('require_type')).hide();
            }
        });

        if (checked_flg == 1) {
            $('.jsCpPhotoShareActionText').show();
        }else{
            $('.jsCpPhotoShareActionText').hide();
        }
    });

    $('.jsPhotoSharePlaceholder').on('input', function(){
        $('.jsCpPhotoSharePlaceholder').attr('placeholder', $(this).val());
    });

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker({
        minDate: new Date()
    });

    EditPhotoActionService.initPreview();
});
