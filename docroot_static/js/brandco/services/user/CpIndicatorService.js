if (typeof(CpIndicatorService) === 'undefined') {
    var CpIndicatorService = function() {
        return {
            getIndicatorWrap: function() {
                return $('.barIndicatorWrap');
            },

            getIndicatorInner: function() {
                return $('.barIndicatorInner');
            },

            getIndicator: function() {
                return $('.barIndicator');
            },

            getIndicatorAnchor: function() {
                return $('#indicatorAnchor');
            },

            getMapIncrementGauge: function() {
                return CpIndicatorService.getIndicatorAnchor().data('map_increment_gauge');
            },

            generateCompleteView: function() {
                var monipla_media_url = CpIndicatorService.getIndicatorWrap().find('input[name=monipla_media_url]').val();

                var view = '';
                view += '<h1 class="finishHd">参加が完了しました</h1>';
                view += '<p class="btnSet"><span class="btn4"><a href="' + monipla_media_url + '" target="_blank" class="middle1 jsGATracker">今すぐモニプラへ</a></span></p>';

                return view;
            },

            // length: the number of messages
            countDisplayedMessages: function(length) {
                var map = CpIndicatorService.getMapIncrementGauge();
                var count = 0;
                for (var i = 0; i < Math.min(length, map.length); i++) {
                    count += parseInt(map[i]);
                }

                return count;
            },

            countCurrentDisplayedMessages: function() {
                return CpIndicatorService.countDisplayedMessages($('section.jsMessage').length);
            },

            countTotalDisplayedMessages: function() {
                return CpIndicatorService.countDisplayedMessages(CpIndicatorService.getMapIncrementGauge().length);
            },

            isPC: function() {
                return CpIndicatorService.getIndicatorAnchor().attr('data-device') === 'PC';
            },

            isSP: function() {
                return CpIndicatorService.getIndicatorAnchor().attr('data-device') === 'SP';
            },

            isExisted: function() {
                return CpIndicatorService.getIndicatorWrap().length !== 0
            },

            isCompleted: function() {
                return CpIndicatorService.getIndicatorAnchor().attr('data-completed') === '1';
            },

            isFinished: function() {
                return CpIndicatorService.countCurrentDisplayedMessages() >= CpIndicatorService.countTotalDisplayedMessages();
            },

            isAllowedToDisplayMoniplaMediaLink: function() {
                return CpIndicatorService.getIndicatorAnchor().attr('data-shown_monipla_media_link') === '1';
            },

            canShow: function() {
                return CpIndicatorService.countTotalDisplayedMessages() !== 0;
            },

            show: function() {
                if (CpIndicatorService.canShow()) {
                    CpIndicatorService.getIndicatorWrap().fadeIn();
                }
            },

            hide: function() {
                CpIndicatorService.getIndicatorWrap().fadeOut();
            },

            update: function() {
                var n_current_messages = CpIndicatorService.countCurrentDisplayedMessages();
                var n_total_messages = CpIndicatorService.countTotalDisplayedMessages();
                var progress = 100 * Math.min(n_current_messages / n_total_messages, 1);

                CpIndicatorService.getIndicator().barIndicator('loadNewData', progress);
                if (progress === 100) {
                    CpIndicatorService.getIndicatorAnchor().attr('data-completed', '1');
                }
            },

            jumpToAnchor: function() {
                var speed = 500;
                var position = CpIndicatorService.getIndicatorAnchor().get(0).offsetTop;

                CpIndicatorService.getIndicatorWrap().animate({top: position}, speed, 'swing');
            },

            sendGAEvent: function(event_category) {
                var tracker_name = CpIndicatorService.getIndicatorWrap().find('input[name=tracker_name]').val();
                var cp_id = CpIndicatorService.getIndicatorWrap().find('input[name=cp_id]').val();
                var page_url = CpIndicatorService.getIndicatorWrap().find('input[name=page_url]').val();

                if (typeof(ga) !== 'undefined') {
                    ga(tracker_name + '.send', 'event', event_category, 'campaigns_' + cp_id, location.href, {'page': page_url});
                }
            },

            // ------------------------------------------------------------------
            init: function() {
                if (CpIndicatorService.isExisted() && !CpIndicatorService.isCompleted()) {
                    // 開始位置
                    // CpIndicatorService.getIndicatorWrap().css('top', $('section.jsMessage:last-child').offsetTop);
                    CpIndicatorService.update();
                    if (!CpIndicatorService.isFinished()) {
                        if (CpIndicatorService.isPC()) {
                            CpIndicatorService.jumpToAnchor();
                        }

                        CpIndicatorService.show();

                        // Footerのmargin対応
                        $('footer').addClass('barIndicatorFooter');
                    } else {
                        CpIndicatorService.hide();
                    }
                }
            },

            pinAction: function() {
                if (CpIndicatorService.isExisted() && !CpIndicatorService.isCompleted()) {
                    CpIndicatorService.update();
                    if (CpIndicatorService.isPC()) {
                        CpIndicatorService.jumpToAnchor();
                    }

                    if (CpIndicatorService.isFinished()) {
                        setTimeout(function () {
                            CpIndicatorService.getIndicatorInner().fadeOut(300, function() {
                                if (CpIndicatorService.isAllowedToDisplayMoniplaMediaLink()) {
                                    $(this).html(CpIndicatorService.generateCompleteView()).fadeIn(300);
                                    CpIndicatorService.sendGAEvent('show-back-monipla-from-ingicator');
                                } else {
                                    $(this).find('h1').text('参加完了しました');
                                    $('.bi-wrp.bi-default-theme .bi-barInner').css('background', '#53D5B9');

                                    $(this).fadeIn(300);
                                }
                            })
                        }, 500);
                    }
                }
            }
        };
    }();
}

$(document).ready(function() {
    $('.barIndicatorWrap').on('click', '.jsGATracker', function() {
        CpIndicatorService.sendGAEvent('back-monipla-from-ingicator');
    });
});
