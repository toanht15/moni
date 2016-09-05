if (typeof(EditPopularVoteActionService) === 'undefined') {
    var EditPopularVoteActionService = function() {
        var FILE_TYPE_IMAGE = 1;
        var FILE_TYPE_MOVIE = 2;

        var SHARE_URL_TYPE_CP = 1;
        var SHARE_URL_TYPE_RANKING = 2;

        return {
            initPreview: function() {
                var file_type = EditPopularVoteActionService.getFileType();

                EditPopularVoteActionService.refreshPopularVoteSetting(file_type);
                EditPopularVoteActionService.refreshRandomFlg(file_type);
                EditPopularVoteActionService.refreshCandidateTextPreview(file_type);
                EditPopularVoteActionService.refreshCandidateListPreview(file_type);
                EditPopularVoteActionService.refreshCandidateModal(file_type);
                EditPopularVoteActionService.refreshSharePreview();
                EditPopularVoteActionService.refreshShowRankingFlg();

                EditPopularVoteActionService.initCandidateTextPreview(file_type);
                EditPopularVoteActionService.initCandidateListPreview(file_type);
                EditPopularVoteActionService.setSortableCandidateList(file_type);
            },

            initCandidateTextPreview: function(file_type) {
                EditPopularVoteActionService.updateCandidateTextPreview('.jsCandidateText[data-file_type="' + file_type + '"]');
            },

            initCandidateListPreview: function(file_type) {
                EditPopularVoteActionService.cleanCandidateListPreview(file_type);
                $('.jsCandidate[data-file_type="' + file_type + '"]').each(function(i) {
                    var order_no = i + 1;
                    var prefix = '.jsCandidate[data-order_no="' + order_no + '"][data-file_type="' + file_type + '"]';

                    EditPopularVoteActionService.addCandidatePreview(file_type);
                    EditPopularVoteActionService.countCandidateTitle(prefix + ' .jsCandidateTitle', order_no);
                    EditPopularVoteActionService.updateCandidateTitlePreview(prefix + ' .jsCandidateTitle', order_no);
                    EditPopularVoteActionService.updateCandidateSavedImagePreview(prefix + ' .jsCandidateOriginalUrl', prefix + ' .jsCandidateThumbnailUrl', file_type, order_no);
                    EditPopularVoteActionService.updateCandidateDescriptionPreview(prefix + ' .jsCandidateDescription', order_no);
                });
            },

            setSortableCandidateList: function(file_type) {
                $('.jsCandidateList').sortable({
                    opacity: 0.5,
                    cursor: 'move',
                    items: '.jsCandidate',
                    update: function() {
                        EditPopularVoteActionService.sortCandidateListPreview(file_type);
                        EditPopularVoteActionService.refreshCandidateNo(file_type);
                    },
                });
            },

            sortCandidateListPreview: function(file_type) {
                var candidate_list_preview = $('.jsCandidateListPreview[data-file_type="' + file_type + '"]');
                var node_tmp = $('<div></div>');
                $('.jsCandidate[data-file_type="' + file_type + '"]').each(function(){
                    var order_no = parseInt($(this).attr('data-order_no'));

                    node_tmp.append(candidate_list_preview.find('li:eq(' + (order_no - 1) + ')').clone(true));
                });

                candidate_list_preview.empty();
                candidate_list_preview.html(node_tmp.html());
            },

            renameInputTags: function(file_type, suffix) {
                var parent = $('.jsPopularVoteSettingDetail[data-file_type="' + file_type + '"]');

                parent.find('.jsCandidateId').each(function() {
                    $(this).attr('name', 'candidate_id' + suffix + '[]');
                })

                parent.find('.jsCandidateText').each(function() {
                    $(this).attr('name', 'text' + suffix);
                })

                parent.find('.jsCandidateTitle').each(function() {
                    $(this).attr('name', 'candidate_title' + suffix + '[]');
                })

                parent.find('.jsCandidateDescription').each(function() {
                    $(this).attr('name', 'candidate_description' + suffix + '[]');
                })

                parent.find('.jsRandomFlg input').each(function() {
                    $(this).attr('name', 'random_flg' + suffix);
                })
            },

            addCandidate: function(file_type) {
                var candidate_list = $('.jsCandidateList[data-file_type="' + file_type + '"]');
                var order_no = (candidate_list.children().length) + 1;

                var candidate = $('<li></li>').attr({
                    "data-order_no": order_no,
                    "data-file_type": file_type,
                }).addClass('jsCandidate');

                // 候補の番号
                var candidate_no = $('<span></span>').text('候補' + order_no).addClass('jsCandidateNo');

                // 候補のOriginalUrl
                var original_url = $('<input>').attr({
                    name: 'candidate_original_url[]',
                    type: 'hidden',
                });

                // 候補のThumbnailUrl
                var thumbnail_url = $('<input>').attr({
                    name: 'candidate_thumbnail_url[]',
                    type: 'hidden',
                });

                // 候補のID
                var id = $('<input>').attr({
                    name: 'candidate_id[]',
                    type: 'hidden',
                }).addClass('jsCandidateId').val(0);

                // 候補のタイトル
                var title = $('<input>').attr({
                    name: 'candidate_title[]',
                    type: 'text',
                    placeholder: 'タイトル',
                    maxlength: 33
                }).addClass('jsCandidateTitle');

                // 候補のタイトルのカウント
                var title_count = $('<small></small>').addClass('jsTextLimit').text('（0文字/33文字）');

                // 候補のコンテンツ (画像 / 動画)
                var content = '';
                if (parseInt(file_type) == FILE_TYPE_IMAGE) {
                    content = $('<input>').attr({
                        name: 'candidate_image[]',
                        type: 'file'
                    }).addClass('jsCandidateImage');
                } else { // if (parseInt(file_type) == FILE_TYPE_MOVIE) {
                    content = $('<span></span>').addClass('youtubeUrl jsCandidateMovie');
                    content.append($('<span></span>').text('https://www.youtube.com/watch?v='));
                    content.append($('<input>').attr({
                        name: 'candidate_movie[]',
                        type: 'text'
                    }));
                }

                // 候補の説明
                var description = $('<textarea></textarea>').attr({
                    name: 'candidate_description[]',
                    cols: 30,
                    rows: 10,
                    placeholder: '任意の説明 (任意入力)'
                }).addClass('jsCandidateDescription');

                // 削除ボタン
                var btn_delete = $('<a></a>').attr({
                    href: 'javascript:void(0)',
                    "data-open_modal_type": 'Confirm'
                }).addClass('iconBtnDelete jsOpenModal');

                candidate.append(original_url);
                candidate.append(thumbnail_url);
                candidate.append(id);
                candidate.append(candidate_no);
                candidate.append(title);
                candidate.append(title_count);
                candidate.append(content);
                if (parseInt(file_type) == FILE_TYPE_IMAGE) {
                    candidate.append($('<small></small>').text('（推奨サイズ: 横1000px × 縦524px）'));
                }
                candidate.append(description);
                candidate.append(btn_delete);

                candidate_list.append(candidate);
            },

            addCandidatePreview: function(file_type) {
                var candidate_list_preview = $('.jsCandidateListPreview[data-file_type="' + file_type + '"]');
                var order_no = candidate_list_preview.find('li').length + 1;

                var candidate_preview = $('<li></li>');

                // 選択ボタン
                var btn_select = $('<input>').attr({
                    name: 'cp_popular_candidate_id',
                    type: 'radio',
                    id: 'rankingRadio' + file_type + '-' + order_no
                }).addClass('customRadio');

                var content;
                var figure;
                var btn_zoom;
                // (画像 / 動画)
                if (parseInt(file_type) == FILE_TYPE_IMAGE) {
                    // 候補のコンテンツ (画像)
                    content = $('<label></label>').attr('for', 'rankingRadio' + file_type + '-' + order_no);

                    figure = $('<figure></figure>').addClass('itemCont');
                    figure.append($('<figcaption></figcaption>').addClass('itemTitle jsCandidateTitlePreview').text('No title'));
                    figure.append($('<span></span>').addClass('contImg').append($('<img>').attr({
                        src: $('input[name="static_url"]').val() + '/img/icon/iconNoImage1.png',
                        alt: 'image title',
                        width: 100,
                        height: 100,
                        "data-modal_content": $('input[name="static_url"]').val() + '/img/icon/iconNoImage1.png'
                    }).addClass('jsCandidateImagePreview')));

                    content.append(figure);
                    content.append($('<p></p>').addClass('itemText jsCandidateDescriptionPreview'));

                    // 拡大ボタン (画像)
                    btn_zoom = $('<a></a>').attr({
                        href: 'javascript:void(0)',
                        "data-open_modal_type": 'CandidatePreview'
                    }).addClass('imgPreview jsOpenModal').text('拡大表示する');
                } else { // if (parseInt(file_type) == FILE_TYPE_MOVIE) {
                    // 候補のコンテンツ (動画)
                    content = $('<label></label>').attr('for', 'rankingRadio' + file_type + '-' + order_no);

                    figure = $('<figure></figure>').addClass('itemCont');
                    figure.append($('<figcaption></figcaption>').addClass('itemTitle jsCandidateTitlePreview').text('No title'));
                    figure.append($('<span></span>').addClass('contImg').append($('<img>').attr({
                        src: $('input[name="static_url"]').val() + '/img/message/bgRanking1.png',
                        alt: 'movie title',
                        width: 100,
                        height: 100,
                    }).addClass('jsCandidateImagePreview')));

                    content.append(figure);
                    content.append($('<p></p>').addClass('itemText jsCandidateDescriptionPreview'));

                    // 拡大ボタン (動画)
                    btn_zoom = $('<a></a>').attr({
                        href: 'javascript:void(0)',
                        "data-open_modal_type": 'CandidatePreview',
                    }).addClass('moviePreview jsOpenModal').text('拡大表示する');
                }

                candidate_preview.append(btn_select);
                candidate_preview.append(content);
                candidate_preview.append(btn_zoom);

                candidate_list_preview.append(candidate_preview);
            },

            insertInputHiddenTag: function(name, value) {
                if (value != 0) {
                    $('form').append($('<input>').attr({
                        name: name,
                        type: 'hidden',
                        value: value
                    }).addClass('jsInputHiddenTag'));
                }
            },

            removeInputHiddenTag: function() {
                $('jsInputHiddenTag').remove();
            },

            insertDeleteTag: function(src, order_no, file_type) {
                $(src).after($('<span></span>').attr({
                    "data-order_no": order_no,
                    "data-file_type": file_type
                }).addClass('jsDeleteCandidate'));
            },

            removeDeleteTag: function() {
                $('.jsDeleteCandidate').remove();
            },

            deleteCandidate: function(src) {
                var candidate = $(src).parents().filter('.jsCandidate');
                candidate.remove();
            },

            deleteCandidatePreview: function(order_no, file_type) {
                var candidate_preview = $('.jsCandidateListPreview[data-file_type="' + file_type + '"] li:eq(' + (parseInt(order_no) - 1) + ')');
                candidate_preview.remove();
            },

            refreshPopularVoteSetting: function(file_type) {
                $('.jsPopularVoteSettingTitle[data-file_type="' + file_type + '"]').show();
                $('.jsPopularVoteSettingDetail[data-file_type="' + file_type + '"]').show();
                $('.jsCandidateText[data-file_type="' + file_type + '"]').attr('id', 'jsCandidateText');

                $('.jsPopularVoteSettingTitle[data-file_type="' + EditPopularVoteActionService.getOppositeFileType(file_type) + '"]').hide();
                $('.jsPopularVoteSettingDetail[data-file_type="' + EditPopularVoteActionService.getOppositeFileType(file_type) + '"]').hide();
                $('.jsCandidateText[data-file_type="' + EditPopularVoteActionService.getOppositeFileType(file_type) + '"]').attr('id', '');

                EditPopularVoteActionService.renameInputTags(file_type, '');
                EditPopularVoteActionService.renameInputTags(EditPopularVoteActionService.getOppositeFileType(file_type), '_hidden');
            },

            refreshRandomFlg: function(file_type) {
                var switch_random_flg = $('.jsRandomFlg[data-file_type="' + file_type + '"]');
                var random_flg = switch_random_flg.find('input[name="random_flg"]').val();

                switch_random_flg.removeClass('on off');
                if (random_flg == 1) {
                    switch_random_flg.addClass('on');
                } else {
                    switch_random_flg.addClass('off');
                }
            },

            refreshCandidateTextPreview: function(file_type) {
                $('.jsCandidateTextPreview[data-file_type="' + file_type + '"]').show();
                $('.jsCandidateTextPreview[data-file_type="' + file_type + '"]').attr('id', 'jsCandidateTextPreview');

                $('.jsCandidateTextPreview[data-file_type="' + EditPopularVoteActionService.getOppositeFileType(file_type) + '"]').hide();
                $('.jsCandidateTextPreview[data-file_type="' + EditPopularVoteActionService.getOppositeFileType(file_type) + '"]').attr('id', '');
            },

            refreshCandidateListPreview: function(file_type) {
                $('.jsCandidateListPreview[data-file_type="' + file_type + '"]').show();
                $('.jsCandidateListPreview[data-file_type="' + EditPopularVoteActionService.getOppositeFileType(file_type) + '"]').hide();
            },

            refreshCandidateNo: function(file_type) {
                $('.jsCandidateList[data-file_type="' + file_type + '"]').find('.jsCandidateNo').each(function(i) {
                    $(this).text('候補' + (i + 1));
                });
                $('.jsCandidateList[data-file_type="' + file_type + '"]').find('.jsCandidate').each(function(i) {
                    $(this).attr("data-order_no", i + 1);
                });
            },

            refreshShowRankingFlg: function() {
                var share_url_type = $('.jsShareUrlType:checked');

                if (parseInt(share_url_type.val()) == SHARE_URL_TYPE_RANKING && share_url_type.is(':disabled') == false) {
                    $('.jsShowRankingFlg').attr('disabled', false);
                } else {
                    $('.jsShowRankingFlg').attr('disabled', true);
                }
            },

            refreshSharePreview: function() {
                var checked_flg = 0;
                $('.jsShareRequired').each(function() {
                    if (this.checked) {
                        checked_flg = 1;
                        $('.jsSocialMediaTypePreview[data-require_type="' + $(this).attr('data-require_type') + '"]').show();
                    } else {
                        $('.jsSocialMediaTypePreview[data-require_type="' + $(this).attr('data-require_type') + '"]').hide();
                    }
                });

                if (checked_flg == 1) {
                    $('.jsSharePreset').show();
                    $('.jsPopularVoteSharePreview').show();

                    EditPopularVoteActionService.updateSharePlaceholderPreview();
                } else {
                    $('.jsSharePreset').hide();
                    $('.jsPopularVoteSharePreview').hide();
                }
            },

            refreshCandidateModal: function(file_type) {
                if (parseInt(file_type) == FILE_TYPE_IMAGE) {
                    $('.jsModalImagePreview').show();
                    $('.jsModalMoviePreview').hide();
                } else if (parseInt(file_type) == FILE_TYPE_MOVIE) {
                    $('.jsModalImagePreview').hide();
                    $('.jsModalMoviePreview').show();
                }
            },

            updateCandidateTextPreview: function(src) {
                var target = $('.jsCandidateTextPreview[data-file_type="' + $(src).attr('data-file_type') + '"]');
                var param = {
                    data: {
                        text_content: $(src).val()
                    },
                    url: 'admin-cp/parse_markdown',
                    success: function (response) {
                        if (response.result == 'ok') {
                            target.html(response.data.html_content);
                        }
                    }
                };

                Brandco.api.callAjaxWithParam(param, false, false);
            },

            updateCandidateTitlePreview: function(src, order_no) {
                var candidate = $(src).parents().filter('.jsCandidate');

                if (order_no > 0) {
                    order_no = order_no - 1;
                } else {
                    order_no = parseInt((candidate.attr('data-order_no')) - 1);
                }

                var target = $('.jsCandidateListPreview[data-file_type="' + candidate.attr('data-file_type') + '"] li:eq(' + order_no + ')').find('.jsCandidateTitlePreview');
                if ($(src).val().length > 0) {
                    target.text($(src).val());
                } else {
                    target.text('No Title');
                }
            },

            updateCandidateImagePreview: function(src, order_no) {
                var candidate = $(src).parents().filter('.jsCandidate');

                if (order_no > 0) {
                    order_no = order_no - 1;
                } else {
                    order_no = parseInt((candidate.attr('data-order_no')) - 1);
                }

                var target = $('.jsCandidateListPreview[data-file_type="' + candidate.attr('data-file_type') + '"] li:eq(' + order_no + ')').find('.jsCandidateImagePreview');
                if ($(src)[0].files && $(src)[0].files[0]) {
                    if(window.FileReader) {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            var image = new Image();
                            image.src = e.target.result;
                            image.onload = function() {
                                target.attr({
                                    src: e.target.result,
                                    "data-modal_content": e.target.result
                                });
                            };
                        };

                        reader.readAsDataURL($(src)[0].files[0]);
                    }
                }
            },

            updateCandidateSavedImagePreview: function(src_original, src_thumbnail, file_type, order_no) {
                var candidate = $(src_original).parents().filter('.jsCandidate');

                if (order_no > 0) {
                    order_no = order_no - 1;
                } else {
                    order_no = parseInt((candidate.attr('data-order_no')) - 1);
                }

                var target = $('.jsCandidateListPreview[data-file_type="' + candidate.attr('data-file_type') + '"] li:eq(' + order_no + ')').find('.jsCandidateImagePreview');
                var img_default = (parseInt(file_type) == FILE_TYPE_IMAGE) ? '/img/icon/iconNoImage1.png' : '/img/message/bgRanking1.png';
                if ($(src_original).val() == '') {
                    target.attr('src', $('input[name="static_url"]').val() + img_default);
                    target.attr('data-modal_content', $('input[name="static_url"]').val() + img_default);
                } else {
                    target.attr('src', $(src_thumbnail).val());
                    target.attr('data-modal_content', $(src_original).val());
                }
            },

            updateCandidateDescriptionPreview: function(src, order_no) {
                var candidate = $(src).parents().filter('.jsCandidate');

                if (order_no > 0) {
                    order_no = order_no - 1;
                } else {
                    order_no = parseInt((candidate.attr('data-order_no')) - 1);
                }

                var target = $('.jsCandidateListPreview[data-file_type="' + candidate.attr('data-file_type') + '"] li:eq(' + order_no + ')').find('.jsCandidateDescriptionPreview');
                target.text($(src).val());
            },

            updateCandidateModal: function(src, index) {
                var parent = $(src).parents().filter('li');
                var file_type = $(src).parents().filter('.jsCandidateListPreview').attr('data-file_type');

                $(index + ' .jsModalTitle').text(parent.find('.jsCandidateTitlePreview').text());
                if (parseInt(file_type) == FILE_TYPE_IMAGE) {
                    $(index + ' .jsModalImage').attr('src', parent.find('.jsCandidateImagePreview').attr('data-modal_content'));
                } else if (parseInt(file_type) == FILE_TYPE_MOVIE) {
                    $(index + ' .jsModalMovie').html('<iframe frameborder="0" allowfullscreen></iframe>');
                    $(index + ' .jsModalMovie iframe').attr({
                        src: parent.find('.jsCandidateImagePreview').attr('data-modal_content'),
                    });
                }
                $(index + ' .jsModalDescription').text(parent.find('.jsCandidateDescriptionPreview').text());
            },

            updateRandomFlg: function(file_type) {
                var switch_random_flg = $('.jsRandomFlg[data-file_type="' + file_type + '"]');

                if (switch_random_flg.attr('data-disabled') != 'disabled') {
                    if (switch_random_flg.hasClass('on')) {
                        switch_random_flg.find('input[name="random_flg"]').val(0);
                    } else {
                        switch_random_flg.find('input[name="random_flg"]').val(1);
                    }
                }
            },

            updateSharePlaceholderPreview: function() {
                $('.jsShareTextPreview').attr('placeholder', $('.jsSharePlaceholder').val());
            },

            cleanCandidateListPreview: function(file_type) {
                $(' .jsCandidateListPreview[data-file_type="' + file_type + '"]').empty();
            },

            cleanCandidateModalMovie: function(index) {
                $(index + ' .jsModalMovie iframe').attr('src', '');
                $(index + ' .jsModalMovie').empty();
            },

            getFileType: function() {
                return $('input[name="file_type"]:checked').val();
            },

            getOppositeFileType: function(file_type) {
                switch (parseInt(file_type)) {
                    case FILE_TYPE_IMAGE:
                        return FILE_TYPE_MOVIE;
                        break;
                    case FILE_TYPE_MOVIE:
                        return FILE_TYPE_IMAGE;
                        break;
                }
            },

            setIePreviewNotification: function() {
                if(!window.FileReader) {
                    var notification = '<p class="iconError1">IE9をお使いの場合、設定を保存後に画像がプレビューに表示されます。</p>';
                    $('.jsModulePreviewArea').before(notification);
                }
            },

            countCandidateTitle: function(src) {
                var parent = $(src).parents().filter('.jsCandidate');
                var counter = parent.find('.jsTextLimit');

                counter.text('（' + $(src).val().length + '文字/33文字）');
            }
        };
    }()
}

$(document).ready(function() {
    //---------------------------------------
    // モーダルの設定
    //---------------------------------------
    $('.jsModulePreviewArea').on('click', '.jsOpenModal', function(event) {
        //event.preventDefault();
        Brandco.unit.openModal('#modal' + $(this).attr('data-open_modal_type'));

        EditPopularVoteActionService.updateCandidateModal(this, '#modal' + $(this).attr('data-open_modal_type'));

        return false;
    });
    $('.jsPopularVoteSettingList').on('click', '.jsOpenModal', function(event) {
        //event.preventDefault();
        Brandco.unit.openModal('#modal' + $(this).attr('data-open_modal_type'));

        var candidate = $(this).parents().filter('.jsCandidate');
        EditPopularVoteActionService.insertDeleteTag(this, candidate.attr('data-order_no'), candidate.attr('data-file_type'));

        return false;
    });
    $('.jsModalPopularVote').on('click', '.jsCloseModal', function(event) {
        //event.preventDefault();
        Brandco.unit.closeModal($(this).attr('data-close_modal_type'));

        EditPopularVoteActionService.removeDeleteTag();
        EditPopularVoteActionService.cleanCandidateModalMovie('#modal' + $(this).attr('data-close_modal_type'));

        return false;
    });
    $('.jsModalPopularVote').on('click', '.jsExecuteDelete', function(event) {
        //event.preventDefault();
        $('.jsDeleteCandidate').click();
        $('.jsCloseModal[data-close_modal_type="Confirm"]').click();

        return false;
    });

    //---------------------------------------
    // 投票候補の設定
    //---------------------------------------
    $('.jsFileType').on('change', function() {
        var file_type = $(this).val();
        // IDの切り替え
        EditPopularVoteActionService.refreshPopularVoteSetting(file_type);
        EditPopularVoteActionService.refreshRandomFlg(file_type);
        EditPopularVoteActionService.refreshCandidateListPreview(file_type);
        EditPopularVoteActionService.refreshCandidateTextPreview(file_type);
        EditPopularVoteActionService.refreshCandidateModal(file_type);

        EditPopularVoteActionService.setSortableCandidateList(file_type);
        EditPopularVoteActionService.removeInputHiddenTag();
    });

    $('.jsRandomFlg').on('click', function() {
        var file_type = $(this).attr('data-file_type');
        EditPopularVoteActionService.updateRandomFlg(file_type);
    });

    $('.jsPopularVoteSettingList').on('input', '.jsCandidateText', function() {
        EditPopularVoteActionService.updateCandidateTextPreview(this)
    });

    $('.jsPopularVoteSettingList').on('change', '.jsCandidateImage', function() {
        EditPopularVoteActionService.updateCandidateImagePreview(this, 0);
    });

    $('.jsPopularVoteSettingList').on('input', '.jsCandidateTitle', function() {
        EditPopularVoteActionService.countCandidateTitle(this);
        EditPopularVoteActionService.updateCandidateTitlePreview(this, 0);
    });

    $('.jsPopularVoteSettingList').on('input', '.jsCandidateDescription', function() {
        EditPopularVoteActionService.updateCandidateDescriptionPreview(this, 0);
    });

    $('.jsPopularVoteSettingList').on('click', '.jsAddCandidate', function() {
        //event.preventDefault();

        var file_type = $(this).attr('data-file_type');
        EditPopularVoteActionService.addCandidate(file_type);
        EditPopularVoteActionService.addCandidatePreview(file_type);

        return false;
    });

    $('.jsPopularVoteSettingList').on('click', '.jsDeleteCandidate', function() {
        //event.preventDefault();

        var id = $(this).parents().filter('.jsCandidate').find('.jsCandidateId').val();
        var order_no = $(this).attr('data-order_no');
        var file_type = $(this).attr('data-file_type');

        EditPopularVoteActionService.deleteCandidate(this);
        EditPopularVoteActionService.deleteCandidatePreview(order_no, file_type);
        EditPopularVoteActionService.refreshCandidateNo(file_type);
        EditPopularVoteActionService.insertInputHiddenTag('candidate_del[]', id);


        return false;
    });

    //---------------------------------------
    // シェアの設定
    //---------------------------------------
    $('.jsPopularVoteSettingList').on('change', '.jsShareUrlType', function() {
        EditPopularVoteActionService.refreshShowRankingFlg();
    });

    $('.jsPopularVoteSettingList').on('change', '.jsShareRequired', function() {
        EditPopularVoteActionService.refreshSharePreview();
    });

    $('.jsPopularVoteSettingList').on('input', '.jsSharePlaceholder', function() {
        EditPopularVoteActionService.updateSharePlaceholderPreview();
    });

    EditPopularVoteActionService.initPreview();
    EditPopularVoteActionService.setIePreviewNotification();

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker({
        minDate: new Date()
    });
});