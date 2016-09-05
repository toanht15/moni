if (typeof(UserActionGiftService) === 'undefined') {
    var UserActionGiftService = {
        alreadyClicked: false,
        initUtility: function() {
            $('.jsGiftCardMessage .jsCardAddressee').css({'font-family':'mplus-2p-regular'});
            $('.jsGiftCardMessage .jsCardMessage').css({'font-family':'mplus-2p-regular'});
            $('.jsGiftCardMessage .jsCardSender').css({'font-family':'mplus-2p-regular'});
        },
        sendGiftMessage: function(target, media_type) {
            if (!UserActionGiftService.alreadyClicked) {
                UserActionGiftService.alreadyClicked = true;
            } else {
                return false;
            }
            var form            = $(target).parents().filter('.executeGiftActionForm');
            var section         = $(target).parents().filter('.jsMessage');
            var url             = $(form).attr('action');
            var cpActionId      = $('input[name=cp_action_id]', form).val();
            var cpUserId        = $('input[name=cp_user_id]', form).val();
            var csrfToken       = $('input[name=csrf_token]', form).val();

            var param = {
                data: {
                    csrf_token          : csrfToken,
                    cp_action_id        : cpActionId,
                    cp_user_id          : cpUserId,
                    media_type          : media_type
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
                                Brandco.unit.createAndJumpToAnchor();
                            });
                        }
                        // disable button
                        $('#' + section.attr('id') + ' .jsLineSendingBtn').html('<span>LINE<br>で贈る</span>');
                        $('#' + section.attr('id') + ' .jsFBSendingBtn').html('<span>Facebook<br>で贈る</span>');
                        $('#' + section.attr('id') + ' .jsMailSendingBtn').html('<span>メール<br>で贈る</span>');
                        $('#' + section.attr('id') + ' .jsEditGiftCardBtn').css('display', 'none');

                    } else {
                        alert("エラーが発生しました");
                    }
                    UserActionGiftService.alreadyClicked = false;
                },

                complete: function () {
                    Brandco.helper.hideLoading();
                }
            };
            Brandco.api.callAjaxWithParam(param, false, false);
        },
        //グリーティングカードを作成する為のAjax処理
        generateImage: function(target, receiverText, senderText, contentText) {
            if (!UserActionGiftService.alreadyClicked) {
                UserActionGiftService.alreadyClicked = true;
            } else {
                return false;
            }
            var form            = $(target).parents().filter('.executeGiftActionForm');
            var section         = $(target).parents().filter(".jsMessage");

            var contentWidth    = $('#' + section.attr('id') + ' .jsCardMessage').width();
            var contentHeight   = $('#' + section.attr('id') + ' .jsCardMessage').height();
            var receiverHeight  = $('#' + section.attr('id') + ' .jsCardAddressee').height();
            var senderHeight    = $('#' + section.attr('id') + ' .jsCardSender').height();
            var imageUrl        = $('#' + section.attr('id') + ' .jsCardBackground').find('img').attr('src');
            var cpActionId      = $('input[name=cp_action_id]', form).val();
            var cpUserId        = $('input[name=cp_user_id]', form).val();
            var csrfToken       = $('input[name=csrf_token]', form).val();
            var url             = $('input[name=image_generate_url]', form).val();

            var param = {

                data: {
                    csrf_token          : csrfToken,
                    image_url           : imageUrl,
                    receiver_text       : receiverText,
                    sender_text         : senderText,
                    content_text        : contentText,
                    content_width       : contentWidth,
                    content_height      : contentHeight,
                    cp_action_id        : cpActionId,
                    cp_user_id          : cpUserId,
                    sender_height       : senderHeight,
                    receiver_height     : receiverHeight
                },
                url: url,

                beforeSend: function () {
                    Brandco.helper.showLoading(section);
                },

                success: function (json) {
                    if (json.result === "ok") {
                        UserActionGiftService.giftMessageAfterGenerateImage(target, json.data.card_image_url)

                    } else {
                        alert("エラーが発生しました");
                    }
                    UserActionGiftService.alreadyClicked = false;
                },

                complete: function () {
                    Brandco.helper.hideLoading();
                }
            };
            Brandco.api.callAjaxWithParam(param, false, false);
        },
        //画像合成された後のギフト表示
        giftMessageAfterGenerateImage: function(target, card_image_url) {
            var section = $(target).parents().filter(".jsMessage");
            //スライダーを非表示にする
            $('#' + section.attr('id') + ' .jsMessageGift .jsGiftcardSlider').css('display', 'none');

            //インプットフォームを非表示にする
            $('#' + section.attr('id') + ' .jsGiftCardMessage').css('display', 'none');

            //「作成する」ボタンを非表示にする
            $('#' + section.attr('id') + ' #imageGenerateBtn').css('display', 'none');

            //グリーティングカードを表示する
            if (card_image_url != null) {
                $('#' + section.attr('id') + ' .jsMessageGift .jsCardBackground').find('img').attr('src', card_image_url);
            }

            //グリーティングカードやクーポン情報を表示する
            $('#' + section.attr('id') + ' #editGiftCard').css('display', 'block');

            //SNSボタンを表示する
            $('#' + section.attr('id') + ' .jsSnsBtnList').css('display', 'block');

            //【Android低いバージョン】
            if ($('#' + section.attr('id') + ' .jsGiftCardMessage').hasClass('androidLowVersion')) {
                $('#' + section.attr('id') + ' .jsGiftCard').css('pointer-events', 'none');
            }

            //【スマホ】グリーティングカードを表示する所の高さを調整
            if ($('#' + section.attr('id') + ' #giftModule1').hasClass('SP') && card_image_url != null) {
                var newHeight = parseInt($('#' + section.attr('id') + ' #giftModule1').css('height')) - parseInt($('#' + section.attr('id') + ' .jsGiftcardSlider').height());
                $('#' + section.attr('id') + ' #giftModule1').css('height', newHeight);
            }
        },

        //スライダーを初期化する
        initFlexsliderPC: function(target) {
            var section = $(target).parents().filter(".jsMessage");
            var sliders = {};
            $('#' + section.attr('id') + ' .jsGiftcardSlider').flexslider({
                animation: "slide",
                controlNav: false,
                animationLoop: false,
                slideshow: false,
                itemWidth: 100,
                itemMargin: 5,
                start: function (carousel) {
                    var id = carousel.attr('id');
                    sliders[id] = carousel;

                    var cardWrap = carousel.parents('.jsMessageGift');
                    var card = cardWrap.find('.jsGiftCard');
                    if (card.find('.jsCardBackground').outerHeight() > card.outerHeight()) {
                        card.css({
                            'padding-top': card.find('.jsCardBackground').outerHeight()
                        });
                    }
                }
            });
            $(document).on('click', '.jsGiftCardCange', function (event) {
                var imgAttr = $(this).attr('src');
                var card = $(this).parents('.jsMessageGift').find('.jsCardBackground');

                if (card.find('img').length < 1) {
                    card.append('<img>');
                }
                card.find('img').attr('src', imgAttr);
                card.find('img').attr('alt', imgAttr);
            });
        },
        initFlexsliderSP: function(target) {
            var section = $(target).parents().filter(".jsMessage");
            var sliders = {};
            $('#' + section.attr('id') + ' .jsGiftcardSlider').flexslider({
                animation: "slide",
                controlNav: false,
                directionNav: false,
                animationLoop: false,
                slideshow: false,
                itemWidth: 100,
                itemMargin: 5,
                start: function (carousel) {
                    var id = carousel.attr('id');
                    sliders[id] = carousel;

                    var cardWrap = carousel.parents('.jsMessageGift');
                    var card = cardWrap.find('.jsGiftCard');
                    if (card.find('.jsCardBackground').outerHeight() > card.outerHeight()) {
                        card.css({
                            'padding-top': card.find('.jsCardBackground').outerHeight()
                        });
                    }

                    // android fit
                    if (androidVersion() < 4.4 && (browserType() == 'others' || browserType() == 'safari')) {
                        $('#' + section.attr('id') + ' .jsGiftCardMessage').addClass('androidLowVersion');
                        card.find('input, textarea').each(function (i, elem) {
                            var elemClass = $(elem).attr('class');
                            var elemStyle = $(elem).attr('style');
                            var elemValue = Brandco.helper.escapeSpecialCharacter($(elem).val() ? $(elem).val() : $(elem).attr('placeholder'));
                            if ($(elem).prop("tagName") == 'textarea') {
                                elemValue = $(elem).text();
                            }
                            $(elem).replaceWith(function () {
                                return '<span class="' + elemClass + '" style="' + elemStyle + '">' + elemValue + '</span>';
                            });
                        });
                        card.on('click', function (event) {
                            openModal('#modal1');
                        });
                    }

                    var winWid;
                    var scale;
                    var height;
                    if ($(window).outerWidth() > 320) {
                        winWid = $(window).outerWidth() - 40;
                    } else {
                        winWid = 320 - 40;
                    }
                    scale = winWid / 580; // card width fixed 580px
                    card.css({
                        'transform': 'scale(' + scale + ')'
                    });
                    if (cardWrap.hasClass('resizeGiftCard')) {
                        height = cardWrap.outerHeight() + 20 + 100 * (card.outerHeight() / card.outerWidth());
                    } else {
                        height = cardWrap.outerHeight() + card.outerHeight() * (scale - 1);
                    }
                    cardWrap.css({
                        'height': height
                    });
                }
            });
            $(document).on('click', '.jsGiftCardCange', function (event) {
                var imgAttr = $(this).attr('src');
                var card = $(this).parents('.jsMessageGift').find('.jsCardBackground');

                if (card.find('img').length < 1) {
                    card.append('<img>');
                }
                card.find('img').attr('src', imgAttr);
                card.find('img').attr('alt', imgAttr);
            });
        },
        initFlexslider: function(target) {
            var section = $(target).parents().filter('.jsMessage');
            if ($('#' + section.attr('id') + ' #giftModule1').hasClass('SP')) {
                UserActionGiftService.initFlexsliderSP(target);
            } else {
                UserActionGiftService.initFlexsliderPC(target);
            }
        },
        resizeGeneratedGiftCard : function(target) { //画像合成した後のブラウザリロード
            var section = $(target).parents().filter('.jsMessage');
            $('#' + section.attr('id') + ' .jsGiftCard .jsCardBackground img').one("load", function() {
                if ($('#' + section.attr('id') + ' #giftModule1').hasClass('SP')) {
                    var scale;
                    var winWid;
                    if($(window).outerWidth() > 320) {
                        winWid = $(window).outerWidth() - 40;
                    } else {
                        winWid = 320 - 40;
                    }
                    scale = winWid / 580; // card width fixed 580px
                    $('#' + section.attr('id') + ' .jsGiftCard').css({
                        'transform': 'scale('+scale+')'
                    });
                    $('#' + section.attr('id') + ' #giftModule1').css({
                        'height':$('#' + section.attr('id') + ' .jsGiftCard .jsCardBackground').outerHeight() * scale
                    });
                    $('#' + section.attr('id') + ' #giftModule1').addClass('resizeGiftCard');

                } else {
                    $('#' + section.attr('id') + ' .jsGiftCard').css({
                        'padding-top': $('#' + section.attr('id') + ' .jsGiftCard .jsCardBackground').outerHeight()
                    });
                }
            }).each(function() {
                if(this.complete) $(this).load();
            });
        }

    }
    $(document).ready(function(){

        //グリーティングカードを作成する為のAjax処理
        $(document).on('click', '#cardGenerate', function() {
            var section = $(this).parents().filter('.jsMessage');
            if ($('#' + section.attr('id') + ' .jsGiftCardMessage').hasClass('androidLowVersion')) {             //【Androidの低いバージョン】
                var receiverText    = $('#modal_' + section.attr('id') + ' .jsModalGiftMessage .jsCardAddressee').val()
                var senderText      = $('#modal_' + section.attr('id') + ' .jsModalGiftMessage .jsCardSender').val()
                var contentText     = $('#modal_' + section.attr('id') + ' .jsModalGiftMessage .jsCardMessage').val()
            } else {
                var receiverText    = $('#' + section.attr('id') + ' .jsMessageGift .jsCardAddressee').val();
                var senderText      = $('#' + section.attr('id') + ' .jsMessageGift .jsCardSender').val();
                var contentText     = $('#' + section.attr('id') + ' .jsMessageGift .jsCardMessage').val();
            }
            var hasError = false;
            if (!receiverText) {
                $('#' + section.attr('id') + ' .jsMessageGift .jsCardAddressee').addClass('emptyError');
                hasError = true;
            } else {
                $('#' + section.attr('id') + ' .jsMessageGift .jsCardAddressee').removeClass('emptyError');
            }
            if (!senderText) {
                $('#' + section.attr('id') + ' .jsMessageGift .jsCardSender').addClass('emptyError');
                hasError = true;
            } else {
                $('#' + section.attr('id') + ' .jsMessageGift .jsCardSender').removeClass('emptyError');
            }
            if (!contentText) {
                $('#' + section.attr('id') + ' .jsMessageGift .jsCardMessage').addClass('emptyError');
                hasError = true;
            } else {
                $('#' + section.attr('id') + ' .jsMessageGift .jsCardMessage').removeClass('emptyError');
            }
            if (hasError) {
                $('#' + section.attr('id') + ' .jsGiftCardInputError').css('display', 'block');
            } else {
                $('#' + section.attr('id') + ' .jsGiftCardInputError').css('display', 'none');
                UserActionGiftService.generateImage(this, receiverText, senderText, contentText);
            }
        });

        //グリーティングカードを再作成する
        $(document).on('click', '.jsEditGiftCardBtn', function() {
            var section = $(this).parents().filter('.jsMessage');
            //SNSボタンを非表示にする
            $('#' + section.attr('id') + ' .jsSnsBtnList').css('display', 'none');

            //クーポン情報を非表示にする
            $('#' + section.attr('id') + ' #editGiftCard').css('display', 'none');

            //オリジナル画像にロールバックする
            $('#' + section.attr('id') + ' .jsMessageGift .jsCardBackground').find('img').attr('src', $('#' + section.attr('id') + ' .jsMessageGift .jsCardBackground').find('img').attr('alt'));

            //「作成する」ボタンを表示する
            $('#' + section.attr('id') + ' #imageGenerateBtn').css('display', 'block');

            //インプットフォームを表示する
            $('#' + section.attr('id') + ' .jsGiftCardMessage').css('display', 'block');

            //スライダーを表示する
            $('#' + section.attr('id') + ' .jsMessageGift .jsGiftcardSlider').css('display', 'block');

            //【Android低いバージョン】
            if ($('#' + section.attr('id') + ' .jsGiftCardMessage').hasClass('androidLowVersion')) {
                $('#' + section.attr('id') + ' .jsGiftCard').css('pointer-events', 'auto');
            }

            UserActionGiftService.initFlexslider(this);
            //【スマホ】グリーティングカードを表示する所の高さを調整
            if ($('#' + section.attr('id') + ' #giftModule1').hasClass('SP')) {
                var newHeight = parseInt($('#' + section.attr('id') + ' #giftModule1').css('height')) + parseInt($('#' + section.attr('id') + ' .jsGiftcardSlider').height());
                $('#' + section.attr('id') + ' #giftModule1').css('height', newHeight);
            }
        });

        /*
         【Android低いバージョン】
         Androidデバイスの低いバージョンはグリーティングカードを設定する際にモーダルで設定する必要があります。
         モダールからグリーティングカードにテキストを持ってくる
         */
        $(document).on('click', '#setMessageAndroid', function() {
            var section = $(this).parents().filter('.jsModalMessage');
            var sectionId = section.attr('id').split('modal_');
            $('#' + sectionId[1] + ' .jsMessageGift .jsCardAddressee').text($('#' + section.attr('id') + ' .jsModalGiftMessage .jsCardAddressee').val());
            $('#' + sectionId[1] + ' .jsMessageGift .jsCardMessage').html(Brandco.helper.escapeSpecialCharacter($('#' + section.attr('id') + ' .jsModalGiftMessage .jsCardMessage').val()));
            $('#' + sectionId[1] + ' .jsMessageGift .jsCardSender').text($('#' + section.attr('id') + ' .jsModalGiftMessage .jsCardSender').val());
            closeModal(1);
        });

        $(document).on('click', '.jsLineSendingBtn a', function() {
            var section = $(this).parents().filter('.jsMessage');
            window.open('http://line.naver.jp/R/msg/text/?' + $('#' + section.attr('id') + ' .jsGreetingCardUrl').text(), '_blank');
            UserActionGiftService.sendGiftMessage(this, 2);
        });

        $(document).on('click', '.jsFBSendingBtn a', function() {
            var section = $(this).parents().filter('.jsMessage');
            var target = this;
            FB.ui({
                method: 'send',
                link: $('#' + section.attr('id') + ' .jsGreetingCardUrl').text()
            }, function(data) {
                if (data.success) {
                    UserActionGiftService.sendGiftMessage(target, 1);
                }
            })
        });

        $(document).on('click', '.jsMailSendingBtn a', function() {
            var section = $(this).parents().filter('.jsMessage');
            window.location.href = "mailto:?subject=ギフトが届きました。&body=" + encodeURIComponent($('#' + section.attr('id') + ' .jsGreetingCardUrl').text());
            UserActionGiftService.sendGiftMessage(this, 3);
        });

    });
}
$(document).ready(function() {
    UserActionGiftService.initUtility();

    //スライダー表示
    $('.jsSnsBtnList').each(function() {
        var section = $(this).parents().filter('.jsMessage');
        if ($('#' + section.attr('id') + ' .jsMessageGift .jsCardBackground').hasClass('hasCard')) {
            UserActionGiftService.giftMessageAfterGenerateImage(this);
            UserActionGiftService.resizeGeneratedGiftCard(this);

        } else {
            if ($('#' + section.attr('id') + ' #giftModule1').length > 0) {
                UserActionGiftService.initFlexslider(this);
            }
        }
    });
});
