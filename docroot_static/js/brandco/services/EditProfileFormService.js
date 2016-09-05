var EditProfileFormService = (function(){
    return {
        readURL: function (input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('.thumbnail').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                $('.thumbnail').attr('src', '');
            }
        },
        readFaviconImage: function (input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('.thumbnail-small').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                $('.thumbnail-small').attr('src', '');
            }
        },
        readBackgroundImage: function (input) {
            if (input[0].files && input[0].files[0]) {
                if (input.hasClass('background_img_file')) {
                    $('#background_img_repeat_x').removeAttr('disabled');
                    $('#background_img_repeat_y').removeAttr('disabled');
                }
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('.thumbnail1').attr('src', e.target.result);
                }
                reader.readAsDataURL(input[0].files[0]);
            } else {
                $('.thumbnail1').attr('src', '');
                if (input.hasClass('background_img_file')) {
                    $('#background_img_repeat_x').attr('disabled', 'disabled');
                    $('#background_img_repeat_y').attr('disabled', 'disabled');
                }
            }
        }

    }
})();

$(document).ready(function() {
    $('#input_image').on('change', function() {
       EditProfileFormService.readURL($(this)[0]);
    });

    $('#favicon_img_file').on('change', function() {
        EditProfileFormService.readFaviconImage($(this)[0]);
    });

    $('#background_img_file').on('change', function() {
       EditProfileFormService.readBackgroundImage($(this));
    });

    $('#submit').click(function() {
        $(window).unbind('beforeunload');
        document.frmProfile.submit();
    });

    if($('#profile_name')[0]) {
        $(".textLimit").html(("（")+($('#profile_name')[0].value.length)+("/35文字）"));
    }

    $('#profile_name').on('input', function(){
        $(".textLimit").html(("（")+($('#profile_name')[0].value.length)+("/35文字）"));
    });

    $(":input").each(function(){
        $(this).change(function(){
            $('a[href="#closeModalFrame"]').unbind('click');
            $('a[href="#closeModalFrame"]').click(function(){
                if(confirm(Brandco.message.reloadMessage) == true) {
                    $(window).unbind('beforeunload');
                    Brandco.unit.closeModalFlame(this);
                }
            });
            $(window).unbind('beforeunload');
            $(window).on('beforeunload', function() {
                return Brandco.message.reloadMessage;
            });
        });
    });
});
