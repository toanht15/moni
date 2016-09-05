var EditNotificationService = (function(){
    return{
        openPreview: function (){
            var param = {
                data: {
                    'subject':$('input[name="subject"]').val(),
                    'contents':CKEDITOR.instances.contents.getData(),
                    'message_type':$('[name ="message_type"]').val(),
                    'author':$('input[name="author"]').val(),
                    'publish_at':$('input[name="publish_at"]').val(),
                    'preview_type': '1'
                },
                url: '/dashboard/brand_notification_edit_preview.json',
                success: function(data) {
                    window.open(data.data.preview_url, '_blank');
                },
                error: function(data) {
                    console.log(data);
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

    $('#previewButton1').click(function(){
        EditNotificationService.openPreview();
    });
});