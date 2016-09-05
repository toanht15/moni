var CreateQuestionnaireService = (function(){
    return{
        addQuestion: function (type, question_next_id, url, choice_id_max) {
            var data = {type:type, question_next_id:question_next_id};
            var url = url;
            var param = {
                data: data,
                type: 'GET',
                url: url,
                success: function(data) {
                    CreateQuestionnaireService.setQuestionMaxId(question_next_id);
                    CreateQuestionnaireService.setChoiceMaxId(choice_id_max);
                    //追加される設問のidはマイナスで設定されている
                    question_next_id = -question_next_id;
                    $('.jsFirstType').before(data.html);
                    CreateQuestionnaireService.updateQuestionNo();

                    var add_question_target = $('li[data-question_id="' + question_next_id + '"]');
                    add_question_target.slideDown(200,function() {
                        CreateQuestionnaireService.setSortableQuestion();
                        CreateQuestionnaireService.setSortableChoice();
                        CreateQuestionnaireService.addQuestionPreview(type, question_next_id);
                        CreateQuestionnaireService.setCursorMove();
                    });
                }
            };
            Brandco.api.callAjaxWithParam(param, false);
        },
        getMaxId: function (max_id, id) {
            if(id < 0) {
                id = -id;
            }
            if(max_id < id) {
                max_id = id;
            }
            return max_id;
        },
        setQuestionMaxId: function (question_id_max) {
            $('.editEnquete1').attr('data-question_id_max', question_id_max);
        },
        addChoice: function (choice_next_id, url, target, question_id) {
            var question_type = $('*[data-question_id="' + question_id + '"].linkAdd').attr('data-question_type');
            var data = {choice_next_id:choice_next_id, question_id:question_id, question_type:question_type};
            var url = url;
            var param = {
                data: data,
                type: 'GET',
                url: url,
                success: function(data) {
                    if ($('*[data-question_id="' + question_id + '"].linkAdd').parent().parent().find('.inputHelper')[0]) {
                        $('*[data-question_id="' + question_id + '"].linkAdd').parent().parent().find('.inputHelper').before(data.html);
                    } else {
                        $('*[data-question_id="' + question_id + '"].linkAdd').parent().before(data.html);
                    }
                    CreateQuestionnaireService.setChoiceMaxId(choice_next_id);
                    CreateQuestionnaireService.updateChoiceNo(question_id);
                    //追加される設問のidはマイナスで設定されている
                    choice_next_id = -choice_next_id;
                    $('li[data-choice_id="' + question_id + '_' + choice_next_id + '"]').parent('div[name="addChoiceDiv"]').slideDown(200,function() {
                        $('li[data-choice_id="' + question_id + '_' + choice_next_id + '"]').unwrap();
                        CreateQuestionnaireService.setSortableChoice();
                        CreateQuestionnaireService.addChoicePreview(question_id, choice_next_id, question_type);
                        CreateQuestionnaireService.setCursorMove();
                    });
                }
            };
            Brandco.api.callAjaxWithParam(param, false);
        },
        setChoiceMaxId: function (choice_id_max) {
            $('.editEnquete1').attr('data-choice_id_max', choice_id_max);
        },
        deleteQuestion: function (question_id) {
            $('li[data-question_id="' + question_id + '"]').slideUp('slow',function() {
                $('li[data-question_id="' + question_id + '"]').remove();
                CreateQuestionnaireService.updateQuestionNo();
                CreateQuestionnaireService.deleteQuestionPreview(question_id);
                CreateQuestionnaireService.setAlert();
            });
        },
        deleteChoice: function (delete_choice_id, target) {
            // validateエラーの注意書きも一緒に削除対象となる
            var delete_selector = $('li[data-choice_id="' + delete_choice_id + '"],.iconError1[data-choice_id="' + delete_choice_id + '"]');
            var choice_id = delete_choice_id.split("_");
            // 一旦divタグで囲み、そのdivタグ毎削除する
            delete_selector.wrap("<div name='deleteChoiceDiv'></div>");
            delete_selector.parent('div[name="deleteChoiceDiv"]').slideUp(200,function() {
                delete_selector.parent('div[name="deleteChoiceDiv"]').remove();
                CreateQuestionnaireService.updateChoiceNo(choice_id[0]);
                CreateQuestionnaireService.setAlert();
                CreateQuestionnaireService.deleteChoicePreview(delete_choice_id);
            });
        },
        setSortableQuestion: function () {
            $("#moduleEnqueteList").sortable({
                opacity: 0.5,
                items: '.moduleEnqueteDetail1',
                stop: function () {
                    CreateQuestionnaireService.updateQuestionNo();
                    CreateQuestionnaireService.initPreview();
                }
            });
        },
        setSortableChoice: function () {
            $(".moduleEnqueteDetail1").sortable({
                opacity: 0.5,
                items: '*[name=moduleEnqueteChoice]',
                stop: function(event, ui) {
                    var question_id = ui.item.attr('data-choice_id').split("_")[0];
                    CreateQuestionnaireService.updateChoiceNo(question_id);
                    CreateQuestionnaireService.initPreview();
                }
            });
        },
        updateQuestionNo: function () {
            $('li[data-question_id]').each(function(no) {
                var new_question_no = no + 1;
                $(this).children('p.title').find('.num').html('Q' + new_question_no + '.');
            });
        },
        updateChoiceNo: function (question_id) {
            $('li[data-choice_id^="' + question_id + '_"]').each(function(no) {
                var new_choice_no = no + 1;
                var choice_id = $(this).attr('data-choice_id').split('_');
                $('li[data-choice_id="' + question_id + '_' + choice_id[1] + '"]').find('span.num').html('A' + new_choice_no + '.');
            });
        },
        setAlert: function () {
            $(window).unbind('beforeunload');
            Brandco.helper.set_reload_alert(Brandco.message.reloadMessage);
        },
        initPreview: function() {
            var question_type = 0;
            var question_id = 0;
            var choice_id = 0;
            var question_element = '';
            var value;
            var add_choice_flg = 0;

            $('.moduleEnqueteDetail1').each(function(){
                question_type = $(this).attr('data-type').split('_')[1];
                question_id = $(this).find('input:hidden').attr('name').split('_')[1];
                question_element += CreateQuestionnaireService.getQuestionStartElement(question_id, question_type);
                // テキストの選択肢の場合
                if(question_type == '1') {
                    $('input[name^="choice_id_' + question_id + '"]').each(function(){
                        // 選択肢のinputのname構成は、choice_id_設問ID_選択肢IDとなっている
                        choice_id = $(this).attr('name').split('_')[3];
                        value = Brandco.helper.escapeSpecialCharacter($('input[name="choice_id_' + question_id + '_' + choice_id + '"]').val());
                        question_element += CreateQuestionnaireService.getChoiceElement(question_id, choice_id, value, add_choice_flg);
                    });
                    if($('input[name="use_other_choice_' + question_id + '"]').val() == 1 ) {
                        choice_id = 0;
                        value = 'その他';
                        question_element += CreateQuestionnaireService.getChoiceElement(question_id, choice_id, value, add_choice_flg);
                    }
                // 画像選択肢の場合
                } else if(question_type == '3') {
                    $('input[name^="choice_id_' + question_id + '"]').each(function(){
                        // 選択肢のinputのname構成は、choice_id_設問ID_選択肢IDとなっている
                        choice_id = $(this).attr('name').split('_')[3];
                        value = Brandco.helper.escapeSpecialCharacter($('input[name="choice_id_' + question_id + '_' + choice_id + '"]').val());
                        question_element += CreateQuestionnaireService.getImageChoiceElement(question_id, choice_id, value, add_choice_flg);
                    });
                } else if (question_type == '4') {
                    if ($('textarea[name="pulldown_choice_'+question_id+'"]')[0]) {
                        question_element += CreateQuestionnaireService.getPullDownChoiceElementByTextArea(question_id);
                    } else {
                        question_element += CreateQuestionnaireService.getPullDownChoiceElementByInput(question_id);
                    }
                }
                question_element += CreateQuestionnaireService.getQuestionEndElement(question_type);
                CreateQuestionnaireService.initQuestionInputEvent(question_id, question_type);
            });
            $('#questionnairePreview').html(question_element);
        },
        initQuestionInputEvent: function(question_id, question_type) {
            $(document).on('input','[name="question_id_' + question_id + '"]',function(){
                var question_value = Brandco.helper.escapeSpecialCharacter($(this).prev('.num').html() + $(this).val());
                $('dt#question_' + question_id + '_preview').html(question_value);
            });
            $('input[name^="choice_id_' + question_id + '"]').each(function(){
                // 選択肢のinputのname構成は、choice_id_設問ID_選択肢IDとなっている
                var choice_id = $(this).attr('name').split('_')[3];
                CreateQuestionnaireService.initChoiceInputEvent(question_id, choice_id, question_type);
            });
            $('textarea[name^="pulldown_choice_' + question_id + '"]').on('input', function() {
                CreateQuestionnaireService.initPullDownChoiceElementByTextArea(question_id);
            });
        },
        initChoiceInputEvent: function(question_id, choice_id, question_type) {
            $(document).on('input','[name="choice_id_' + question_id + '_' + choice_id + '"]',function(){
                var temp = Brandco.helper.escapeSpecialCharacter($(this).val());
                if(question_type == 1) {
                    $('label[for="choice_' + question_id + '_' + choice_id + '_preview"]').html(temp);
                } else if (question_type == 4) {
                    $('#choice_' + question_id + '_' + choice_id + '_preview').html(temp.substring(0, 12));
                } else {
                    if(!temp || temp == ' ') {
                        var cut_text = '　';
                        temp = '　';
                    } else {
                        var cut_text = Brandco.helper.cutLongText(temp, 120);
                    }
                    $('label[for="choice_' + question_id + '_' + choice_id + '_preview"]').find('figcaption').html(cut_text);
                    $('label[for="choice_' + question_id + '_' + choice_id + '_preview"]').find('img').attr('alt',temp);
                }
            });
        },
        initPullDownChoiceElementByTextArea: function(question_id) {
            $('#select_'+question_id).html(CreateQuestionnaireService.getPullDownChoiceElementByTextArea(question_id));
        },
        getQuestionStartElement: function(question_id, question_type) {
            var question_target = $('input[name="question_id_' + question_id + '"]');
            var question_value = Brandco.helper.escapeSpecialCharacter(question_target.prev('.num').html() + question_target.val());

            if($('input[name="requirement_' + question_id + '"]').val() == 1 ){
                var question_start_element = '<dt id="question_' + question_id + '_preview" class="require1">' + question_value + '</dt>';
            } else {
                var question_start_element = '<dt id="question_' + question_id + '_preview">' + question_value + '</dt>';
            }
            if(question_type == '1') {
                question_start_element += '<dd id="question_' + question_id + '_list"><ul class="moduleItemList">';
            } else if(question_type == '3') {
                question_start_element += '<dd id="question_' + question_id + '_list"><ul class="moduleItemImg">';
            } else if (question_type == '4') {
                question_start_element += '<dd id="question_' + question_id + '_list"><select id="select_' + question_id + '">';
            } else {
                question_start_element += '<dd id="question_' + question_id + '_list"><textarea>';
            }
            return question_start_element;
        },
        getQuestionEndElement: function(question_type) {
            var question_end_element;
            if(question_type == '1' || question_type == '3') {
                question_end_element = '</ul></dd>';
            } else if (question_type == 4) {
                question_end_element = '</select></dd>'
            } else {
                question_end_element = '</textarea></dd>';
            }
            return question_end_element;
        },
        getChoiceElement: function(question_id, choice_id, value, add_choice_flg) {
            var choice_element;
            var display_style = '';
            if(add_choice_flg == 1) {
                display_style = 'style="display:none"';
            }
            if(choice_id == 0) {
                value = 'その他';
            }
            var target_id = 'choice_' + question_id + '_' + choice_id;
            if($('input[name="multi_answer_' + question_id + '"]').val() == 0 ) {
                choice_element = '<li ' + display_style + '><input type="radio" class="customRadio" id="' + target_id +
                '_preview" name="checkbox_' + question_id + '"><label for="' + target_id + '_preview">' + value + '</label>';
                if(choice_id == 0) {
                    choice_element += '<textarea name="" id="" cols="30" rows="10"></textarea>';
                }
                choice_element += '</li>';
            } else {
                choice_element = '<li ' + display_style + '><input type="checkbox" class="customCheck" id="' + target_id +
                '_preview" name="customCheck_' + question_id + '"><label for="' + target_id + '_preview">' + value + '</label>';
                if(choice_id == 0) {
                    choice_element += '<textarea name="" id="" cols="30" rows="10"></textarea>';
                }
                choice_element += '</li>';
            }
            return choice_element;
        },
        getImageChoiceElement: function(question_id, choice_id, value, add_choice_flg) {
            var choice_element;
            var display_style = '';
            var target_id = 'choice_' + question_id + '_' + choice_id;
            if(value) {
                var alt_value = value;
                value = Brandco.helper.cutLongText(value, 120);
            } else {
                value = '　';
            }
            if(add_choice_flg == 1) {
                display_style = 'style="display:none"';
            }

            if($('label[for="choice_' + question_id + '_' + choice_id + '_preview"]').find('img').attr('src')) {
                var image_url = $('label[for="choice_' + question_id + '_' + choice_id + '_preview"]').find('img').attr('src');
            } else if($('input[name="choice_image_url_' + question_id + '_' + choice_id + '"]:hidden').val()) {
                var image_url = $('input[name="choice_image_url_' + question_id + '_' + choice_id + '"]:hidden').val();
            } else {
                var image_url = '"' + $('input[name="static_url"]').val() + '/img/icon/iconNoImage1.png" width="100" height="100"';
            }
            if($('input[name="multi_answer_' + question_id + '"]').val() == 0 ) {
                choice_element = '<li ' + display_style + '><input type="radio" class="customRadio" id="' + target_id +
                    '_preview" name="checkbox_' + question_id + '"><label for="' + target_id + '_preview">' +
                    '<figure><figcaption class="title">' + value + '</figcaption><span class="img"><img src=' + image_url + ' alt="' + alt_value + '"></span></figure></label>' +
                    '<a href="javascript:void(0)" class="previwe jsOpenModal" onclick="return false";>拡大表示する</a></li>';
            } else {
                choice_element = '<li ' + display_style + '><input type="checkbox" class="customCheck" id="' + target_id +
                    '_preview" name="customCheck_' + question_id + '"><label for="' + target_id + '_preview">' +
                    '<figure><figcaption class="title">' + value + '</figcaption><span class="img"><img src=' + image_url + ' alt="' + alt_value + '"></span></figure></label>' +
                    '<a href="javascript:void(0)" class="previwe jsOpenModal" onclick="return false";>拡大表示する</a></li>';
            }
            return choice_element;
        },
        getPullDownChoiceElementByTextArea: function(question_next_id) {
            var choices = $('textarea[name="pulldown_choice_'+question_next_id+'"]').val().split("\n");
            var choices_element = "<option>選択してください</option>";
            $.each(choices, function() {
                if ($.trim(this) != '') {
                    choices_element += '<option>'+this+'</option>';
                }
            });
            return choices_element;
        },
        getPullDownChoiceElementByInput: function (question_next_id, choice_next_id) {
            var choices_element = "<option>選択してください</option>";
            $('input[name^="choice_id_'+ question_next_id +'"]').each(function() {
                if ($.trim($(this).val()) != '') {
                    var choice_id = $(this).attr('name').split('choice_id_'+ question_next_id + '_')[1];
                    choices_element += CreateQuestionnaireService.getAPullDownChoiceElementByInput(question_next_id, choice_id, $(this).val());
                }
            });
            return choices_element;
        },
        getAPullDownChoiceElementByInput: function (question_id, choice_next_id, val) {
            return '<option id="'+ 'choice_' + question_id + '_' + choice_next_id+'_preview' +'">'+ val.substring(0, 12) +'</option>';
        },
        addQuestionPreview: function(type, question_next_id) {
            var question_element;
            var add_choice_flg = 0;

            question_element = '<div name="addQuestionPreviewDiv" style="display:none">';
            question_element += CreateQuestionnaireService.getQuestionStartElement(question_next_id, type);
            if(type == 1) {
                $('input[name^="choice_id_' + question_next_id + '"]').each(function(){
                    // 選択肢のinputのname構成は、choice_id_設問ID_選択肢IDとなっている
                    var choice_id = $(this).attr('name').split('_')[3];
                    question_element += CreateQuestionnaireService.getChoiceElement(question_next_id, choice_id, '', add_choice_flg);
                });
            } else if(type == 3) {
                $('input[name^="choice_id_' + question_next_id + '"]').each(function(){
                    // 選択肢のinputのname構成は、choice_id_設問ID_選択肢IDとなっている
                    var choice_id = $(this).attr('name').split('_')[3];
                    question_element += CreateQuestionnaireService.getImageChoiceElement(question_next_id, choice_id, '', add_choice_flg);
                });
            } else if(type == 4) {
                if ($('textarea[name="pulldown_choice_'+question_next_id+'"]')[0]) {

                    question_element += CreateQuestionnaireService.getPullDownChoiceElementByTextArea(question_next_id);
                } else {
                    question_element += CreateQuestionnaireService.getPullDownChoiceElementByInput(question_next_id);
                }
            }
            question_element += CreateQuestionnaireService.getQuestionEndElement(type);
            question_element += '</div>';
            $('#questionnairePreview').append(question_element);
            $('dt#question_' + question_next_id + '_preview').parents('div[name="addQuestionPreviewDiv"]').slideDown(200,function() {
                $('dt#question_' + question_next_id + '_preview').unwrap();
                CreateQuestionnaireService.initQuestionInputEvent(question_next_id, type);
            });
            CreateQuestionnaireService.resetClickClass();
        },
        addChoicePreview: function(question_id, choice_next_id, question_type) {
            var choice_element;
            var add_choice_flg = 1;

            if(question_type == 1) {
                choice_element = CreateQuestionnaireService.getChoiceElement(question_id, choice_next_id, '', add_choice_flg);
            } else if(question_type == 3) {
                choice_element = CreateQuestionnaireService.getImageChoiceElement(question_id, choice_next_id, '', add_choice_flg);
            } else if (question_type == 4) {
                choice_element = CreateQuestionnaireService.getAPullDownChoiceElementByInput(question_id, choice_next_id, "");
            }
            if($('#choice_' + question_id + '_0_preview').size() > 0) {
                $('#choice_' + question_id + '_0_preview').parent().before(choice_element);
            } else {
                if (question_type == 4) {
                    $('dd#question_' + question_id + '_list').find('select').append(choice_element);
                } else {
                    $('dd#question_' + question_id + '_list').find('ul').append(choice_element);
                }
            }
            if(question_type == 1) {
                $('#choice_' + question_id + '_' + choice_next_id + '_preview').parent().slideDown(200,function() {
                    CreateQuestionnaireService.initChoiceInputEvent(question_id, choice_next_id, question_type);
                });
            } else if(question_type == 3) {
                $('#choice_' + question_id + '_' + choice_next_id + '_preview').parent().fadeIn(200,function() {
                    CreateQuestionnaireService.initChoiceInputEvent(question_id, choice_next_id, question_type);
                });
            } else if (question_type == 4) {
                CreateQuestionnaireService.initChoiceInputEvent(question_id, choice_next_id, question_type);
            }
            CreateQuestionnaireService.resetClickClass();
        },
        deleteQuestionPreview: function(question_id) {
            $('dt#question_' + question_id + '_preview').wrap('<div name="deleteQuestionPreview"></div>');
            $('dd#question_' + question_id + '_list').appendTo('div[name="deleteQuestionPreview"]');
            $('div[name="deleteQuestionPreview"]').slideUp(200,function() {
                $('div[name="deleteQuestionPreview"]').remove();
                $('#questionnairePreview').find('span[name^="num_"]').each(function(){
                    var update_question_id = $(this).attr('name').split('_')[1];
                    var update_value = $('input[name="question_id_' + update_question_id + '"]').prev('span').html().replace('.','');
                    $(this).html(update_value);
                });
            });
            CreateQuestionnaireService.resetClickClass();
        },
        deleteChoicePreview: function(delete_choice_id) {
            if ($('#choice_' + delete_choice_id + '_preview')[0].tagName == "OPTION") {
                $('#choice_' + delete_choice_id + '_preview').remove();

            } else if($('#choice_' + delete_choice_id + '_preview').closest('ul').hasClass('moduleItemImg')) {
                $('#choice_' + delete_choice_id + '_preview').parent().fadeOut(200,function() {
                    $('#choice_' + delete_choice_id + '_preview').parent().remove();
                });
            } else {
                $('#choice_' + delete_choice_id + '_preview').parent().slideUp(200,function() {
                    $('#choice_' + delete_choice_id + '_preview').parent().remove();
                });
            }
            CreateQuestionnaireService.resetClickClass();
        },
        setCursorMove: function() {
            $('.moduleEnqueteDetail1').css('cursor','move');
            $('li[name="moduleEnqueteChoice"]').find('.num').css('cursor','move');
        },
        resetClickClass: function() {
            $('.not_click').each(function() {
                $(this).removeClass('not_click');
            });
        },
        switchModuelEnquete: function(target) {
            var target_detail = target.next('.jsModuleContTarget');
            if(!target.hasClass('jsHasError')) {
                if(target.hasClass('close')) {
                    target_detail.slideDown(200, function(){
                        target.removeClass('close');
                    });
                } else {
                    target_detail.slideUp(200, function(){
                        target.addClass('close');
                    });
                }
            }
            CreateQuestionnaireService.resetClickClass();
        },
        updateImageFile: function(target) {
            var choice_id = target.closest('li').attr('data-choice_id');
            var target_preview = $('label[for="choice_' + choice_id + '_preview"]');
            var input = target[0];
            if (input.files && input.files[0]) {
                if(window.FileReader) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        var image = new Image();
                        image.src = e.target.result;
                        image.onload = function() {
                            if(image.width < 100 || image.height < 100) {
                                alert('画像のサイズは縦横100px以上にしてください。');
                                if(!$('span.iconError1[data-choice_id="' + choice_id + '"]').length) {
                                    $('li[data-choice_id="' + choice_id + '"]').prepend('<span class="iconError1">画像のサイズは縦横100px以上にしてください。</span>');
                                }
                                target_preview.find('img').attr('height','100px');
                                target_preview.find('img').attr('width','100px');
                                target_preview.find('img').attr('src', $('input[name="static_url"]').val() + '/img/icon/iconNoImage1.png');
                            } else {
                                target_preview.find('img').removeAttr('height');
                                target_preview.find('img').removeAttr('width');
                                target_preview.find('img').attr('src', e.target.result);
                            }
                        };
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            } else {
                target_preview.find('img').attr('height','100px');
                target_preview.find('img').attr('width','100px');
                target_preview.find('img').attr('src', $('input[name="static_url"]').val() + '/img/icon/iconNoImage1.png');
            }
        },
        openImageModal: function(target) {
            var question_id = target.prev('label').attr('for').split('_')[1];
            var choice_id = target.prev('label').attr('for').split('_')[2];
            var image_src = target.prev('label').find('img').attr('src');
            var image_title = $('input[name="choice_id_' + question_id + '_' + choice_id + '"]').val();
            var target_image = $('.modalImgPreview').children('img');
            var image = new Image();
            image.src = image_src;
            image.onload = function() {
                $('.modalImgPreview').children('figcaption.title').html(image_title);
                target_image.attr('src', image_src);
                target_image.attr('alt', image_title);
                if(image.width < 520) {
                    if(image.height > image.width) {
                        if(image.width*520/image.width > 450) {
                            target_image.attr('height', 450);
                        }
                    } else {
                        target_image.attr('width', 520);
                    }
                }
                Brandco.unit.openModal("#modal5");
            };
        },
        setIePreviewNotification: function() {
            if(!window.FileReader) {
                var notification = '<p class="iconError1">IE9をお使いの場合、設定を保存後に画像がプレビューに表示されます。</p>';
                $('.jsModulePreviewArea').before(notification);
            }
        }
    };
})();

$(document).ready(function() {
    CreateQuestionnaireService.initPreview();
    CreateQuestionnaireService.setIePreviewNotification();

    $(document).on('focus','.jsModuleContTitle',function(){
        if(!$(this).hasClass('not_click')) {
            $(this).addClass('not_click');
            CreateQuestionnaireService.switchModuelEnquete($(this));
        } else {
            return false;
        }
    });

    $(document).on('click','.jsModuleContTitle',function(){
        if(!$(this).hasClass('not_click')) {
            $(this).addClass('not_click');
            CreateQuestionnaireService.switchModuelEnquete($(this));
        } else {
            return false;
        }
    });

    $(document).on('click','.jsOpenModal',function() {
        if(!$(this).hasClass('not_click')) {
            $(this).addClass('not_click');
            CreateQuestionnaireService.openImageModal($(this));
        } else {
            return false;
        }
    });

    $(document).on('click','a[data-close_modal_type]',function(){
        Brandco.unit.closeModal($(this).attr('data-close_modal_type'));
        CreateQuestionnaireService.resetClickClass();
        if($(this).attr('data-close_modal_type') == 5) {
            setTimeout(function() {
                $('#modal5').find('img').removeAttr('width height');
            }, 300);
        }
    });

    // ステータスが確定状態の時は下記のイベントは動作不可
    if(!$('.moduleEnqueteList').attr('data-disable')) {
        var question_id_max = 0;
        var question_next_id = 0;
        var choice_id_max = 0;
        var choice_next_id = 0;
        var question_id = 0;
        var choice_id = 0;

        $('li[data-question_id]').each(function() {
            question_id_max = CreateQuestionnaireService.getMaxId(question_id_max, $(this).data('question_id'));
        });

        $('li[data-choice_id]').each(function() {
            choice_id = $(this).data('choice_id').split("_");
            if($.isNumeric(choice_id[1])) {
                choice_id_max = CreateQuestionnaireService.getMaxId(choice_id_max, choice_id[1]);
            }
        });

        // validateエラー発生時は対象のタブを開いた状態にして、保存状態をエラーにする
        $('.iconError1').parents('.moduleEnqueteDetail1').removeClass('close').addClass('open');
        $('.iconError1').parents('.moduleEnqueteDetail1').attr('data-save_condition','err');

        if(question_id_max == 0) {
            question_id_max = 1;
        }
        if(choice_id_max == 0) {
            choice_id_max = 2;
        }
        CreateQuestionnaireService.setQuestionMaxId(question_id_max);
        CreateQuestionnaireService.setChoiceMaxId(choice_id_max);
        CreateQuestionnaireService.setSortableQuestion();
        CreateQuestionnaireService.setSortableChoice();
        CreateQuestionnaireService.setCursorMove();

        $(document).on('click','a[data-add_question_url]',function() {
            if(!$(this).hasClass('not_click')) {
                $(this).addClass('not_click'); // not_clickは多重クリック防止のために付与
                question_next_id = parseInt($('.editEnquete1').attr('data-question_id_max')) + 1;
                CreateQuestionnaireService.addQuestion($(this).data('type'), question_next_id, $(this).data('add_question_url'), choice_id_max);
                CreateQuestionnaireService.setAlert();
            } else {
                return false;
            }
        });

        $(document).on('click','a[data-add_choice_url]',function() {
            if(!$(this).hasClass('not_click')) {
                $(this).addClass('not_click');
                question_id = $(this).attr('data-question_id');
                choice_next_id = parseInt($('.editEnquete1').attr('data-choice_id_max')) + 1;
                CreateQuestionnaireService.addChoice(choice_next_id, $(this).data('add_choice_url'), $(this).parents('.moduleEnqueteDetail1'), question_id);
                CreateQuestionnaireService.setAlert();
            } else {
                return false;
            }
        });

        $(document).on('click','a[data-delete_type]',function() {
            if(!$(this).hasClass('not_click')) {
                $(this).addClass('not_click');
                if($(this).attr('data-delete_type') == "Question") {
                    $('#deleteQuestionPush').attr('data-delete_question', $(this).parents('.moduleEnqueteDetail1').attr('data-question_id'));
                    Brandco.unit.openModal("#modal3");
                } else {
                    $('#deleteChoicePush').attr('data-delete_choice', $(this).parents('li[name="moduleEnqueteChoice"]').attr('data-choice_id'));
                    Brandco.unit.openModal("#modal4");
                }
            } else {
                return false;
            }
        });

        $(document).on('click',"#deleteQuestionPush",function(){
            Brandco.unit.closeModalFlame(this);
            CreateQuestionnaireService.deleteQuestion($(this).attr('data-delete_question'));
        });

        $(document).on('click','#deleteChoicePush',function() {
            var target = $('li[data-choice_id="' + $(this).attr('data-delete_choice') + '"]').parents('.moduleEnqueteDetail1');
            Brandco.unit.closeModalFlame(this);
            CreateQuestionnaireService.deleteChoice($(this).attr('data-delete_choice'), target);
        });

        $('input').each(function(){
            $(document).on('change','input',function(){
                CreateQuestionnaireService.setAlert();
            });
        });

        $(document).on('change','input[name^="choice_image_file"]',function(){
            CreateQuestionnaireService.updateImageFile($(this));
        });

        // 必須回答フラグの値を変更する処理
        $(document).on('click','a[data-switch_question_id^="requirement_"]',function(){
            var question_id = $(this).attr('data-switch_question_id').split("_")[1];
            if($('input[name=requirement_' + question_id + ']').val() == '1') {
                $('input[name=requirement_' + question_id + ']').val('0');
            } else {
                $('input[name=requirement_' + question_id + ']').val('1');
            }
            CreateQuestionnaireService.initPreview();
            CreateQuestionnaireService.setAlert();
        });

        // 複数回答フラグの値を変更する処理
        $(document).on('click','a[data-switch_question_id^="multianswer_"]',function(){
            var question_id = $(this).attr('data-switch_question_id').split("_")[1];
            if($('input[name=multi_answer_' + question_id + ']').val() == '1') {
                $('input[name=multi_answer_' + question_id + ']').val('0');
            } else {
                $('input[name=multi_answer_' + question_id + ']').val('1');
            }
            CreateQuestionnaireService.initPreview();
            CreateQuestionnaireService.setAlert();
        });

        // ランダム表示フラグの値を変更する処理
        $(document).on('click','a[data-switch_question_id^="random_"]',function(){
            var question_id = $(this).attr('data-switch_question_id').split("_")[1];
            if($('input[name=random_order_' + question_id + ']').val() == '1') {
                $('input[name=random_order_' + question_id + ']').val('0');
            } else {
                $('input[name=random_order_' + question_id + ']').val('1');
            }
            CreateQuestionnaireService.initPreview();
            CreateQuestionnaireService.setAlert();
        });

        // その他選択肢フラグの値を変更する処理
        $(document).on('click','a[data-switch_question_id^="otherchoice_"]',function(){
            var question_id = $(this).attr('data-switch_question_id').split("_")[1];
            if($('input[name=use_other_choice_' + question_id + ']').val() == '1') {
                $('input[name=use_other_choice_' + question_id + ']').val('0');
                //その他の選択肢のプレビュー表示では、選択肢ID = 設問id_0となる
                var delete_choice_id = question_id + '_0';
                CreateQuestionnaireService.deleteChoicePreview(delete_choice_id);
            } else {
                $('input[name=use_other_choice_' + question_id + ']').val('1');
                CreateQuestionnaireService.addChoicePreview(question_id, 0, 1);
            }
            CreateQuestionnaireService.setAlert();
        });
    }
});
