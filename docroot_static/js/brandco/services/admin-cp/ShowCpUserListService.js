var beforeTargetCount = 0;
var ShowCpUserListService = (function(){
    return{
        hidePopupFlg: false,
        get_fan: function (page_no, display_action_id, tab_no, query_flg, query_user, limit, join_user) {

            if (ShowCpUserListService.isChangedTarget() && !ShowCpUserListService.hidePopupFlg) {
                $('#getFanConfirm').removeData();
                $('#getFanConfirm').attr('data-cf_callback', 'get_fan').attr('data-cf_page_no', page_no).attr('data-cf_display_action_id', display_action_id).attr('data-cf_tab_no',tab_no).attr('data-cf_query_flg',query_flg).attr('data-cf_query_user',query_user);
                Brandco.unit.openModal("#modal5");
                return;
            }

            var cp_id = $('[name="cp_id"]').attr('value');
            var action_id = $('[name="action_id"]').attr('value');
            var url = $('*[name=list_url]').attr('value');
            var segment_condition_session = $('[name=segment_condition_session]').val();
            if (!page_no && !tab_no) {
                var shift_in_page = null;
            } else {
                var shift_in_page = true;
            }
            if (!limit || limit == 'undefine') {
                limit = $('select[name="fan_limit"]').val();
            }
            var data = {cp_id: cp_id,
                action_id: action_id,
                page_no: page_no,
                display_action_id: display_action_id,
                tab_no: tab_no,
                query_flg: query_flg,
                query_user: query_user,
                shift_in_page: shift_in_page,
                limit: limit,
                join_user: join_user,
                segment_condition_session: segment_condition_session
            };
            var tableMoving = false;
            var param = {
                data: data,
                type: 'GET',
                url: url,
                success: function (data) {
                    beforeTargetCount = 0; //グローバル変数を一旦リセット
                    $('p.checkedUser').attr('data-checked', '');
                    $('.campaignEditCont').html(data.html);
                    if(tab_no == 1 || tab_no == null) {
                        ShowUserListService.showSocialLikeAlertModal();
                    }
                    if (($('span.userNum').length === 0 || $('span.userNum').html() == '--') &&
                        ($('span.setTargetNum').length === 0 || $('span.setTargetNum').html() == '--')) {
                        ShowCpUserListService.delete_target_a_tag();
                    }
                    ShowCpUserListService.firefoxTabVertical();
                    ShowCpUserListService.store_element();
                    ShowUserListService.apply_checkbox_store_element();
                    ShowUserListService.resetNotClickClass();
                    $.datepicker.setDefaults($.datepicker.regional['ja']);
                    $('.jsDate').datepicker();
                    FanRateService.fan_rate();
                    if (query_user) {
                        ShowCpUserListService.change_header_status(query_user);
                    }
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
                    ShowCpUserListService.hidePopupFlg = false;
                    ShowUserListService.showUserListCount(page_no, limit, action_id, cp_id, tab_no);
                }
            };
            if (query_flg) {
                // 検索結果の取得では起動中のoverlayを止める
                Brandco.api.callAjaxWithParam(param, false, true);
            } else {
                // 画面起動時は通常のoverlayを使用
                Brandco.api.callAjaxWithParam(param);
            }
        },
        search_fan: function (search_type, search_no, order, sns_action_key) {

            if (ShowCpUserListService.isChangedTarget() && !ShowCpUserListService.hidePopupFlg) {
                $('#getFanConfirm').removeData();
                $('#getFanConfirm').attr('data-cf_callback', 'search_fan').attr('data-cf_search_type', search_type).attr('data-cf_search_no', search_no).attr('data-cf_order', order);
                Brandco.unit.openModal("#modal5");
                return;
            }
            ShowCpUserListService.hidePopupFlg = true;
            var cp_id = $('[name="cp_id"]').attr('value');
            var action_id = $('[name="action_id"]').attr('value');
            var page_info = $('[name="page_info"]').attr('value').split("/");
            // 「絞り込み」を押下した場合は検索項目一覧(search_no:1)で検索したのか、カラム(search_no:2)で検索したのか分ける
            if (search_no && search_no >= 1) {
                var data = $('#frmSearchFan' + search_no + '').serialize() + "&search_no=" + search_no;
            } else {
                var csrf_token = $('input[name="csrf_token"]:first').val();
                var data = {} + "&csrf_token=" + csrf_token + "&order=" + order;
                if(sns_action_key){
                    data +=  "&sns_action_key=" + sns_action_key;
                }
            }
            data = data + "&cp_id=" + cp_id + "&action_id=" + action_id + "&search_type=" + search_type;
            var url = $('*[name=search_url]').attr('value');
            var param = {
                data: data,
                type: 'POST',
                url: url,
                success: function (json) {
                    if (json.result === "ok") {
                        ShowCpUserListService.get_fan(null, page_info[1], page_info[2], true, null, null, null);
                    } else {
                        $.unblockUI();
                        var search_err = 0;
                        $('p.attention1').remove();
                        $.each(json.errors, function (i, value) {
                            if (i.match(/^searchError\//)) {
                                var search_key = i.split("searchError/")[1];
                                $('div.sortBox.jsAreaToggleTarget[data-search_type="' + search_key + '"]').find('.boxCloseBtn').after('<p class="attention1">' + value + '</p>');
                                search_err = 1;
                            }
                        });
                        if (!search_err) {
                            alert("操作が失敗しました");
                        }
                    }
                    ShowCpUserListService.hidePopupFlg = false;
                }
            };
            // 検索結果を返すタイミングではoverlayを止めない(GETで止める)
            Brandco.api.callAjaxWithParam(param, true, false);
        },
        update_fan_target: function (check_no) {
            ShowCpUserListService.hidePopupFlg = true;
            var select_all_users = $('p.checkedUser').attr('data-checked');
            var cp_id = $('[name="cp_id"]').attr('value');
            var action_id = $('[name="action_id"]').attr('value');
            var page_info = $('[name="page_info"]').attr('value').split("/");
            var update_type = check_no == '3' ? '3' : $('input:radio[name="checkedUser1/' + check_no + '"]:checked').val(); // ランダム抽選を考慮
            var data = $('#formAddTarget').serialize();
            var url = $('*[name=update_target_url]').attr('value');
            var random_target_num = 0;
            var duplicated_address_check = 0;

            //メッセージ送信権限があるかどうかチェックする
            var has_send_message_permission = $('input[name="has_send_message_permission"]').attr('value');

            var has_fix_target_step = $('[name="has_fix_target_step"]').attr('value');
            var fix_target = $('[name="fix_target_type"]').attr('value');

            if (fix_target) {
                update_type = fix_target == '1' ? '4' : '5';
            }
            if (check_no == '3') {
                // 自動抽選の場合の対応
                random_target_num = $('#random_text').val();
                duplicated_address_check = $('#duplicated_address_check').prop('checked') ? 1 : 0;
            }
            var param = {
                data: data + "&cp_id=" + cp_id + "&action_id=" + action_id + "&update_type=" + update_type + "&select_all_users=" + select_all_users +
                        "&random_target_num=" + random_target_num + "&duplicated_address_check=" + duplicated_address_check,
                type: 'POST',
                url: url,
                success: function (json) {
                    if (json.result === "ok") {
                        ShowCpUserListService.get_fan(page_info[0], page_info[1], page_info[2], true, null, null, null);
                        $.each(json.data, function (i, value) {
                            if (has_fix_target_step == '1') {
                                //対象に入れたユーザーカウント
                                if($('span.userNum').length == 0){
                                    var before_set_target_count = parseInt($('span.setTargetNum').html() == '--' ? 0 : $('span.setTargetNum').html().replace(/,/g, ""));
                                }else{
                                    var before_set_target_count = parseInt($('span.userNum').html() == '--' ? 0 : $('span.userNum').html().replace(/,/g, ""));
                                }
                                var before_target_count = 0;
                            } else {
                                var before_target_count = parseInt($('span.userNum').html() == '--' ? 0 : $('span.userNum').html().replace(/,/g, ""));
                            }

                            if (i == '1' || i == '3' || i == '4') {
                                if(has_fix_target_step == '1'){
                                    var html_class_name = i == "4" ? "userNum" : "setTargetNum";
                                    var small_text = i !== "4" ? "送信対象(候補)" : '送信対象(確定)';
                                    $('.showTarget').children('span').attr('class', html_class_name);
                                    $('.showTarget').children('small').text(small_text);
                                    console.log(small_text);
                                }
                                //当選者確定ボタンがあるかどうかチェックする
                                if (has_fix_target_step != '1' || i == '4') {
                                    // 三桁区切りに
                                    var after_target_count = Brandco.helper.conversion_comma3(before_target_count + parseInt(value));
                                    $('span.userNum').html(after_target_count == 0 ? '--' : after_target_count);
                                } else {
                                    var after_set_target_count = before_set_target_count + parseInt(value);
                                    $('span.setTargetNum').html(after_set_target_count == 0 ? '--' : after_set_target_count);
                                }

                                ShowCpUserListService.add_target_a_tag();

                                // ヘッダーのステータス変更
                                if ($('header.campaignEditHeader').children('div[data-header_type="userList"]').hasClass('campaignEditItem') ||
                                        $('header.campaignEditHeader').children('div[data-header_type="userList"]').hasClass('campaignEditItem_finished')) {
                                    // 対象ユーザ選択のチェックを入れる
                                    if (after_target_count > 0 || after_set_target_count > 0) {
                                        $('header.campaignEditHeader').children('div[data-header_type="userList"]').attr('class', 'campaignEditItem_finished');
                                    }
                                    if ($('[data-header_type="message"]').hasClass('campaignEditItem_finished') && $('[data-header_type="option"]').hasClass('campaignEditItem_finished')) {
                                        if ($('input[name="shipping_method_present"]').val() == 1 || $('input[name="coupon_action"]').val() == 1) {
                                            return;
                                        }
                                        if (after_target_count > 0 && has_send_message_permission) {
                                            $('span.jsAnnounceBtn').parent('.btn3_area[data-btn_type="send_mail"]').hide();
                                            $('a.jsAnnounceBtn').parent('.btn3_area[data-btn_type="send_mail"]').show();
                                            $('p[data-btn_type="send_mail"]').find('.jsOpenModal').css('cursor', 'pointer');
                                        } else {
                                            $('a.jsAnnounceBtn').parent('.btn3_area[data-btn_type="send_mail"]').hide();
                                            $('span.jsAnnounceBtn').parent('.btn3_area[data-btn_type="send_mail"]').show();
                                            $('p[data-btn_type="send_mail"]').find('.jsOpenModal').css('cursor', 'default');
                                        }
                                    }
                                } else if($('header.campaignEditHeaderShipping').length == 1 || $('header.campaignEditItemShipping_finished').length == 1) {
                                    $('header.campaignEditHeaderShipping').children('div[data-header_type="userList"]').attr('class', 'campaignEditItemShipping_finished');
                                    if (after_target_count > 0 && has_send_message_permission) {
                                        $('span.jsAnnounceBtn').parent('.btn3_area[data-btn_type="send_mail"]').hide();
                                        $('a.jsAnnounceBtn').parent('.btn3_area[data-btn_type="send_mail"]').show();
                                        $('p[data-btn_type="send_mail"]').find('.jsOpenModal').css('cursor', 'pointer');
                                    } else {
                                        $('a.jsAnnounceBtn').parent('.btn3_area[data-btn_type="send_mail"]').hide();
                                        $('span.jsAnnounceBtn').parent('.btn3_area[data-btn_type="send_mail"]').show();
                                        $('p[data-btn_type="send_mail"]').find('.jsOpenModal').css('cursor', 'default');
                                    }
                                }
                            } else {
                                if(has_fix_target_step != '1'){
                                    var after_target_count = before_target_count - parseInt(value);
                                    var user_num = after_target_count == 0 ? '--' : Brandco.helper.conversion_comma3(after_target_count);

                                    if(after_target_count == 0){
                                        $('.showTarget').children('small').text("送信対象");
                                    }

                                    $('span.userNum').html(user_num);
                                } else if(has_fix_target_step == '1' && i != "5") {
                                    var after_target_count = before_set_target_count - parseInt(value);
                                    var target_num = parseInt(after_target_count) == 0 ? '--' : Brandco.helper.conversion_comma3(after_target_count);

                                    if(after_target_count == 0){
                                        $('.showTarget').children('small').text("送信対象");
                                    }

                                    $('span.setTargetNum').html(target_num);
                                } else {
                                    $('.showTarget').children('span').attr('class', "setTargetNum");
                                    $('.showTarget').children('small').text("送信対象(候補)");
                                    var after_target_count = 0;
                                }

                                // ヘッダーのステータス変更
                                if (after_target_count == 0) {
                                    if ($('header.campaignEditHeader').children('div[data-header_type="userList"]').hasClass('campaignEditItem_finished')) {
                                        // 対象ユーザ選択のチェックを外す
                                        if(i != '5'){
                                            $('header.campaignEditHeader').children('div[data-header_type="userList"]').attr('class', 'campaignEditItem');
                                        }
                                        if ($('p[data-btn_type]').find('a')[0]) {
                                            if ($('input[name="shipping_method_present"]').val() == 1 || $('input[name="coupon_action"]').val() == 1) {
                                                return;
                                            }
                                            $('a.jsAnnounceBtn').parent('.btn3_area[data-btn_type="send_mail"]').hide();
                                            $('span.jsAnnounceBtn').parent('.btn3_area[data-btn_type="send_mail"]').show();
                                            $('p[data-btn_type="send_mail"]').find('.jsOpenModal').css('cursor', 'default');
                                        }
                                    } else if ($('header.campaignEditHeaderShipping').length == 1) {
                                        $('header.campaignEditHeaderShipping').children('div[data-header_type="userList"]').attr('class', 'campaignEditItemShipping');
                                        $('a.jsAnnounceBtn').parent('.btn3_area[data-btn_type="send_mail"]').hide();
                                        $('span.jsAnnounceBtn').parent('.btn3_area[data-btn_type="send_mail"]').show();
                                        $('p[data-btn_type="send_mail"]').find('.jsOpenModal').css('cursor', 'default');
                                    }
                                }
                            }
                        });
                    } else {
                        $.unblockUI();
                        var search_err = 0;
                        $('p.attention1').remove();
                        $.each(json.errors, function (i, value) {
                            if (i == 'updateTargetError') {
                                $('div.checkedUserAction.jsAreaToggleTarget[style="display: block;"]').prepend('<p class="attention1">' + value + '</p>');
                                search_err = 1;
                            }
                        });
                        if (!search_err) {
                            alert("操作が失敗しました");
                        }
                    }
                    if ($('#modal3')[0]) {
                        Brandco.unit.closeModal(3);
                    }
                    if ($('#modal4')[0]) {
                        Brandco.unit.closeModal(4);
                    }
                    ShowCpUserListService.hidePopupFlg = false;
                }
            };
            // 対象に入れるタイミングではoverlayを止めない(GETで止める)
            Brandco.api.callAjaxWithParam(param, true, false);
        },
        store_element: function () {
            var serialize_array1 = $('#frmSearchFan1').serializeArray();
            var serialize_array2 = $('#frmSearchFan2').serializeArray();
            serialize = {}; // 再度初期化
            $.each(serialize_array1, function (key, val) {
                serialize[val.name] = val.value;
            });
            $.each(serialize_array2, function (key, val) {
                if (!serialize[val.name]) {
                    serialize[val.name] = val.value;
                }
            });
        },
        toggleAnimation: function (target) {
            $('.jsAreaToggleTarget[style="display: block;"]').not(target).fadeOut(200, function () {
                setTimeout(function () {
                    ShowUserListService.apply_store_element();
                    ShowCpUserListService.deleteAttention();
                }, 300)
            });
            if (target.is(':hidden')) {
                target.fadeIn(200);
            } else {
                target.fadeOut(200);
            }
        },
        deleteAttention: function () {
            $('p.attention1').remove();
        },
        countCheckbox: function () {
            var updateCountNum = $('.jsCountTarget:checked[name="user[]"]').not(':disabled').parents('li.checkedUser').length; //ページ内で送信対象に対するチェック数
            var insertCountNum = $('.jsCountTarget:checked[name="user[]"]').not(':disabled').parents('li').not('.checkedUser').length; //ページ内で送信対象外に対するチェック数
            var countNum = updateCountNum + insertCountNum;

            if ($('p.checkedUser').attr('data-checked')) {
                var updateCountInOtherPage = parseInt($('span.userNum').html()) - updateCountNum;//ページ外で送信対象に対するチェック数
                var insertCountInOtherPage = parseInt($('input[name="all_not_sent_user_count"]').val()) - (updateCountNum + updateCountInOtherPage) - insertCountNum;//ページ外で送信対象に対するチェック数
            }

            if ($('p.checkedUser').attr('data-checked')) {
                $('.jsCountArea').html($('a[data-select="select_all_users"]').find('.num').html());
            } else {
                if (countNum == 0) {
                    $('.jsCountArea').html('---');
                } else {
                    $('.jsCountArea').html(countNum);
                }
            }
            ShowCpUserListService.switchUpdateButtonStatus(countNum, insertCountNum, insertCountInOtherPage, updateCountNum, updateCountInOtherPage);
            ShowCpUserListService.switchCheckboxText(insertCountNum, insertCountInOtherPage, updateCountNum, updateCountInOtherPage);

            beforeTargetCount = countNum;
        },
        firefoxTabVertical: function () {
            var userAgent = window.navigator.userAgent.toLowerCase();
            userAgent.match(/firefox\//i);
            if (userAgent.indexOf('firefox') != -1 && parseInt(RegExp.rightContext) < 41) {
                $('.tablink2').css({
                    width: 'auto'
                }).find('.current>*').css({
                    width: 'auto',
                    height: 29
                });
                var tabs = $('.tablink2').find('li').children();
                tabs.css({
                    width: 'auto',
                    height: 30,
                    padding: '0 12px',
                    'border-radius': '2px 2px 0 0',
                    'transform-origin': '0 0',
                    'margin-left': 30
                });
                for(var i = 0; i<tabs.length; i++){
                    var tab = $(tabs[i]);
                    var tabWid = tab.outerWidth() + 1;
                    var tabHei = tab.outerHeight();
                    tab.css({
                        'transform': 'rotate(90deg)'
                    }).outerWidth(tabWid);
                    tab.parent('li').width(tabHei).height(tabWid);
                }
                $('.tablink2').width(30);
            }
        },
        display_alert: function (check_no) {
            Brandco.unit.openModal("#modal3");
            // モーダルの「対象に入れる」ボタンに情報を格納
            $('a[data-update_type="announce_insert"]').attr('data-check_no', check_no);
        },
        switchMultiChecked: function (target) {
            ShowCpUserListService.toggleAnimation(target.parents('.jsAreaToggleWrap').find('.jsAreaToggleTarget'));
            if (target.hasClass('btnArrowB1')) {
                $('.jsSortItemTarget').toggle();
            } else {
                if (target.attr('data-query') == "some_query") {
                    $('a.btnArrowB1').parent('li.jsAreaToggleWrap').show();
                    target.attr('data-query', "all_query");
                } else {
                    $('a.btnArrowB1').parent('li.jsAreaToggleWrap').hide();
                    target.attr('data-query', "some_query");
                }
            }
        },
        change_header_status: function (query_user) {
            var query_sent_user = $('p.select').attr('data-query_user');
            var query_target_user = $('p.selectedUser').attr('data-query_user');
            if (query_user == query_sent_user) { //送信済ユーザ選択の時は送信済のリンクを外して送信対象のリンクを戻す
                ShowCpUserListService.delete_sent_a_tag();
                ShowCpUserListService.add_target_a_tag();
            } else if (query_user == query_target_user) {//送信対象ユーザ選択の時は送信対象のリンクを外して送信済のリンクを戻す
                ShowCpUserListService.add_sent_a_tag();
            } else { // 全ユーザ選択の時は2つのリンクを戻す
                ShowCpUserListService.add_sent_a_tag();
                ShowCpUserListService.add_target_a_tag();
            }
        },
        add_sent_a_tag: function () {
            var query_sent_user = $('p.select').attr('data-query_user');
            var sent_user_link = $('span.stepTitle[data-sent_count]').children('small');
            if ($('span.stepTitle[data-sent_count]').children('small').children('br').length) { // 当選発表の場合
                var user_link_html = sent_user_link.html().split('<br>');
                var sent_user_count = user_link_html[1].substr(0, 1);
            } else { // 当選発表以外の場合
                var sent_user_count = sent_user_link.html().substr(0, 1);
            }
            if (!$('span.stepTitle[data-sent_count]').find('a[data-query_user="' + query_sent_user + '"]').length && sent_user_count != 0) {
                if ($('span.stepTitle[data-sent_count]').children('small').children('br').length) { // 当選発表の場合
                    sent_user_link.html(user_link_html[0] + '<br><a href="javascript:void(0)" data-query_user="' + query_sent_user + '">' + user_link_html[1] + '</a>');
                } else { // 当選発表以外の場合
                    sent_user_link.html('<a href="javascript:void(0)" data-query_user="' + query_sent_user + '">' + sent_user_link.html() + '</a>');
                }
            }
        },
        delete_sent_a_tag: function () {
            var query_sent_user = $('p.select').attr('data-query_user');
            var sent_user_link = $('span.stepTitle[data-sent_count]').children('small');
            if ($('span.stepTitle[data-sent_count]').find('a[data-query_user="' + query_sent_user + '"]').length) {
                if ($('span.stepTitle[data-sent_count]').children('small').children('br').length) { // 当選発表の場合
                    var user_link_html = sent_user_link.html().split('<br>');
                    sent_user_link.html(user_link_html[0] + '<br>' + sent_user_link.children('a').html());
                } else { // 当選発表以外の場合
                    sent_user_link.html(sent_user_link.children('a').html());
                }
            }
        },
        add_target_a_tag: function () {
            var query_target_user = $('p.selectedUser').attr('data-query_user');
            var target_user_count = $('span.userNum').length != 0 ? $('span.userNum').html() : 0;
            if (!$('p.selectedUser').children('a').length && target_user_count != '--') {
                var target_user_link = $('p.selectedUser');
                target_user_link.html('<a href="javascript:void(0)" data-query_user="' + query_target_user + '">' + target_user_link.html() + '</a>')
            }
        },
        delete_target_a_tag: function () {
            if ($('p.selectedUser').children('a').length) {
                var target_user_link = $('p.selectedUser').children('a');
                target_user_link.parent('p').html('<span>' + target_user_link.html() + '</span>');
            }
        },
        select_all_users: function () {
            $('p.checkedUser').attr('data-checked', true);
            $('a[data-select="select_all_users"]').parent('p').hide();
            $('a[data-select="deselect_all_users"]').parent('p').show();
            ShowCpUserListService.countCheckbox();
        },
        deselect_all_users: function () {
            $('p.checkedUser').attr('data-checked', '');
            $('.jsCountAll').prop('checked', false); //すべてを選択するチェックボックスを解除
            ShowCpUserListService.countCheckbox();
            $('.userListMessage').stop(true, false).slideUp(200, function () {
                setTimeout(function () {
                    $('a[data-select="deselect_all_users"]').parent('p').hide();
                    $('a[data-select="select_all_users"]').parent('p').show();
                }, 200)
            });
        },
        replaceATag: function (target, update_type, check_no, label) {
            $('*[data-update_type="' + target + '"]').parent().html('<a href="javascript:void(0)" class="jsAreaToggle" data-update_type="' + update_type + '" data-check_no=' + check_no + '>' + label + '</a>');
        },
        replaceSpanTag: function (target, update_type, check_no, label) {
            $('*[data-update_type="' + target + '"]').parent().html('<span class="jsAreaToggle" data-update_type="' + update_type + '" data-check_no=' + check_no + '>' + label + '</span>');

        },
        switchCheckboxText: function (insertCountNum, insertCountInOtherPage, updateCountNum, updateCountInOtherPage) {
            var target_add = $('li[data_checkbox_type="add"]');
            var target_del = $('li[data_checkbox_type="delete"]');
            // 送信対象外であったが、新たにチェックが入った数
            if (insertCountNum > 0 || ($('p.checkedUser').attr('data-checked') && insertCountInOtherPage)) {
                if (target_add.css('display') == 'none') {
                    target_add.show();
                    target_add.find('input[type="radio"]').prop('checked', true);
                }
            } else {
                if (target_add.css('display') == 'list-item') {
                    target_add.hide();
                    target_del.find('input[type="radio"]').prop('checked', true);
                }
            }

            // 送信対象であり、チェックが入った数
            if (updateCountNum > 0 || ($('p.checkedUser').attr('data-checked') && updateCountInOtherPage)) {
                if (target_del.css('display') == 'none') {
                    target_del.show();
                    // 「対象に入れる」が表示されていないのであれば、デフォルトのチェックを入れる。
                    if (target_add.css('display') == 'none') {
                        target_del.find('input[type="radio"]').prop('checked', true);
                    }
                }
            } else {
                if (target_del.css('display') == 'list-item') {
                    target_del.hide();
                    // デフォルトのチェックを対象に入れるに戻す。
                    if (target_add.css('display') == 'list-item') {
                        target_add.find('input[type="radio"]').prop('checked', true);
                    }
                }
            }
        },
        switchUpdateButtonStatus: function (countNum, insertCountNum, insertCountInOtherPage, updateCountNum, updateCountInOtherPage) {
            var update_type = $('*[data-update_type][data-check_no]').attr('data-update_type');
            var check_no = $('*[data-update_type][data-check_no]').attr('data-check_no');

            //当選者確定ユーザーは変更できなくて、ボタンを表示しない
            var fixedUser = $('.jsCountTarget:checked[name="user[]"]').not(':disabled').parents('li.fixedUser').length;
            var hideUpdateButton = $('[name="hide_update_target_button"]').val();

            if(fixedUser || hideUpdateButton){
                return false;
            }

            // 全員選択時
            if ((updateCountNum || updateCountInOtherPage || insertCountNum || insertCountInOtherPage) && $('p.checkedUser').attr('data-checked')) {
                if (updateCountInOtherPage) {
                    if (update_type == "update") {
                        ShowCpUserListService.replaceATag(update_type, 'update', check_no, '対象の変更');
                    } else {
                        ShowCpUserListService.replaceATag(update_type, 'update', check_no, '対象の変更');
                    }
                } else {
                    if (update_type == "update") {
                        ShowCpUserListService.replaceATag(update_type, 'update', check_no, '対象の変更');
                    } else {
                        ShowCpUserListService.replaceATag(update_type, 'insert', check_no, '対象に入れる');
                    }
                }
            } else {
                if (countNum > 0 && beforeTargetCount == 0 && $('span[data-update_type][data-check_no]').length) {
                    if (update_type == "update") {
                        ShowCpUserListService.replaceATag(update_type, 'update', check_no, '対象の変更');
                    } else {
                        ShowCpUserListService.replaceATag(update_type, 'insert', check_no, '対象に入れる');
                    }
                } else if (countNum == 0 && beforeTargetCount > 0 && $('a[data-update_type][data-check_no]').length) {
                    // デフォルトのものに戻す
                    var page_info = $('[name="check_info"]').val().split('/');
                    if (page_info[0] == "update") {
                        ShowCpUserListService.replaceSpanTag(update_type, 'update', page_info[1], '対象の変更');
                    } else {
                        ShowCpUserListService.replaceSpanTag(update_type, 'insert', page_info[1], '対象に入れる');
                    }
                } else if (countNum > 0 && beforeTargetCount > 0 && $('a[data-update_type][data-check_no]').length) {
                    // デフォルトのものに戻す
                    var page_info = $('[name="check_info"]').val().split('/');
                    if (page_info[0] == "update") {
                        if (!$('a[data-update_type][data-update_type="update"]').length) {
                            ShowCpUserListService.replaceATag(update_type, 'update', page_info[1], '対象の変更');
                        }
                    } else {
                        if (!$('a[data-update_type][data-update_type="insert"]').length) {
                            ShowCpUserListService.replaceATag(update_type, 'insert', page_info[1], '対象に入れる');
                        }
                    }
                }
            }
        },
        execute_fan_update: function (check_no) {
            // 当選発表の時のみアラートを出すかどうかの条件分岐
            if ($('span.stepTitle[data-winner_count]').attr('data-winner_count')) {
                if ($('#modal4')[0]) {
                    Brandco.unit.closeModal(4);
                }
                var winnter_count = parseInt($('span.stepTitle[data-winner_count]').attr('data-winner_count').replace(/,/g,""));
                var sent_count = parseInt($('span.stepTitle[data-sent_count]').attr('data-sent_count').replace(/,/g,""));
                var target_count = parseInt($('strong.jsCountArea').html() == '---' ? 0 : $('strong.jsCountArea').html().replace(/,/g,""));

                // ランダム抽出の場合は
                // リストの全体件数表示の部分から取得する
                var candidate_count;
                if (check_no == '3') {
                    // ランダム抽選の場合は、一覧の絞り込み件数を取得する
                    candidate_count = $('#random_text').val();
                } else {
                    candidate_count = $('span.userNum').length != 0 ? parseInt($('span.userNum').html() == '--' ? 0 : $('span.userNum').html().replace(/,/g,"")) : 0;
                }
                if (winnter_count < sent_count + target_count + candidate_count) {
                    ShowCpUserListService.display_alert(check_no);
                } else {
                    ShowCpUserListService.update_fan_target(check_no);
                }
            } else {
                ShowCpUserListService.update_fan_target(check_no);
            }
        },
        display_all_update: function (check_no) {
            $('#modal4').find('span:first').html($('strong.jsCountArea').html());
            Brandco.unit.openModal("#modal4");
            // モーダルの「対象に入れる」ボタンに情報を格納
            $('a[data-update_type="insert_all_users"]').attr('data-check_no', check_no);
        },
        check_shipping_address_action_status: function(check_no, execute_type, is_update_fan_target_flg) {
            var select_all_users = $('p.checkedUser').attr('data-checked');
            var cp_id = $('[name="cp_id"]').attr('value');
            var action_id = $('[name="action_id"]').attr('value');
            var data = $('#formAddTarget').serialize();
            var url = $('*[name=api_check_shipping_address_action_status_url]').attr('value');
            // check_no: 3はランダム当選
            var update_type = check_no == '3' ? '3' : $('input:radio[name="checkedUser1/' + check_no + '"]:checked').val();
            var param = {
                data: data + "&cp_id=" + cp_id + "&action_id=" + action_id + "&select_all_users=" + select_all_users + "&update_type=" + update_type,
                type: 'GET',
                url: url,
                success: function(json) {
                    if (json.result === 'ok') {
                        var isNotInputShippingAddress = json.data.isNotInputShippingAddress;
                        if (isNotInputShippingAddress == 1) {
                            Brandco.unit.openModal('#modal8');
                        } else {
                            ShowCpUserListService.execute_fan_update_by_type(check_no,execute_type,is_update_fan_target_flg);
                        }
                    } else {
                        var error_msg;
                        if (json.errors['msg']) {
                            error_msg = json.errors['msg'];
                        } else {
                            error_msg = '操作が失敗しました';
                        }
                        alert(error_msg);
                    }
                }
            };
            Brandco.api.callAjaxWithParam(param, true, true);
        },
        check_answer_status: function(check_no, is_update_fan_target_flg) {
            // Ajaxにて対象ユーザの回答状況のチェックを行う
            // * 未回答ユーザがいる場合
            // ** アラートダイアログを表示
            // **  対象に入れるを押下時に、既存の処理を行う
            // * 未回答ユーザがいない場合
            // ** 既存の処理を行う
            var select_all_users = $('p.checkedUser').attr('data-checked');
            var cp_id = $('[name="cp_id"]').attr('value');
            var action_id = $('[name="action_id"]').attr('value');
            var data = $('#formAddTarget').serialize();
            var url = $('*[name=api_check_answer_status_url]').attr('value');
            // check_no: 3はランダム当選
            var update_type = check_no == '3' ? '3' : $('input:radio[name="checkedUser1/' + check_no + '"]:checked').val();
            var param = {
                data: data + "&cp_id=" + cp_id + "&action_id=" + action_id + "&select_all_users=" + select_all_users + "&update_type=" + update_type,
                type: 'GET',
                url: url,
                success: function(json) {
                    if ($('#modal7')[0]) {
                        Brandco.unit.closeModal(7);
                    }
                    if (json.result === 'ok') {
                        var unfinish_count = json.data.unfinish_count;
                        if (unfinish_count > 0) {
                            if (is_update_fan_target_flg) {
                                $('a[data-update_type="check_answer_status"]').attr('data-is_update_fan_target_flg', true);
                            }
                            $('a[data-update_type="check_answer_status"]').attr('data-check_no', check_no);
                            $('#modal6_step_name').text(json.data.title);
                            $('#modal6_user_count').text(unfinish_count);
                            Brandco.unit.openModal('#modal6');
                        } else {
                            if (is_update_fan_target_flg) {
                                var winnter_count = parseInt($('span.stepTitle[data-winner_count]').attr('data-winner_count').replace(/,/g,""));
                                var sent_count = parseInt($('span.stepTitle[data-sent_count]').attr('data-sent_count').replace(/,/g,""));
                                var target_count = parseInt($('strong.jsCountArea').html() == '---' ? 0 : $('strong.jsCountArea').html().replace(/,/g,""));
                                var candidate_count;
                                if (check_no == '3') {
                                    // ランダム抽選の場合は、一覧の絞り込み件数を取得する
                                    candidate_count = $('#random_text').val();
                                } else {
                                    candidate_count = $('span.userNum').length != 0 ? parseInt($('span.userNum').html() == '--' ? 0 : $('span.userNum').html().replace(/,/g,"")) : 0;
                                }
                                if ((winnter_count < sent_count + target_count + candidate_count) &&
                                    $('input:radio[name="checkedUser1/' + check_no + '"]:checked').val() == '1') {
                                    ShowCpUserListService.display_alert(check_no);
                                } else {
                                    ShowCpUserListService.update_fan_target(check_no);
                                }
                            } else {
                                if ($('p.checkedUser').attr('data-checked') && check_no != '3') {
                                    ShowCpUserListService.display_all_update(check_no);
                                } else {
                                    ShowCpUserListService.execute_fan_update(check_no);
                                }
                            }
                        }
                    } else {
                        var error_msg;
                        if (json.errors['msg']) {
                            error_msg = json.errors['msg'];
                        } else {
                            error_msg = '操作が失敗しました';
                        }
                        alert(error_msg);
                    }
                }
            };
            Brandco.api.callAjaxWithParam(param, true, true);
        },
        open_image_window: function (img_src) {
            window.open(img_src, "imgwindow", "width=600, height=600");
        },
        isChangedTarget: function() {
            var changed_count = $('input[name="user[]"]').filter(function() {
                return this.checked && !this.disabled;
            }).length;

            return changed_count ? true : false;
        },
        execute_fan_update_by_type: function (check_no, execute_type, is_update_fan_target_flg) {
            switch (execute_type){
                case 1:
                    ShowCpUserListService.display_all_update(check_no);
                    break;
                case 2:
                    ShowCpUserListService.execute_fan_update(check_no);
                    break;
                case 3:
                    ShowCpUserListService.update_fan_target(check_no);
                    break;
                case 4:
                    ShowCpUserListService.check_answer_status(check_no, is_update_fan_target_flg);
                    break;
                default:
                    alert('エラーが発生しました。システム管理者に問い合わせてください。');
                    break;
            }
        }
    }
})();

$(document).ready(function(){
    var join_user = $('input[name="join_user"]').val();
    ShowCpUserListService.get_fan(null, null, null, null, null, null, join_user);

    $(document).on('click', 'a[data-tab="linkTab"]', function(){
        ShowCpUserListService.get_fan($(this).attr('data-page_no'), $(this).attr('data-display_action_id'), $(this).attr('data-tab_no'), null, null, null, null);
    });

    $(document).on('click', 'a[data-submit="updateFanTarget"]', function() {
        var check_no = $(this).attr('data-check_no');
        var is_check_shipping_address = $('[name="is_check_shipping_address"]').attr('value');
        if($('p.checkedUser').attr('data-checked') && $('input:radio[name="checkedUser1/' + check_no + '"]:checked').val() == '1') {
            if ($('span.stepTitle[data-winner_count]').attr('data-winner_count')) {
                // 対象者の回答状況チェックを行う
                if(is_check_shipping_address){
                    ShowCpUserListService.check_shipping_address_action_status(check_no, 4, true);
                } else {
                    ShowCpUserListService.check_answer_status(check_no, true);
                }
            } else {
                if(is_check_shipping_address){
                    ShowCpUserListService.check_shipping_address_action_status(check_no,1, false);
                } else {
                    ShowCpUserListService.display_all_update(check_no);
                }
            }
        } else {
            // 当選発表の時のみアラートを出すかどうかの条件分岐
            if ($('span.stepTitle[data-winner_count]').attr('data-winner_count') &&
                $('input:radio[name="checkedUser1/' + check_no + '"]:checked').val() == '1') {
                // 対象者の回答状況チェックを行う
                if(is_check_shipping_address){
                    ShowCpUserListService.check_shipping_address_action_status(check_no, 4, true);
                } else {
                    ShowCpUserListService.check_answer_status(check_no, true);
                }
            } else {
                if(is_check_shipping_address && $('input:radio[name="checkedUser1/' + check_no + '"]:checked').val() == '1'){
                    ShowCpUserListService.check_shipping_address_action_status(check_no, 3, false);
                } else {
                    ShowCpUserListService.update_fan_target(check_no);
                }
            }
        }
    });

    $(document).on('click', 'a.jsAreaToggle', function(){
        if(($(this).hasClass('iconBtnSort') || $(this).hasClass('btnArrowB1') || $(this).attr('data-update_type') =="update" || $(this).attr('data-update_type') =="cancel") && !$(this).hasClass('jsSortItem')) {
            ShowCpUserListService.toggleAnimation($(this).parents('.jsAreaToggleWrap').find('.jsAreaToggleTarget'));
        }
    });

    $(document).on('click', 'a[data-update_type="insert"]', function(){
        var check_no = $(this).attr('data-check_no');
        var is_check_shipping_address = $('[name="is_check_shipping_address"]').attr('value');
        if ($('span.stepTitle[data-winner_count]').attr('data-winner_count')) {
            // 当選通知の場合
            if(is_check_shipping_address){
                ShowCpUserListService.check_shipping_address_action_status(check_no, 4, false);
            } else {
                ShowCpUserListService.check_answer_status(check_no,false);
            }
        } else {
            if ($('p.checkedUser').attr('data-checked')) {
                if(is_check_shipping_address){
                    ShowCpUserListService.check_shipping_address_action_status(check_no,1, false);
                } else {
                    ShowCpUserListService.display_all_update(check_no);
                }
            } else {
                if(is_check_shipping_address){
                    ShowCpUserListService.check_shipping_address_action_status(check_no,2, false);
                } else {
                    ShowCpUserListService.execute_fan_update(check_no);
                }
            }
        }
    });

    $(document).on('click', 'a[data-search_type]', function(){
        ShowCpUserListService.search_fan($(this).attr('data-search_type'), $(this).attr('data-search_no'), '');
    });

    $(document).on('click', 'a[data-clear_type]', function(){
        ShowCpUserListService.search_fan($(this).attr('data-clear_type'), '', '',$(this).attr('data-sns_action_key'));
    });

    $(document).on('click','a[data-order]',function(){
        ShowCpUserListService.search_fan($(this).closest('div[data-search_type]').attr('data-search_type'), '', $(this).attr('data-order'));
    });

    $(document).on('click', 'a[data-page]', function(){
        var page_info = $('[name="page_info"]').attr('value').split("/");
        ShowCpUserListService.get_fan($(this).attr('data-page'), page_info[1], page_info[2], null, null, null, null);
    });

    $(document).on('change', '.jsCheckToggle', function(){
        var target_wrap = $(this).parents('.jsCheckToggleWrap')[0];
        $(target_wrap).find('.jsCheckToggleTarget').slideToggle(300, function() {
            $(this).closest('ul').scrollTop(300);
        });
    });

    $(document).on('change', '.jsCountTarget', function(){
        if(!$(this).prop('checked') && $('.userListMessage').is(':visible')) {
            ShowCpUserListService.deselect_all_users();
        } else {
            ShowCpUserListService.countCheckbox();
        }
    });

    $(document).on('change', '.jsCountAll', function(){
        if ($(this).prop('checked')) {
            $('.userListMessage').stop(true, false).slideDown(200);
            $('[name="user[]"]').not(':disabled').prop('checked', true);
            ShowCpUserListService.countCheckbox();
        } else {
            $('[name="user[]"]').not(':disabled').prop('checked', false);
            $('.userListMessage').stop(true, false).slideUp(200, function () {
                setTimeout(function () {
                    $('a[data-select="deselect_all_users"]').parent('p').hide();
                    $('a[data-select="select_all_users"]').parent('p').show();
                }, 200)
            });
            if ($('p.checkedUser').attr('data-checked')) {
                ShowCpUserListService.deselect_all_users();
            } else {
                ShowCpUserListService.countCheckbox();
            }
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

    $('#submitReservationSchedule').click(function () {
        $(window).unbind('beforeunload');
        Brandco.helper.updateReservationStatus($(this));
    });

    $('#fixAnnounceDeliveryUser').click(function(){
        $(window).unbind('beforeunload');
        Brandco.helper.updateReservationStatus($(this));
    });

    $('a[href="#modal1"]').unbind('click');
    $('a[href="#modal1"]').click(function(Event){
        Event.preventDefault();
        if (ShowCpUserListService.isChangedTarget() && !ShowCpUserListService.hidePopupFlg) {
            $('#getFanConfirm').removeData();
            $('#getFanConfirm').attr('data-cf_callback', 'submit_reservation');
            Brandco.unit.openModal("#modal5");
            return;
        }
        Brandco.unit.showModal(this);
    });

    $(document).on('click','a[data-update_type="announce_insert"]',function(){
        ShowCpUserListService.update_fan_target($(this).attr('data-check_no'));
    });

    $(document).on('click','.jsSortItem',function(){
        ShowCpUserListService.switchMultiChecked($(this));
    });

    $(document).on('click','a[data-query_user]',function(){
        var page_info = $('[name="page_info"]').attr('value').split("/");
        ShowCpUserListService.get_fan(null, page_info[1], page_info[2], null, $(this).attr('data-query_user'), null, null);
    });

    $(document).on('click','a[data-select="select_all_users"]',function(){
        ShowCpUserListService.select_all_users();
    });

    $(document).on('click','a[data-select="deselect_all_users"]',function(){
        $('[name="user[]"]').not(':disabled').prop('checked', false); //個別のチェックボックスを全部解除
        ShowCpUserListService.deselect_all_users();
    });

    $(document).on('click','a[data-update_type="insert_all_users"]',function(){
        ShowCpUserListService.execute_fan_update($(this).attr('data-check_no'));
    });

    $(document).on('click','.open_image_window',function(){
        ShowCpUserListService.open_image_window($(this).find('img').attr('src'));
    });

    $(document).on('click', '#applyFanLimit', function() {
        var page_info = $('[name="page_info"]').attr('value').split("/");
        ShowCpUserListService.get_fan(1, page_info[1], page_info[2], null, null, $('select[name="fan_limit"]').val(), null);
    });

    $('a[href!="javascript:void(0)"]').not('[href="#closeModal"]').not('[target="_blank"]').not('.jsOpenModal').on('click', function(event) {
        event.preventDefault();
        if (!$(this).parent().hasClass('download') && ShowCpUserListService.isChangedTarget() && !ShowCpUserListService.hidePopupFlg) {
            $('#getFanConfirm').removeData();
            $('#getFanConfirm').attr('data-cf_callback', 'link').attr('data-url', $(this).attr('href'));
            Brandco.unit.openModal("#modal5");
            return;
        } else {
            window.location.href = $(this).attr('href');
        }
    });

    $('#getFanConfirm').click(function() {
        ShowCpUserListService.hidePopupFlg = true;
        $(window).unbind('beforeunload');
        if ($(this).attr('data-cf_callback') == 'search_fan') {
            ShowCpUserListService.search_fan($(this).attr('data-cf_search_type'), $(this).attr('data-cf_search_no'), $(this).attr('data-cf_order'));
        } else if ($(this).attr('data-cf_callback') == 'get_fan') {
            ShowCpUserListService.get_fan($(this).attr('data-cf_page_no'), $(this).attr('data-cf_display_action_id'), $(this).attr('data-cf_tab_no'), $(this).attr('data-cf_query_flg'), $(this).attr('data-cf_query_user'), null, null);
        } else if ($(this).attr('data-cf_callback') == 'submit_reservation') {
            Brandco.unit.openModal('#modal1');
        } else  if ($(this).attr('data-cf_callback') == 'link') {
            window.location.href = $(this).data('url');
        }
        Brandco.unit.closeModal(5);
    });

    $(document).on('click','a[data-update_type="check_answer_status"]',function(){
        var check_no = $(this).attr('data-check_no');
        if ($(this).attr('data-is_update_fan_target_flg')) { 
            var winnter_count = parseInt($('span.stepTitle[data-winner_count]').attr('data-winner_count').replace(/,/g,""));
            var sent_count = parseInt($('span.stepTitle[data-sent_count]').attr('data-sent_count').replace(/,/g,""));
            var target_count = parseInt($('strong.jsCountArea').html() == '---' ? 0 : $('strong.jsCountArea').html().replace(/,/g,""));
            var candidate_count = $('span.userNum').length != 0 ? parseInt($('span.userNum').html() == '--' ? 0 : $('span.userNum').html().replace(/,/g,"")) : 0;
            if ((winnter_count < sent_count + target_count + candidate_count) &&
                $('input:radio[name="checkedUser1/' + check_no + '"]:checked').val() == '1') {
                ShowCpUserListService.display_alert(check_no);
            } else {
                ShowCpUserListService.update_fan_target(check_no);
            }
        } else {
            if ($('p.checkedUser').attr('data-checked') && check_no != '3') { // 自動抽選の場合は除く
                ShowCpUserListService.display_all_update(check_no);
            } else {
                ShowCpUserListService.execute_fan_update(check_no);
            }
        }
        Brandco.unit.closeModal(6);
    });
    // -------------------------------------------------------------------------
    // 自動抽選
    // -------------------------------------------------------------------------
    $(document).on('click','a[data-update_type="random_insert"]',function() {
        Brandco.unit.openModal('#modal7');
        // 抽出件数の取得
        $('.pager1').children("p:not('.showItemNum')").each(function(index, elm) {
            if (index == 0) {
                $(elm).find('strong').each(function(index, elm) {
                    if ($(elm).text() != '「送信対象」') {
                        $('#random_target_count').text($(elm).text());
                        return false;
                    }
                });
            }
        });
        // 「住所重複を除外」のチェックボックスの
        // デフォルトチェックをONにする
        $('#modal7').find('input[type="checkbox"]').prop("checked", true);
        $('#random_text').keyup(function() {
            // 当選者数チェック
            $('#modal7').find('.attention1').text('');
            if (!$(this).val().match(/^[1-9][0-9]*$/)) {
                $('#modal7').find('.jsModalCont').prepend(
                    '<p class="attention1">' + '半角数値で正しく入力してください' + '</p>'
                );
            } else {
            }
        });
    });
    $(document).on('click','a[data-update_type="random_select"]',function() {
        $('#modal7').find('.attention1').text('');
        if (!$('#random_text').val().match(/^[1-9][0-9]*$/)) {
            $('#modal7').find('.attention1').text('');
            $('#modal7').find('.jsModalCont').prepend(
                '<p class="attention1">' + '半角数値で正しく入力してください' + '</p>'
            );
            return false;
        }
        var check_no = '3';
        ShowCpUserListService.check_answer_status(check_no, false);
    });

    // -------------------------------------------------------------------------
    // 当選者確定
    // -------------------------------------------------------------------------
    $(document).on('click', 'a[data-update_type="fix_target"]' , function() {
        Brandco.unit.openModal("#modal9");
        var winner_count = parseInt($('span.stepTitle[data-winner_count]').attr('data-winner_count').replace(/,/g,""));
        var fix_target_count = parseInt($('span.setTargetNum').html());

        $("#modal9").find("#winner_num").text(winner_count);
        $("#modal9").find("#fix_target_num").text(fix_target_count);

        if($(".admin_rule_check").not(':checked').length != 0) {
            $('#modal9').find('#fixDisableButton').show();
            $('#modal9').find('#fixEnableButton').hide();
        }else{
            $('#modal9').find('#fixEnableButton').show();
            $('#modal9').find('#fixDisableButton').hide();
        }

        $('#modal9').find(".admin_rule_check").change(function(){
            if($(".admin_rule_check").not(':checked').length == 0){
                $('#modal9').find('#fixEnableButton').show();
                $('#modal9').find('#fixDisableButton').hide();
            }else{
                $('#modal9').find('#fixEnableButton').hide();
                $('#modal9').find('#fixDisableButton').show();
            }
        });

        $("#modal9").find('#fixEnableButton').off('click');
        $("#modal9").find('#fixEnableButton').click(function(){
            Brandco.unit.closeModal(9);
            $('[name="fix_target_type"]').val(1);
            ShowCpUserListService.update_fan_target(null);
        });

    });

    // -------------------------------------------------------------------------
    // 当選者解除
    // -------------------------------------------------------------------------
    $(document).on('click', 'a[data-update_type="cancel_fix_target"]' , function() {
        Brandco.unit.openModal("#modal10");

        $("#modal10").find('.btn3').off('click');
        $("#modal10").find('.btn3').click(function(){
            Brandco.unit.closeModal(10);
            $('[name="fix_target_type"]').val(2);
            ShowCpUserListService.update_fan_target(null);
        });
    });

});
