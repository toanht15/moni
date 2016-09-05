var UserSettingsFormService = (function() {
    return {
        question_count: 0,
        new_choice_count: 0,
        is_change: 0,
        getQuestionList: function(is_new_question){
            var param = {
                data: {question_type: '1', is_new_question: is_new_question, question_num: UserSettingsFormService.question_count},
                type: 'GET',
                url: 'admin-cp/api_get_question.json',
                success: function(data) {
                    $('#ProfileQuestionnaire').append(data.html);
                    UserSettingsFormService.initQuestionDOMEvent();
                    UserSettingsFormService.question_count += data.data.question_count;
                    if (!is_new_question) {
                        $('.iconError1').each(function() {
                            if ($(this).closest('.adminCustomProfile').length > 0 && $(this).closest('.adminCustomProfile').children('.jsModuleContTile').hasClass('close')) {
                                $(this).closest('.adminCustomProfile').children('.jsModuleContTile').removeClass('close');
                            }
                        });
                    }
                    UserSettingsFormService.initQuestionOrder();
                    //init choice order
                    $( ".customProfileText").each(function() {
                        UserSettingsFormService.initChoiceOrder($(this));
                    });
                    UserSettingsFormService.checkChangeForm();
                    if(is_new_question) {
                        UserSettingsFormService.is_change = 1;
                    }
                }
            };
            Brandco.api.callAjaxWithParam(param);
        },
        initQuestionDOMEvent: function() {
            $('.jsModuleContTile').unbind('click');
            $('.jsModuleContTile').click(function(){
                var targetParent = $($(this).parents('.jsModuleContWrap')[0]);
                var slideDowntarget = $(targetParent.find('.jsModuleContTarget')[0]);
                var target = $(this);
                if($(this).hasClass('close')){
                    slideDowntarget.slideDown(200, function(){
                        target.removeClass('close');
                    });
                }else{
                    slideDowntarget.slideUp(200, function(){
                        target.addClass('close');
                    });
                }
            });

            UserSettingsFormService.initDeleteChoice();
            UserSettingsFormService.initChoiceSortable();
            UserSettingsFormService.initQuestionLabel();

            $('.customItemDelete').unbind('click');
            $('.customItemDelete').click(function(){
                $('#modal2_text').html('この設問を削除しますか？');
                $('#deleteButton').attr('data-delete-item-id', $(this).closest('li').attr('id'));
                Brandco.unit.openModal('#modal2');
            });

            $('.question_type').unbind('change');
            $('.question_type').on('change', function(){
                var type = $('input[name="' + $(this).attr('name') + '"]:checked').val();
                var question_id = $(this).attr('name').split("/")[1];
                if(type == 1) {
                    UserSettingsFormService.showChoiceText(question_id);
                    UserSettingsFormService.hideTextArea(question_id);
                    UserSettingsFormService.showMultiAnswerSetting(question_id);
                    UserSettingsFormService.showRandomChoiceSetting(question_id);
                    UserSettingsFormService.showOtherChoiceSetting(question_id);
                    UserSettingsFormService.hideSupplement(question_id);
                } else if(type == 2) {
                    UserSettingsFormService.hideChoiceText(question_id);
                    UserSettingsFormService.hideTextArea(question_id);
                    UserSettingsFormService.hideMultiAnswerSetting(question_id);
                    UserSettingsFormService.hideRandomChoiceSetting(question_id);
                    UserSettingsFormService.hideOtherChoiceSetting(question_id);
                    UserSettingsFormService.showSupplement(question_id);
                } else if(type == 4) {
                    UserSettingsFormService.hideChoiceText(question_id);
                    UserSettingsFormService.showTextArea(question_id);
                    UserSettingsFormService.hideMultiAnswerSetting(question_id);
                    UserSettingsFormService.showRandomChoiceSetting(question_id);
                    UserSettingsFormService.hideOtherChoiceSetting(question_id);
                    UserSettingsFormService.hideSupplement(question_id);
                }
            });

            $('.addChoice').unbind('click');
            $('.addChoice').click(function(){
                var question_id = $(this).closest('ul').data('question-id'),
                    choice_new_id = 'new_'+(++UserSettingsFormService.new_choice_count),
                    question_type = $(this).closest('.adminCustomProfile').data('question-type');
                if(question_type == 1) {
                    var choice_order = $(this).closest('ul').children().length,
                        html = '<li id="choice_'+question_id+'_'+choice_new_id+'" data-choice-id="'+choice_new_id+'" style="display: none"><label><span class="num">A'+choice_order+'.</span><input type="text" name="choice/'+question_id+'/new_'+UserSettingsFormService.new_choice_count+'" placeholder="選択肢を入力してください"><a href="javascript:void(0)" class="iconBtnDelete">削除する</a></label></li>';
                } else {
                    var html = '<li id="choice_'+question_id+'_'+choice_new_id+'" data-choice-id="'+choice_new_id+'" style="display: none"><label><input type="text" name="choice/'+question_id+'/new_'+UserSettingsFormService.new_choice_count+'" placeholder="選択肢を入力してください"><a href="javascript:void(0)" class="iconBtnDelete">削除する</a></label></li>';
                }
                $(html).insertBefore($(this).closest('li')).slideDown();
                UserSettingsFormService.initDeleteChoice();
                UserSettingsFormService.initChoiceOrder($(this).closest('.customProfileText'));
                UserSettingsFormService.is_change = 1;
            });

            $('.switchInner').unbind('click');
            $('.switchInner').click(function(){
                if ($(this).closest('a').hasClass('switch off')) {
                    $(this).closest('dd').find('input').val("1");
                } else {
                    $(this).closest('dd').find('input').val("0");
                }
                UserSettingsFormService.is_change = 1;
            });
        },
        initDeleteChoice: function() {
            $('.iconBtnDelete').unbind('click');
            $('.iconBtnDelete').click(function(){
                $('#modal2_text').html('この選択肢を削除しますか？');
                $('#deleteButton').attr('data-delete-item-id', $(this).closest('li').attr('id'));
                Brandco.unit.openModal('#modal2');
            });
        },
        initChoiceLabel: function(target) {
            var num = 0;
            target.find('.num').each(function(){
                $(this).html('A'+(++num)+'.');
            });
        },
        initQuestionLabel: function() {
            var num = 0;
            $('.label').each(function(){
                var question_type = $(this).closest('.adminCustomProfile').data('question-type');
                if($(this).next('.customProfileWrap').find('.itemType').length == 0) {
                    if(question_type == 1) {
                        var question_type_name = '選択回答（テキスト）';
                    } else if(question_type == 2) {
                        var question_type_name = '自由回答（文字入力）';
                    } else if(question_type == 4) {
                        var question_type_name = '選択回答（プルダウン）';
                    }
                    $(this).find('label').html('フリー項目'+(++num)+'&nbsp;&nbsp;'+question_type_name);
                } else {
                    $(this).find('label').html('フリー項目'+(++num));
                }
            });
        },
        initChoiceOrder: function(target) {
            var list='';
            target.find('li').not(':last').each (function() {
                list = list +$(this).data('choice-id') + ',';
            });
            $('input[name="choice_order_'+target.data('question-id')+'"]').val(list.slice(0, -1));
        },
        initChoiceSortable: function() {
            $( ".customProfileText").sortable({
                opacity: 0.5,
                items: 'li:not(:last)',
                update: function (event, ui) {
                    UserSettingsFormService.initChoiceOrder($(this));
                    UserSettingsFormService.initChoiceLabel($(this));
                }
            });
        },
        initQuestionOrder: function() {
            var list='';
            $( "#ProfileQuestionnaire").find('li.adminCustomProfile').each (function() {
                list = list + $(this).data('question-id') + ',';
            });
            $('input[name="question_order"]').val(list.slice(0, -1));
        },
        initQuestionSortable: function() {
            $( "#ProfileQuestionnaire").sortable({
                //opacity: 0.5,
                items: 'li.adminCustomProfile',
                update: function (event, ui) {
                    UserSettingsFormService.initQuestionOrder();
                    UserSettingsFormService.initQuestionLabel();
                }
            });
        },
        showChoiceText: function(question_id) {
            $('.customProfileText[data-question-id="' + question_id + '"]').slideDown();
        },
        hideChoiceText: function(question_id) {
            $('.customProfileText[data-question-id="' + question_id + '"]').slideUp();
        },
        showTextArea: function(question_id) {
            $('.customProfilePulldown[data-question-id="' + question_id + '"]').slideDown();
        },
        hideTextArea: function(question_id) {
            $('.customProfilePulldown[data-question-id="' + question_id + '"]').slideUp();
        },
        showMultiAnswerSetting: function(question_id) {
            $('input[name="is_multi_answer/' + question_id + '"]').parent('dd').prev('dt').slideDown();
            $('input[name="is_multi_answer/' + question_id + '"]').parent('dd').slideDown();
        },
        showRandomChoiceSetting: function(question_id) {
            $('input[name="is_random_choice/' + question_id + '"]').parent('dd').prev('dt').slideDown();
            $('input[name="is_random_choice/' + question_id + '"]').parent('dd').slideDown();
        },
        showOtherChoiceSetting: function(question_id) {
            $('input[name="is_use_other/' + question_id + '"]').parent('dd').prev('dt').slideDown();
            $('input[name="is_use_other/' + question_id + '"]').parent('dd').slideDown();
        },
        hideMultiAnswerSetting: function(question_id) {
            $('input[name="is_multi_answer/' + question_id + '"]').parent('dd').prev('dt').slideUp();
            $('input[name="is_multi_answer/' + question_id + '"]').parent('dd').slideUp();
        },
        hideRandomChoiceSetting: function(question_id) {
            $('input[name="is_random_choice/' + question_id + '"]').parent('dd').prev('dt').slideUp();
            $('input[name="is_random_choice/' + question_id + '"]').parent('dd').slideUp();
        },
        hideOtherChoiceSetting: function(question_id) {
            $('input[name="is_use_other/' + question_id + '"]').parent('dd').prev('dt').slideUp();
            $('input[name="is_use_other/' + question_id + '"]').parent('dd').slideUp();
        },
        showSupplement: function(question_id) {
            $('.adminCustomProfile[data-question-id="' + question_id + '"]').find('.supplement1').show();
        },
        hideSupplement: function(question_id) {
            $('.adminCustomProfile[data-question-id="' + question_id + '"]').find('.supplement1').hide();
        },
        showAlertWhenNotSaveChangedForm: function(preview_url) {
            if(UserSettingsFormService.is_change == 0 ) {
                window.open(preview_url, '_blank');
            } else {
                Brandco.unit.openModal('.jsModalPreview');
            }
        },
        checkChangeForm: function() {
            $("form").change(function(){
                UserSettingsFormService.is_change = 1;
            });
        },
        initAuthenticationPageSettingModal: function(){

            var page_content = $("input[name=authentication_page_content]").val();

            if(page_content != undefined){
                CKEDITOR.config.coreStyles_strike = {element:"del",overrides:"strike"};

                CKEDITOR.config.filebrowserWindowWidth = 1000;
                CKEDITOR.config.filebrowserWindowHeight = 745;

                CKEDITOR.config.height =370;

                CKEDITOR.on('instanceCreated', function (e) {
                    e.editor.on('change', function (ev) {
                        $(window).unbind('beforeunload');
                        $(window).on('beforeunload', function() {
                            return Brandco.message.reloadMessage;
                        });
                    });
                });

                CKEDITOR.replace( 'pageContentSetting', {
                    filebrowserUploadUrl: $('input[name=upload_url]').val(),
                    filebrowserBrowseUrl: $('input[name=list_file_url]').val()
                });
            }
        },
        previewAuthenticationPage: function(){
            var csrf_token = $('input[name="csrf_token"]:first').val();
            data = "page_content="+CKEDITOR.instances.pageContentSetting.getData()+"&csrf_token="+csrf_token;
            var url = $('base').attr('href') + 'admin-settings/api_authentication_page_preview.json';
            var param = {
                data: data,
                type: 'POST',
                url: url,
                success: function (json) {
                    if (json.result === "ok") {
                        window.open(json.data.preview_url, '_blank');
                    }
                }
            };
            Brandco.api.callAjaxWithParam(param);
        },
        settingAuthenticationPageContent: function(){
            var csrf_token = $('input[name="csrf_token"]:first').val();
            data = "page_content="+CKEDITOR.instances.pageContentSetting.getData()+"&csrf_token="+csrf_token;
            var url = $('base').attr('href') + 'admin-settings/api_authentication_page_setting.json';
            var param = {
                data: data,
                type: 'POST',
                url: url,
                success: function (json) {
                    if(json.result === "ok") {
                        window.location.href = $("base").attr("href") + 'admin-settings/user_settings_form?mid=updated';
                    }else{
                        window.location.href = $("base").attr("href") + 'admin-settings/user_settings_form?mid=failed';
                    }
                    Brandco.unit.closeModal(4);
                }
            };
            Brandco.api.callAjaxWithParam(param);
        },
        changeAgreementCheckboxStatus: function () {
            if ($('input[name="show_agreement_checkbox"]').length !== 0) {
                if ($('textarea[name="agreement"]').val() == '') {
                    $('input[name="show_agreement_checkbox"]').prop('disabled', true);
                } else {
                    $('input[name="show_agreement_checkbox"]').prop('disabled', false);
                }
            }
        }
    }
})();

$(document).ready(function(){

    UserSettingsFormService.getQuestionList();
    UserSettingsFormService.initQuestionSortable();
    UserSettingsFormService.initAuthenticationPageSettingModal();
    UserSettingsFormService.changeAgreementCheckboxStatus();

    $('#openAuthenticationPageModal').click(function(){
        var page_content = $("input[name=authentication_page_content]").val();
        CKEDITOR.instances.pageContentSetting.setData(page_content);
        $('#pageContentSettingError').hide();
    });

    $('#frmPrivacySubmit').click(function(){
        document.frmPrivacy.submit();
    });

    $('.cancelPrivacy').click(function(){
        location.reload();
    });

    $('#addNewQuestionnaire').click(function(){
        UserSettingsFormService.getQuestionList(true);
    });

    $('#submitProfileQuestion').click(function(){
        document.profileQuestionForm.submit();
    });

    $('#deleteButton').click(function(){
        var ul = $('#'+$(this).attr('data-delete-item-id')).closest('ul');
        $('#'+$(this).attr('data-delete-item-id')).slideUp(300, function() {
            $(this).remove();
            if (ul.hasClass('customProfileText')) {
                UserSettingsFormService.initChoiceOrder(ul);
                UserSettingsFormService.initChoiceLabel(ul);
            } else {
                UserSettingsFormService.initQuestionOrder();
                UserSettingsFormService.initQuestionLabel();
            }
        });
        Brandco.unit.closeModal(2);
        UserSettingsFormService.is_change = 1;
    });

    $('#save_authentication_page').click(function(){
        var page_content = CKEDITOR.instances.pageContentSetting.getData();
        if(page_content != '' && (!page_content.match(/<a.*?href="##LINKYES##".*?>/) || !page_content.match(/<a.*?href="##LINKNO##".*?>/))){
            $('#pageContentSettingError').show();
        }else{
            $(window).unbind('beforeunload');
            UserSettingsFormService.settingAuthenticationPageContent();
        }
    });

    $("#preview_authentication_page").click(function(){
        UserSettingsFormService.previewAuthenticationPage();
    });

    $('.preview_button').click(function(){
        var preview_url = $(this).attr('preview_url');
        UserSettingsFormService.showAlertWhenNotSaveChangedForm(preview_url);
    });

    $('#submitPreviewButton').click(function(){
        var preview_url = $(this).data('url');
        window.open(preview_url, '_blank');
        Brandco.unit.closeModal(3);
    });

    $('textarea[name="agreement"]').bind('input propertychange', function(){
        UserSettingsFormService.changeAgreementCheckboxStatus();
    });

    Brandco.helper.doJsCheckToggle();

    if ($('input[id="privacy[]_privacy_required_restricted"]').is(':checked').length === 0) {
        var targetWrap = $('.jsCheckToggle').parents('.jsCheckToggleWrap')[0];
        $(targetWrap).find('.jsCheckToggleTarget').slideToggle(300);
    }

    UserSettingsFormService.checkChangeForm();

    $('.jsSettingFormSubmit').on('click', function() {
        $(this).closest('form').submit();
    });
});
