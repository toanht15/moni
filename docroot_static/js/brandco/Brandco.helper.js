Brandco.helper = (function(){
    return {
        showConfirm: function(modal_name, data){
            $("#delete_area").attr({"data-entry" : data});
           Brandco.unit.openModal(modal_name);
        },
        deleteEntry: function(a, url, callback_url){
            var data = a.getAttribute('data-entry');
            Brandco.api.callAjax(data, url,null, callback_url);
        },
        getPriority: function (entryId){
            var sw = document.getElementById('switch'+entryId);
            return sw.getAttribute('data-priority');
        },

        selectActionChange: function(select, public_url, public_callback, type){
            var data = select.getAttribute('data-entry'),
                csrf_token = document.getElementsByName("csrf_token")[0].value,
                id,li;
            data = data + '&csrf_token=' + csrf_token;
            if(type == 'staticHTML'){
                id = select.getAttribute('data-id');
                li = document.getElementById('static_type'+id);
            }
            switch(select.value){
                case 'default':
                    break;
                case 'public':
                    if(type == 'staticHTML'){
                        var A= select.options, L= A.length;
                        while(L){
                            L --;
                            if (A[L].value == 'public'){
                                if(A[L].text == '公開'){
                                    li.className = 'contPublishing';
                                    A[L].text = '下書き';
                                }else{
                                    li.className = '';
                                    A[L].text = '公開';
                                }
                                select.selectedIndex = 0;
                                break;
                            }
                        }
                    }
                    Brandco.api.callAjax(data, public_url, null, public_callback);
                    break;
                case 'delete':
                    Brandco.helper.showConfirm('.modal2', data);
                    break;
                default:
                    document.location = select.value;
                    break;
            }
        },
        disconnect_soclial_app: function (data, url, callbackUrl){
           var param = {
                data: data,
                url: url,
                success: function(data){
                    document.location = callbackUrl
                }
            };
            Brandco.api.callAjaxWithParam(param);
        },
        changeHiddenFlg: function (data, url){
            var param = {
                data: data,
                url: url
            };
            Brandco.api.callAjaxWithParam(param);
        },
        loadPanel: function (data, url, callback){
            var param = {
                data: data,
                url: url,
                success: function(data){
                    if (data.result == 'ok'){
                        window.location = callback;
                    } else {
                        if (data.errors.message) {
                            $('#modal4 #ajaxMessage #message').html(data.errors.message);
                        }
                        $.unblockUI();
                        Brandco.unit.openModal('#modal4');
                    }
                }
            };
            Brandco.api.callAjaxWithParam(param, true, false);
        },
        cmp_module_preview: function(){
            $( ".attention1" ).each(function() {
                if($(this).html()){
                    var targetParent = $(this).parents('.moduleCont1');
                    if(targetParent.length > 0){
                        targetParent.find('.moduleSettingWrap').slideDown(200, function(){
                            targetParent.removeClass('close');
                        });
                    }
                };
            });

            $('#text_title').on('input', function(){
                var temp = Brandco.helper.escapeSpecialCharacter($(this).val());
                $("#titlePreview").html(temp);
            });

            $('#text_area').on('input', function(){
                var text_content = $(this).val();
                var param = {
                    data: {
                        text_content: text_content
                    },
                    url: 'admin-cp/parse_markdown',
                    success: function(response) {
                        if (response.result == 'ok') {
                            $("#textPreview").html(response.data.html_content);
                        }
                    }
                };
                Brandco.api.callAjaxWithParam(param, false,  false);
            });

            $('#image_file').on('change', function(){
                var input = $(this)[0];
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#imagePreview').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                    $('#imagePreview').parent().show();
                }else{
                    $('#imagePreview').parent().hide();
                }
            });

            $('#image_url').on('change', function(){
                if($(this).val() == ''){
                    $('#imagePreview').parent().hide();
                }else{
                    $('#imagePreview').attr('src', $(this).val());
                    $('#imagePreview').parent().show();
                }
            });

            $('#btn_text').on('input', function(){
                var temp = Brandco.helper.escapeSpecialCharacter($(this).val());
                $("#btnPreview").attr('style', 'display:yes');
                $("#btnPreview").html(temp);
            });
        },
        init_module_preview: function(){
            if ($('#text_title').val()) {
                var temp = Brandco.helper.escapeSpecialCharacter($('#text_title').val());
                $("#titlePreview").html(temp);
            }

            if ($('#text_area').val()) {
                var text_content = $('#text_area').val();
                var param = {
                    data: {
                        text_content: text_content
                    },
                    url: 'admin-cp/parse_markdown',
                    success: function(response) {
                        if (response.result == 'ok') {
                            $("#textPreview").html(response.data.html_content);
                        }
                    }
                };
                Brandco.api.callAjaxWithParam(param, false,  false);
            }

            if ($('#btn_text').val()) {
                temp = Brandco.helper.escapeSpecialCharacter($('#btn_text').val());
                $("#btnPreview").attr('style', 'display:yes');
                $("#btnPreview").html(temp);
            }

            if($('#image_url').val() != ''){
                $('#imagePreview').attr('src', $('#image_url').val());
            } else {
                $('#imagePreview').parent().hide();
            }
        },
        edit_cp: function(button){
            //call api
            var csrf_token = document.getElementsByName("csrf_token")[0].value,
                data = button.data('action'),
                url = button.data('url'),
                param = {
                    data: data+'&csrf_token='+csrf_token,
                    url: url,
                    success: function(data){
                        if(data.result == 'ok'){
                            Brandco.helper.reloadWithMIDMessage('action-can-edit');
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(param);
        },

        updateReservationStatus: function (button) {
            var csrf_token = document.getElementsByName("csrf_token")[0].value,
                data = button.data('action'),
                url = button.data('url'),
                mid = button.data('mid'),
                param = {
                    data: data + '&csrf_token=' + csrf_token,
                    url: url,
                    success: function (data) {
                        if (data.result == 'ok') {
                            Brandco.helper.reloadWithMIDMessage(mid);
                        } else {
                            if (data.errors.message) {
                                Brandco.helper.reloadWithMIDMessage(data.errors.message);
                            }
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(param);
        },

        set_reload_alert: function(message){
            var editButton = document.getElementById('editButton');
            if(!editButton){
                $(window).on('beforeunload', function() {
                    return message;
                });
            }
        },
        initConfirmBox: function() {
            $(":input").each(function(){
                $(this).change(function(){
                    $(window).unbind('beforeunload');
                    $(window).on('beforeunload', function() {
                        return Brandco.message.reloadMessage;
                    });
                });
            });
        },
        brandcoBlockUI: function() {
            $.blockUI.defaults.css = {
                padding: 0,
                margin: 0,
                top:  ($(window).height() - 30) /2 + 'px',
                left: ($(window).width() - 30) /2 + 'px',
                width: '30px',
                textAlign: 'center',
                border:0,
                cursor: 'wait'
            };
            $.blockUI({
                message: $('#ajaxReloadBox')
            });
        },
        brandcoUnblockUI: function() {
            var clone = $('#ajaxReloadBox').clone();

            $.unblockUI({onUnblock: function() {
                $('#ajaxReloadBox').remove();
                clone.hide();
                $('body').append(clone);
            }});
        },
        showLoading: function (target) {
            var loading = $("#loading");
            loading.insertAfter(target);
            loading.show();
        },
        hideLoading: function () {
            var loading = $("#loading");
            loading.hide();
        },
        escapeSpecialCharacter: function (character) {
            var escape_character = character.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\n/g, "<br>");
            return escape_character;
        },
        autoLink: function (character) {
            pattern = /(^|[\s\n]|<br>)((?:https?|ftp):\/\/[\-A-Z0-9+\u0026\u2019@#\/%?=()~_|!:,.;]*[\-A-Z0-9+\u0026@#\/%=~()_|])/gi;
            return character.replace(pattern, "$1<a href='$2'>$2</a>");
        },
        facebookParsing: function(sns_action) {
            if (sns_action === true) {
                FB.XFBML.parse();
            }
        },
        getWidth: function(text) {
            var body = $(document.body);
            var dummyWrapper = $('<div>');
            var dummy = $('<span>');
            var width;
            var dummy_txt = $('<span>');
            dummy_txt.text(text);

            dummyWrapper.css({
                position: 'absolute',
                top: 0, left: 0,
                width: 9999,
                'z-index': -1
            });
            dummy.text(text);
            dummy.css({
                color: 'transparent',
                font: dummy_txt.css('font'),
                'letter-spacing': dummy_txt.css('letter-spacing')
            });

            body.append(dummyWrapper.append(dummy));
            width = dummy.width();

            setTimeout(function() {
                dummy.remove();
            }, 0);
            return width;
        },
        cutLongText: function (text, width, adding_txt) {
            var adding_text = adding_txt ? adding_txt : '...';
            var temp_width = Brandco.helper.getWidth(adding_text);
            var split_text = text.split('');
            var split_width = 0;
            var unit_text = '';
            var temp_text = '';
            $.each(split_text, function (i, value) {
                unit_text += value;
                if(value == ' ') {
                    split_width += 5;
                } else {
                    split_width += Brandco.helper.getWidth(value);
                }

                if (split_width <= width - temp_width) {
                    temp_text = unit_text;
                }
            });
            if (split_width <= width) {
                return unit_text;
            } else {
                return temp_text + adding_text;
            }
        },
        doJsCheckToggle: function () {
            $('.jsCheckToggle').on('change', function(){
                var targetWrap = $(this).parents('.jsCheckToggleWrap')[0];
                $(targetWrap).find('.jsCheckToggleTarget').slideToggle(300);
            });
        },
        conversion_comma3: function(string) {
            var str = new String(string);
            var comma_string = "";
            var count = 0;
            for (var i = str.length-1; i >= 0; i--){
                comma_string = str.charAt(i) + comma_string;
                count++;
                if (((count % 3) == 0) && (i != 0)) {
                    comma_string = "," + comma_string;
                }
            }
            return comma_string;
        },
        reloadWithMIDMessage: function (mid) {
            location.href = location.protocol+'//'+location.hostname+location.pathname+'?mid='+mid;
        },
        loadZeroClipboard: function() {
            // ZeroClipboard copy url to clipboard
            $('.jsCopyToClipboardBtn').each(function() {
                var zero_clipboard = new ZeroClipboard(this);

                zero_clipboard.on('error', function(event) {
                    ZeroClipboard.destroy();
                });
            });
        }
    };
})();