var PageSettingsFormService = (function() {
    return {
        checkInputLength: function(input) {
            var count = input ? input.value.length : 0;
            var text_limit = $(input).siblings('.textLimit');

            text_limit.html("（" + count + "文字/" + $(input).attr('maxlength') + "文字）");
        }
    }
})();

$(function(){
   $('#top_page_replace').click(function(){
      document.forms.frmTopPageReplace.submit();
       $(window).off('beforeunload');
   });

    $('#add_header_tag_text_button').click(function() {
        document.forms.frmHeaderTag.submit();
        $(window).off('beforeunload');
    });

    $('#add_tag_text_button').click(function() {
        document.forms.frmCvTag.submit();
        $(window).off('beforeunload');
    });

    $('#meta_setting_confirm_btn').on('click', function() {
        document.frmMetaInfoSetting.submit();
        $(window).off('beforeunload');
    });

    $('#edit_profile_submit_btn').on('click', function() {
        document.frmProfile.submit();
        $(window).off('beforeunload');
    });

    $('.jsOgImage').on('change', function(){
        if ($(this)[0].files && $(this)[0].files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#og_image_preview').attr('src', e.target.result);
                $('#og_image_preview').show();
            }
            reader.readAsDataURL($(this)[0].files[0]);
        } else {
            $('#og_image_preview').attr('src', '');
        }
    });

    $('.jsMetaDataInput').each(function() {
        PageSettingsFormService.checkInputLength(this);
    });

    $('.jsMetaDataInput').on('input', function() {
        PageSettingsFormService.checkInputLength(this);
    });
});

