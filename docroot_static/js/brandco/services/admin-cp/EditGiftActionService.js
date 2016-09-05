var EditGiftActionService = {
    initMessageCardPreview: function() {
        $('.jsGiftCardConfigRequired').each(function() {
            if (this.checked) {
                $('.jsMessageGiftPreview').show();
                EditGiftActionService.initCardConfigSlider();
            } else {
                $('.jsMessageGiftPreview').hide();
            }
        });

    },
    initCardConfigPreview: function(){
        $(".jsCardMessagePreview").css('resize', 'none');
        $(".jsCardMessagePreview").css('pointer-events', 'none');

        if ($('.text_color').val()) {
            var temp = Brandco.helper.escapeSpecialCharacter($('.text_color').val());
            $(".jsCardMessagePreview").css('color', temp);
            $(".jsCardAddresseePreview").css('color', temp);
            $(".jsCardSenderPreview").css('color', temp);
        }

        //Receiver
        if ($('.to_x').val()) {
            var temp = Brandco.helper.escapeSpecialCharacter($('.to_x').val());
            $('.jsCardAddresseePreview').css({left : parseInt(temp)});
        }
        if ($('.to_y').val()) {
            var temp = Brandco.helper.escapeSpecialCharacter($('.to_y').val());
            $('.jsCardAddresseePreview').css({top : parseInt(temp)});
        }
        if ($('.to_text_size').val()) {
            var temp = Brandco.helper.escapeSpecialCharacter($('.to_text_size').val());
            $('.jsCardAddresseePreview').css('font-size', parseInt(temp));
        }
        if ($('.to_size').val()) {
            var temp = Brandco.helper.escapeSpecialCharacter($('.to_size').val());
            $('.jsCardAddresseePreview').css('width', parseInt(temp));
        }

        //Sender
        if ($('.from_x').val()) {
            var temp = Brandco.helper.escapeSpecialCharacter($('.from_x').val());
            $('.jsCardSenderPreview').css('left', parseInt(temp));
        }
        if ($('.from_y').val()) {
            var temp = Brandco.helper.escapeSpecialCharacter($('.from_y').val());
            $('.jsCardSenderPreview').css('top', parseInt(temp));
        }
        if ($('.from_text_size').val()) {
            var temp = Brandco.helper.escapeSpecialCharacter($('.from_text_size').val());
            $('.jsCardSenderPreview').css('font-size', parseInt(temp));
        }
        if ($('.from_size').val()) {
            var temp = Brandco.helper.escapeSpecialCharacter($('.from_size').val());
            $('.jsCardSenderPreview').css('width', parseInt(temp));
        }

        //メッセージ
        if ($('.content_x').val()) {
            var temp = Brandco.helper.escapeSpecialCharacter($('.content_x').val());
            $('.jsCardMessagePreview').css('left', parseInt(temp));
        }
        if ($('.content_y').val()) {
            var temp = Brandco.helper.escapeSpecialCharacter($('.content_y').val());
            $('.jsCardMessagePreview').css('top', parseInt(temp));
        }
        if ($('.content_width').val()) {
            var temp = Brandco.helper.escapeSpecialCharacter($('.content_width').val());
            $('.jsCardMessagePreview').css('width', parseInt(temp));
        }
        if ($('.content_height').val()) {
            var temp = Brandco.helper.escapeSpecialCharacter($('.content_height').val());
            $('.jsCardMessagePreview').css('height', parseInt(temp));
        }
        if ($('.content_text_size').val()) {
            var temp = Brandco.helper.escapeSpecialCharacter($('.content_text_size').val());
            $('.jsCardMessagePreview').css('font-size', parseInt(temp));
        }
        if ($('.content_default_text').val()) {
            $('.jsCardMessagePreview').val($('.content_default_text').val());
            var temp = Brandco.helper.escapeSpecialCharacter($('.content_default_text').val());
            $('#receiverPreview .jsCardMessagePreview').html(temp);
        }

    },
    initIncentiveConfigPreview: function() {
        $('.jsCouponPreview .jsCouponNameWithButton').html($('.jsGiftCouponSetting option:selected').text());
        $('.jsCouponPreview .jsCouponNameWithDetail').html($('.jsGiftCouponSetting option:selected').text());
        if ($('.jsGiftIncentiveDescription').val()) {
            var param = {
                data: {
                    text_content: $('.jsGiftIncentiveDescription').val()
                },
                url: 'admin-cp/parse_markdown',
                success: function(response) {
                    if (response.result == 'ok') {
                        $('.jsIncentiveDescriptionPreview').each(function() {
                            $(this).html(response.data.html_content);
                        });
                    }
                }
            };
            Brandco.api.callAjaxWithParam(param, false,  false);
        }
        $('.jsProductSetting input[name=gift_product_postal_name_flg]').each(function() {
            if (this.checked) {
                $('.jsProductPreview .jsProductPostalName').css('display', 'block');
            } else {
                $('.jsProductPreview .jsProductPostalName').css('display', 'none');
            }
        });
        $('.jsProductSetting input[name=gift_product_postal_address_flg]').each(function() {
            if (this.checked) {
                $('.jsProductPreview .jsProductPostalAddress').css('display', 'block');
            } else {
                $('.jsProductPreview .jsProductPostalAddress').css('display', 'none');
            }
        });
        $('.jsProductSetting input[name=gift_product_postal_tel_flg]').each(function() {
            if (this.checked) {
                $('.jsProductPreview .jsProductPostalTel').css('display', 'block');
            } else {
                $('.jsProductPreview .jsProductPostalTel').css('display', 'none');
            }
        });
        $('.jsIncentiveSetting input[name=incentive_type]').each(function() {
            if ($(this).val() == 1 && this.checked) {
                $('.jsProductPreview').css('display', 'none');
                $('.jsCouponPreview').css('display', 'block');
            } else if($(this).val() == 2 && this.checked) {
                $('.jsCouponPreview').css('display', 'none');
                $('.jsProductPreview').css('display', 'block');
            }
        });


    },
    initReceiverConfigPreview: function() {
        if ($('.jsReceiverCampaignDetail').val()) {
            $('.jsReceiverDescriptionPreview').html(Brandco.helper.escapeSpecialCharacter($('.jsReceiverCampaignDetail').val()));
        }
    },
    //スライダーを表示する為の対処（PC版）
    initCardConfigSliderPC : function() {
        // gift card
        if($('ul#sliderPreview li').length >=1) {
            var sliders = {};
            $('.jsGiftcardSlider').flexslider({
                animation: "slide",
                controlNav: false,
                animationLoop: false,
                slideshow: false,
                itemWidth: 100,
                itemHeight: 100,
                itemMargin: 5,
                start: function(carousel) {
                    var id = carousel.attr('id');
                    sliders[id] = carousel;
                    var cardWrap = carousel.parents('.jsMessageGiftPreview');
                    var card = cardWrap.find('.jsGiftCardPreview');
                    card.css({
                        'padding-top': card.find('.jsCardBackgroundPreview').outerHeight()
                    });
                    cardWrap.css({
                        'height': cardWrap.outerHeight()
                    });

                    $('#receiverPreview .jsGiftCardPreview').css({
                        'padding-top': card.find('.jsCardBackgroundPreview').outerHeight()
                    });
                }
            });
            $('.jsGiftcardSlider').addClass('called');
            $(document).on('click','.jsGiftCardCange', function() {
                var imgAttr = $(this).attr('src');
                var card =  $(this).parents('.jsMessageGiftPreview').find('.jsCardBackgroundPreview');
                card.find('img').attr('src', imgAttr);
            });
        } else {
            $('.jsMessageGiftPreview').hide();
        }
    },
    //スライダーを表示する為の対処（SP版）
    initCardConfigSliderSP : function() {
        // gift card
        if($('ul#sliderPreview li').length >=1) {
            var sliders = {};
            $('.jsGiftcardSlider').flexslider({
                animation: "slide",
                controlNav: false,
                animationLoop: false,
                slideshow: false,
                itemWidth: 100,
                itemHeight: 100,
                itemMargin: 5,
                start: function(carousel) {
                    var id = carousel.attr('id');
                    sliders[id] = carousel;
                    var cardWrap = carousel.parents('.jsMessageGiftPreview');
                    var card = cardWrap.find('.jsGiftCardPreview');
                    card.css({
                        'padding-top': card.find('.jsCardBackgroundPreview').outerHeight()
                    });
                    var scale = 278/578;
                    $('.jsGiftCardPreview').css({
                        'transform': 'scale('+scale+')'
                    });
                    $('.jsMessageGiftPreview').css({
                        'height': $('.jsMessageGiftPreview').outerHeight() + $('.jsGiftCardPreview').outerHeight() * (scale - 1)
                    });
                    //受け取り側
                    $('#receiverPreview .jsGiftCardPreview').css({
                        'padding-top': card.find('.jsCardBackgroundPreview').outerHeight()
                    });
                    $('#receiverPreview .jsGiftCardPreview').css({
                        'transform': 'scale('+scale+')'
                    });
                    $('#receiverPreview .jsMessageGiftPreview').css({
                        'height': $('#receiverPreview .jsGiftCardPreview').outerHeight() * scale
                    });
                }
            });
            $('.jsGiftcardSlider').addClass('called');
            $(document).on('click','.jsGiftCardCange', function() {
                var imgAttr = $(this).attr('src');
                var card =  $(this).parents('.jsMessageGiftPreview').find('.jsCardBackgroundPreview');
                card.find('img').attr('src', imgAttr);
            });
        } else {
            $('.jsMessageGiftPreview').hide();
        }
    },
    initCardConfigSlider : function() {
        if (!$('.jsGiftcardSlider').hasClass('called')) {
            if($('.jsModulePreviewSwitch').hasClass('left')) {
                EditGiftActionService.initCardConfigSliderSP();
            } else {
                EditGiftActionService.initCardConfigSliderPC();
            }
        }
    }
}

$(document).ready(function(){

    //送る側と受け取る側との切り替えるタブ
    $('#tabPreview li').click(function(e) {
        $('#tabPreview li').removeClass('current');
        $(this).addClass('current');
        if ($(this).attr('id') == 1) {
            $('#receiverPreview').css('display', 'none');
            $(".jsCardMessagePreview").css('border-style', 'dotted');
            $(".jsCardAddresseePreview").css('border-style', 'dotted');
            $(".jsCardSenderPreview").css('border-style', 'dotted');
            $('#senderPreview').css('display', 'block');
        } else if ($(this).attr('id') == 2) {
            $('#senderPreview').css('display', 'none');
            $(".jsCardMessagePreview").css('border-style', 'none');
            $(".jsCardAddresseePreview").css('border-style', 'none');
            $(".jsCardSenderPreview").css('border-style', 'none');
            $('#receiverPreview').css('display', 'block');
        }
    });

    //グリーティングカードを使う、使わないという設定
    $('.jsGiftCardConfigRequired').on('change', function() {
        if (this.checked) {
            //送る側に切り替える
            if (parseInt($('#tabPreview .current').attr('id')) != 1) {
                $('#tabPreview li#1').trigger('click');
            }
            
            $('.jsGiftCardSetting').css('pointer-events', 'auto');
            if ($('ul#sliderPreview li').length >=1) {
                $('.jsMessageGiftPreview').show();
                EditGiftActionService.initCardConfigSlider();
            } else {
                $('.jsMessageGiftPreview').hide();
            }
        } else {
            $('.jsGiftCardSetting').css('pointer-events', 'none');
            $('.jsMessageGiftPreview').hide();
        }
    });

    //アップロードを追加する
    $(document).on('click', '.jsUploadPhotoLink', function() {

        //送る側に切り替える
        if (parseInt($('#tabPreview .current').attr('id')) != 1) {
            $('#tabPreview li#1').trigger('click');
        }


        var id = $('ul#uploadPreview li').length;
        if(id != 0) {
            var li = $('ul#uploadPreview li:last-child');
            if(li.find('img').attr('src')) {
                id = parseInt(li.attr('id')) + 1;
            } else {
                id = parseInt(li.attr('id'));
                li.remove();
            }
        }
        $('#uploadPreview').append('<li id="'+id+'"><input class="card_upload" id="cardUpload'+id+'" name="gift_card_upload_'+id+'" type="file" style="display: none"/></li>');
        $('input#cardUpload'+id).trigger('click');

    });

    //画像アップロードのアクション対処
    $(document).on('change', 'input.card_upload', function(e1){
        var li = $(this).parent();
        if (this.files && this.files[0]) {
            if(window.FileReader) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var image = new Image();
                    image.src = e.target.result;
                    image.onload = function() {
                        if(image.width == 580) {
                            var oldImage = $('#senderPreview').find('.jsCardBackgroundPreview');
                            var canUpload = false;
                            if(oldImage.outerHeight() != 0) {
                                canUpload =  (oldImage.outerHeight()/oldImage.outerWidth() == image.height/image.width);
                            }

                            if(oldImage.outerHeight() == 0 || canUpload) {
                                li.append('<img src="' + e.target.result + '" alt="image title"><a href="javascript:void(0)" class="iconBtnDelete jsIconBtnDelete">削除する</a>');

                                var cardBackground =  $('.jsMessageGiftPreview').find('.jsCardBackgroundPreview');
                                // flexsliderが初期化済み
                                if($('ul#sliderPreview li').length >=1 || cardBackground.find('img').attr('src') != '') {
                                    $('.flexslider').data('flexslider').addSlide('<li id="'+li.attr('id')+'"><img src="' + e.target.result + '" alt="Card title" class="jsGiftCardCange"></li>');
                                } else {
                                    $('ul#sliderPreview').append('<li id="'+li.attr('id')+'"><img src="' + e.target.result + '" alt="Card title" class="jsGiftCardCange"></li>');
                                    cardBackground.find('img').attr('src', e.target.result);
                                    $('.jsMessageGiftPreview').show();
                                    EditGiftActionService.initCardConfigSlider();
                                }
                            } else {
                                li.remove();
                                alert('過去の画像のサイズ率（縦／横）を同じ様にしてください。');
                            }
                        } else {
                            li.remove();
                            alert('画像の横幅を580pxにしてください！');
                        }
                    };
                };
                reader.readAsDataURL(this.files[0]);
            }
        }
    });

    //アップロード画像を削除する
    $(document).on('click', '.jsIconBtnDelete', function(){
        var li = $('#sliderPreview').find('li#'+$(this).parent().attr('id'));
        $('.flexslider').data('flexslider').removeSlide(li);
        this.parentNode.parentNode.removeChild(this.parentNode);
    });

    //カード設定イベントをキャッチする

    $(document).on('input', '.text_color', function() {
        var temp = Brandco.helper.escapeSpecialCharacter($(this).val());
        $(".jsCardMessagePreview").css('color', temp);
        $(".jsCardAddresseePreview").css('color', temp);
        $(".jsCardSenderPreview").css('color', temp);
    });
    $(document).on('click', '.jsFarbtastic', function() {
        var temp = Brandco.helper.escapeSpecialCharacter($('.text_color').val());
        $(".jsCardMessagePreview").css('color', temp);
        $(".jsCardAddresseePreview").css('color', temp);
        $(".jsCardSenderPreview").css('color', temp);
    });

    //Receiver
    $(document).on('input', '.to_x', function() {
        var temp = Brandco.helper.escapeSpecialCharacter($(this).val());
        $('.jsCardAddresseePreview').css({left : parseInt(temp)});
    });
    $(document).on('input', '.to_y', function() {
        var temp = Brandco.helper.escapeSpecialCharacter($(this).val());
        $('.jsCardAddresseePreview').css({top : parseInt(temp)});
    });
    $(document).on('input', '.to_text_size', function() {
        var temp = Brandco.helper.escapeSpecialCharacter($(this).val());
        $('.jsCardAddresseePreview').css('font-size', parseInt(temp));
    });
    $(document).on('input', '.to_size', function() {
        var temp = Brandco.helper.escapeSpecialCharacter($(this).val());
        $('.jsCardAddresseePreview').css('width', parseInt(temp));
    });

    //Sender
    $(document).on('input', '.from_x', function() {
        var temp = Brandco.helper.escapeSpecialCharacter($(this).val());
        $('.jsCardSenderPreview').css({left : parseInt(temp)});
    });
    $(document).on('input', '.from_y', function() {
        var temp = Brandco.helper.escapeSpecialCharacter($(this).val());
        $('.jsCardSenderPreview').css({top : parseInt(temp)});
    });
    $(document).on('input', '.from_text_size', function() {
        var temp = Brandco.helper.escapeSpecialCharacter($(this).val());
        $('.jsCardSenderPreview').css('font-size', parseInt(temp));
    });
    $(document).on('input', '.from_size', function() {
        var temp = Brandco.helper.escapeSpecialCharacter($(this).val());
        $('.jsCardSenderPreview').css('width', parseInt(temp));
    });

    //メッセージ
    $(document).on('input', '.content_x', function() {
        var temp = Brandco.helper.escapeSpecialCharacter($(this).val());
        $('.jsCardMessagePreview').css({left : parseInt(temp)});
    });
    $(document).on('input', '.content_y', function() {
        var temp = Brandco.helper.escapeSpecialCharacter($(this).val());
        $('.jsCardMessagePreview').css({top : parseInt(temp)});
    });
    $(document).on('input', '.content_text_size', function() {
        var temp = Brandco.helper.escapeSpecialCharacter($(this).val());
        $('.jsCardMessagePreview').css('font-size', parseInt(temp));
    });
    $(document).on('input', '.content_width', function() {
        var temp = Brandco.helper.escapeSpecialCharacter($(this).val());
        $('.jsCardMessagePreview').css('width', parseInt(temp));
    });
    $(document).on('input', '.content_height', function() {
        var temp = Brandco.helper.escapeSpecialCharacter($(this).val());
        $('.jsCardMessagePreview').css('height', parseInt(temp));
    });
    $(document).on('input', '.content_default_text', function() {
        $('.jsCardMessagePreview').val($(this).val());
        var temp = Brandco.helper.escapeSpecialCharacter($(this).val());
        $('#receiverPreview .jsCardMessagePreview').html(temp);
    });

    //クーポンプレビュー
    $(document).on('change', '.jsGiftCouponSetting', function() {
        $('.jsCouponPreview .jsCouponNameWithDetail').html($('.jsGiftCouponSetting option:selected').text());
        $('.jsCouponPreview .jsCouponNameWithButton').html($('.jsGiftCouponSetting option:selected').text());
    });
    $(document).on('input', '.jsGiftIncentiveDescription', function() {
        var param = {
            data: {
                text_content: $(this).val()
            },
            url: 'admin-cp/parse_markdown',
            success: function(response) {
                if (response.result == 'ok') {
                    $('.jsIncentiveDescriptionPreview').html(response.data.html_content);
                }
            }
        };
        Brandco.api.callAjaxWithParam(param, false,  false);

    });

    $(document).on('input', '.jsReceiverCampaignDetail', function() {
        $('.jsReceiverDescriptionPreview').html(Brandco.helper.escapeSpecialCharacter($(this).val()));
    });

    //スマホやPCに切り替える時のカード画像のサイズ調整
    $('.jsModulePreviewSwitch').click(function(){
        var senderCardBackground =  $('#senderPreview .jsMessageGiftPreview').find('.jsCardBackgroundPreview');
        var receiverCardBackground =  $('#receiverPreview .jsMessageGiftPreview').find('.jsCardBackgroundPreview');
        if($(this).hasClass('left')){
            if(senderCardBackground.find('img').attr('src') != '' && $('.jsGiftcardSlider').hasClass('called')) {
                var scale = 278/578;
                $('#senderPreview .jsGiftCardPreview').css({
                    'transform': 'scale('+scale+')'
                });
                $('#senderPreview .jsMessageGiftPreview').css({
                    'height': $('#senderPreview .jsMessageGiftPreview').outerHeight() + $('#senderPreview .jsGiftCardPreview').outerHeight() * (scale - 1)
                });
            }
            if(receiverCardBackground.find('img').attr('src') != '' && $('.jsGiftcardSlider').hasClass('called')) {
                var scale = 278/578;
                $('#receiverPreview .jsGiftCardPreview').css({
                    'transform': 'scale('+scale+')'
                });
                $('#receiverPreview .jsMessageGiftPreview').css({
                    'height': $('#receiverPreview .jsGiftCardPreview').outerHeight() * scale
                });
            }
            $('#btnSns .jsFBSendingBtn').hide();
            $('#btnSns .jsLineSendingBtn').show();

        }else if($(this).hasClass('right')){
            if(senderCardBackground.find('img').attr('src') != '' && $('.jsGiftcardSlider').hasClass('called')) {
                var scale = 278/578;
                $('#senderPreview .jsGiftCardPreview').css({
                    'transform': 'scale(1)'
                });
                $('#senderPreview .jsMessageGiftPreview').css({
                    'height': $('#senderPreview .jsMessageGiftPreview').outerHeight() - $('#senderPreview .jsGiftCardPreview').outerHeight() * (scale - 1)
                });
            }
            if(receiverCardBackground.find('img').attr('src') != '' && $('.jsGiftcardSlider').hasClass('called')) {
                $('#receiverPreview .jsGiftCardPreview').css({
                    'transform': 'scale(1)'
                });
                $('#receiverPreview .jsMessageGiftPreview').css({
                    'height': $('#receiverPreview .jsGiftCardPreview').outerHeight()
                });
            }
            $('#btnSns .jsLineSendingBtn').hide();
            $('#btnSns .jsFBSendingBtn').show();
        }
    });

    $(document).on('change', '.jsIncentiveSetting input[name=incentive_type]', function() {
        if ($(this).val() == 1) {
            $('.jsProductPreview').css('display', 'none');
            $('.jsCouponPreview').css('display', 'block');
        } else {
            $('.jsCouponPreview').css('display', 'none');
            $('.jsProductPreview').css('display', 'block');
        }
    });

    $(document).on('change', '.jsProductSetting input[name=gift_product_postal_name_flg]', function() {
        if (this.checked) {
            $('.jsProductPreview .jsProductPostalName').css('display', 'block');
        } else {
            $('.jsProductPreview .jsProductPostalName').css('display', 'none');
        }
    });
    $(document).on('change', '.jsProductSetting input[name=gift_product_postal_address_flg]', function() {
        if (this.checked) {
            $('.jsProductPreview .jsProductPostalAddress').css('display', 'block');
        } else {
            $('.jsProductPreview .jsProductPostalAddress').css('display', 'none');
        }
    });
    $(document).on('change', '.jsProductSetting input[name=gift_product_postal_tel_flg]', function() {
        if (this.checked) {
            $('.jsProductPreview .jsProductPostalTel').css('display', 'block');
        } else {
            $('.jsProductPreview .jsProductPostalTel').css('display', 'none');
        }
    });

    EditGiftActionService.initCardConfigPreview();
    EditGiftActionService.initMessageCardPreview();
    EditGiftActionService.initIncentiveConfigPreview();
    EditGiftActionService.initReceiverConfigPreview();

    $.datepicker.setDefaults($.datepicker.regional['ja']);
    $(".jsDate").datepicker({
        minDate: new Date()
    });
});

