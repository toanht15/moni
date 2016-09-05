var FileListService = (function() {
    var upload_file_preview = {
        'zip': '/img/file/zip.png',
        'pdf': '/img/file/pdf.png',
        'css': '/img/file/css.png',
        'mp4': '/img/file/mp4.png',
        'js': '/img/file/js.png'
    };
    var image_file_exts = [
        'jpeg',
        'jpg',
        'png',
        'gif'
    ];
    var video_file_exts = [
        'mp4',
    ];
    var cp_status_image_define = {
        'width': 1000,
        'height': 524
    };
    var total_size = 0;

    return {
        handleFileUpload: function(files) {
            var file_upload_error = $(document).find('.jsFileUploadError');
            for (var i = 0; i < files.length; i++) {
                if(files[i].type == 'video/mp4'){
                    if (files[i].size > 500000000) {
                        file_upload_error.html('ファイルの容量が500MBを超えています');
                        if (!file_upload_error.is(":visible")) {
                            file_upload_error.show();
                            file_upload_error.after('<br>');
                        }
                        continue;
                    }
                } else {
                    if (files[i].size > 10000000) {
                        file_upload_error.html('ファイルの容量が10MBを超えています');
                        if (!file_upload_error.is(":visible")) {
                            file_upload_error.show();
                            file_upload_error.after('<br>');
                        }
                        continue;
                    }
                }

                total_size += files[i].size;
                if (total_size > 500000000) {
                    total_size -= files[i].size;

                    file_upload_error.html('ファイルの合計が500MBを超えています');
                    if (!file_upload_error.is(":visible")) {
                        file_upload_error.show();
                        file_upload_error.after('<br>');
                    }
                    continue;
                }

                if (file_upload_error.is(":visible")) {
                    file_upload_error.nextAll('br').remove();
                    file_upload_error.hide();
                }

                var form_data = new FormData(),
                    csrf_token = document.getElementsByName('csrf_token')[0].value;

                form_data.append('file_upload', files[i]);
                form_data.append('csrf_token', csrf_token);

                var status_bar = new FileListService.addUploadStatusBar();
                status_bar.setFileInfo(files[i]);

                FileListService.checkFileUpload(form_data, status_bar);
            }
        },
        addUploadStatusBar: function() {
            this.statusBar = $('<li></li>');
            this.fileName = $('<span class="fileName"></span>').appendTo(this.statusBar);
            $('<small class="status jsFileValidatingProgress">0% / 100%</small>').appendTo(this.statusBar);
            this.cancel = $('<small class="cancel"><a href="javascript:void(0);" id="file_upload_size">削除</a></small>').appendTo(this.statusBar);
            $('.jsFileUploadList').append(this.statusBar);

            this.setFileInfo = function(file) {
                this.fileName.html('<img src="" width="40" height="40" alt="画像ファイル" class="jsFileUploadPreviewImg">' + file.name + '(' + FileListService.convertFileSize(file.size) + ')');
                this.cancel.find('#file_upload_size').attr('data-upload_file_size', file.size);

                var ext = FileListService.getFileExtension(file.name);
                var static_url = document.getElementsByName('static_url')[0].value;

                if ($.inArray(ext, image_file_exts) > -1) {
                    var reader = new FileReader();
                    var status_bar = this.statusBar;

                    reader.onload = function(e) {
                        status_bar.find('.jsFileUploadPreviewImg').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                } else if (upload_file_preview[ext]) {
                    this.statusBar.find('.jsFileUploadPreviewImg').attr('src', static_url + upload_file_preview[ext]);
                } else {
                    this.statusBar.find('.jsFileUploadPreviewImg').attr('src', static_url + '/img/file/other.png');
                }
            };
            this.setValidatingProgress = function(progress) {
                if (progress == 'ok') {
                    this.statusBar.find('.jsFileValidatingProgress').html('準備完了');
                } else if (progress == 'ng') {
                    this.statusBar.find('.jsFileValidatingProgress').html('アップロード失敗');
                } else if (progress == '100') {
                    this.statusBar.find('.jsFileValidatingProgress').html('処理中...');
                } else {
                    this.statusBar.find('.jsFileValidatingProgress').html(progress + '% / 100%');
                }
            };
            this.cancelUpload = function(jqxhr) {
                var status_bar = this.statusBar;
                this.cancel.click(function() {
                    jqxhr.abort();
                    status_bar.remove();
                    total_size -= status_bar.find('#file_upload_size').data('upload_file_size');

                    if (total_size <= 500000000) {
                        var file_upload_error = $(document).find('.jsFileUploadError');
                        file_upload_error.nextAll('br').remove();
                        file_upload_error.hide();
                    }
                });
            };
            this.setFileInputInfo = function(file_id) {
                $('<input type="text" value="' + file_id + '" style="display: none" name="upload_file_ids[]">').appendTo(this.statusBar);
            }
        },
        checkFileUpload: function(form_data, status_bar) {
            var jqXHR = $.ajax({
                xhr: function() {
                    var xhr_obj = $.ajaxSettings.xhr();

                    if (xhr_obj.upload) {
                        xhr_obj.upload.addEventListener('progress', function(event) {
                            if (event.lengthComputable) {
                                var position = event.loaded || event.position;
                                var total = event.total;

                                var progress = Math.ceil(position / total * 100);
                                status_bar.setValidatingProgress(progress);
                            }
                        }, false);
                    }

                    return xhr_obj;
                },
                url: 'api_validate_file_upload.json',
                type: 'POST',
                contentType: false,
                processData: false,
                dataType: 'json',
                cache: false,
                data: form_data,
                success: function(response) {
                    if (response != null && response.result == 'ok') {
                        status_bar.setFileInputInfo(response.data.file_id);
                        status_bar.setValidatingProgress('ok');
                    } else {
                        status_bar.setValidatingProgress('ng')
                    }
                }
            });

            status_bar.cancelUpload(jqXHR);
        },
        convertFileSize: function(file_size) {
            var size_str = "";
            var sizeKB = file_size/1024;

            if (parseInt(sizeKB) > 1024) {
                size_str = (sizeKB/1024).toFixed(2) + " MB";
            } else {
                size_str = sizeKB.toFixed(2) + " KB";
            }

            return size_str;
        },
        getFileExtension: function(file_name) {
            var ext = file_name.split('.');

            if (ext.length === 1 || (ext.length === 2 && ext[0] === "")) {
                return "";
            }

            return ext.pop().toLowerCase();
        },
        isChecked: function() {
            var checked_flg = false;
            $('.jsFileGroup').each(function() {
                if ($(this).prop('checked') == true) {
                    checked_flg = true;
                }
            });

            if (checked_flg == false) alert('チェックしてください。');
            return checked_flg;
        },
        feedFileInfo: function(feed_file_info, callback, f_id, stt, photo_width, photo_height) {
            // Disable feeding file info while access from top menu
            if (f_id == 1) return false;

            // Disable feeding file info while cp_action is disabled
            if (f_id != 2 && stt != 2) return false;

            if (f_id == 2) { // Feeding from CKEditor
                window.opener.CKEDITOR.tools.callFunction(callback, feed_file_info);
                window.close();
            } else if (f_id == 3) { // Feeding from CpActionModuleImage
                window.opener.handlePopupResult(feed_file_info);
                window.close();
            } else if (f_id == 10) { // Feeding from StaticHTML Image slider
                window.opener.PartsTemplateService.setSliderImage(feed_file_info);
                window.close();
            } else if (f_id == 11) { // Feeding from StaticHTML Image float
                window.opener.PartsTemplateService.setFloatImage(feed_file_info);
                window.close();
            } else if (f_id == 12) { // Feeding from StaticHTML Image full
                window.opener.PartsTemplateService.setFullImage(feed_file_info);
                window.close();
            } else if (f_id == 13) { // Feeding from Stamp Rally Joined Cp
                if(photo_width != cp_status_image_define.width || photo_height != cp_status_image_define.height){
                    alert('JPEG,GIF,PNGの横1000px * 縦524pxを選択してください！');
                } else {
                    window.opener.PartsTemplateService.setCpStatusJoinedImage(feed_file_info);
                    window.close();
                }
            } else if (f_id == 14) { // Feeding from Stamp Rally Finish Cp
                if(photo_width != cp_status_image_define.width || photo_height != cp_status_image_define.height){
                    alert('JPEG,GIF,PNGの横1000px * 縦524pxを選択してください！');
                } else {
                    window.opener.PartsTemplateService.setCpStatusFinishImage(feed_file_info);
                    window.close();
                }
            } else if (f_id == 15) { // Feeding from Stamp Rally Coming Soon Cp
                    if(photo_width != cp_status_image_define.width || photo_height != cp_status_image_define.height){
                        alert('JPEG,GIF,PNGの横1000px * 縦524pxを選択してください！');
                    } else{
                        window.opener.PartsTemplateService.setCpStatusComingSoonImage(feed_file_info);
                        window.close();
                    }
            } else if (f_id == 16) {
                window.opener.EditMovieActionService.handlePopupResultMovie(feed_file_info);
                window.close();
            } else { // Feeding from CpActionModuleText
                window.opener.handleTextModuleResult(feed_file_info, f_id);
                window.close();
            }

            return false;
        }
    }
})();

$(document).ready(function() {
    $('.jsFileUploader').on('click', function() {
        $(window).unbind('beforeunload');
        document.fileUploadForm.action = $(this).data('action_url');
        document.fileUploadForm.submit();
    });

    var upload_handler = $('#file_upload_area_handler');

    upload_handler.on('dragenter', function(event) {
        event.stopPropagation();
        event.preventDefault();
        $(this).addClass('hover');
    });

    upload_handler.on('dragover', function(event) {
        event.stopPropagation();
        event.preventDefault();
    });

    upload_handler.on('drop', function(event) {
        event.preventDefault();
        $(this).removeClass('hover');

        var files = event.originalEvent.dataTransfer.files;
        FileListService.handleFileUpload(files);
    });

    // Prevent default drag and drop event on page
    $(document).on('dragenter', function(event) {
        event.stopPropagation();
        event.preventDefault();
    });

    $(document).on('dragover', function(event) {
        event.stopPropagation();
        event.preventDefault();
        upload_handler.removeClass('hover');
    });

    $(document).on('drop', function(event) {
        event.stopPropagation();
        event.preventDefault();
    });

    // multiple file input
    $('#multiple_file_input').off('change');
    $('#multiple_file_input').on('change', function() {
        var files = this.files;
        FileListService.handleFileUpload(files);
    });

    // ZeroClipboard copy url to clipboard
    $('.jsCopyToClipboardBtn').each(function() {
        var zero_clipboard = new ZeroClipboard(this);

        zero_clipboard.on('error', function(event) {
            ZeroClipboard.destroy();
        });
    });

    // common toggle area
    $('.jsAreaToggle').click(function(){
        $(this).parents('.jsAreaToggleWrap').find('.jsAreaToggleTarget').stop(true, true).fadeToggle(200);
        return false;
    });

    // File remove action
    $('.jsFileDeleteBtn').click(function(){
        var data = this.getAttribute('data-brand_upload_file_id'),
            csrf_token = document.getElementsByName("csrf_token")[0].value;
        data = data + '&csrf_token=' + csrf_token;
        Brandco.helper.showConfirm('.modal1', data);
    });

    $('#delete_area').click(function(){
        var url = this.getAttribute('data-url'),
            callback = this.getAttribute('data-callback');
        Brandco.helper.deleteEntry(this, url, callback);
    });

    $(document).on('click', '.jsFeedFileInfoBtn', function() {
        FileListService.feedFileInfo($(this).data('feed_file_info'), $(this).data('callback'), $(this).data('f_id'), $(this).data('status'),$(this).data('photo_width'),$(this).data('photo_height'));
    });
});