<?php if($data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
    <?php write_html($this->formHidden('is_fan_list_page', 1)) ?>
<?php else: ?>
    <?php $disable = ($data['action']->status == CpAction::STATUS_FIX) ? 'disabled' : '' ?>
<?php endif; ?>

<section class="moduleEdit1">
    <?php write_html($this->parseTemplate('CpActionModuleTitle.php', array('disable'=>$disable))); ?>
    <?php write_html($this->parseTemplate('CpActionModuleImage.php', array('disable'=>$disable))); ?>
    <?php write_html($this->parseTemplate('CpActionModuleText.php', array('disable'=>$disable))); ?>
    <section class="moduleCont1 jsModuleContWrap">
        <h1 class="editGift1 jsModuleContTile">ギフト</h1>
        <div class="moduleSettingWrap jsModuleContTarget">
            <dl class="moduleSettingList">
                <?php write_html($this->parseTemplate('CpActionGiftCardConfig.php', array('disable' => $disable))); ?>
                <?php write_html($this->parseTemplate('CpActionGiftIncentiveConfig.php', array('disable' => $disable))); ?>
                <!-- /.moduleLot --></dl>
            <!-- /.moduleSettingWrap --></div>
        <!-- /.moduleCont1 --></section>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('CpActionModuleDeadLine')->render([
            'ActionForm'       => $data['ActionForm'],
            'ActionError'      => $data['ActionError'],
            'cp_action'        => $data['action'],
            'is_login_manager' => $data['pageStatus']['isLoginManager'],
            'disable'          => $disable,
        ])); ?>
    <!-- /.moduleEdit1 --></section>

<section class="modulePreview1">
    <header class="modulePreviewHeader">
        <p>スマートフォン<a href="#" class="toggle_switch right jsModulePreviewSwitch">toggle_switch</a>PC</p>
    <!-- /.modulePreviewHeader --></header>
    <ul class="tablink1" id="tabPreview">
        <li class="current" id="1"><span>送る側</span></li>
        <li id="2"><span>受け取る側</span></li>
        <!-- /.tablink1 --></ul>
    <div class="displayPC jsModulePreviewArea" id="senderPreview">
        <section class="messageWrap">
            <section class="message">
                <p class="messageImg"><img src="" width="600" height="300" id="imagePreview"></p>
                <p class="messageText" id="textPreview"></p>
                <div class="messageGift jsMessageGiftPreview">
                    <div class="giftCarousel flexslider jsGiftcardSlider">
                        <ul class="slides" id="sliderPreview">
                            <?php foreach ($this->gift_card_uploads as $key=>$value): ?>
                                <li id="<?php assign_js($key)?>"><img src="<?php assign_js($value->image_url);?>" alt="Card title" class="jsGiftCardCange"></li>
                            <?php endforeach;?>
                        </ul>
                        <!-- /.giftCarousel --></div>
                    <div class="giftCard jsGiftCardPreview" id="giftCard1">
                        <p class="cardBackground jsCardBackgroundPreview">
                            <img src="<?php assign_js($this->gift_card_uploads != null ? $value->image_url : '')?>">
                            <!-- /.cardBackground --></p>
                        <p class="cardArea" id="giftCardMessage1">
                            <input type="text" class="cardAddressee jsCardAddresseePreview" placeholder="あて先">
                            <textarea name="" id="" cols="30" rows="10" class="cardMessage jsCardMessagePreview"></textarea>
                            <input type="text" class="cardSender jsCardSenderPreview" placeholder="送り主">
                            <!-- /.cardArea --></p>
                        <!-- /.giftCard --></div>

                    <link rel="stylesheet" href="<?php assign_js($this->setVersion('/css/flexslider/flexslider.css'))?>">
                    <script src="<?php assign_js($this->setVersion('/js/flexslider/jquery.flexslider-min.js'))?>"></script>
                    <!-- /.messageGift --></div>

                <div class="messageFooter">
                    <ul class="btnSet">
                        <li class="btn3"><a href="javascript:void(0)" class="middle1" id="btnPreview"><?php assign($this->ActionForm['button_label_text'])?></a></li>
                        <!-- /.btnSet --></ul>
                    <ul class="btnSet" id="btnSns">
                        <li class="btnSnsLn1 jsLineSendingBtn" style="display: none"><a href="javascript:void(0)" class="arrow1"><span class="inner">LINE<br>で贈る</span></a></li>
                        <li class="btnSnsFb1 jsFBSendingBtn"><a href="javascript:void(0)" class="arrow1"><span class="inner">Facebook<br>で贈る</span></a></li>
                        <li class="btnMail1"><a href="javascript:void(0)" class="arrow1"><span class="inner">メール<br>で贈る</span></a></li>
                        <!-- /.btnSet --></ul>
                </div>

            </section>
        </section>
    </div>
    <div class="displayPC jsModulePreviewArea" id="receiverPreview" style="display: none">
        <section class="messageWrap">

            <section class="message jsCouponPreview">
                <h1 class="messageHd1"> ギフトが届いています。</h1>
                <div class="messageGift jsMessageGiftPreview">
                    <div class="giftCard jsGiftCardPreview" id="giftCard1">
                        <p class="cardBackground jsCardBackgroundPreview">
                            <img src="<?php assign_js($this->gift_card_uploads != null ? $value->image_url : '')?>">
                            <!-- /.cardBackground --></p>
                        <div class="cardArea" id="giftCardMessage1">
                            <div class="cardAddressee jsCardAddresseePreview">あて先</div>
                            <div style="line-height: 1.5em" class="cardMessage jsCardMessagePreview"></div>
                            <div class="cardSender jsCardSenderPreview">送り主</div>
                            <!-- /.cardArea --></div>
                        <!-- /.giftCard --></div>
                    <!-- /.messageGift --></div>
                <ul class="btnSet">
                    <li class="btn3"><a href="javascript:void(0)" class="large1">クーポンを受け取る</a></li>
                    <!-- /.btnSet --></ul>
                <p class="couponExtra">クーポン名：<span class="couponName jsCouponNameWithButton"></span><br>有効期限：<span class="couponLimit">2015年8月31日 23:59まで</span></p>
                <section class="messageText jsIncentiveDescriptionPreview"></section>
                <!-- /.message --></section>
            <section class="message jsCouponPreview">
                <h1 class="messageHd1"> ギフトが届いています。</h1>
                <div class="messageGift jsMessageGiftPreview">
                    <div class="giftCard jsGiftCardPreview" id="giftCard1">
                        <p class="cardBackground jsCardBackgroundPreview">
                            <img src="<?php assign_js($this->gift_card_uploads != null ? $value->image_url : '')?>">
                            <!-- /.cardBackground --></p>
                        <div class="cardArea" id="giftCardMessage1">
                            <div class="cardAddressee jsCardAddresseePreview">あて先</div>
                            <div style="line-height: 1.5em" class="cardMessage jsCardMessagePreview"></div>
                            <div class="cardSender jsCardSenderPreview">送り主</div>
                            <!-- /.cardArea --></div>
                        <!-- /.giftCard --></div>
                    <!-- /.messageGift --></div>
                <div class="messageCoupon">
                    <p class="couponName jsCouponNameWithDetail"></p>
                    <p class="couponNum"><strong>123456789-123456789</strong></p>
                    <p class="couponLimit">0000年00月00日 23:59まで</p>
                    <!-- /.messageCoupon --></div>
                <section class="messageText jsIncentiveDescriptionPreview"></section>
                <!-- /.message --></section>
            <section class="message jsProductPreview">
                <h1 class="messageHd1"> ギフトが届いています。</h1>
                <div class="messageGift jsMessageGiftPreview">
                    <div class="giftCard jsGiftCardPreview" id="giftCard1">
                        <p class="cardBackground jsCardBackgroundPreview">
                            <img src="<?php assign_js($this->gift_card_uploads != null ? $value->image_url : '')?>">
                            <!-- /.cardBackground --></p>
                        <div class="cardArea" id="giftCardMessage1">
                            <div class="cardAddressee jsCardAddresseePreview">あて先</div>
                            <div style="line-height: 1.5em" class="cardMessage jsCardMessagePreview"></div>
                            <div class="cardSender jsCardSenderPreview">送り主</div>
                            <!-- /.cardArea --></div>
                        <!-- /.giftCard --></div>
                    <!-- /.messageGift --></div>
                <section class="messageText jsIncentiveDescriptionPreview"></section>
                <!-- /.message --></section>
            <section class="message jsProductPreview">
                <h1 class="messageHd1"> ギフトの配送先を入力してください。</h1>
                <ul class="commonTableList1">
                    <li class="element_name jsProductPostalName">
                        <p class="title1">
                            <span class="require1">氏名（かな）</span>
                            <!-- /.title1 --></p>
                        <p class="itemEdit">
                                <span class="editInput">
                                    <label class="editName"><span>姓</span><input type="text" class="name"></label><label class="editName"><span>名</span><input type="text" class="name"></label>
                                </span>
                                <span class="editInput">
                                    <label class="editName"><span>せい</span><input type="text" class="name"></label><label class="editName"><span>めい</span><input type="text" class="name"></label>
                                <!-- /.editInput --></span>
                            <!-- /.itemEdit --></p>
                    </li>
                    <li class="element_address jsProductPostalAddress">
                        <p class="title1">
                            <span class="require1">郵便番号</span>
                            <!-- /.title1 --></p>
                        <p class="itemEdit">
                                <span class="editInput">
                                    <input type="text" class="inputNum">－<input type="text" class="inputNum">
                                    <a href="javascript:void(0);">住所検索</a><span class="supplement1">※半角数字</span>
                                <!-- /.editInput --></span>
                            <!-- /.itemEdit --></p>
                    </li>
                    <li class="element_address jsProductPostalAddress">
                        <p class="title1">
                            <span class="require1">都道府県</span>
                            <!-- /.title1 --></p>
                        <p class="itemEdit">
                                <span class="editInput">
                                    <?php write_html($this->formSelect("pref", '13'/*東京*/, array(), $data['prefectures']));?>
                                    <!-- /.editInput --></span>
                            <!-- /.itemEdit --></p>
                    </li>
                    <li class="element_address jsProductPostalAddress">
                        <p class="title1">
                            <span class="require1">市区町村</span>
                            <!-- /.title1 --></p>
                        <p class="itemEdit">
                                <span class="editInput">
                                    <input type="text">
                                <!-- /.editInput --></span>
                            <!-- /.itemEdit --></p>
                    </li>
                    <li class="element_address jsProductPostalAddress">
                        <p class="title1">
                            <span class="require1">番地</span>
                            <!-- /.title1 --></p>
                        <p class="itemEdit">
                                <span class="editInput">
                                    <input type="text">
                                <!-- /.editInput --></span>
                            <!-- /.itemEdit --></p>
                    </li>
                    <li class="element_address jsProductPostalAddress">
                        <p class="title1">建物<!-- /.title1 --></p>
                        <p class="itemEdit">
                                <span class="editInput">
                                    <input type="text">
                                <!-- /.editInput --></span>
                            <!-- /.itemEdit --></p>
                    </li>
                    <li class="element_tel jsProductPostalTel">
                        <p class="title1">
                            <span class="require1">電話番号</span>
                            <!-- /.title1 --></p>
                        <p class="itemEdit">
                                <span class="editInput">
                                    <input type="text" class="inputNum">－<input type="text" class="inputNum">－<input type="text" class="inputNum">
                                <!-- /.editInput --></span>
                            <span class="supplement1">※半角数字</span>
                            <!-- /.itemEdit --></p>
                    </li>
                    <!-- /.commonTableList1 --></ul>

                <div class="messageFooter">
                    <ul class="btnSet">
                        <li class="btn3"><a href="javascript:void(0)" class="middle1" id="btnPreview">送信</a></li>
                    </ul>
                </div>
            </section>

            <section class="message_introduceCp">
                <h1 class="messageHd1">キャンペーンの紹介</h1>
                <div class="introduceInner">
                    <p class="jsReceiverDescriptionPreview"></p>
                    <div class="cpPrev">
                        <?php if ($this->campaign_info['image_url']) :?>
                            <figure><img src="<?php assign($this->campaign_info['image_url'])?>" alt=""></figure>
                        <?php endif;?>
                        <p>
                            <strong class="title"><?php assign($this->campaign_info['title'])?></strong>
                            <small class="description"><?php write_html($this->campaign_info['html_content'] ? strip_tags($this->campaign_info['html_content']) : $this->toHalfContentDeeply($this->campaign_info['text']))?></small>
                            <small class="account">
                                <img src="<?php assign($this->pageStatus['brand']->getProfileImage());?>" width="10" height="10" alt="account name">
                                <?php assign($this->pageStatus['brand']->name)?>
                            </small>
                        </p>
                        <!-- /.cpPrev --></div>
                    <!-- /.introduceInner --></div>
                <ul class="btnSet">
                    <li class="btn3"><a href="javascript:void(0)" class="large2">キャンペーンを見る</a></li>
                </ul>
                <!-- /.message_introduceCp --></section>
        </section>
    </div>
    <!-- /.modulePreview --></section>
