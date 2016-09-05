var EditFreeAreaEntryService = (function(){
    return {
        previewClick: function (){
            var csrf_token = document.getElementsByName("csrf_token")[0].value,
                param = {
                    data: {'body':CKEDITOR.instances.body.getData(),
                        'csrf_token':csrf_token},
                    url: 'admin-top/api_free_area_preview.json',
                    success: function(data) {
                        window.open('index?preview=on' + '&free_area_entry_preview=true', '_blank');
                    }
                };
            Brandco.api.callAjaxWithParam(param);
        }
    }
})();

$(document).ready(function(){
    $( '#preview' ).click( function(){
        EditFreeAreaEntryService.previewClick($(this));
    });

    CKEDITOR.config.coreStyles_strike = {element:"del",overrides:"strike"};
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

    CKEDITOR.replace( 'body', {
        filebrowserUploadUrl: $('#preview').data('uploadurl'),
        filebrowserBrowseUrl: $('#preview').data('listfileurl')
    });

    $('#submitEntry').click(function(){
        $(window).unbind('beforeunload');
        document.frmEntry.submit();
    });

});