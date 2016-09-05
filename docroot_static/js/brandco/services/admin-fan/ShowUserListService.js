var serialize = {};
var ShowUserListService = (function(){
    return{
        get_fan: function (page_no, tab_no, query_flg, limit) {
            var url = $('*[name=list_url]').attr('value');
            if (!limit || limit == 'undefine') {
                limit = $('select[name="fan_limit"]').val();
            }
            var data = {page_no:page_no,
                tab_no:tab_no,
                query_flg:query_flg,
                limit: limit
            };
            var tableMoving = false;
            var param = {
                data: data,
                type: 'GET',
                url: url,
                success: function(data) {
                    if($('.userListWrap')[0]) {
                        $('article[data-input_place="brand_user"]').find('.hd1').next().replaceWith(data.html);
                    } else {
                        $('article[data-input_place="brand_user"]').append(data.html);
                    }
                    ShowUserListService.showSocialLikeAlertModal();
                    ShowUserListService.showUserListCount(page_no, limit, '', '', tab_no);
                    ShowUserListService.store_element();
                    ShowUserListService.apply_checkbox_store_element();
                    ShowUserListService.resetNotClickClass();
                    $.datepicker.setDefaults($.datepicker.regional['ja']);
                    $('.jsDate').datepicker();
                    FanRateService.fan_rate();
                    $('.itemTableWrap').mousemove(function(e){
                        var width = $('.itemTableWrap').width();
                        var tableWrapOffset = $('.itemTableWrap').offset().left;
                        var tableOffset = $('.itemTable').offset().left;
                        if(!tableMoving && e.clientX - tableWrapOffset < 50) {                //left
                            tableMoving = true;
                            $('.itemTableWrap').animate({scrollLeft: tableWrapOffset - tableOffset - 150}, 200, 'linear', function(){
                                tableMoving = false;
                            });
                        }
                        if(!tableMoving && e.clientX - tableWrapOffset > width - 50) {        //right
                            tableMoving = true;
                            $('.itemTableWrap').animate({scrollLeft: tableWrapOffset - tableOffset + 150}, 200, 'linear', function(){
                                tableMoving = false;
                            });
                        }
                    });
                }
            };
            if(query_flg) {
                // 検索結果の取得では起動中のoverlayを止める
                Brandco.api.callAjaxWithParam(param, false, true);
            } else {
                // 画面起動時は通常のoverlayを使用
                Brandco.api.callAjaxWithParam(param);
            }
        },
        search_fan: function (search_type, search_no, order, sns_action_key) {
            var page_info = $('[name="page_info"]').attr('value').split("/");
            // 「絞り込み」を押下した場合は検索項目一覧(search_no:1)で検索したのか、カラム(search_no:2)で検索したのか分ける
            if (search_no && search_no >= 1) {
                var data = $('#frmSearchFan').serialize() + "&search_no=" + search_no;
            } else {
                var csrf_token = $('input[name="csrf_token"]:first').val();
                var data = {} + "&csrf_token=" + csrf_token + "&order=" + order;
                if(sns_action_key){
                    data +=  "&sns_action_key=" + sns_action_key;
                }
            }
            var url = $('*[name=search_url]').attr('value');
            var param = {
                data: data + "&search_type=" + search_type,
                type: 'POST',
                url: url,
                success: function(json) {
                    if (json.result === "ok") {
                        ShowUserListService.get_fan(null, page_info[1], true);
                    } else {
                        $.unblockUI();
                        var search_err = 0;
                        $('p.attention1').remove();
                        $.each(json.errors ,function(i, value) {
                            if(i.match(/^searchError\//)) {
                                var search_key = i.split("searchError/")[1];
                                $('div.sortBox.jsAreaToggleTarget[data-search_type="' + search_key + '"]').find('.boxCloseBtn').after('<p class="attention1">' + value + '</p>');
                                search_err = 1;
                            }
                        });
                        if(!search_err) {
                            alert("操作が失敗しました");
                        }
                    }
                }
            };
            // 検索結果を返すタイミングではoverlayを止めない(GETで止める)
            Brandco.api.callAjaxWithParam(param, true, false);
        },
        store_element: function () {
            var serialize_array = $('#frmSearchFan').serializeArray();
            serialize = {}; // 再度初期化
            $.each(serialize_array ,function(key, val) {
                serialize[val.name] = val.value;
            });
        },
        apply_store_element: function () {
            ShowUserListService.apply_input_store_element();
            ShowUserListService.apply_checkbox_store_element();
            ShowUserListService.apply_switch_store_element();
            ShowUserListService.apply_option_store_element();
        },
        apply_input_store_element: function () {
            $('input').each(function(){
                if($(this).attr('name') in serialize) {
                    $(this).val(serialize[$(this).attr('name')]);
                }
            });
        },
        apply_checkbox_store_element: function () {
            $(':checkbox').each(function () {
                if ($(this).attr('name') == 'user[]' || $(this).hasClass('jsSegmentCheckbox')) {
                    return true;
                }
                var target_wrap = $(this).parents('.jsCheckToggleWrap')[0];
                if ($(this).attr('name') in serialize) {
                    $(this).prop('checked', true);
                    if($(this).hasClass('jsNotToggleTarget')) {
                        return true;
                    }
                    $(target_wrap).find('.jsCheckToggleTarget').show();
                } else {
                    $(this).prop('checked', false);
                    if($(this).hasClass('jsNotToggleTarget')) {
                        return true;
                    }
                    $(target_wrap).find('.jsCheckToggleTarget').hide();
                }
            });
        },
        apply_switch_store_element: function () {
            $('a.toggle_switch').each(function(){
                var target_name = 'switch_type/' + $(this).attr('data-switch_type');
                if(target_name in serialize) {
                    if(serialize[target_name] == '1') {
                        if($(this).hasClass('right')) {
                            $(this).removeClass('right').addClass('left');
                        }
                    } else {
                        if($(this).hasClass('left')) {
                            $(this).removeClass('left').addClass('right');
                        }
                    }
                }
            });
        },
        apply_option_store_element: function () {
            $('select[data-time_type]').each(function(){
                if($(this).attr('data-time_type') == 'hh') {
                    var hh = serialize[$(this).attr('name')];
                    $(this).find('option[value="' + hh + '"]').prop('selected', true);
                }
                if($(this).attr('data-time_type') == 'mm') {
                    var mm = serialize[$(this).attr('name')];
                    $(this).find('option[value="' + mm + '"]').prop('selected', true);
                }
            });
        },
        toggleAnimation: function (target) {
            $('.jsAreaToggleTarget').not(target).fadeOut(200, function() {
                setTimeout(function(){
                    ShowUserListService.apply_store_element();
                    ShowUserListService.deleteAttention();
                },300)
            });
            if(target.is(':hidden')) {
                target.fadeIn(200);
            } else {
                target.fadeOut(200);
            }
        },
        deleteToggle: function (target) {
            target.stop(true, true).fadeToggle(200,function() {
                setTimeout(function(){
                    ShowUserListService.apply_store_element();
                    ShowUserListService.deleteAttention();
                },300)
            });
        },
        deleteAttention: function () {
            $('p.attention1').remove();
        },
        csvDownload: function(target) {
            if(!target.hasClass('not_click')) {
                target.addClass('not_click');
                window.location.href = target.attr('data-download_url');
            }
        },
        resetNotClickClass: function () {
            if($('a[data-download_url]').hasClass('not_click')) {
                $('a[data-download_url]').removeClass('not_click');
            }
        },
        showUserListCount: function(page_no, limit, action_id, cp_id, tab_no) {
            var fan_count = $('input[name="fan_count"]').val(),
                page_sent_user_count = $('input[name="page_sent_user_count"]').val();
            var data = {
                page_no: page_no,
                limit: limit,
                action_id: action_id,
                cp_id: cp_id,
                tab_no: tab_no,
                fan_count: fan_count ? fan_count : 0,
                page_sent_user_count: page_sent_user_count ? page_sent_user_count : 0
            };
            var param = {
                data: data,
                type: 'GET',
                url: 'admin-cp/api_show_user_list_count.json',
                success: function (json) {
                    $('.pager1').children("p:not('.showItemNum')").replaceWith(json.html[0]);
                    $('.userListMessage').html(json.html[1]);
                }
            };
            Brandco.api.callAjaxWithParam(param);
        },
        showSocialLikeAlertModal: function() {
            if($('#socialLikeAlert').hasClass('jsShowModal')) {
                Brandco.unit.openModal('#socialLikeAlert');
            }
        }
    }
})();

$(document).ready(function(){

    $(document).on('click', '.boxCloseBtn', function () {
        ShowUserListService.deleteToggle($(this).parents('.jsAreaToggleWrap').find('.jsAreaToggleTarget'));
    });
        $(document).on('change', '.jscheckIsRetweetCount', function () {
            var element_id = $(this).data("element_id");
            // console.log(element_id);return; 
            if(this.checked) {
                $('input[name = "search_tw_tweet_retweet_count/'+element_id+'/from"]').prop({disabled: false});
                $('input[name = "search_tw_tweet_retweet_count/'+element_id+'/to"]').prop({disabled: false});
            }else{
                $('input[name = "search_tw_tweet_retweet_count/'+element_id+'/from"]').prop({disabled: true});
                $('input[name = "search_tw_tweet_retweet_count/'+element_id+'/to"]').prop({disabled: true});
            }
        });

        $(document).on('change', '.jscheckIsReplyCount', function () {
            var element_id = $(this).data("element_id");
            if(this.checked) {
                $('input[name = "search_tw_tweet_reply_count/'+element_id+'/from"]').prop({disabled: false});
                $('input[name = "search_tw_tweet_reply_count/'+element_id+'/to"]').prop({disabled: false});
            }else{
                $('input[name = "search_tw_tweet_reply_count/'+element_id+'/from"]').prop({disabled: true});
                $('input[name = "search_tw_tweet_reply_count/'+element_id+'/to"]').prop({disabled: true});
            }
        });

    $(document).on('change', '.jscheckIsLikeCount', function () {
        var element_id = $(this).data("element_id")
        if(this.checked) {
            $('input[name = "search_fb_posts_like_count/'+element_id+'/from"]').prop({disabled: false});
            $('input[name = "search_fb_posts_like_count/'+element_id+'/to"]').prop({disabled: false});
        }else{
            $('input[name = "search_fb_posts_like_count/'+element_id+'/from"]').prop({disabled: true});
            $('input[name = "search_fb_posts_like_count/'+element_id+'/to"]').prop({disabled: true});
        }
    });

    $(document).on('change', '.jscheckIsCommentCount', function () {
        var element_id = $(this).data("element_id")
        if(this.checked) {
            $('input[name = "search_fb_posts_comment_count/'+element_id+'/from"]').prop({disabled: false});
            $('input[name = "search_fb_posts_comment_count/'+element_id+'/to"]').prop({disabled: false});
        }else{
            $('input[name = "search_fb_posts_comment_count/'+element_id+'/from"]').prop({disabled: true});
            $('input[name = "search_fb_posts_comment_count/'+element_id+'/to"]').prop({disabled: true});
        }
    });

    $(document).on('click', '.toggle_switch[data-switch_type]', function () {
        var switch_type = $(this).attr('data-switch_type');
        if ($(this).hasClass('right')) {
            $(this).removeClass('right').addClass('left');
            $('input[name="switch_type/' + switch_type + '"]').val('1');
        } else {
            $(this).removeClass('left').addClass('right');
            $('input[name="switch_type/' + switch_type + '"]').val('2');
        }
    });

    $(document).on('click', 'a[data-download_url]', function () {
        ShowUserListService.csvDownload($(this));
    });

    $(document).on('click','a[data-close_modal_id]',function(){
        if($('div.checkedUserAction.jsAreaToggleTarget').length > 0) {
            $('div.checkedUserAction.jsAreaToggleTarget').fadeOut(200);
        }
        Brandco.unit.closeModal($(this).attr('data-close_modal_id'));
        if($(this).attr('data-close_modal_id') == 7) {
            ShowUserListService.resetNotClickClass();
        }
    });

    $(document).on('change', '#have_duplicate_address_checkbox', function(){
        if ($(this).is(':checked')) {
            $("[name='search_duplicate_address_from']").removeAttr("disabled");
            $("[name='search_duplicate_address_to']").removeAttr("disabled");
        } else {
            $("[name='search_duplicate_address_from']").attr("disabled", "disabled");
            $("[name='search_duplicate_address_to']").attr("disabled", "disabled");
        }
    });

    if(typeof(ShowCpUserListService) === 'undefined') {
        ShowUserListService.get_fan(null, null, null);

        $(document).on('click', 'a[data-search_type]', function () {
            ShowUserListService.search_fan($(this).attr('data-search_type'), $(this).attr('data-search_no'), '');
        });

        $(document).on('click', 'a[data-clear_type]', function () {
            ShowUserListService.search_fan($(this).attr('data-clear_type'), '', '',$(this).attr('data-sns_action_key'));
        });

        $(document).on('click', '.jsAreaToggle', function () {
            if ($(this).hasClass('iconBtnSort') || $(this).hasClass('btnArrowB1')) {
                ShowUserListService.toggleAnimation($(this).parents('.jsAreaToggleWrap').find('.jsAreaToggleTarget'), $(this).attr('data-check_no'));
            }
        });

        $(document).on('click', 'a[data-order]', function () {
            ShowUserListService.search_fan($(this).closest('div[data-search_type]').attr('data-search_type'), '', $(this).attr('data-order'));
        });

        $(document).on('click', 'a[data-page]', function () {
            var page_info = $('[name="page_info"]').attr('value').split("/");
            ShowUserListService.get_fan($(this).attr('data-page'), page_info[1], null);
        });

        $(document).on('change', '.jsCheckToggle', function () {
            var target_wrap = $(this).parents('.jsCheckToggleWrap')[0];
            $(target_wrap).find('.jsCheckToggleTarget').slideToggle(300);
        });

        $(document).on('change', '.jsCountTarget', function () {
            var targetGroup = $(this).attr('name');
            ShowUserListService.countCheckbox(targetGroup);
        });

        $(document).on('click', '.jsOpenModal', function () {
            Brandco.unit.showModal(this);
        });

        $(document).on('click', '#applyFanLimit', function () {
            var page_info = $('[name="page_info"]').attr('value').split("/");
            ShowUserListService.get_fan(1, page_info[1], null, $('select[name="fan_limit"]').val());
        });
    }
});
