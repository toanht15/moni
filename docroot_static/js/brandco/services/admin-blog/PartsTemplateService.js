var PartsTemplateService = {
    templateData: {},
    templateType: "",
    editingSliderImageNo:-1,
    currentPartNo: -1,
    body_height: 0,
    templateModals:{1:"pagePartsImageSliderSetting",2:"pagePartsFloatImageSetting",3:"pagePartsFullImageSetting",4:"pagePartsTextSetting",5:"pagePartsInstaSetting",6:"jsPagePartsStampRallySetting"},
    blankImageData:"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA3NCSVQICAjb4U/gAAAABlBMVEX///////9VfPVsAAAACXBIWXMAAAsSAAALEgHS3X78AAAAHHRFWHRTb2Z0d2FyZQBBZG9iZSBGaXJld29ya3MgQ1M26LyyjAAAABZ0RVh0Q3JlYXRpb24gVGltZQAxMC8xNS8xNbG4+7IAAAAKSURBVAiZY2AAAAACAAH0cWSmAAAAAElFTkSuQmCC",
    //一覧表示用
    toTemplateHtml: {
        1:function (no, json) {
            var template = $("#ImageSliderListTemplate").clone();
            template.find("li").attr("data-no", no);
            template.find("[href = '#pagePartsImageSliderSetting']").attr("data-no", no);
            return template.html();
        },
        2:function (no, json) {
            var template = $("#FloatImageListTemplate").clone();
            template.find("li").attr("data-no", no);
            template.find("[href = '#pagePartsFloatImageSetting']").attr("data-no", no);
            return template.html();
        },
        3:function (no, json) {
            var template = $("#FullImageListTemplate").clone();
            template.find("li").attr("data-no", no);
            template.find("[href = '#pagePartsFullImageSetting']").attr("data-no", no);
            return template.html();
        },
        4:function (no, json) {
            var template = $("#TextListTemplate").clone();
            template.find("li").attr("data-no", no);
            template.find("[href = '#pagePartsTextSetting']").attr("data-no", no);
            return template.html();
        },
        5:function (no, json) {
            var template = $("#InstagramListTemplate").clone();
            template.find("li").attr("data-no", no);
            template.find("[href = '#pagePartsInstaSetting']").attr("data-no", no);
            return template.html();
        },
        6:function (no, json) {
            var template = $("#StampRallyListTemplate").clone();
            template.find("li").attr("data-no", no);
            template.find("[href = '#jsPagePartsStampRallySetting']").attr("data-no", no);
            return template.html();
        },
        99:function (no, json) {
            var template = $("#BoundaryListTemplate").clone();
            template.find("li").attr("data-no", no);
            return template.html();
        }
    },
    //フォームからjsonへ戻す用
    updatePartsData: {
        1:function () {
            PartsTemplateService.templateData.slider_pc_image_count = $("#pagePartsImageSliderPcImageCount").val();
            PartsTemplateService.templateData.slider_sp_image_count = $("#pagePartsImageSliderSpImageCount").val();
        },
        2:function () {
            PartsTemplateService.templateData = {
                "image_url":$("#pagePartsFloatImageImage").attr('src'),
                "position_type":$("input[name='pagePartsFloatImageViewType']:radio:checked").val(),
                "smartphone_float_off_flg":$("input[name='pagePartsFloatImageSmartphoneFloatOffFlg']:radio:checked").val(),
                "link":$("#pagePartsFloatImageLink").val(),
                "text":$("#pagePartsFloatImageText").val(),
                "caption":$("#pagePartsFloatImageCaption").val()
            };
        },
        3:function () {
            PartsTemplateService.templateData = {
                "image_url":$("#pagePartsFullImageImage").attr('src'),
                "link":$("#pagePartsFullImageLink").val(),
                "caption":$("#pagePartsFullImageCaption").val()
            };
        },
        4:function () {
            PartsTemplateService.templateData = {
                "text":CKEDITOR.instances.pagePartsTextCaption.getData()
            };
        },
        5:function () {
            var apiUrlInputs = $("input[id^=api_url_]");
            var urls = [];
            apiUrlInputs.each(function(index){
                if($(this).val() != ''){
                    urls[index] = $(this).val();
                }
            });
            PartsTemplateService.templateData = {
                "api_url": JSON.stringify(urls)
            };
        },
        6:function() {
            PartsTemplateService.templateData = {
                "campaign_count": $("#pcSlideNum").val(),
                "stamp_status_joined_image":$("#cp_status_joined").attr('src'),
                "stamp_status_finished_image":$("#cp_status_finish").attr('src'),
                "stamp_status_coming_soon_image":$("#cp_status_coming_soon").attr('src'),
                "cp_ids": PartsTemplateService.stampRallyCpSelect[PartsTemplateService.currentPartNo]
            };
        }
    },
    stampRallyCpStatusImages: {
        'joined_cp' : '/img/stampRally/stampStatusJoined.png',
        'finish_cp' : '/img/stampRally/stampStatusFinished.png',
        'comming_soon_cp' : '/img/stampRally/stampStatusComingsoon.png'
    },
    stampRallyCpSelect: [],
    stampRallyCpStatusConditon: [],
    stampRallyCpSelectConditon: [],
    stampRallyCpOrderConditon: [],
    stampRallyCpPerPageCount: [],
    stampRallyCurrentPage: [],
    stampRallyTargetCpNum: [],
    stampRallyCpSearchType: {
        'search_by_select': 1,
        'search_by_status': 2,
        'search_by_open_date': 3,
        'search_by_finish_date': 4
    },
    stampRallyCpSearchAction: {
        'action_search': 1,
        'action_clear': 2
    },
    cpStatus: {
        demo_cp: 4,
        draft_cp:1,
        schedule_cp:2,
        close: 5,
        wait_announce: 4,
        open: 3
    },
    cpSelect: {
        selected: 1
    },
    order: {
        'asc' : 1,
        'desc' : 2
    },
    initModal: function (no, type) {
        if(no != -1) { //already
            PartsTemplateService.templateData = $.extend(true, {}, EditStaticHtmlService.templateData[no].template);
            PartsTemplateService.templateType = EditStaticHtmlService.templateData[no].type;
        }else { //new
            PartsTemplateService.templateData = {"type":type, "item_list":[]};
            PartsTemplateService.templateType = type;
            PartsTemplateService.removeValidateMessage();
        }
        PartsTemplateService.setDefaultModal[PartsTemplateService.templateType](no);
    },
    setSliderImage: function (result) {
        if (result != '') {
            var dom = $('#pagePartsImageSliderSetting .photo img');
            dom.attr('src', result);
            PartsTemplateService.showSubController();
        }
    },
    setFloatImage: function (result) {
        if (result != '') {
            var dom = $('#pagePartsFloatImageSetting .photo img');
            dom.attr('src', result);
            PartsTemplateService.showSubController();
        }
    },
    setFullImage: function (result) {
        if (result != '') {
            var dom = $('#pagePartsFullImageSetting .photo img');
            dom.attr('src', result);
            PartsTemplateService.showSubController();
        }
    },
    setCpStatusJoinedImage: function (result) {
        if (result != '') {
            $('#cp_status_joined').attr('src', result);
        }
    },
    setCpStatusFinishImage: function (result) {
        if (result != '') {
            $('#cp_status_finish').attr('src', result);
        }
    },
    setCpStatusComingSoonImage: function (result) {
        if (result != '') {
            $('#cp_status_coming_soon').attr('src', result);
        }
    },
    setDefaultModal: {
        1:function (no) {
            if(no != -1) { //already
                $("#pagePartsImageSliderPcImageCount").val(PartsTemplateService.templateData.slider_pc_image_count);
                $("#pagePartsImageSliderSpImageCount").val(PartsTemplateService.templateData.slider_sp_image_count);
            }else { //new
                $("#pagePartsImageSliderPcImageCount").val(5);
                $("#pagePartsImageSliderSpImageCount").val(2);
            }

            PartsTemplateService.emptySlideImageForm();
            PartsTemplateService.renderImageSliderContainer();
        },
        2:function (no) {
            if(no != -1) { //already
                formData = PartsTemplateService.templateData;
                if(formData.position_type == '1') {
                    $('#pagePartsFloatImageViewType1').prop('checked', true);
                }else if(formData.position_type == '2') {
                    $('#pagePartsFloatImageViewType2').prop('checked', true);
                }
                if(formData.smartphone_float_off_flg == '1') {
                    $('#pagePartsFloatImageSmartphoneFloatOffFlg1').prop('checked', true);
                }else{
                    $('#pagePartsFloatImageSmartphoneFloatOffFlg0').prop('checked', true);
                }
                $("#pagePartsFloatImageCaption").val(formData.caption);
                $("#pagePartsFloatImageText").val(formData.text);
                $("#pagePartsFloatImageLink").val(formData.link);
                $("#pagePartsFloatImageImage").attr("src", formData.image_url);
                PartsTemplateService.showSubController();
            }else { //new
                $('#pagePartsFloatImageViewType1').prop('checked', false);
                $('#pagePartsFloatImageViewType2').prop('checked', false);
                $('#pagePartsFloatImageSmartphoneFloatOffFlg0').prop('checked', false);
                $('#pagePartsFloatImageSmartphoneFloatOffFlg1').prop('checked', false);
                $("#pagePartsFloatImageCaption").val("");
                $("#pagePartsFloatImageText").val("");
                $("#pagePartsFloatImageLink").val("");
                $("#pagePartsFloatImageImage").attr("src", PartsTemplateService.blankImageData);
                PartsTemplateService.hideSubController();
            }
        },
        3:function (no) {
            if(no != -1) { //already
                formData = PartsTemplateService.templateData;
                $("#pagePartsFullImageLink").val(formData.link);
                $("#pagePartsFullImageCaption").val(formData.caption);
                $("#pagePartsFullImageImage").attr("src", formData.image_url);
                PartsTemplateService.showSubController();
            }else { //new
                $("#pagePartsFullImageLink").val("");
                $("#pagePartsFullImageCaption").val("");
                $("#pagePartsFullImageImage").attr("src", PartsTemplateService.blankImageData);
                PartsTemplateService.hideSubController();
            }
        },
        4:function (no) {
            if(CKEDITOR.instances.pagePartsTextCaption) {
                CKEDITOR.instances.pagePartsTextCaption.destroy();
            }
            CKEDITOR.replace( 'pagePartsTextCaption', {
                toolbar:[
                    ['Link','-','Bold','Italic','Underline','Strike','-','Font','FontSize','TextColor','-','RemoveFormat','-','Source']
                ]
                ,height:200
            });
            if(no != -1) { //already
                formData = PartsTemplateService.templateData;
                CKEDITOR.instances.pagePartsTextCaption.setData(formData.text);
            }else { //new
                CKEDITOR.instances.pagePartsTextCaption.setData("");
            }
        },
        5:function (no) {
            if(no != -1) { //already
                formData = PartsTemplateService.templateData;
                var urls = JSON.parse(formData.api_url);

                $(".jsPagePartsInstagramApiUrl").empty();

                var html = '<p class="api_url_box">';
                html += '<input type="text" id="api_url_1" class="url" maxlength="250"><a href="javascript:void(0)" class="iconBtnDelete jsDeleteApiUrlBox">削除</a><span class="pagePartsErrorMessage" id="apiUrlError_1"></span>';
                html += '</p>';
                $(".jsPagePartsInstagramApiUrl").html(html);

                for(var index = 0; index < urls.length; index++){
                    if($("#api_url_"+(index+1)).length){
                        $("#api_url_"+(index+1)).val(urls[index]);
                    }else{
                        $(".jsPagePartsInstagramApiUrl").append('<p class="api_url_box"><input type="text" id="api_url_'+(index+1)+'" class="url" maxlength="250">' +
                        '<a href="javascript:void(0)" class="iconBtnDelete jsDeleteApiUrlBox">削除</a><span class="pagePartsErrorMessage" id="apiUrlError_'+(index+1)+'"></span></p>');
                        $("#api_url_"+(index+1)).val(urls[index]);
                    }
                }

                if(urls.length == 6){
                    $(".linkAdd").hide();
                }
            }else { //new

                $(".jsPagePartsInstagramApiUrl").empty();

                var html = '<p class="api_url_box">';
                html += '<input type="text" id="api_url_1" class="url" maxlength="250"><a href="javascript:void(0)" class="iconBtnDelete jsDeleteApiUrlBox">削除</a><span class="pagePartsErrorMessage" id="apiUrlError_1"></span>';
                html += '</p>';

                $(".jsPagePartsInstagramApiUrl").html(html);
            }
        },
        6:function (no) {

            if(no != -1) { //already

                formData = PartsTemplateService.templateData;
                $('#pcSlideNum').val(formData.campaign_count);

                PartsTemplateService.setCpStatusJoinedImage(formData.stamp_status_joined_image);
                PartsTemplateService.setCpStatusFinishImage(formData.stamp_status_finished_image);
                PartsTemplateService.setCpStatusComingSoonImage(formData.stamp_status_coming_soon_image);

                PartsTemplateService.removeValidateMessage();

                $('#stampRallyCpSetting').empty();
                PartsTemplateService.getListCp(null, null, null, null);

                PartsTemplateService.stampRallyCpSelect[no] = formData.cp_ids;

                PartsTemplateService.stampRallyCpSelectConditon[no] = [];
                PartsTemplateService.stampRallyCpStatusConditon[no] = [];
                PartsTemplateService.stampRallyCpOrderConditon[no] = [];
                PartsTemplateService.stampRallyCurrentPage[no] = 1;
                PartsTemplateService.stampRallyTargetCpNum[no] = formData.campaign_count;
            }else { //new

                $('#pcSlideNum').val('');

                var static_base_url = $('input[name=static_base_url]').val();
                PartsTemplateService.setCpStatusJoinedImage(static_base_url+PartsTemplateService.stampRallyCpStatusImages.joined_cp);
                PartsTemplateService.setCpStatusFinishImage(static_base_url+PartsTemplateService.stampRallyCpStatusImages.finish_cp);
                PartsTemplateService.setCpStatusComingSoonImage(static_base_url+PartsTemplateService.stampRallyCpStatusImages.comming_soon_cp);

                $('#stampRallyCpSetting').empty();
                PartsTemplateService.getListCp(null, null, null, null);

                PartsTemplateService.stampRallyCpSelect[no] = [];
                PartsTemplateService.stampRallyCpSelectConditon[no] = [];
                PartsTemplateService.stampRallyCpStatusConditon[no] = [];
                PartsTemplateService.stampRallyCpOrderConditon[no] = [];
                PartsTemplateService.stampRallyCurrentPage[no] = 1;
                PartsTemplateService.stampRallyTargetCpNum[no] = 0;
            }
        }
    },
    deleteSlideImage: function(no) {
        PartsTemplateService.deleteSlideImageData(no);
        PartsTemplateService.renderImageSliderContainer();
    },
    saveSlideImage: function() {
        if(PartsTemplateService.commonValidateForm("pagePartsImageSliderSetting")) {
            if(PartsTemplateService.editingSliderImageNo != -1) {
                PartsTemplateService.editSlideImageData(PartsTemplateService.editingSliderImageNo);
            }else {
                PartsTemplateService.addSlideImageData();
            }
            PartsTemplateService.renderImageSliderContainer();
            PartsTemplateService.emptySlideImageForm();
        }
    },
    removeValidateMessage: function() {
        $(".pagePartsErrorMessage").empty();
        $(".pagePartsErrorMessage").removeClass("iconError1");
    },
    viewValidateMessage: function(jqDom, message) {
        jqDom.html(message);
        jqDom.addClass("iconError1");
    },
    commonValidateForm: function(templateId) {
        var result = true;
        PartsTemplateService.removeValidateMessage();
        $("#" + templateId + " [data-validate]").each(function() {
            validateDefines = $(this).data("validate").split("@");
            for(var vKey in validateDefines){
                rules        = validateDefines[vKey].split("|");
                validType    = rules[0];
                selector     = rules[1];
                type         = rules[2];
                message      = rules[3];
                if(rules[4]) {
                    option = rules[4];
                }else{
                    option = "";
                }

                if(type == 'val' || type == 'radio') {
                    value = $(selector).val();
                }else if(type == 'ckeditor') {
                    value = CKEDITOR.instances[selector].getData();
                }else {
                    value = $(selector).attr(type);
                }
                if(!PartsTemplateService.validateBase($(this), validType, type, value, message, option)){
                    result = false;
                }
            }
        });
        return result;
    },
    validateBase: function(errorJqObj, validType, type, value, message, option) {
        var result = true;
        if( validType == "require" ) {
            message     = message ? message : "入力して下さい";
            if(!value || type == 'src' && value == PartsTemplateService.blankImageData) {
                PartsTemplateService.viewValidateMessage(errorJqObj, message);
                result = false;
            }
        }else if( validType == "max" ) {
            length      = option;
            message     = message ? message : length + "文字以内で入力して下さい";
            if(!PartsTemplateService.validateMaxLength(value, length)) {
                PartsTemplateService.viewValidateMessage(errorJqObj, message);
                result = false;
            }
        }else if( validType == "between" ) {
            range       = option.split("-");
            message     = message ? message : range[0] + "〜" + range[1] + "の範囲で入力して下さい";
            if(!PartsTemplateService.validateBetween(value, range[0], range[1])) {
                PartsTemplateService.viewValidateMessage(errorJqObj, message);
                result = false;
            }
        }else if( validType == "url" ) {
            message     = message ? message : "URL形式で入力して下さい";
            if(value && !PartsTemplateService.validateUrl(value)) {
                PartsTemplateService.viewValidateMessage(errorJqObj, message);
                result = false;
            }
        }
        return result;

    },
    imageSliderValidateForm: function() {
        var result = true;
        PartsTemplateService.removeValidateMessage();
        if(PartsTemplateService.templateData.item_list.length == 0) {
            PartsTemplateService.viewValidateMessage($("#imageSliderCommonErrorMessage"), "画像を1つ以上設定して下さい");
            result = false;
        }

        if(PartsTemplateService.imageSliderNumValidate() == false){
            result = false;
        }

        return result;
    },
    imageSliderNumValidate: function() {
        if(false == PartsTemplateService.validateBase($("#imageSliderPcImageCountErrorMessage"), "between", "val", $("#pagePartsImageSliderPcImageCount").val(), "", "1-10")) {
            return false;
        }

        if(false == PartsTemplateService.validateBase($("#imageSliderSpImageCountErrorMessage"), "between", "val", $("#pagePartsImageSliderSpImageCount").val(), "", "1-10")){
            return false;
        }

        return true;
    },
    instagramValidateBeforeAddNewUrlInput: function() {
        var result = true;
        PartsTemplateService.removeValidateMessage();

        var apiUrlInputs = $("input[id^=api_url_]");

        apiUrlInputs.each(function(index){
            if(!PartsTemplateService.validateBase($("#apiUrlError_"+(index+1)), "require", "val", $(this).val(), "", "")) {
                result = false;
            }

            if(!PartsTemplateService.validateBase($("#apiUrlError_"+(index+1)), "url", "val", $(this).val(), "", "")) {
                result = false;
            }
        });

        return result;
    },
    instagramValidateForm: function() {
        var result = true;
        PartsTemplateService.removeValidateMessage();
        var apiUrlInputs = $("input[id^=api_url_]");
        var notInputCount = 0;
        apiUrlInputs.each(function(index){

            if(!PartsTemplateService.validateBase($("#apiUrlError_"+(index+1)), "url", "val", $(this).val(), "", "")) {
                result = false;
            }

            if($(this).val() == ""){
                notInputCount++;
            }
        });

        if(notInputCount == apiUrlInputs.length){
            PartsTemplateService.viewValidateMessage($("#notInputUrlError"), "1つ項目以上入力して下さい");
            result =false;
        }
        return result;
    },
    stampRallyValidateSelectTargetCp: function(){

        var targetCpNum = $("input[id=pcSlideNum]").val();
        var selectCpNum = PartsTemplateService.stampRallyCpSelect[PartsTemplateService.currentPartNo].length + 1;

        if(!PartsTemplateService.validatePositiveNumber(targetCpNum)){
            PartsTemplateService.viewValidateMessage($("#targetCpNumError"), "対象キャンペーン数入力して下さい");
            PartsTemplateService.scrollToTargetCpNumBox();
            return false;
        }

        if(!PartsTemplateService.validateTargetCpNum(targetCpNum,selectCpNum)){
            PartsTemplateService.viewValidateMessage($("#selectCpNumError"), "選択するキャンペーン数が上限を超えています");
            PartsTemplateService.scrollToCheckCpErrorMsg();
            return false;
        }

        return true;
    },
    stampRallyValidateInputTargetCp: function(){

        var targetCpNum = $("input[id=pcSlideNum]").val();
        var selectCpNum = PartsTemplateService.stampRallyCpSelect[PartsTemplateService.currentPartNo].length;

        if(targetCpNum != ''){

            if(!PartsTemplateService.validatePositiveNumber(targetCpNum)){
                PartsTemplateService.viewValidateMessage($("#targetCpNumError"), "対象キャンペーン数を正しく入力してください");
                return false;
            }

            if(!PartsTemplateService.validateTargetCpNum(targetCpNum,selectCpNum)){
                PartsTemplateService.viewValidateMessage($("#targetCpNumError"), "対象キャンペーン数を正しく入力してください");
                return false;
            }

        }else{

            if(selectCpNum != 0) {
                PartsTemplateService.viewValidateMessage($("#targetCpNumError"), "対象キャンペーン数を正しく入力してください");
                return false;
            }
        }

        return true;
    },
    stampRallyValidateForm: function() {
        var targetCpNum = $("input[id=pcSlideNum]").val();
        var selectCpNum = PartsTemplateService.stampRallyCpSelect[PartsTemplateService.currentPartNo].length;

        if(targetCpNum != ''){

            if(!PartsTemplateService.validatePositiveNumber(targetCpNum)){
                PartsTemplateService.viewValidateMessage($("#targetCpNumError"), "対象キャンペーン数を正しく入力してください");
                return false;
            }

            if(!PartsTemplateService.validateTargetCpNum(targetCpNum,selectCpNum)){
                PartsTemplateService.viewValidateMessage($("#targetCpNumError"), "対象キャンペーン数を正しく入力してください");
                return false;
            }

        }else{

            PartsTemplateService.viewValidateMessage($("#targetCpNumError"), "対象キャンペーン数入力して下さい");
            return false;
        }

        return true;
    },
    validateUrl: function(str) {
        res = str.match(/\(?(?:(http|https|ftp):\/\/)?(?:((?:[^\W\s]|\.|-|[:]{1})+)@{1})?((?:www.)?(?:[^\W\s]|\.|-)+[\.][^\W\s]{2,4}|localhost(?=\/)|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})(?::(\d*))?([\/]?[^\s\?]*[\/]{1})*(?:\/?([^\s\n\?\[\]\{\}\#]*(?:(?=\.)){1}|[^\s\n\?\[\]\{\}\.\#]*)?([\.]{1}[^\s\?\#]*)?)?(?:\?{1}([^\s\n\#\[\]]*))?([\#][^\s\n]*)?\)?/gi);
        if(res) {
            return true;
        }else {
            return false;
        }
    },
    validateMaxLength: function(str, maxLength) {
        if(str.length > maxLength) {
            return false;
        }else {
            return true;
        }
    },
    validateBetween: function(val, from, to) {
        if(Math.round(parseInt(val)) !== parseInt(val)) {
            return false;
        }
        val = parseInt(val);
        from = parseInt(from);
        to = parseInt(to);
        if(val > to || val < from) {
            return false;
        }else {
            return true;
        }
    },
    validateTargetCpNum: function(val, selectedCpNum) {
        if(val < selectedCpNum){
            return false;
        }
        return true;
    },
    validatePositiveNumber: function(val) {
        val = parseInt(val);
        if(Math.round(val) !== val || val <= 0) {
            return false;
        }
        return true;
    },
    renderImageSliderContainer: function() {
        if(PartsTemplateService.templateData.item_list.length){
            $("#slideContainer").show();
        }else{
            $("#slideContainer").hide();
        }
        $("#slideContainer").empty();
        for(var no in PartsTemplateService.templateData.item_list) {
            html = '<li data-no="' + no + '">';
            html+= '<p><img src="' + PartsTemplateService.templateData.item_list[no].image_url + '"></p>';
            html+= '<p class="text">' + PartsTemplateService.templateData.item_list[no].caption + '<span class="url">' + PartsTemplateService.templateData.item_list[no].link + '</span></p>';
            html+= '    <p class="btn">';
            html+= '        <span class="btn2 btnDelete"><a href="javascript:void(0);" class="small1 jsDeleteImageSlide">削除</a></span>';
            html+= '        <span class="btn3 jsSlideEdit"><a href="javascript:void(0);" class="small1 jsEditImageSlide">編集</a></span>';
            html+= '    </p>';
            html+= '</li>';
            $("#slideContainer").append(html);
        }
    },
    addSlideImageData: function() {
        template = {"image_url":$("#pagePartsImageSliderImage").attr('src'), "caption":$("#pagePartsImageSliderCaption").val(), "link":$("#pagePartsImageSliderLink").val()};
        PartsTemplateService.templateData.item_list.unshift(template);
    },
    editSlideImageData: function(no) {
        template = {"image_url":$("#pagePartsImageSliderImage").attr('src'), "caption":$("#pagePartsImageSliderCaption").val(), "link":$("#pagePartsImageSliderLink").val()};
        PartsTemplateService.templateData.item_list[no] = template;
    },
    deleteSlideImageData: function(no) {
        PartsTemplateService.templateData.item_list.splice(no, 1);
    },
    bindSortable: function() {
        $("#slideContainer").sortable({
            update: function(ev, ui) {
                var newArray = [];
                $("#slideContainer li").each(function(index) {
                    newArray[index] = PartsTemplateService.templateData.item_list[$(this).data('no')];
                });
                PartsTemplateService.templateData.item_list = newArray;
                PartsTemplateService.renderImageSliderContainer();
            }
        });
    },
    emptySlideImageForm: function() {
        $("#pagePartsImageSliderCaption").val("");
        $("#pagePartsImageSliderLink").val("");
        $("#pagePartsImageSliderImage").attr("src", PartsTemplateService.blankImageData);
        $("#pagePartsImageSliderSave").html("追加");
        PartsTemplateService.hideSubController();
        PartsTemplateService.editingSliderImageNo = -1;
    },

    hideSubController: function() {
        $(".subController").hide();
    },
    showSubController: function() {
        $(".subController").show();
    },
    updateSlideImageForm: function(no) {
        formData = PartsTemplateService.templateData.item_list[no];
        $("#pagePartsImageSliderCaption").val(formData.caption);
        $("#pagePartsImageSliderLink").val(formData.link);
        $("#pagePartsImageSliderImage").attr("src", formData.image_url);
        $("#pagePartsImageSliderSave").html("更新");
        PartsTemplateService.editingSliderImageNo = no;
        PartsTemplateService.showSubController();
    },
    addApiUrlInputBox: function() {
        var apiUrlInputs = $("input[id^=api_url_]");
        var apiUrlInputCount = apiUrlInputs.length;
        var canAddNewBox = true;
        if(apiUrlInputCount < 6){

            if(PartsTemplateService.instagramValidateBeforeAddNewUrlInput() == false){
                canAddNewBox = false;
            }

            if(canAddNewBox == true){
                $(".jsPagePartsInstagramApiUrl").append('<p class="api_url_box"><input type="text" id="api_url_'+(apiUrlInputCount+1)+'" class="url" maxlength="250">' +
                '<a href="javascript:void(0)" class="iconBtnDelete jsDeleteApiUrlBox">削除</a><span class="pagePartsErrorMessage" id="apiUrlError_'+(apiUrlInputCount+1)+'"></span></p>');
            }

            if(apiUrlInputCount == 5 && canAddNewBox == true){
                $(".linkAdd").hide();
            }
        }
    },
    deleteApiUrlInputBox: function(api_url_box) {
        var apiUrlInputs = $("input[id^=api_url_]");
        if(apiUrlInputs.length == 1){
            $(apiUrlInputs[0]).val('');
        }else{
            $(api_url_box).closest('.api_url_box').slideToggle(300,function(){
                $(this).remove();
            });
            $(".linkAdd").show();
        }
    },
    getListCp: function(page, limit, search_conditions, orders) {
        var url = $("input[name=get_list_cp_url]").val();
        var data = {
            'page' : page,
            'limit' : limit,
            'search_conditions' : search_conditions,
            'orders' : orders,
            'cp_status_joined_image': $('#cp_status_joined').attr('src'),
            'cp_status_finished_image': $('#cp_status_finish').attr('src')
        };
        var param = {
            data: data,
            type: 'GET',
            url: url,
            beforeSend: function(){
                Brandco.helper.brandcoBlockUI();
            },
            success: function(data) {
                $('#stampRallyCpSetting').html(data.html);
                PartsTemplateService.stampRallyCpPerPageCount[PartsTemplateService.currentPartNo] = $("select[name=cp_limit]").val();
                PartsTemplateService.updateSelectCpNum(PartsTemplateService.stampRallyCpSelect[PartsTemplateService.currentPartNo].length);
                if(PartsTemplateService.stampRallyTargetCpNum[PartsTemplateService.currentPartNo] == ""){
                    $(".targetCpNum").text(0);
                }else{
                    $(".targetCpNum").text(PartsTemplateService.stampRallyTargetCpNum[PartsTemplateService.currentPartNo]);
                }
                $(".jsSelectCp").each(function(){
                    if(jQuery.inArray($(this).data('cp_id'),PartsTemplateService.stampRallyCpSelect[PartsTemplateService.currentPartNo]) >= 0){
                        $(this).prop('checked',true);
                    }
                });
            },
            complete: function() {
                $.unblockUI();

                var modalID = '#jsPagePartsStampRallySetting';

                $(modalID).height($('body').height()).fadeIn(300, function(){
                    $(this).find('.jsModalCont').css({
                        display: 'block',
                        opacity: 0,
                        top: $(window).scrollTop()
                    }).animate({
                        top: $(window).scrollTop() + 30,
                        opacity: 1
                    }, 300, function() {
                        var modal_height = $(modalID).find('.jsModalCont').position().top + $(modalID).find('.jsModalCont').outerHeight(true);
                        var body_height = $('body').outerHeight(true);
                        var default_height = $('body').data('prev_height');

                        if (default_height === undefined || default_height == '') {
                            $('body').data('prev_height', body_height);
                            default_height = body_height;
                        }

                        if (body_height >= default_height && body_height < modal_height) {
                            $('body').height(modal_height + 10);
                            $(modalID).height($('body').height());
                        }
                    });
                });
            }
        };
        Brandco.api.callAjaxWithParam(param,false,false);
    },
    toggleAnimation: function (target) {
        $('.jsAreaToggleTarget').not(target).fadeOut(200, function() {
            setTimeout(function(){
                PartsTemplateService.apply_store_element($(target).data('search_type'));
                PartsTemplateService.deleteAttention();
            },300)
        });
        if(target.is(':hidden')) {
            target.fadeIn(200);
        } else {
            target.fadeOut(200);
        }
    },
    deleteToggle: function (target) {
        target.stop(true, true).fadeToggle(200,function() {
            setTimeout(function(){
                PartsTemplateService.apply_store_element($(target).data('search_type'));
                PartsTemplateService.deleteAttention();
            },300)
        });
    },
    apply_store_element: function (search_type) {
        switch(search_type){
            case PartsTemplateService.stampRallyCpSearchType.search_by_select:
                PartsTemplateService.apply_checkbox_select_store_element();
                break;
            case PartsTemplateService.stampRallyCpSearchType.search_by_status:
                PartsTemplateService.apply_checkbox_status_store_element();
                break;
        }
    },
    apply_checkbox_select_store_element: function(){
        $('input[name=search_selected_cp]').each(function () {
            if(jQuery.inArray($(this).data('select') , PartsTemplateService.stampRallyCpSelectConditon[PartsTemplateService.currentPartNo]) >= 0){
                $(this).prop('checked', true);
            }else{
                $(this).prop('checked', false);
            }
        });
    },
    apply_checkbox_status_store_element: function(){
        $('input[name^=search_cp_status]').each(function () {
            if(jQuery.inArray($(this).data('status') ,PartsTemplateService.stampRallyCpStatusConditon[PartsTemplateService.currentPartNo]) >= 0){
                $(this).prop('checked', true);
            }else{
                $(this).prop('checked', false);
            }
        });
    },
    deleteAttention: function () {
        $('p.attention1').remove();
    },
    searchCp: function(){
        var page = PartsTemplateService.stampRallyCurrentPage[PartsTemplateService.currentPartNo];
        var limit = PartsTemplateService.stampRallyCpPerPageCount[PartsTemplateService.currentPartNo];
        var search_conditions = {'get_select_cp': PartsTemplateService.stampRallyCpSelectConditon[PartsTemplateService.currentPartNo],'status': PartsTemplateService.stampRallyCpStatusConditon[PartsTemplateService.currentPartNo],'select_cp': PartsTemplateService.stampRallyCpSelect[PartsTemplateService.currentPartNo]};
        var orders = PartsTemplateService.stampRallyCpOrderConditon[PartsTemplateService.currentPartNo];
        PartsTemplateService.getListCp(page, limit, search_conditions, orders);
    },
    updateSearchCpCondition: function(search_type, action_type){
        if(action_type == PartsTemplateService.stampRallyCpSearchAction.action_search){
            PartsTemplateService.addSearchCpConditon(search_type);
        }else if(action_type == PartsTemplateService.stampRallyCpSearchAction.action_clear){
            PartsTemplateService.clearSearchCpConditon(search_type);
        }
    },
    updateOrderCondition: function(search_type, action_type){
        switch (search_type){
            case PartsTemplateService.stampRallyCpSearchType.search_by_open_date:
                PartsTemplateService.addCpOpenDateOrder(action_type);
                break;
            case PartsTemplateService.stampRallyCpSearchType.search_by_finish_date:
                PartsTemplateService.addCpFinishDateOrder(action_type);
                break;
        }
    },
    clearSearchCpConditon: function(search_type){
        switch (search_type){
            case PartsTemplateService.stampRallyCpSearchType.search_by_select:
                PartsTemplateService.stampRallyCpSelectConditon[PartsTemplateService.currentPartNo] = [];
                $("input[name=search_selected_cp]").prop('checked', false);
                break;
            case PartsTemplateService.stampRallyCpSearchType.search_by_status:
                PartsTemplateService.stampRallyCpStatusConditon[PartsTemplateService.currentPartNo] = [];
                $("input[name^=search_cp_status]").each(function(){
                    $(this).prop('checked', false);
                });
                break;
        }
    },
    addSearchCpConditon: function(search_type){
        switch (search_type){
            case PartsTemplateService.stampRallyCpSearchType.search_by_select:
                PartsTemplateService.addSelectCpCondition();
                break;
            case PartsTemplateService.stampRallyCpSearchType.search_by_status:
                PartsTemplateService.addStatusCondition();
                break;
        }
    },
    addSelectCpCondition: function(){
        if($("input[name=search_selected_cp]:checked").length > 0){
            PartsTemplateService.stampRallyCpSelectConditon[PartsTemplateService.currentPartNo].push(PartsTemplateService.cpSelect.selected);
        }
    },
    addStatusCondition: function(){
        $("input[name^=search_cp_status]").each(function(){
            var status = $(this).data('status');
            if(this.checked){
                if(jQuery.inArray(status , PartsTemplateService.stampRallyCpStatusConditon[PartsTemplateService.currentPartNo]) == -1){
                    PartsTemplateService.stampRallyCpStatusConditon[PartsTemplateService.currentPartNo].push(status);
                }
            }else{
                PartsTemplateService.stampRallyCpStatusConditon[PartsTemplateService.currentPartNo] = jQuery.grep(PartsTemplateService.stampRallyCpStatusConditon[PartsTemplateService.currentPartNo], function(value){
                    return value != status;
                });
            }
        });
    },
    addCpOpenDateOrder: function(action_type){
        PartsTemplateService.stampRallyCpOrderConditon[PartsTemplateService.currentPartNo] = {
            'name' : 'start_date',
            'direction': action_type
        };
    },
    addCpFinishDateOrder: function(action_type){
        PartsTemplateService.stampRallyCpOrderConditon[PartsTemplateService.currentPartNo] = {
            'name' : 'end_date',
            'direction': action_type
        };
    },
    validateCpSearch: function(search_box){
        if($(search_box).closest('div[data-search_type]').find(":checked").length == 0){
            $(search_box).closest('div[data-search_type]').find('.boxCloseBtn').after('<p class="attention1">1つ以上選択してください。</p>');
            return false;
        }
        return true;
    },
    updateSelectCpNum: function(num){
        $(".jsCountArea").text(num);
    },
    scrollToTargetCpNumBox: function(){
        var speed = 500;
        if ($('input[name="isSP"]:first').val()) {
            var sp_account_header = $('section.account').height();
        } else {
            var sp_account_header = 0;
        }
        var position = $("#pcSlideNum").offset().top - sp_account_header;
        $('body,html').animate({scrollTop: position}, speed, 'swing');
    },
    scrollToCheckCpErrorMsg: function(){
        var speed = 500;
        if ($('input[name="isSP"]:first').val()) {
            var sp_account_header = $('section.account').height();
        } else {
            var sp_account_header = 0;
        }
        var position = $(".checkedCampaign").first().offset().top - sp_account_header;
        $('body,html').animate({scrollTop: position}, speed, 'swing');
    }
};

$(document).ready(function() {

    $(document).on('click', '.openPartsModal', function () {
        no = $(this).attr('data-no') ? $(this).attr('data-no') : -1;
        if(no == -1) {
            type = $(this).attr('data-type') ? $(this).attr('data-type') : "";
        }else {
            type = EditStaticHtmlService.templateData[no].type;
        }
        EditStaticHtmlService.editingNo = no;
        PartsTemplateService.initModal(no, type);
        PartsTemplateService.currentPartNo = no;
        Brandco.unit.showModal(this);
        Brandco.helper.initConfirmBox();
        return false;
    });

    $(document).on('click', '.saveAndCloseModal', function () {
        if(PartsTemplateService.templateModals[PartsTemplateService.templateType] == 'pagePartsImageSliderSetting') {
            if(!PartsTemplateService.imageSliderValidateForm()) {
                return false;
            }
        }else if(PartsTemplateService.templateModals[PartsTemplateService.templateType] == 'pagePartsInstaSetting'){
            if(!PartsTemplateService.instagramValidateForm()) {
                return false;
            }
        }else if(PartsTemplateService.templateModals[PartsTemplateService.templateType] == 'jsPagePartsStampRallySetting'){
            if(!PartsTemplateService.stampRallyValidateForm()){
                PartsTemplateService.scrollToTargetCpNumBox();
                return false;
            }
        }else{
            if(!PartsTemplateService.commonValidateForm(PartsTemplateService.templateModals[PartsTemplateService.templateType])) {
                return false;
            }
        }
        no = $(this).attr('data-no') ? $(this).attr('data-no') : -1;
        PartsTemplateService.updatePartsData[PartsTemplateService.templateType]();
        EditStaticHtmlService.saveParts(no);
        Brandco.unit.closeModalFlame(this);
        return false;
    });

    $(document).on('click', '.jsEditImageSlide', function () {
        no = $(this).closest("li").attr('data-no');
        PartsTemplateService.updateSlideImageForm(no);
        return false;
    });

    $(document).on('click', '.jsDeleteImageSlide', function () {
        no = $(this).closest("li").attr('data-no');
        PartsTemplateService.deleteSlideImage(no);
        return false;
    });

    $("#pagePartsImageSliderSave").on('click', function() {
        PartsTemplateService.saveSlideImage();
        return false;
    });

    $(".linkAdd").on('click', function() {
        PartsTemplateService.addApiUrlInputBox();
    });

    $(document).on('click', '.jsDeleteApiUrlBox', function () {
        PartsTemplateService.deleteApiUrlInputBox(this);
        return false;
    });

    /*--------------Stamp Rally Cp二関する-----------*/

    $(document).on('click', '.jsAreaToggle', function () {
        if ($(this).hasClass('iconBtnSort') || $(this).hasClass('btnArrowB1')) {
            PartsTemplateService.toggleAnimation($(this).parents('.jsAreaToggleWrap').find('.jsAreaToggleTarget'));
        }
    });

    $(document).on('click', '.boxCloseBtn', function () {
        PartsTemplateService.deleteToggle($(this).parents('.jsAreaToggleWrap').find('.jsAreaToggleTarget'));
    });

    $(document).on('click', 'a[data-search_type]', function(){
        if(PartsTemplateService.validateCpSearch(this)){
            PartsTemplateService.updateSearchCpCondition($(this).data('search_type'),PartsTemplateService.stampRallyCpSearchAction.action_search);
            PartsTemplateService.stampRallyCurrentPage[PartsTemplateService.currentPartNo] = 1;
            PartsTemplateService.searchCp();
        }
    });

    $(document).on('click', 'a[data-clear_type]', function(){
        PartsTemplateService.updateSearchCpCondition($(this).data('clear_type'),PartsTemplateService.stampRallyCpSearchAction.action_clear);
        PartsTemplateService.stampRallyCurrentPage[PartsTemplateService.currentPartNo] = 1;
        PartsTemplateService.searchCp();
    });

    $(document).on('click','a[data-order]',function(){
        PartsTemplateService.updateOrderCondition($(this).closest('div[data-search_type]').data('search_type'),$(this).data('order'));
        PartsTemplateService.stampRallyCurrentPage[PartsTemplateService.currentPartNo] = 1;
        PartsTemplateService.searchCp();
    });

    $(document).on('click', 'a[name=applyFanLimit]', function () {
        PartsTemplateService.stampRallyCpPerPageCount[PartsTemplateService.currentPartNo] = $(this).closest('.checkedStampRallyCampaignWrap').find('select[name=cp_limit]').val();
        PartsTemplateService.stampRallyCurrentPage[PartsTemplateService.currentPartNo] = 1;
        PartsTemplateService.searchCp();
    });

    $(document).on('click', '.jsCpDataListPager', function () {
        PartsTemplateService.stampRallyCurrentPage[PartsTemplateService.currentPartNo] = $(this).data('page');
        PartsTemplateService.searchCp();
    });

    $(document).on('click', '.jsResetCpImage', function () {
        var cp_image = $(this).closest('li').find('.statusIcon').find('img');
        cp_image.attr('src',cp_image.data('default_image'));
    });

    $(document).on('click','.jsRemoveActiveCp',function(){
        var current_check_cp_id = $('input[name=current_check_cp_id]').val();
        $('input[data-cp_id='+current_check_cp_id+']').prop('checked',false);
        PartsTemplateService.stampRallyCpSelect[PartsTemplateService.currentPartNo] = jQuery.grep(PartsTemplateService.stampRallyCpSelect[PartsTemplateService.currentPartNo], function(value){
            return value != current_check_cp_id;
        });
        PartsTemplateService.updateSelectCpNum(PartsTemplateService.stampRallyCpSelect[PartsTemplateService.currentPartNo].length);
        $('body').data('prev_height',PartsTemplateService.body_height);
        Brandco.unit.closeModal(2);
        PartsTemplateService.searchCp();
    });

    $(document).on('click','a[data-close_modal_id]',function(){
        $('body').data('prev_height',PartsTemplateService.body_height);
        Brandco.unit.closeModal($(this).attr('data-close_modal_id'));
    });

    $(document).on('change', '.jsSelectCp', function(){
        var cp_id = $(this).data('cp_id');
        if($(this).prop('checked')){
            if(!PartsTemplateService.stampRallyValidateSelectTargetCp()){
                $(this).prop('checked',false);
            }else{
                PartsTemplateService.removeValidateMessage();
                PartsTemplateService.stampRallyCpSelect[PartsTemplateService.currentPartNo].push(cp_id);
                PartsTemplateService.updateSelectCpNum(PartsTemplateService.stampRallyCpSelect[PartsTemplateService.currentPartNo].length);
            }
        }else{
            var cp_status = $(this).data('cp_status');
            if(cp_status == PartsTemplateService.cpStatus.close || cp_status == PartsTemplateService.cpStatus.open || cp_status == PartsTemplateService.cpStatus.open){
                $(this).prop('checked',true);
                $('input[name=current_check_cp_id]').val(cp_id);
                PartsTemplateService.body_height = $('body').data('prev_height');
                Brandco.unit.openModal("#modal2");
            }else{
                PartsTemplateService.stampRallyCpSelect[PartsTemplateService.currentPartNo] = jQuery.grep(PartsTemplateService.stampRallyCpSelect[PartsTemplateService.currentPartNo], function(value){
                    return value != cp_id;
                });
                PartsTemplateService.updateSelectCpNum(PartsTemplateService.stampRallyCpSelect[PartsTemplateService.currentPartNo].length);
            }
        }
    });

    $("#pcSlideNum").on('change', function(){
        if(!PartsTemplateService.stampRallyValidateInputTargetCp()){
            PartsTemplateService.scrollToTargetCpNumBox();
        }else{
            PartsTemplateService.removeValidateMessage();
            $(".targetCpNum").text($(this).val());
            PartsTemplateService.stampRallyTargetCpNum[PartsTemplateService.currentPartNo] = parseInt($(this).val());
        }
    });
    /*--------------Stamp Rally Cp二関する------------*/
    PartsTemplateService.bindSortable();
});