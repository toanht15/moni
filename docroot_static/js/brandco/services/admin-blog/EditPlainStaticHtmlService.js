var EditPlainStaticHtmlService = (function(){
    return{
        initLayoutType: function() {
            $("input[name='layout_type']:radio").prop("disabled", true);
            $("input[name='layout_type']:radio:checked").prop("disabled", false);
        },
        openPreview: function () {
            param = {
                data: {
                    'body': $('textarea[name="body"]').val(),
                    'title': $('input[name="title"]').val(),
                    'category_id': $('#category_selection').find(':selected').attr('value'),
                    'layout_type': $('input[name="layout_type"]:checked').val(),
                    'csrf_token': document.getElementsByName("csrf_token")[0].value,
                    'preview_type': 1
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

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker();

    // ZeroClipboard copy url to clipboard
    $('.jsCopyToClipboardBtn').each(function() {
        var zero_clipboard = new ZeroClipboard(this);

        zero_clipboard.on('error', function(event) {
            ZeroClipboard.destroy();
        });
    });

    $('#submitEntry').click(function(){
        document.frmEntry.submit();
    });

    $('#previewButtonBlog').click(function(){
        EditPlainStaticHtmlService.openPreview();
    });

    $('#previewButtonTemplate').click(function(){
        EditPlainStaticHtmlService.openPreview();
    });

    $('.linkDelete').click(function(){
        var modal_class = this.getAttribute('data-modal_class');
        var data = this.getAttribute('data-entry'),
            csrf_token = document.getElementsByName("csrf_token")[0].value,id,li;
        data = data + '&csrf_token=' + csrf_token;
        if (modal_class) {
            Brandco.helper.showConfirm(modal_class, data);
        } else {
            Brandco.helper.showConfirm('.modal2', data);
        }
    });

    $('#delete_area').click(function(){
        var url = this.getAttribute('data-url'),
            callback = this.getAttribute('data-callback');
        Brandco.helper.deleteEntry(this, url, callback);
    });

    EditPlainStaticHtmlService.initLayoutType();
});