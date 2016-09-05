var StaticHtmlEmbedPageService = (function(){
   return{
       publicType: {
           PUBLIC:  1,
           NOT_PUBLIC: 2
       },
       settingLoginType: function(publicFlg){
           if(publicFlg == StaticHtmlEmbedPageService.publicType.PUBLIC){
               $('input[name^=login_types]').each(function(){
                   $(this).prop('checked',false);
                   $(this).prop('disabled',true);

                   var loginTypeContainer = $('.loginAccount');
                   loginTypeContainer.removeClass('loginAccount');
                   loginTypeContainer.toggleClass('loginAccountDisabled');
               });
           }else{
               $('input[name^=login_types]').attr('disabled', false);

               var loginTypeContainer = $('.loginAccountDisabled');
               loginTypeContainer.removeClass('loginAccountDisabled');
               loginTypeContainer.toggleClass('loginAccount');
           }
       }
   }
})();

$(document).ready(function() {

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker();

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

    CKEDITOR.replace( 'body', {
        filebrowserUploadUrl: $('#display').data('uploadurl'),
        filebrowserBrowseUrl: $('#display').data('listurl')
    });
    
    if($('#titleInput')[0]) {
        $(".textLimit").html(("（")+($('#titleInput')[0].value.length)+("文字/100文字）"));
    }

    $('#titleInput').on('input', function(){
        $(".textLimit").html(("（")+($('#titleInput')[0].value.length)+("文字/100文字）"));
    });

    $('input[name=public_flg]').on('change', function(){
        StaticHtmlEmbedPageService.settingLoginType($(this).val());
    });

    $('#submitEntry').click(function(){
        $(window).unbind('beforeunload');
        document.frmEntry.submit();
    });

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
            csrf_token = document.getElementsByName("csrf_token")[0].value,id,li;
        data = data + '&csrf_token=' + csrf_token;
        Brandco.helper.showConfirm(modal_class, data);
    });

    $('#delete_area').click(function(){
        var url = this.getAttribute('data-url'),
            callback = this.getAttribute('data-callback');
        Brandco.helper.deleteEntry(this, url, callback);
    });
});