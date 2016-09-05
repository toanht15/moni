var CommentListService = (function() {
    return {
        cur_cu_relation_id: null,
        cur_page: 1,
        loadCommentList: function() {
            var type = $('.jsOrderType:checked').val(),
                order_type = typeof type !== 'undefined' ? type : 1,
                kind = $('.jsOrderKind').val(),
                order_kind = typeof kind !== 'undefined' ? kind : 1,
                nickname = $('input[name="nickname"]').val(),
                comment_content = $('input[name="comment_content"]').val(),
                note_status = $('input[name="note_status"]:checked').val(),
                sns_share = $('input[name="sns_share"]:checked').val(),
                discard_flg = $('input[name="discard_flg"]:checked').val(),
                status = $('input[name="status"]:checked').val(),
                bur_no = $('textarea[name="bur_no"]').val(),
                comment_plugin_id = document.getElementsByName("comment_plugin_id")[0].value,
                from_date = $('input[name="from_date"]').val(),
                to_date = $('input[name="to_date"]').val(),
                limit = $('select[name="item_limit"]').val(),
                pager = $('.jsListPager'),
                params = {
                    data: {
                        page: CommentListService.cur_page,
                        comment_plugin_id: comment_plugin_id,
                        status: status,
                        discard_flg: discard_flg,
                        note_status: note_status,
                        sns_share: sns_share,
                        nickname: nickname,
                        comment_content: comment_content,
                        bur_no: bur_no,
                        order_type: order_type,
                        order_kind: order_kind,
                        from_date: from_date,
                        to_date: to_date,
                        page_limit: limit
                    },
                    url: 'admin-comment/api_load_comment_list.json',
                    type: 'GET',
                    beforeSend: function () {
                        $(pager).html("<p><strong>件数計算中...</strong></p>");
                    },
                    success: function(response) {
                        if (response.result == 'ok') {
                            $(pager).replaceWith(response.html.pager);
                            CommentListService.changePagerOrderName();
                            $('.jsCommentList').replaceWith(response.html.comment_list);
                            CommentListService.resetCommonData();
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(params);
        },
        saveCommentNote: function(target) {
            var form = $(target).closest('.jsNoteForm'),
                note = $(form).find('textarea[name="note"]').val(),
                csrf_token = document.getElementsByName("csrf_token")[0].value,
                cu_relation_id = CommentListService.cur_cu_relation_id,
                params = {
                    data: {
                        note: note,
                        csrf_token: csrf_token,
                        cu_relation_id: cu_relation_id
                    },
                    url: 'admin-comment/api_save_comment_user_relation_note.json',
                    type: 'POST',
                    success: function(response) {
                        if (response.result == 'ok') {
                            var comment_container = $('#comment_container_' + CommentListService.cur_cu_relation_id),
                                note_icon = $(comment_container).find('.jsNoteIcon'),
                                note_content = $(comment_container).find('.jsNoteContent');;

                            if (response.data.is_removed == "1") {
                                $(note_icon).removeClass('on');
                                if (!$(note_icon).hasClass('off')) {
                                    $(note_icon).addClass('off');
                                }

                                $(note_content).hide();
                            } else {
                                $(note_icon).removeClass('off');
                                if (!$(note_icon).hasClass('on')) {
                                    $(note_icon).addClass('on');
                                }

                                $(note_content).show();
                            }
                            $(note_content).html(note);

                            CommentListService.cur_cu_relation_id = null;
                            Brandco.unit.closeModalFlame(target);
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(params);
        },
        toggleStatus: function(target) {
            var comment_container = $(target).closest('.jsCommentContainer'),
                cu_relation_id = $(comment_container).attr('data-id'),
                csrf_token = document.getElementsByName("csrf_token")[0].value,
                status = $(target).hasClass('on') ? 2 : 1,
                params = {
                    data: {
                        status: status,
                        csrf_token: csrf_token,
                        cu_relation_id: cu_relation_id
                    },
                    url: 'admin-comment/api_toggle_comment_user_relation_status.json',
                    type: 'POST',
                    success: function(response) {
                        if (response.result == 'ok') {
                            $(target).toggleClass('on off');
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(params);
        },
        updateStatuses: function() {
            var cur_form_status = $('input[name="cur_form_status"]:checked').val(),
                cur_form_ids = $('.jsItemCheck:checked').map(function(){ return $(this).val(); }).get(),
                csrf_token = document.getElementsByName("csrf_token")[0].value,
                params = {
                    data: {
                        csrf_token: csrf_token,
                        cur_form_ids: cur_form_ids,
                        cur_form_status: cur_form_status
                    },
                    url: 'admin-comment/api_update_comment_user_relation_statuses.json',
                    type: 'POST',
                    success: function(response) {
                        if (response.result == 'ok') {
                            var switch_class = "on",
                                not_switch_class = "off";

                            if (cur_form_status == 2) {
                                switch_class = "off";
                                not_switch_class = "on"
                            }

                            $('.jsItemCheck:checked').each(function() {
                                var toggle_class = $(this).closest('.jsCommentContainer').find('.jsToggleStatus');

                                $(toggle_class).removeClass(not_switch_class);
                                if (!$(toggle_class).hasClass(switch_class)) {
                                    $(toggle_class).addClass(switch_class);
                                }

                                $(this).prop('checked', false);
                            });

                            $('.jsItemCheckAll').prop('checked', false);
                            CommentListService.updateCounter();
                        }
                    }
                };
            Brandco.api.callAjaxWithParam(params);
        },
        showExposedText: function(target) {
            var text_container = $(target).closest('.jsTextContainer');

            $(text_container).find('.exposed_text_show').show();
            $(text_container).find('.exposed_text_hide').hide();
            if ($(text_container).find('.jsHideExposedText').length < 1) {
                $(text_container).append('<span class="jsHideExposedText exposed_text_show"><a href="javascript:void(0);">less</a></span>');
            }
        },
        hideExposedText: function(target) {
            var text_container = $(target).closest('.jsTextContainer');

            $(text_container).find('.exposed_text_show').hide();
            $(text_container).find('.exposed_text_hide').show();
            $(target).remove();
        },
        displayContent: function(target) {
            var type = $(target).attr('data-type');

            if (type == "show") {
                $('.exposed_text_show').show();
                $('.exposed_text_hide').hide();
                $('.exposed_text_hide').closest('.jsTextContainer').each(function() {
                    if ($(this).find('.jsHideExposedText').length < 1) {
                        $(this).append('<span class="jsHideExposedText exposed_text_show"><a href="javascript:void(0);">less</a></span>');
                    }
                });
            } else {
                $('.exposed_text_show').hide();
                $('.exposed_text_hide').show();
                $('.jsHideExposedText').remove();
            }
        },
        isChecked: function() {
            if ($('.jsItemCheck:checked').length) {
                return true;
            }

            alert('対象投稿を選択してください！');
            return false;
        },
        updateCounter: function() {
            var target = $('.jsTargetItemCounter'),
                to_value = $('.jsItemCheck:checked').length;

            $(target).prop('Counter', $(target).html())
                .animate({
                    Counter: to_value
                }, {
                    duration: 200,
                    easing: 'swing',
                    step: function (now) {
                        $(this).text(Math.ceil(now));
                    }
                });
        },
        resetCommonData: function() {
            $('.jsItemCheckAll').prop('checked', false);
            CommentListService.updateCounter();
        },
        downloadData: function(target) {
            var params = {
                order_type : $('input[name="order_type"]:checked').val(),
                order_kind : $('select[name="order_kind"]').val(),
                nickname : $('input[name="nickname"]').val(),
                comment_content : $('input[name="comment_content"]').val(),
                note_status : $('input[name="note_status"]:checked').val(),
                sns_share : $('input[name="sns_share"]:checked').val(),
                discard_flg : $('input[name="discard_flg"]:checked').val(),
                status : $('input[name="status"]:checked').val(),
                bur_no : $('textarea[name="bur_no"]').val(),
                comment_plugin_id : document.getElementsByName("comment_plugin_id")[0].value,
                from_date : $('input[name="from_date"]').val(),
                to_date : $('input[name="to_date"]').val()
            };

            window.location.href = $(target).attr('data-url') + '?' + jQuery.param(params);
        },
        changePagerOrderName: function() {
            //id to change name
            var current_id = Math.floor((Math.random() * 100) + 1);

            var order_type_id = $('.jsOrderType:checked').attr('id');
            $('.jsOrderType').removeAttr('checked');

            $('.jsListPager').each(function() {
                var input_name = 'order_type_' + current_id;
                var select_name = 'order_kind_' + current_id;
                $(this).find('input').attr('name',input_name);
                $(this).find('select').attr('name',select_name);
                $(this).find('#'+ order_type_id).prop('checked',true);

                current_id++;
            });
        }
    }
})();

$(document).ready(function() {
    CommentListService.loadCommentList();

    $(".jsDate").datepicker();

    $('.jsSettingContTile').click(function(){
        var trigger = $(this);
        var target = trigger.parents('.jsSettingContWrap').find('.jsSettingContTarget');

        if(trigger.hasClass('close')) {
            target.slideDown(200, function() {
                trigger.removeClass('close');
            });
        }else{
            target.slideUp(200, function() {
                trigger.addClass('close');
            });
        }
    });

    $(document).on('click', '.jsPager', function() {
        CommentListService.cur_page = $(this).attr('data-page');
        CommentListService.loadCommentList();
    });

    $(document).on('click', '.jsUpdateItemList', function() {
        CommentListService.loadCommentList();
    });

    $(document).on('change', '.jsOrderKind', function() {
        $('.jsOrderKind').val($(this).val());
        CommentListService.loadCommentList();
    });

    $(document).on('change', '.jsOrderType', function() {
        $('.jsOrderType').val($(this).val());
        CommentListService.loadCommentList();
    });

    $(document).on('click', '.jsOpenNoteModal', function() {
        var comment_container = $(this).closest('.jsCommentContainer');
        $('#note_modal').find('textarea[name="note"]').val($(this).find('.jsNoteContent').html());

        CommentListService.cur_cu_relation_id = $(comment_container).attr('data-id');
        Brandco.unit.showModal(this);
        return false;
    });

    $(document).on('click', '.jsSubmitNoteForm', function() {
        CommentListService.saveCommentNote(this);
    });

    $(document).on('click', '.jsToggleStatus', function() {
        CommentListService.toggleStatus(this);
    });

    $(document).on('change', '.jsItemCheckAll', function() {
        $('.jsItemCheck').not(':disabled').prop('checked', this.checked);
        CommentListService.updateCounter();
    });

    $(document).on('change', '.jsItemCheck', function() {
        if (!this.checked && $('.jsItemCheckAll').is(':checked')) {
            $('.jsItemCheckAll').prop('checked', false);
        }

        if ($('.jsItemCheck:checked').length == $('.jsItemCheck').not(':disabled').length) {
            $('.jsItemCheckAll').prop('checked', true);
        }

        CommentListService.updateCounter();
    });

    $(document).on('click', ('.jsApprovalFormSubmit'), function() {
        if (CommentListService.isChecked() == false) return false;

        var submit_msg = $('input[name="cur_form_status"]:checked').val() == '1' ? '公開' : '非公開に';
        if (confirm('チェック済みのコメントを' + submit_msg + 'しますか？')) {
            CommentListService.updateStatuses();
        }
    });

    $(document).on('click', '.jsResetSearchCondition', function() {
        $('select[name="order_kind"]').val("1");
        $('input[name="order_type"][value="1"]').prop('checked', true);
        $('input[name="note_status"][value="0"]').prop('checked', true);
        $('input[name="sns_share"][value="0"]').prop('checked', true);
        $('input[name="status"][value="0"]').prop('checked', true);
        $('input[name="order_type"][value="1"]').prop('checked', true);
        $('input[name="nickname"]').val('');
        $('input[name="comment_content"]').val('');
        $('textarea[name="bur_no"]').val('');
        $('input[name="from_date"]').val('');
        $('input[name="to_date"]').val('');

        if($('.jsDiscardFlgSearchCondition').length !== 0) {
            $('input[name="discard_flg"][value="1"]').prop('checked', false);
            $('.jsDiscardFlgSearchCondition').hide();
        }

        CommentListService.loadCommentList();
    });

    $(document).on('click', '.jsSeeMore', function() {
        CommentListService.showExposedText(this);
    });

    $(document).on('click', '.jsHideExposedText', function() {
        CommentListService.hideExposedText(this);
    });

    $(document).on('click', '.jsAllContentDisplay', function() {
        CommentListService.displayContent(this);
    });

    $(document).on('change', '.jsStatusSearchCondition', function() {
        var discard_flg_search = $('.jsDiscardFlgSearchCondition');
        if ($(this).val() == 2) {
            $(discard_flg_search).show();
        } else {
            $(discard_flg_search).find('input').prop('checked', false);
            $(discard_flg_search).hide();
        }
    });

    $(document).on('click', '.jsDataDownload', function() {
        CommentListService.downloadData(this);
    });

    // Binding input event to dynamically created elements
    $(document).on('input', '.jsReplaceLbComma', function() {
        var txt = $(this).val();
        $(this).val(txt.replace(/\r?\n/g,','));
    });
});