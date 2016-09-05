var AddNotificationService = (function(){
    return{
        openPreview: function (){
            var param = {
                data: {
                    'subject':$('input[name="subject"]').val(),
                    'contents':CKEDITOR.instances.contents.getData(),
                    'test_page':$('[name ="test_page"]').val(),
                    'author':$('input[name="author"]').val(),
                    'public_date':$('input[name="public_date"]').val(),
                    'preview_type': '1'
                },
                url: 'brand_notification_add_preview.json',
                success: function(data) {
                    window.open(data.data.preview_url, '_blank');
                }
            };
            Brandco.api.callAjaxWithParam(param, false, false);
        }
    }
})();

$(document).ready(function(){

    CKEDITOR.config.coreStyles_strike = {element:"del",overrides:"strike"};
    CKEDITOR.config.height = '500px';
    CKEDITOR.replace( 'contents', {
        filebrowserUploadUrl: $('#display').data('uploadurl'),
        filebrowserBrowseUrl: $('#display').data('listurl')
    });

    $('#previewButton').click(function(){
        AddNotificationService.openPreview();
    });
});