var EditPanelService = (function(){
    return {
        textChange: function (input){
            if (!input.val()) {
                $('#text_preview').hide();
            } else {
                if($('#text_preview').hide()) {
                    $('#text_preview').show();
                }
            }
            var temp = Brandco.helper.escapeSpecialCharacter(input.val());
                $("#text_preview").html(temp);
        },
        commentChange: function(input) {
            if (!input.val() || input.val() == "") {
                $('#comment_preview').parent('.panelComment').hide();
            } else {
                if ($('#comment_preview').parent('.panelComment').hide()) {
                    $('#comment_preview').parent('.panelComment').show();
                }

                var comment = Brandco.helper.escapeSpecialCharacter(input.val());
                $('#comment_preview').html(comment);
            }
        },
        imageUrlChange: function (input){
            if(input.value == ''){
                $('#image_preview').attr('style', 'display:none');
            }else{
                $('#image_preview').attr('style', 'display:yes');
            }
            $('#image_preview').attr('src', input.val());
        },
        readURL: function (input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#image_preview').attr('src', e.target.result);
                    $('#image_preview').attr('style', 'display:yes');
                }
                reader.readAsDataURL(input.files[0]);
            }else{
                $('#image_preview').attr('style', 'display:none');
            }
        },
        checkLength: function (input){
            var count = 0;
            if(input) {
                count = input.value.length
            }
            $(".textLimit").html(("（")+(count)+("文字/" + $(input).attr('maxlength') + "文字）"));
            if(input.value) {
                var temp = Brandco.helper.escapeSpecialCharacter(input.value);
                $("#title_preview").html(temp);
            } else{
                $("#title_preview").html('Page Title');
            }
        }
    }
})();

$(document).ready(function(){
    $('#submitButton').click(function(){
        $(window).unbind('beforeunload');
        document.frmPanel.submit();
    });

    $('#panel_text').on('input', function(){
        EditPanelService.textChange($(this));
    });

    $('#panel_comment').on('input', function() {
        EditPanelService.commentChange($(this));
    });

    $('#imageUrlInput').on('change', function(){
        EditPanelService.imageUrlChange($(this));
    });

    $('.jsPanelImageInput').on('change', function(){
        EditPanelService.readURL($(this)[0]);
    });

    if($('#panel_title')[0]) {
        EditPanelService.checkLength($('#panel_title')[0]);
    }

    $('#panel_title').on('input', function(){
       EditPanelService.checkLength($(this)[0]);
    });

    $('.switchInner').on('click', function(){
        ($("#display").val() == '0')? $("#display").val("1"): $("#display").val("0");
    });

    $(":input").each(function(){
        $(this).change(function(){
            $('a[href="#closeModalFrame"]').unbind('click');
            $('a[href="#closeModalFrame"]').click(function(event){
                event.preventDefault();
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

    $(window).unload(function() {
        $(window).unbind('beforeunload');
    });
});