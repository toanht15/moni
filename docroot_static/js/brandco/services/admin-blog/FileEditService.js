$(document).ready(function() {
    $('#file_edit_form').on('submit', function(e) {
        e.preventDefault();
    });

    $('.jsFileEditSubmitBtn').on('click', function() {
        $(window).unbind('beforeunload');
        document.fileEditForm.submit();
    });

    // ZeroClipboard copy url to clipboard
    $('.jsCopyToClipboardBtn').each(function() {
        var zero_clipboard = new ZeroClipboard(this);

        zero_clipboard.on('error', function(event) {
            ZeroClipboard.destroy();
        });
    });

    // For removing action
    $('.jsFileDeleteBtn').click(function(){
        var data = this.getAttribute('data-brand_upload_file_id'),
            csrf_token = document.getElementsByName("csrf_token")[0].value;
        data = data + '&csrf_token=' + csrf_token;
        Brandco.helper.showConfirm('.modal1', data);
    });

    $('#delete_area').click(function(){
        var url = this.getAttribute('data-url'),
            callback = this.getAttribute('data-callback');
        Brandco.helper.deleteEntry(this, url, callback);
    });

    // File name limit
    if ($('#file_name_text')[0]) {
        $('#file_name_limit').html(("（") + ($('#file_name_text')[0].value.length) + ("文字/" + $('#file_name_text').attr('maxlength') + "文字）"));
    }

    $('#file_name_text').on('input', function() {
        $('#file_name_limit').html(("（") + ($('#file_name_text')[0].value.length) + ("文字/" + $('#file_name_text').attr('maxlength') + "文字）"));
    });
});