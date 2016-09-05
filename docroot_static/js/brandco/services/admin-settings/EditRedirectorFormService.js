$(document).ready(function(){
    // ZeroClipboard copy url to clipboard
    $('.jsCopyToClipboardBtn').each(function() {
        var zero_clipboard = new ZeroClipboard(this);

        zero_clipboard.on('error', function(event) {
            ZeroClipboard.destroy();
        });
    });

    $('.linkDelete').click(function(){
        var modal_class = this.getAttribute('data-modal_class');
        var data = this.getAttribute('data-entry'),
            csrf_token = document.getElementsByName("csrf_token")[0].value;
        data = data + '&csrf_token=' + csrf_token;
            Brandco.helper.showConfirm(modal_class, data);
    });

    $('.jsDeleteButton').click(function() {
        $('.jsDeleteData').val(1);
        $('.jsSubmitDeleteData').click();
    });
});