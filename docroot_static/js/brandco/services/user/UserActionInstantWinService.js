if(typeof(UserActionInstantWinService) === 'undefined') {
    var UserActionInstantWinService = (function () {
        return{

            executeAction: function (target) {

                var form = $(target).parents().filter(".executeInstantWinActionForm");
                var section = $(target).parents().filter(".jsMessage");
                var csrf_token = document.getElementsByName("csrf_token")[0].value;
                var url = form.attr("action");
                var cp_action_id = $('input[name=cp_action_id]', form).val();
                var cp_user_id = $('input[name=cp_user_id]', form).val();
                var param = {

                    data: {
                        csrf_token: csrf_token,
                        cp_action_id: cp_action_id,
                        cp_user_id: cp_user_id
                    },
                    url: url,

                    beforeSend: function () {
                        Brandco.helper.showLoading(section);
                    },

                    success: function (json) {
                        if (json.result === "ok") {
                            if (json.data.next_action === true) {
                                var message = $(json.html);
                                message.hide();
                                section.after(message);

                                Brandco.helper.facebookParsing(json.data.sns_action);

                                $('#message_' + json.data.message_id).stop(true, false).show(200, function () {
                                    Brandco.unit.createAndJumpToAnchor(true);
                                });
                            }
                            $('.cmd_execute_instant_win_action').remove();
                        } else {
                            $.each(json.errors, function (i, value) {
                                if (value) {
                                    alert(value);
                                } else {
                                    alert("エラーが発生しました");
                                }
                            });
                        }
                    },

                    complete: function () {
                        Brandco.helper.hideLoading();
                    }
                };
                Brandco.api.callAjaxWithParam(param, true);
            },

            preExecuteAction: function (target) {

                var form = $(target).parents().filter(".preExecuteDrawInstantWinActionForm");
                var section = $(target).parents().filter(".campaign");
                var csrf_token = document.getElementsByName("csrf_token")[0].value;
                var url = form.attr("action");
                var cp_action_id = $('input[name=cp_action_id]', form).val();
                var cp_user_id = $('input[name=cp_user_id]', form).val();
                var user_id = $('input[name=user_id]', form).val();
                var animation_time = $('input[name=animation_time]', form).val();
                var isVisibleNextButton = $('input[name=is_visible_next_button]', form).val();
                var secondChanceImage = $('input[name=second_chance_challenge_image]',form).val();
                var trackerName = $('input[name=tracker_name]',form).val();
                var messageid = $('input[name=messageid]', form).val();

                var param = {

                    data: {
                        csrf_token: csrf_token,
                        cp_action_id: cp_action_id,
                        cp_user_id: cp_user_id,
                        user_id: user_id,
                        messageid: messageid
                    },
                    url: url,

                    beforeSend: function () {
                        Brandco.helper.showLoading(section);
                    },

                    success: function (json) {
                        if (json.result === "ok") {
                            $('input[name=next_time]', form).val(json.data.last_join_at);
                            $("#drawBtn").html('<span href="javascript:void(0)" class="large1">チャレンジする</span>');
                            $('#drawImg').find('img').attr('src', $("input[name=ani_draw]", form).val());
                            $('<p class="messageImg" style="display:none" id="resultImg"><img src="' + json.data.image_url + '"></p>').insertAfter('#drawImg');
                            $('<section class="jsMessage" style="display:none"></section>').insertAfter('#newMessage');

                            setTimeout(function () {
                                CpIndicatorService.pinAction();

                                $('drawBtn').removeAttr('disabled');
                                $('#resultImg').show().slideDown();
                                $("#drawImg").hide().slideUp();
                                $("#drawText").html(json.data.text).hide().slideDown();
                            }, animation_time);
                            if (json.data.prize_status == 2) {
                                setTimeout(function () {
                                    var btn_set = $("#drawBtn").parents().filter('.btnSet');
                                    btn_set.after('<span class="cmd_execute_instant_win_action middle1"></span>')
                                    btn_set.remove();

                                    $(".cmd_execute_instant_win_action").click();
                                }, animation_time);
                            } else if (json.data.prize_status == 1) {
                                if (json.data.has_draw_chance) {
                                    setTimeout(function () {
                                        if(isVisibleNextButton == 'true'){
                                            wchanceText = "";
                                            if(json.data.can_show_second_chance){
                                                wchanceText = '<div class="synLotWchance"><h1>今すぐもう1回挑戦できるWチャンス！</h1><p><img src="'+ secondChanceImage +'" alt="メニューを開いて他のサービスを楽しむ！メニューを経由して毎日ラッキーくじにもう1回チャレンジ！"></p></div>';
                                            }
                                            setTimeout(function() {
                                                openMenu($("#side-menu"));
                                                openModalBase($("#side-menu"));
                                            }, 3100);

                                            $( wchanceText + '<p class="messageLotNext">次回参加まであと<br><strong id="timeLeft"></strong></p><ul class="btnSet"><li class="btn3"><a class="ynLotMenu1" href="javascript:openMenu($(\'#side-menu\'));openModalBase($(\'#side-menu\'));ga(\''+trackerName+'.send\',\'event\',\'syndot\', \'open_menu\');">メニューを開く</a></li></ul>').insertAfter("#drawText").hide().slideDown();
                                        }else{
                                            $('<p class="messageLotNext jsMessageLotNext">次回参加まであと<br><strong id="timeLeft"></strong></p>').insertAfter("#drawText").hide().slideDown();
                                        }
                                        $("#drawBtn").remove();
                                        UserActionInstantWinService.countDown();

                                        $('<div id="jsShowMoniplaPR"></div>').insertAfter("#message_"+messageid);
                                        UserMessageThreadMoniplaPRService.showMoniplaPR();

                                        // SynExtensionの表示
                                        if (typeof(SynService) !== 'undefined') {
                                            $('.jsSynExtension').show();
                                            $('.jsSynExtension').parent().addClass('message').show();
                                            SynService.generateSynExtensionCode();
                                        }
                                    }, animation_time);
                                } else {
                                    setTimeout(function () {
                                        $("#drawBtn").remove();
                                        $('<p class="messageLotNext jsMessageLotNext"><strong>ご参加ありがとうございました。</strong></p>').insertAfter("#drawText").hide().slideDown();

                                        $('<div id="jsShowMoniplaPR"></div>').insertAfter("#message_"+messageid);
                                        UserMessageThreadMoniplaPRService.showMoniplaPR();

                                        // SynExtensionの表示
                                        if (typeof(SynService) !== 'undefined') {
                                            $('.jsSynExtension').show();
                                            $('.jsSynExtension').parent().addClass('message').show();
                                            SynService.generateSynExtensionCode();
                                        }
                                    }, animation_time);
                                }
                            } else {
                                alert("エラーが発生しました");
                                location.reload();
                            }
                        } else {
                            $.each(json.errors, function (i, value) {
                                if (value) {
                                    alert(value);
                                    location.reload();
                                } else {
                                    alert("エラーが発生しました");
                                    location.reload();
                                }
                            });
                        }
                    },

                    complete: function () {
                        Brandco.helper.hideLoading();
                    }
                };
                Brandco.api.callAjaxWithParam(param, true);
            },

            countDown: function () {
                $("#instantWinApi").attr('action', $("#instantWinApi").data('pre-execute-url'));
                $("#instantWinApi").attr('class', $("#instantWinApi").data('pre-execute-class'));
                var form = $(".preExecuteDrawInstantWinActionForm");
                var nextJoinTime = $("input[name=next_time]", form).val();
                var startDateTime = new Date();
                var endDateTime = new Date(nextJoinTime);
                var left = endDateTime - startDateTime;
                var a_day = 24 * 60 * 60 * 1000;
                var d = Math.floor((Number(left) / Number(a_day)));
                var h = Math.floor((Number(left) % Number(a_day)) / (60 * 60 * 1000));
                var m = Math.floor((Number(left) % Number(a_day)) / (60 * 1000)) % 60;
                var s = Math.floor((Number(left) % Number(a_day)) / 1000) % 60 % 60;

                if (Number(left) <= 0) {
                    location.reload();
                } else {
                    if (Number(d) == 0 && Number(h) == 0 && Number(m) == 0) {
                        $("#timeLeft").text(s + '秒');
                    } else if (Number(d) == 0 && h == 0) {
                        $("#timeLeft").text(m + '分' + s + '秒');
                    } else if (Number(d) == 0) {
                        $("#timeLeft").text(h + '時間' + m + '分' + s + '秒');
                    } else {
                        $("#timeLeft").text(d + '日' + h + '時間' + m + '分' + s + '秒');
                    }
                }
                setTimeout(function() { UserActionInstantWinService.countDown(); }, 1000);
            }
        };
    })();
}

$(document).ready(function () {
    $(document).off('click', '.cmd_execute_instant_win_action');
    $(document).on('click', '.cmd_execute_instant_win_action', function (event) {
        event.preventDefault();
        var parent_form = $(this).closest('form');
        parent_form.attr('action', parent_form.data('execute-url'));
        parent_form.attr('class', parent_form.data('execute-class'));
        UserActionInstantWinService.executeAction(this);
    });

    $(document).off('click', '.cmd_pre_execute_draw_instant_win_action');
    $(document).on('click', '.cmd_pre_execute_draw_instant_win_action', function (event) {
        event.preventDefault();
        var parent_form = $(this).closest('form');
        parent_form.attr('action', parent_form.data('pre-execute-url'));
        parent_form.attr('class', parent_form.data('pre-execute-class'));
        UserActionInstantWinService.preExecuteAction(this);
    });

    if ($("#timeLeft").length) {
        UserActionInstantWinService.countDown();
    }

    $('input[name=ani_draw]').each(function () {
        $("<img>").attr("src", $(this).val());
    });

    $('input[name=ani_win]').each(function () {
        $("<img>").attr("src", $(this).val());
    });

    $('input[name=ani_lose]').each(function () {
        $("<img>").attr("src", $(this).val());
    });

    if ($('.cmd_execute_instant_win_action').length) {
        $('.cmd_execute_instant_win_action').click();
    }
});
