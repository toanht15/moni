if(typeof(UserActionPhotoService) === 'undefined') {
    var UserActionPhotoService = (function () {
        return{
            executeAction: function (target) {
                // Remove the event to prevent duplicate submission.
                $('.cmd_execute_photo_action').off("click");

                var form = $(target).parents().filter(".executePhotoActionForm").get(0);
                var formData = new FormData(form);
                var section = $(target).parents().filter(".jsMessage");
                var url = form.action;
                var param = {
                    data: formData,
                    // file アップロード用
                    processData: false,
                    contentType: false,
                    url: url,
                    beforeSend: function () {
                        Brandco.helper.showLoading(section);
                    },
                    success: function (json) {
                        try {
                            section.find('.js_photo_error').hide();

                            if (json.result === "ok") {
                                $('.invalid_upload_file').each(function () {
                                    $(this).text('');
                                })
                                if (json.data.next_action === true) {
                                    var message = $(json.html);
                                    message.hide();
                                    section.after(message);

                                    Brandco.helper.facebookParsing(json.data.sns_action);

                                    $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                                        Brandco.unit.createAndJumpToAnchor();
                                    });
                                }
                                $(target).replaceWith('<span class="large1">' + $(target).html() + '</span>');

                                section.find('.action_image').attr('disabled', 'disabled');
                                section.find('.jsActionTitle').attr('disabled', 'disabled');
                                section.find('.jsActionComment').attr('disabled', 'disabled');
                                section.find('.jsActionShareText').attr('disabled', 'disabled');
                                section.find('.jsActionShareFb').attr('disabled', 'disabled');
                                section.find('.jsActionShareTw').attr('disabled', 'disabled');

                                $('.invalid_upload_file').hide();

                                UserActionPhotoService.updatePreview(section);
                            } else {
                                var photo_error_flg = 0;
                                $.each(json.errors, function (index, value) {
                                    if (value) {
                                        section.find('.' + index).text(value);
                                        section.find('.' + index).show();
                                        photo_error_flg = 1;
                                    }
                                });

                                if (!photo_error_flg) {
                                    alert('エラーが発生しました');
                                }
                            }
                        } finally {
                            // Add the event.
                            $('.cmd_execute_photo_action').on("click", function (event) {
                                event.preventDefault();
                                if (window.FormData) {
                                    UserActionPhotoService.executeAction(this);
                                } else {
                                    UserActionPhotoService.executeActionForIE(this);
                                }
                            });
                        }
                    },
                    complete: function () {
                        Brandco.helper.hideLoading();
                    }
                };

                Brandco.api.callAjaxWithParam(param, false, false);
            },
            executeActionForIE: function (target) {
                // Remove the event to prevent duplicate submission.
                $('.cmd_execute_photo_action').off("click");

                var form = $(target).parents().filter(".executePhotoActionForm");
                var section = $(target).parents().filter(".jsMessage");

                var param = {
                    beforeSubmit: function () {
                        Brandco.helper.showLoading(section);
                    },
                    success: function (json) {
                        try {
                            if (json.result === "ok") {
                                $('.invalid_upload_file').each(function () {
                                    $(this).text('');
                                })
                                if (json.data.next_action === true) {
                                    var message = $(json.html);
                                    message.hide();
                                    section.after(message);

                                    Brandco.helper.facebookParsing(json.data.sns_action);

                                    $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                                        Brandco.unit.createAndJumpToAnchor();
                                    });
                                }
                                $(target).replaceWith('<span class="large1">' + $(target).html() + '</span>');

                                section.find('.action_image').attr('disabled', 'disabled');
                                section.find('.jsActionTitle').attr('disabled', 'disabled');
                                section.find('.jsActionComment').attr('disabled', 'disabled');
                                section.find('.jsActionShareText').attr('disabled', 'disabled');
                                section.find('.jsActionShareFb').attr('disabled', 'disabled');
                                section.find('.jsActionShareTw').attr('disabled', 'disabled');

                                $('.invalid_upload_file').hide();

                                UserActionPhotoService.updatePreview(section);
                            } else {
                                var file_upload_error_flg = 0;
                                if (json.errors['invalid_upload_file']) {
                                    section.find('.invalid_upload_file').text(json.errors['invalid_upload_file']);
                                    section.find('.invalid_upload_file').show();
                                    file_upload_error_flg = 1;
                                }
                                if (!file_upload_error_flg) {
                                    alert('エラーが発生しました');
                                }
                            }
                            Brandco.helper.hideLoading();
                        } finally {
                            // Add the event.
                            $('.cmd_execute_photo_action').on("click", function (event) {
                                event.preventDefault();
                                if (window.FormData) {
                                    UserActionPhotoService.executeAction(this);
                                } else {
                                    UserActionPhotoService.executeActionForIE(this);
                                }
                            });
                        }
                    }
                };
                form.ajaxSubmit(param);
            },
            updatePreview: function(section) {
                var action_image_src = section.find('.photo_image').attr('src');
                var action_title_text = section.find('.jsActionTitle').val();
                var action_comment_text = section.find('.jsActionComment').val();

                var photo_data = "";
                if (action_title_text != null && action_title_text != "") {
                    photo_data += '<strong>' + action_title_text + '</strong>';
                }
                if (action_comment_text != null && action_comment_text != "") {
                    photo_data += Brandco.helper.escapeSpecialCharacter(action_comment_text);
                }

                section.find('.jsUserPhotoImage').attr('src', action_image_src);
                if (photo_data != "") {
                    var user_photo_data = section.find('.jsUserPhotoData');
                    user_photo_data.html(photo_data);
                    user_photo_data.show();
                }

                var js_user_upload_cont = section.find('.jsUserUploadCont');
                if (!js_user_upload_cont.is(':visible')) {
                    js_user_upload_cont.show();
                }
            },
            connectSns: function(target) {
                var form = $(target).parents().filter(".executePhotoActionForm").get(0);
                var formData = new FormData(form);
                var section = $(target).parents().filter(".jsMessage");
                var url = document.getElementsByName('sns_data_cache_url')[0].value;
                var param = {
                    data: formData,
                    // file アップロード用
                    processData: false,
                    contentType: false,
                    url: url,
                    success: function (json) {
                        if (json.result === "ok") {
                            location.href = json.data.connect_url;
                        } else {
                            var photo_error_flg = 0;
                            $.each(json.errors, function(index, value) {
                                if (value) {
                                    section.find('.' + index).text(value);
                                    section.find('.' + index).show();
                                    photo_error_flg = 1;
                                }
                            });

                            if (!photo_error_flg) {
                                alert('エラーが発生しました');
                            }
                        }
                    }
                }
                Brandco.api.callAjaxWithParam(param);
            }
        };
    })();
}

$(function () {
    $('.cmd_execute_photo_action').off("click");
    $('.cmd_execute_photo_action').on("click", function (event) {
        event.preventDefault();
        if (window.FormData) {
            UserActionPhotoService.executeAction(this);
        } else {
            UserActionPhotoService.executeActionForIE(this);
        }
    });

    $('.action_image').off('change');
    $('.action_image').on('change', function () {
        if ($(this)[0].files && $(this)[0].files[0]) {
            var reader = new FileReader(),
                inputDoc = $(this);
            reader.onload = function (e) {
                var photoImage = inputDoc.closest(".fileUpload_img").find(".photo_image")
                photoImage.attr('src', e.target.result);
                photoImage.show();
            }
            reader.readAsDataURL($(this)[0].files[0]);
        } else {
            $(this).closest(".fileUpload_img").find(".photo_image").attr('src', '');
            UserActionPhotoService.updatePreview($(this));
        }
    });

    $('.thumb').off('click');
    $('.thumb').on('click', function () {
        if (0 < $(this).children().closest('img').attr('src').length) {
            window.open($(this).children().closest('img').attr('src'), '_blank');
        }
    });

    $('.jsUserPhotoImage').off('click');
    $('.jsUserPhotoImage').on('click', function() {
        if ($(this).attr('src').length > 0) {
            window.open($(this).attr('src'), '_blank');
        }
    });

    $('.jsFbConnect').off('click');
    $('.jsFbConnect').on('click', function(event) {
        event.preventDefault();
        if (window.FormData) {
            $("<input>", {
                type: 'hidden',
                name: 'platform',
                value: $(this).data('platform')
            }).appendTo($('.targetFbId'));
            UserActionPhotoService.connectSns(this);
        }else{
            location.href = $('.connectFbUrl').text();
        }
    });

    $('.jsTwConnect').off('click');
    $('.jsTwConnect').on('click', function(event) {
        event.preventDefault();
        if (window.FormData) {
            $("<input>", {
                type: 'hidden',
                name: 'platform',
                value: $(this).data('platform')
            }).appendTo($('.targetTwId'));
            UserActionPhotoService.connectSns(this);
        }else{
            location.href = $('.connectTwUrl').text();
        }
    });
});
