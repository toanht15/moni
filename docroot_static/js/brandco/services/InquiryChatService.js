if (typeof(InquiryChatService) === 'undefined') {
    var InquiryChatService = function() {
        return {
            executeSaveMessage: function(src) {
                var form = $(src).parents().filter('form');
                var url = form.attr('action');
                var content = $('textarea[name=content]', form);

                var param = {
                    data: {
                        csrf_token: $('input[name=csrf_token]', form).val(),
                        inquiry_room_id: $('input[name=inquiry_room_id]', form).val(),
                        inquiry_message_id: $('input[name=inquiry_message_id]', form).val(),
                        sender: $('input[name=sender]', form).val(),
                        draft_flg: $(src).attr('data-draft_flg'),
                        content: content.val()
                    },
                    url: url,
                    type: 'POST',
                    success: function(json) {
                        if (json.result == 'ok') {
                            $('.jsContentError').empty();

                            if (json.data.inquiry_message_id > 0) {
                                $('input[name=inquiry_message_id]', form).val(json.data.inquiry_message_id)
                            } else {
                                content.val('');
                            }

                            $('.jsChatBody').html(json.html);

                            InquiryChatService.setForwardedBar();
                            InquiryChatService.scrollToBottom();
                        }
                    },
                    error: function() {
                        alert('error');
                    }
                }

                Brandco.api.callAjaxWithParam(param);
            },

            executeForwardMessage: function(src) {
                var form = $(src).parents().filter('form');
                form.submit();
            },

            scrollToBottom: function() {
                var chat_body = $('.jsChatBody ul');
                chat_body.scrollTop(chat_body[0].scrollHeight);
            },

            slideToggleArea: function(src) {
                var transmit_area = $(src).parents().filter('.jsCheckToggleWrap');
                transmit_area.find('.jsCheckToggleTarget').slideToggle(300);
            },

            setForwardedMessage: function(src) {
                var message = $(src).parents().filter('.jsMessage');
                var message_user_name = message.find('.jsMessageUserName').text();
                var message_text = message.find('.jsMessageText').text();

                var target = $(src).parents().filter('.jsChat').find('.jsForwardedMessage');
                target.empty();
                target.append($('<strong></strong>').text(message_user_name));
                target.append($('<br>'));
                target.append($('<span></span>').text(Brandco.helper.cutLongText(message_text, 500)));

                // メッセージIDをセットする
                $('input[name=inquiry_message_id][data-action_type=forward]').val($(src).attr('data-inquiry_message_id'));

                // メッセージの背景色を変更する
                $('.jsMessageText.selected').removeClass('selected');
                message.find('.jsMessageText').addClass('selected');
            },

            setForwardedBar: function() {
                var message = $('.jsForwarded').last();

                if (message) {
                    message.after($('<li class="transmited"><small class="iconCheck3">ここまでのお問い合わせは転送済</small></li>'));
                }
            },

            openModal: function(src) {
                var modal_name = $(src).attr('data-open_modal_type');
                Brandco.unit.openModal('#modal' + modal_name);
            },

            closeModal: function(src) {
                var modal_name = $(src).attr('data-close_modal_type');
                Brandco.unit.closeModal(modal_name);
            },

            closeModalFlame: function(src) {
                var modal_name = $(src).attr('data-close_modal_type');
                Brandco.unit.closeModalFlame('#modal' + modal_name);
            },
        };
    }();
}

$(document).ready(function() {
    $('.jsChat').on('click', '.jsMessageSave', function() {
        var message = $.trim($('.jsContent').val());
        if (message.length > 0) {
            InquiryChatService.executeSaveMessage(this);
        } else {
            $('.jsContentError').html($('<span/>').addClass('iconError1').text('必ず入力してください'));
        }

        return false;
    });

    $('.jsChat').on('click', '.jsMessageForward', function() {
        InquiryChatService.executeForwardMessage(this);

        return false;
    });

    $('.jsChat').on('change', '.jsCheckToggle', function(){
        InquiryChatService.slideToggleArea(this);
    });

    $('.jsChat').on('click', '.jsForwardedMessageSet', function(){
        InquiryChatService.setForwardedMessage(this);
        if (!$('.jsChat .jsCheckToggle:checked').val()) {
            $('.jsChat .jsCheckToggle').prop('checked', true).change();
        }

        return false;
    });

    $('.jsChat').on('click', '.jsOpenInquiryForwardModal', function() {
        if ($('.jsNoForwardedMessage')[0]) {
            $('.jsForwardedMessage').html($('<span/>').addClass('iconError1 jsNoForwardedMessage').text('転送するメッセージを選択して下さい。'));
        } else {
            InquiryChatService.openModal(this);
        }

        return false;
    });

    $('.jsModal').on('click', '.jsCloseInquiryForwardModal', function() {
        InquiryChatService.closeModalFlame(this);

        return false;
    });

    $('.jsModal').on('click', '.jsTriggerMessageForward', function() {
        $('.jsMessageForward').click();

        return false;
    });

    $('.jsChat').on('click', '.jsOpenInquiryMessageSaveModal', function() {
        var message = $.trim($('.jsContent').val());
        if (message.length > 0) {
            $('.jsModalContent').html(message.replace(/\r?\n/g, '<br>'));

            InquiryChatService.openModal(this);
        } else {
            $('.jsContentError').html($('<span/>').addClass('iconError1').text('必ず入力してください。'));
        }

        return false;
    });

    $('.jsModal').on('click', '.jsCloseInquiryMessageSaveModal', function() {
        InquiryChatService.closeModal(this);

        return false;
    });

    $('.jsModal').on('click', '.jsTriggerMessageSave', function() {
        $('.jsMessageSave[data-draft_flg="0"]').click();
        InquiryChatService.closeModal(this);

        return false;
    });

    InquiryChatService.setForwardedBar();
    InquiryChatService.scrollToBottom();
});
