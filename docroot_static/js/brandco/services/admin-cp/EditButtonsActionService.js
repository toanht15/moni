var EditButtonsActionService = {
        isShowOption: false,
        iconBtnDeleteClick : function(){
            $('.iconBtnDelete').unbind('click');
            $('.iconBtnDelete').click(function(){
                $(this).closest('li').hide(100,function(){
                    $(this).closest('li').remove();
                    if ($('#buttonPreview li').size() > 2) {
                        var li = $('#addBranch1Li').prev();
                        if (li.find('.attention1')[0]) {
                            $('<a href="javascript:void(0)" class="iconBtnDelete">削除する</a>').insertBefore(li.find('.attention1'));
                        } else {
                            li.append('<a href="javascript:void(0)" class="iconBtnDelete">削除する</a>');
                        }
                        EditButtonsActionService.iconBtnDeleteClick();
                    }
                });
                $('#buttonPreview li').last().hide(100,function(){
                    $(this).remove();
                    EditButtonsActionService.getOrderFromDOM();
                });
            });
        },
        btnBranch2Click: function(){
            $('.btnBranch2').unbind('click');
            $('.btnBranch2').click(function(){
                $('.btnAction select').hide(300,function(){
                    $('.btnAction').hide();
                });
                $(this).removeClass('btnBranch2').addClass('btnBranch1');
                $('.btnBranch1 span').html('分岐する');
                EditButtonsActionService.isShowOption = false;
                EditButtonsActionService.btnBranch1Click();
            });
        },
        btnBranch1Click: function(){
            $('.btnBranch1').unbind('click');
            $('.btnBranch1').click(function(){
                $('.btnAction').show();
                $('.btnAction select').show(300);
                $(this).removeClass('btnBranch1').addClass('btnBranch2');
                $('.btnBranch2 span').html('分岐解除');
                EditButtonsActionService.isShowOption = true;
                EditButtonsActionService.btnBranch2Click();
            });
        },
        initPreview: function() {
            $('#buttonPreview').html('');
            $('#editButtonUl li').each(function(){
                if($(this).attr('id') != 'addBranch1Li') {
                    var input = $(this).find('input');
                    var temp = Brandco.helper.escapeSpecialCharacter(input.val());
                    $('#buttonPreview').append('<li class="btn1"><a href="javascript:void(0)" class="middle1" id="'+input.attr('id')+'_preview">'+temp+'</a></li>');
                    EditButtonsActionService.initInputEvent(input.attr('id'));
                    EditButtonsActionService.getOrderFromDOM();
                }
            });
        },
        initInputEvent: function(id) {
            $('#'+id).on('input',function(){
                var temp = Brandco.helper.escapeSpecialCharacter($(this).val());
               $('#'+id+'_preview').html(temp);
            });
        },
        getOrderFromDOM: function(id) {
            var order = [];
            $(".moduleSetting li").each(function(){
                if ($(this).attr('id') && $(this).attr('id') != 'addBranch1Li') {
                    order.push($(this).attr('id'));
                }
            });
            $('#order').val(order);
        }
};

$(document).ready(function(){

    $('#addBranch1Button').click(function(){
        var display = '',
            buttonNum = $('#buttonPreview li').size();
        if (!EditButtonsActionService.isShowOption) {
            display = 'display: none';
        }
        if ($('#buttonPreview li').size() < 8) {
            var option = $('#addBranch1Li').prev().find('.btnAction').clone();
            option.attr('style',display);
            option.find('select').attr('name', 'newOption'+(buttonNum+1));
            option.find("option[selected='selected']").removeAttr('selected');
            var html = '<li class="lastButton" style="display: none" id="newLi_'+(buttonNum+1)+'">' +
                '<span class="btn1Edit">' +
                '<input type="text" name="newTitle'+(buttonNum+1)+'" placeholder="入力してください" maxlength="80" id="button'+(buttonNum+1)+'">' +
                '</span> ' +
                option[0].outerHTML+
                '<a href="javascript:void(0)" class="iconBtnDelete">削除する</a>' +
                '</li>';
            $('.iconBtnDelete').remove();
            $(html).insertBefore('#addBranch1Li');

            $('#buttonPreview').append('<li class="btn1 newPreview" style="display: none"><a href="javascript:void(0)" class="middle1" id="button'+(buttonNum+1)+'_preview"></a></li>');
            EditButtonsActionService.initInputEvent('button'+(buttonNum+1));

            $('.lastButton').show(100);
            $('.newPreview').show(100);

            $('.lastButton').removeClass('lastButton');
            $('.newPreview').removeClass('newPreview');

            EditButtonsActionService.iconBtnDeleteClick();
            EditButtonsActionService.getOrderFromDOM();
        }
    });

    $(".moduleSetting").sortable({
        items: "li:not(#addBranch1Li)",
        start: function(e, ui) {
            // puts the old positions into array before sorting
        },
        update: function(event, ui) {
            // grabs the new positions now that we've finished sorting
            $('.iconBtnDelete').remove();
            $('#addBranch1Li').prev().append('<a href="javascript:void(0)" class="iconBtnDelete">削除する</a>');
            EditButtonsActionService.iconBtnDeleteClick();
            EditButtonsActionService.initPreview();
            $(window).unbind('beforeunload');
            Brandco.helper.set_reload_alert(Brandco.message.reloadMessage);
        }
    });

    EditButtonsActionService.iconBtnDeleteClick();
    EditButtonsActionService.btnBranch2Click();
    EditButtonsActionService.btnBranch1Click();
    EditButtonsActionService.initPreview();

});
