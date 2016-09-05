$(document).ready(function(){
    CKEDITOR.config.coreStyles_strike = {element:"del",overrides:"strike"};
    CKEDITOR.config.height = '600px';
    CKEDITOR.on('instanceCreated', function (e) {
        e.editor.on('change', function (ev) {
            $(window).unbind('beforeunload');
            $(window).on('beforeunload', function() {
                return Brandco.message.reloadMessage;
            });
        });
    });

    CKEDITOR.replace( 'customize_code', {
        filebrowserUploadUrl: $('#display').data('uploadurl'),
        filebrowserBrowseUrl: $('#display').data('listurl')
    });

    // commone check toggle area
    Brandco.helper.doJsCheckToggle();

    $("#save_category").click(function(){
        $(window).unbind('beforeunload');
        document.addCategoryForm.submit();
    });

    if($('#field1')[0]) {
        $(".textLimit").html(("（<span>")+($('#field1')[0].value.length)+("</span>文字/35文字）"));
    }

    $('#field1').on('input', function(){
        $(".textLimit").html(("（<span>")+($('#field1')[0].value.length)+("</span>文字/35文字）"));
    });

    if ($('.jsCheckToggle').is(':checked')) {
        var targetWrap = $('.jsCheckToggle').parents('.jsCheckToggleWrap')[0];
        $(targetWrap).find('.jsCheckToggleTarget').slideToggle(300);
    }

    $('.actionImage').on('change', function(){
        if ($(this)[0].files && $(this)[0].files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#ogImage').attr('src', e.target.result);
                $('#ogImage').show();
            }
            reader.readAsDataURL($(this)[0].files[0]);
        } else {
            $('#cpImage').attr('src', '');
        }
    });

    $('#category_selection').on('change', function() {
        $('#folder_name').html($(this).attr('data-base-folder')+'/'+$(this).find(':selected').attr('data-directory'));
    });

    $('#categories_preview').click(function(){

        var csrf_token = document.getElementsByName("csrf_token")[0].value,
             sns_plugin = [];
            $('input[name="sns_plugins[]"]').each(function(){
                if ($(this).is(':checked')) {
                    sns_plugin.push($(this).attr('value'));
                }
            });
        var param = {
                data: {'customize_code':CKEDITOR.instances.customize_code.getData(),
                    'name':$('input[name="name"]').val(),
                    'keyword':$('input[name="meta_keyword"]').val(),
                    'parent_id': $('#category_selection').find(':selected').attr('value'),
                    'is_use_customize': $('input[name="is_use_customize"]').is(':checked') ? 1 : 0,
                    'sns_plugin_tag_text': $('textarea[name="sns_plugin_tag_text"]').val(),
                    'id': $('input[name="id"]').val(),
                    'sns_plugin': sns_plugin,
                    'csrf_token':csrf_token,
                    'preview_type': '2'},
                url: 'admin-blog/api_write_tmp.json',
                success: function(data) {
                    window.open(data.data.preview_url, '_blank');
                }
            };
        Brandco.api.callAjaxWithParam(param);
    });

    $('#snsScriptAdd').click(function(){
        $(this).toggle();
        $('#snsScriptText').toggle();
    });

    $('input[name="is_use_customize"]').on('change', function(){
        if ($(this).is(':checked')) {
            $('input[name="sns_plugins[]"]').attr('disabled','disabled');
            $('textarea[name="sns_plugin_tag_text"]').attr('disabled','disabled');
            $('#snsScriptAdd').hide();
        } else {
            $('input[name="sns_plugins[]"]').removeAttr('disabled');
            $('textarea[name="sns_plugin_tag_text"]').removeAttr('disabled');
            $('#snsScriptAdd').show();
        }
    });
});