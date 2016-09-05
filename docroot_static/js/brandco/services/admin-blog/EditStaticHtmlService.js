var EditStaticHtmlService = (function(){
   return{
       variables:{},
       templateData:[],
       editingNo:-1,
       initWriteType: function() {
           var radioButton = $("input[name='write_type']:radio:checked"),
               opposite_set = {1: 'blog', 2: 'tempalte'};
           if (radioButton.val() == 1) {
               $('#jsPageContBlogEdit').show();
               $('#jsPageContTempEdit').hide();
           } else if (radioButton.val() == 2) {
               $('#jsPageContBlogEdit').hide();
               $('#jsPageContTempEdit').show();
           }
       },
       initLayoutType: function() {
           var radioButton = $("input[name='layout_type']:radio:checked");
           if (radioButton.val() == 4) {
               $('.jsNormalLayout').hide();
               $('#jsPageContPlainHtmlEdit').show();
               $('#plainBodyAreaText').prop('disabled', false);
           } else {
               $('#jsPageContPlainHtmlEdit').hide();
               $('#plainBodyAreaText').prop('disabled', true);
               $('.jsNormalLayout').show();
               EditStaticHtmlService.initWriteType();
           }
       },
       renderContainer: function() {
           contentsJson = EditStaticHtmlService.templateData;
           $("#templatePartsContainer").empty();
           for(key in contentsJson) {
               $("#templatePartsContainer").append(PartsTemplateService.toTemplateHtml[contentsJson[key].type](key, contentsJson[key].template));
           }
           $('#template_contents_json').val(JSON.stringify(EditStaticHtmlService.templateData));

       },
       bindSortable: function() {
           $("#templatePartsContainer").sortable({
               update: function(ev, ui) {
                   var newArray = [];
                   $("#templatePartsContainer li").each(function(index) {
                       newArray[index] = EditStaticHtmlService.templateData[$(this).data('no')];
                   });
                   EditStaticHtmlService.templateData = newArray;
                   EditStaticHtmlService.renderContainer();
               }
           });
       },
       saveParts: function() {
           if(EditStaticHtmlService.editingNo != -1) { //already
               EditStaticHtmlService.editPartsData(EditStaticHtmlService.editingNo);
           }else { //new
               EditStaticHtmlService.addPartsData();
           }
           EditStaticHtmlService.renderContainer();
       },
       deleteParts: function(no) {
           EditStaticHtmlService.templateData.splice(no, 1);
           EditStaticHtmlService.renderContainer();
       },
       addPartsData: function() {
           var existsLimitContentsFlg = false;
           for(i = 0 ; i < EditStaticHtmlService.templateData.length ; i++) {
               if(EditStaticHtmlService.templateData[i].type == '99' && i < EditStaticHtmlService.templateData.length - 1) {
                   //boundaryの下にパーツがあったら
                   existsLimitContentsFlg = true;
                   break;
               }
           }
           if(existsLimitContentsFlg) {
               EditStaticHtmlService.templateData.push({"template":PartsTemplateService.templateData,"type":PartsTemplateService.templateType});
           } else {
               EditStaticHtmlService.templateData.splice(EditStaticHtmlService.templateData.length - 1, 0, {"template":PartsTemplateService.templateData,"type":PartsTemplateService.templateType});
           }
       },
       editPartsData: function(no) {
           EditStaticHtmlService.templateData[no] = {"template":PartsTemplateService.templateData,"type":PartsTemplateService.templateType};
       },

       openPreview: function () {
           var sns_plugin = [];
           $('input[name="sns_plugins[]"]').each(function(){
               if ($(this).is(':checked')) {
                   sns_plugin.push($(this).attr('value'));
               }
           });

           var title_hidden_flg = $('input[name="title_hidden_flg"]').is(":checked") ? 1 : 0;
           var layout_type = $('input[name="layout_type"]:checked').val();

           var csrf_token = document.getElementsByName("csrf_token")[0].value,
               param = {
               data: {'body':CKEDITOR.instances.body.getData(),
                   'extra_body': CKEDITOR.instances.extra_body.getData(),
                   'write_type': $('input[name="write_type"]:checked').val(),
                   'template_contents_json': $('input[name="template_contents_json"]').val(),
                   'title':$('input[name="title"]').val(),
                   'category_id': $('#category_selection').find(':selected').attr('value'),
                   'keyword':$('input[name="meta_keyword"]').val(),
                   'custom_plugin': $('textarea[name="sns_plugin_tag_text"]').val(),
                   'title_hidden_flg': title_hidden_flg,
                   'layout_type': layout_type,
                   'sns_plugin': sns_plugin,
                   'csrf_token':csrf_token,
                   'preview_type': '1',
                   'cp_status': $('input[name="cp_status"]:checked').val(),
                   'cp_sns_list': $('input[name="cp_sns_list[]"]:checked').map(function() { return $(this).val(); }).get()
               },
               url: 'admin-blog/api_write_tmp.json',
               success: function(data) {
                   window.open(data.data.preview_url, '_blank');
               }
           };
           Brandco.api.callAjaxWithParam(param);
       },
       openPlainPagePreview: function () {
           param = {
               data: {
                   'body': $('#plainBodyAreaText').val(),
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
       },
       checkInputLength: function(input) {
           var count = input ? input.value.length : 0;
           var label = $(input).data('label');

           $('#' + label + '_text_limit').html("（" + count + "文字/" + $(input).attr('maxlength') + "文字）");
       },
   }
})();

$(document).ready(function() {
    variableContainer = $('#variable-container');
    EditStaticHtmlService.variables["partsDomain"] = variableContainer.data("parts-domain");
    if($('#template_contents_json').val() && JSON.parse($('#template_contents_json').val()).length > 0){
        try {
            EditStaticHtmlService.templateData = JSON.parse($('#template_contents_json').val());
        } catch (e) {
            EditStaticHtmlService.templateData = [{'type':'99'}]; //初めはこのログイン限定境界のみ
        }
    }else {
        EditStaticHtmlService.templateData = [{'type':'99'}]; //初めはこのログイン限定境界のみ
    }
    EditStaticHtmlService.renderContainer();

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker();

    CKEDITOR.config.coreStyles_strike = {element:"del",overrides:"strike"};
    CKEDITOR.config.height = '600px';
    CKEDITOR.config.filebrowserWindowWidth = 1000;
    CKEDITOR.config.filebrowserWindowHeight = 745;

    var variableContainer = $('#variable-container');
    if( variableContainer.data("brand_id") == 479 ) {
        CKEDITOR.config.removeButtons = 'Underline,Subscript,Superscript,Format';
        CKEDITOR.config.stylesSet = [
            { name: '見出し(大)',       element: 'h2',      attributes : {'class': 'kenkenColumnHeadline1'} },
            { name: '見出し(中)',       element: 'h3',      attributes : {'class': 'kenkenColumnHeadline2'} },
            { name: '見出し(小)',       element: 'h4',      attributes : {'class': 'kenkenColumnHeadline3'} }
        ];
    }

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

    CKEDITOR.replace( 'extra_body', {
        filebrowserUploadUrl: $('#display').data('uploadurl'),
        filebrowserBrowseUrl: $('#display').data('listurl')
    });

    CKEDITOR.instances.extra_body.on( 'loaded', function( evt ) {
        if (!$('textarea[name="extra_body"]').val() && !$('#extraBodyIconError').length > 0) {
            $('#cke_extra_body').hide(0, function() {
                $('textarea[name="extra_body"]').prop( "disabled", true );
            });
        }
    } );

    $('#addExtraBody').click(function(){
        $('#cke_extra_body').slideToggle(300, function() {
            if ($('#cke_extra_body').is(":visible")) {
                $('textarea[name="extra_body"]').prop( "disabled", false );
            } else {
                $('textarea[name="extra_body"]').prop( "disabled", true );
            }
        });
    });

    $('#submitEntry').click(function(){
        $(window).unbind('beforeunload');
        document.frmEntry.submit();
    });

    if($('#titleInput')[0]) {
        $(".textLimit").html(("（")+($('#titleInput')[0].value.length)+("文字/100文字）"));
    }

    $('#titleInput').on('input', function(){
        $(".textLimit").html(("（")+($('#titleInput')[0].value.length)+("文字/100文字）"));
    });

    if($('#categorytTitleInput')[0]) {
        $(".categoryTextLimit").html(("（")+($('#categorytTitleInput')[0].value.length)+("文字/35文字）"));
    };

    $('#categorytTitleInput').on('input', function () {
        $(".categoryTextLimit").html(("（")+($(this)[0].value.length)+("文字/35文字）"));
    });

    $('#previewButtonBlog').click(function(){
        EditStaticHtmlService.openPreview();
    });

    $('#previewButtonTemplate').click(function(){
        EditStaticHtmlService.openPreview();
    });

    $('#previewButtonPlainBlog').click(function(){
        EditStaticHtmlService.openPlainPagePreview();
    });

    $('.actionImage').on('change', function(){
        if ($(this)[0].files && $(this)[0].files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#ogImage').attr('src', e.target.result);
                $('#ogImage').show();
            }
            reader.readAsDataURL($(this)[0].files[0]);
        } else {
            $('#ogImage').attr('src', '');
        }
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

    $('#snsScriptAdd').click(function(){
        $(this).toggle();
        $('#snsScriptText').toggle();
    });

    $('.addNewCategory').click(function(){
        $('.newCategory').slideToggle(300);
    });

    $('#addCategoryButton').click(function(){
        $(window).unbind('beforeunload');

        var param = {
            data: $('#frmEntry').serializeArray(),
            url: $(this).data('submit-action'),
            success: function(data){
                $('#categoryError').remove();
                if(data.result == 'ok'){
                    $('#TagTreeSelectionDD').html(data.data.categories_selection);
                    $('#TagTreeListUL').html(data.data.categories_checkbox);
                    Brandco.unit.showNoticeBar($('#jsMessage1'));

                } else if (data.result == 'ng') {
                    $('input[name="name"]').closest('dd').append('<p class="iconError1" id="categoryError">'+data.errors.name+'</p>');
                }
            }
        };
        Brandco.api.callAjaxWithParam(param);
    });

    if ($('#categoryError')[0]) {
        $('.newCategory').slideToggle(300);
    }

    Brandco.helper.initConfirmBox();

    $('.jsMetaDataInput').each(function() {
        EditStaticHtmlService.checkInputLength(this);
    });

    $('.jsMetaDataInput').on('input', function() {
        EditStaticHtmlService.checkInputLength(this);
    });

    // ZeroClipboard copy url to clipboard
    $('.jsCopyToClipboardBtn').each(function() {
        var zero_clipboard = new ZeroClipboard(this);

        zero_clipboard.on('error', function(event) {
            ZeroClipboard.destroy();
        });
    });
    
    EditStaticHtmlService.initWriteType();
    $("input[name='write_type']:radio").on('change', function() {
        EditStaticHtmlService.initWriteType();
    });

    $(document).on('click', '.editPartsButton', function () {
        PartsTemplateService.editingNo = $(this).closest("li").attr('data-no');
        PartsTemplateService.initModal(PartsTemplateService.editingNo);
        Brandco.unit.showModal(this);
        return false;
    });

    $(document).on('click', '.deletePartsButton', function () {
        no = $(this).closest("li").attr('data-no');
        EditStaticHtmlService.deleteParts(no);
        return false;
    });

    EditStaticHtmlService.bindSortable();

    EditStaticHtmlService.initLayoutType();
    $("input[name='layout_type']:radio").on('change', function() {
        EditStaticHtmlService.initLayoutType();
    });

    if ($(".pageLayout").hasClass("jsEditPageLayout")) {
        $(".jsPlainRadio").prop("disabled", true);
    }
});