$(document).ready(function(){

    $('#submit').click(function(){
        $(window).unbind('beforeunload');
        $('#save_type').val(1);
        document.actionForm.submit();
    });

    $('#editButton').click(function(){
        Brandco.helper.edit_cp($(this));
    });

    $('#submitDraft').click(function(){
        $(window).unbind('beforeunload');
        $('#save_type').val(0);
        document.actionForm.submit();
    });

    $('.labelTitle').change(function(){
        $('.actionImage').attr('disabled', 'disabled');
        $(this).parents('li').find('.actionImage').removeAttr('disabled');

        var actionImage = $(this).parent('li').find('.actionImage');

        if (actionImage.attr('id') == 'image_file') {
            var input = actionImage[0];

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#imagePreview').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
                $('#imagePreview').parent().show();
            }
        } else if (actionImage.attr('id') == 'image_url') {
            if ( actionImage.val() != '') {
                $('#imagePreview').attr('src', actionImage.val());
                $('#imagePreview').parent().show();
            }
        } else {
            $('#imagePreview').parent().hide();
        }
    });

    if (document.getElementById('couponSelection')) {
        $('#couponName').html(document.getElementById('couponSelection').options[document.getElementById('couponSelection').selectedIndex].innerHTML);
        $('#couponSelection').on('change', function(){
            $('#couponName').html(this.options[this.selectedIndex].innerHTML.split('(')[0]);
        });
    }

    $( ":input").each(function(){
        $(this).change(function(){
            $(window).unbind('beforeunload');
            Brandco.helper.set_reload_alert(Brandco.message.reloadMessage);
        });
    });

    $('#submitReservationSchedule').click(function () {
        $(window).unbind('beforeunload');
        Brandco.helper.updateReservationStatus($(this));
    });

    if($('#text_title')[0]) {
        $("#limitTitle").html(("（")+($('#text_title')[0].value.length)+("文字/50文字）"));
    }

    $('#text_title').on('input', function(){
        $("#limitTitle").html(("（")+($('#text_title')[0].value.length)+("文字/50文字）"));
    });

    $('.jsEntryQuestionnaires').change(function(){
        changePrefillState();
    });

    Brandco.helper.loadZeroClipboard();
    Brandco.helper.init_module_preview();
    Brandco.helper.cmp_module_preview();
    Brandco.admin.adminCpInit();
    initImageInput();
    changePrefillState();
});

function m_win(url,windowname,width,height) {
    var features="location=no, menubar=no, status=yes, scrollbars=yes, resizable=yes, toolbar=no";
    if (width) {
        if (window.screen.width > width)
            features+=", left="+(window.screen.width-width)/2;
        else width=window.screen.width;
        features+=", width="+width;
    }
    if (height) {
        if (window.screen.height > height)
            features+=", top="+(window.screen.height-height)/2;
        else height=window.screen.height;
        features+=", height="+height;
    }
    window.open(url,windowname,features);
}

function handlePopupResult(result) {
    var image_file_exts = ['jpeg', 'jpg', 'png', 'gif' ];
    var ext = result.substr(-3);
    if((image_file_exts.indexOf(ext)) == -1){
        alert("must be a image file");
        return;
    }
    var image_url = $('#image_url'),
        image_preview = $('#imagePreview');

    $('.actionImage').attr('disabled', 'disabled');
    image_url.parent('li').find('.labelTitle').prop('checked', true);

    image_url.removeAttr('disabled');

    if (result != '') {
        image_url.val(result);
        image_preview.attr('src', result);
        image_preview.parent().show();

        if ($('#imagePreview_normal').length) {
            $('#imagePreview_normal').attr('src', result);
            $('#imagePreview_normal').parent().show();
        }
    }
}


function handleTextModuleResult(result, f_id) {
    if (f_id == 4) {
        var text_area = document.getElementById('text_area'),
            text_preview = document.getElementById('textPreview');
    } else if (f_id == 5) {
        var text_area = document.getElementById('text_area_0'),
            text_preview = document.getElementById('loseTextPreview');
    } else if (f_id == 6) {
        var text_area = document.getElementById('text_area_1'),
            text_preview = document.getElementById('winTextPreview');
    } else if (f_id == 7) {
        var text_area = document.getElementById('jsTextArea'),
            text_preview = document.getElementsByClassName('jsTextPreview');
    } else if (f_id == 8) {
        var text_area = document.getElementById('gift_incentive_description');
    } else if (f_id == 9) {
        var text_area = document.getElementById('jsCandidateText'),
            text_preview = document.getElementById('jsCandidateTextPreview');
    }

    var markdown_pattern = '\n![テキスト](' + result + ' "タイトル")',
        str_pos = 0,
        browser = ((text_area.selectionStart || text_area.selectionStart == '0') ? 'ff' : (document.selection ? 'ie' : false));

    // Appending text to textarea at the current position of cursor
    if (browser == 'ie') {
        text_area.focus();

        var range = document.selection.createRange();
        range.moveStart('character', -text_area.value.length);
        str_pos = range.text.length;
    } else if (browser == 'ff') {
        str_pos = text_area.selectionStart;
    }

    var front_str = (text_area.value).substr(0, str_pos),
        back_str = (text_area.value).substr(str_pos, text_area.value.length);

    text_area.value = front_str + markdown_pattern + back_str;
    str_pos = str_pos + markdown_pattern.length;

    // Focus textarea after appending text
    if (browser == 'ie') {
        text_area.focus();
        var range = document.selection.createRange();
        range.moveStart('character', str_pos - text_area.value.length);
        range.moveEnd('character', 0);
        range.select();
    } else if (browser == 'ff') {
        text_area.selectionStart = str_pos;
        text_area.selectionEnd = str_pos;
        text_area.focus();
    }

    var text_content = $(text_area).val(),
        param = {
            data: {
                text_content: text_content
            },
            url: 'admin-cp/parse_markdown',
            success: function(response) {
                if (response.result == 'ok') {
                    if (f_id == 8) {
                        $('.jsIncentiveDescriptionPreview').html(response.data.html_content);
                    } else {
                        $(text_preview).show();
                        $(text_preview).html(response.data.html_content);

                        if ($('#textPreview_normal').length) {
                            $("#textPreview_normal").html(response.data.html_content);
                        }
                    }
                }
            }
        };
    Brandco.api.callAjaxWithParam(param, false,  false);
}

function initImageInput() {
    $('.actionImage').attr('disabled', 'disabled');

    var checkedType = $('.labelTitle:checked');
    if (checkedType.attr('disabled') != 'disabled') {
        checkedType.parent('li').find('.actionImage').removeAttr('disabled');
    }
}

function changePrefillState() {
    var jsEntryQuestionnaires = $('.jsEntryQuestionnaires input');
    var checkedExists = false;
    for (i = 0 ; i < jsEntryQuestionnaires.length ; i ++) {
        if (jsEntryQuestionnaires[i].checked && !jsEntryQuestionnaires[i].disabled) {
            checkedExists = true;
            break;
        }
    }
    if (checkedExists) {
        $('.jsPrefillFlg').prop('disabled', false);
    } else {
        $('.jsPrefillFlg').prop('disabled', true);
    }
}

$(function () {
    $('.messageText').each(function () {
        $(this).html(Brandco.helper.autoLink($(this).html()));
    });
});