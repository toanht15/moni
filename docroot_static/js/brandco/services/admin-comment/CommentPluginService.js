var CommentPluginService = (function(){
    return {
        openPreview: function () {
            var csrf_token = document.getElementsByName("csrf_token")[0].value,
                param = {
                    data: {
                        csrf_token: csrf_token,
                        preview_type: '3',
                        cp_free_text: CKEDITOR.instances.free_text.getData(),
                        cp_footer_text: CKEDITOR.instances.footer_text.getData(),
                        cp_status: $('input[name="status"]:checked').val(),
                        cp_sns_list: $('input[name="share_sns_list[]"]:checked').map(function() { return $(this).val(); }).get()
                    },
                    url: 'admin-blog/api_write_tmp.json',
                    success: function(data) {
                        window.open(data.data.preview_url, '_blank');
                    }
                };
            Brandco.api.callAjaxWithParam(param);
        }
    }
})();

$(document).ready(function() {
    $(document).on('focus', '.jsPluginScript', function() {
        var cur_target = $(this);
        $(cur_target).select();

        $(cur_target).mouseup(function() {
            $(cur_target).unbind("mouseup");
            return false;
        });
    });

    CKEDITOR.config.coreStyles_strike = {element:"del",overrides:"strike"};
    CKEDITOR.config.height = '600px';
    CKEDITOR.config.filebrowserWindowWidth = 1000;
    CKEDITOR.config.filebrowserWindowHeight = 745;

    CKEDITOR.on('instanceCreated', function (e) {
        e.editor.on('change', function (ev) {
            $(window).unbind('beforeunload');
            $(window).on('beforeunload', function() {
                return Brandco.message.reloadMessage;
            });
        });
    });

    CKEDITOR.replace( 'free_text', {
        filebrowserUploadUrl: $('#display').data('uploadurl'),
        filebrowserBrowseUrl: $('#display').data('listurl')
    });

    CKEDITOR.replace( 'footer_text', {
        filebrowserUploadUrl: $('#display').data('uploadurl'),
        filebrowserBrowseUrl: $('#display').data('listurl')
    });

    CKEDITOR.instances.footer_text.on('loaded', function () {
        if ($('textarea[name="footer_text"]').val()) {
            return false;
        }
        $('#cke_footer_text').hide(0, function () {
            $('textarea[name="footer_text"]').prop('disabled', true);
        });
    });

    $(document).on('click', '#add_footer_text', function () {
        $('#cke_footer_text').slideToggle(300, function() {
            if ($('#cke_footer_text').is(':visible')) {
                $('textarea[name="footer_text"]').prop('disabled', false);
            } else {
                $('textarea[name="footer_text"]').prop('disabled', true);
            }
        });
    });

    $('.jsSettingContTile').click(function(){
        var trigger = $(this);
        var target = trigger.parents('.jsSettingContWrap').find('.jsSettingContTarget');

        if(trigger.hasClass('close')) {
            target.slideDown(200, function() {
                trigger.removeClass('close');
            });
        }else{
            target.slideUp(200, function() {
                trigger.addClass('close');
            });
        }
    });

    $('.jsPreviewPlugin').click(function(){
        CommentPluginService.openPreview();
    });

    $('#submitPlugin').click(function(){
        $(window).unbind('beforeunload');
        document.save_comment_plugin_form.submit();
    });

});