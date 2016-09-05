<?php
    //OG設定
    $data['pageStatus']['og'] = array(
        'url'         => Util::getCurrentUrl(),
        'title'       => 'ギフトが届きました。',
        'image'       => 'http://s3-ap-northeast-1.amazonaws.com/parts.brandco.jp/image/campaigns/gift_card/imgOGPGift' . $data["campaign"]["ogp_image"] . '.png',
        'description' => '【'.$data["campaign"]["title"].'】から届いたギフトページです。',
    );
    if ($data['product_info']) {
        $expire_flg = strtotime('now') > strtotime($data['product_info']->expire_datetime);
        if ($data['gift_message']->receiver_user_id || $expire_flg) {
            $disable = 'disabled';
        }
    }
?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php if ($data['pageStatus']['is_olympus_header_footer']): ?>
    <?php write_html($this->parseTemplate('OlympusHeader.php', $data['pageStatus'])) ?>
<?php endif ?>
<article>
    <section class="messageWrap">
        <?php if ($data['userInfo'] == null) :?>
            <section class="campaign">
                <p class="campaignImg"><img src="<?php assign($this->setVersion('/img/dummy/imgCoupon1.jpg'));?>" alt="img text"></p>
                <?php if ($data['gift_message']->image_url) :?>
                    <p class="campaignImg"><img src="<?php assign($data['gift_message']->image_url)?>" alt="dummy img"></p>
                <?php endif ;?>
                <div class="joinCommSite">
                    <?php if ($data['gift_used']):?>
                        <p class="attention1">このギフトは既に取得済です。</p>
                        <p>取得された方は、ログインでギフトを表示します。</p>
                    <?php else :?>
                        <h1>ギフトを受け取ろう！</h1>
                    <?php endif ;?>
                    <ul class="btnSet">
                        <li class="btnSnsFb1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', array('platform' => 'fb', 'redirect_url' => urlencode(Util::getCurrentUrl())))) ?>" title="Facebook" class="arrow1"><span class="inner">Facebook<br>でログイン</span></a></li>
                        <li class="btnSnsTw1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', array('platform' => 'tw', 'redirect_url' => urlencode(Util::getCurrentUrl())))) ?>" title="Twitter" class="arrow1"><span class="inner">Twitter<br>でログイン</span></a></li>
                        <li class="btnSnsLn1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', array('platform' => 'line', 'redirect_url' => urlencode(Util::getCurrentUrl())))) ?>" title="LINE" class="arrow1"><span class="inner">LINE<br>でログイン</span></a></li>
                        <?php if(!$data['pageStatus']['is_sugao_brand']):?>
                            <li class="btnSnsIg1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', array('platform' => 'insta', 'redirect_url' => urlencode(Util::getCurrentUrl())))) ?>" title="Instagram" class="arrow1"><span class="inner">Instagram<br>でログイン</span></a></li>
                            <li class="btnSnsGp1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', array('platform' => 'ggl', 'redirect_url' => urlencode(Util::getCurrentUrl())))) ?>" title="Google" class="arrow1"><span class="inner">Google<br>でログイン</span></a></li>
                            <li class="btnSnsYh1"><a href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', array('platform' => 'yh', 'redirect_url' => urlencode(Util::getCurrentUrl())))) ?>" title="Yahoo!" class="arrow1"><span class="inner">Yahoo!<br>でログイン</span></a></li>
                        <?php endif; ?>
                        <?php if($data['canLoginByLinkedIn']): ?>
                            <li class="btnIN2"><a href="<?php assign(Util::rewriteUrl( 'auth', 'service_login', '', array('platform' => 'linkedin', 'redirect_url' => urlencode(Util::getCurrentUrl())))) ?>" title="LinkedIn" class="arrow1"><span class="inner">LinkedIn<br>でログイン</span></a></li>
                        <?php endif; ?>
                        <!-- /.btnSet --></ul>
                    <p class="joinCommSiteLogin"><a href="<?php assign(Util::rewriteUrl('my','login')) ?>" class="iconMail1">メールアドレスでログイン</a></p>
                    <p class="toSingup"><a href="<?php assign(Util::rewriteUrl('my','signup')) ?>">アカウントをお持ちでない方</a></p>
                    <?php write_html( $this->parseTemplate('Cooperation.php', array('brand' => $data['brand'], 'action' => '応募'))) ?>
                    <!-- /.joinCommSite --></div>
                <p class="campaignText">
                    【注意事項】<br>
                    ・他の方が既にギフトを受け取られている場合はギフトを受け取ることができません。<br>
                    ・ギフトを受け取るためには<?php assign($data['brand_name']) ?>への登録が必要です。<br>
                    ・ギフトに関する詳細はログイン後に遷移するページに記載をしています。ログインにご利用いただいたSNSやメールへの送付ではありません。</p>

                <p class="notAffiliated"><small>Not affiliated with Facebook, Inc.</small></p>
            <!-- /.campaign --></section>
        <?php else: ?>
            <?php if ($data['gift_myself']) :?>
                <section class="message_thanks">
                    <div class="messageInner">
                        <h1>Sorry...</h1>
                        <h2>このギフトは自分自身で受け取れません。</h2>
                        <!-- /.messageInner --></div>
                    <!-- /.message_thanks --></section>
            <?php elseif ($data['gift_used']) :?>
                <section class="message_thanks">
                    <div class="messageInner">
                        <h1>Sorry...</h1>
                        <h2>このギフトは既に別のユーザが取得済です。申し訳ございません。</h2>
                        <!-- /.messageInner --></div>
                    <!-- /.message_thanks --></section>
            <?php else :?>
                <section class="message">
                    <h1 class="messageHd1">ギフトが届いています</h1>
                    <?php if ($data['gift_message']->image_url) :?>
                        <p class="messageImg"><img src="<?php assign($data['gift_message']->image_url)?>" alt="dummy img"></p>
                    <?php endif ;?>
                    <?php if ($data['coupon_info']):?>
                        <?php $product_flg = false;?>
                        <?php if ($data['coupon_info']['has_official_page']):?>
                            <ul class="btnSet">
                                <li class="btn3"><a href="<?php assign($data['coupon_info']['code'])?>" class="large1">クーポンを受け取る</a></li>
                                <!-- /.btnSet --></ul>
                            <p class="messageCouponText">クーポン名：<?php assign($data['coupon_info']['name'])?><br>有効期限：<?php assign($data['coupon_info']['expire_date'])?></p>
                        <?php else: ?>
                            <div class="messageCoupon">
                                <p class="couponName"><?php assign($data['coupon_info']['name'])?></p>
                                <p class="couponNum"><strong><?php assign($data['coupon_info']['code'])?></strong></p>
                                <p class="couponLimit"><?php assign($data['coupon_info']['expire_date'])?></p>
                                <!-- /.messageCoupon --></div>
                        <?php endif; ?>
                        <section class="messageText"><?php write_html($data['coupon_info']['html_content'] ? : $this->toHalfContentDeeply($data['coupon_info']['description']));?></section>
                    <?php else:?>
                        <?php $product_flg = true;?>
                        <section class="messageText"><?php write_html($data['product_info']->product_html_content ? : $this->toHalfContentDeeply($data['product_info']->product_text));?></section>
                    <?php endif;?>
                    <!-- /.message --></section>
            <?php endif ;?>
        <?php endif; ?>
        <?php if ($product_flg):?>
        <section class="message jsMessageShippingAddress">
            <h1 class="messageHd1"> ギフトの配送先を入力してください。</h1>
            <p class="attention1" style="display: <?php assign($expire_flg ? 'block' : 'none')?>;">期限が過ぎているため、入力することはできません。</p>
            <form class="saveShippingAddressActionForm" action="<?php assign(Util::rewriteUrl('gift', "api_save_shipping_address_action.json")); ?>" method="POST" enctype="multipart/form-data" >
                <?php write_html($this->csrf_tag()); ?>
                <?php write_html($this->formHidden('gift_message_id', $data['gift_message']->id)); ?>
                <?php write_html($this->formHidden('param_hash', $data['gift_message']->param_hash)); ?>
                <ul class="commonTableList1">
                    <?php if($data['product_info']->postal_name_flg):?>
                        <li>
                            <p class="title1">
                                <span class="require1">氏名（かな）</span>
                                <!-- /.title1 --></p>
                            <p class="itemEdit">
                                <span class="editInput">
                                    <label class="editName"><span>姓</span>
                                        <?php write_html( $this->formText('lastName', $data['userShippingAddress']['last_name'] ? $data['userShippingAddress']['last_name'] : PHPParser::ACTION_FORM, array('class' => 'name', $disable=>$disable) ))?>
                                    </label>
                                    <label class="editName"><span>名</span>
                                        <?php write_html( $this->formText('firstName', $data['userShippingAddress']['first_name'] ? $data['userShippingAddress']['first_name'] : PHPParser::ACTION_FORM, array('class' => 'name', $disable=>$disable) ))?>
                                    </label>
                                </span>
                                <span id="error_shipping_address_name" class="iconError1" style="display:none"></span>
                                <span class="editInput">
                                    <label class="editName"><span>せい</span>
                                        <?php write_html( $this->formText('lastNameKana', $data['userShippingAddress']['last_name_kana'] ? $data['userShippingAddress']['last_name_kana'] : PHPParser::ACTION_FORM, array('class' => 'name', $disable=>$disable) ))?>
                                    </label>
                                    <label class="editName"><span>めい</span>
                                        <?php write_html( $this->formText('firstNameKana', $data['userShippingAddress']['first_name_kana'] ? $data['userShippingAddress']['first_name_kana'] : PHPParser::ACTION_FORM, array('class' => 'name', $disable=>$disable) ))?>
                                    </label>
                                <!-- /.editInput --></span>
                                <span id="error_shipping_address_nameKana" class="iconError1" style="display:none"></span>
                                <!-- /.itemEdit --></p>
                        </li>
                    <?php endif;?>
                    <?php if($data['product_info']->postal_address_flg):?>
                        <li>
                            <p class="title1">
                                <span class="require1">郵便番号</span>
                                <!-- /.title1 --></p>
                            <p class="itemEdit">
                                <span class="editInput">
                                    <?php write_html( $this->formText('zipCode1', ($data['userShippingAddress']['zip_code1'] ? $data['userShippingAddress']['zip_code1'] : PHPParser::ACTION_FORM), array('class' => 'inputNum', 'maxlength' => 3, $disable=>$disable) ));?>－<?php write_html( $this->formText('zipCode2', ($data['userShippingAddress']['zip_code2'] ? $data['userShippingAddress']['zip_code2'] : PHPParser::ACTION_FORM), array('class' => 'inputNum', 'maxlength' => 4, 'onKeyUp' => "AjaxZip3.zip2addr('zipCode1','zipCode2','prefId','address1','address2');", $disable=>$disable) ))?>
                                    <a href="javascript:;" onclick="AjaxZip3.zip2addr('zipCode1','zipCode2','prefId','address1','address2');">住所検索</a><span class="supplement1">※半角数字</span>
                                <!-- /.editInput --></span>
                                <span id="error_shipping_address_zipCode" class="iconError1" style="display:none"></span>
                                <!-- /.itemEdit --></p>
                        </li>
                        <li>
                            <p class="title1">
                                <span class="require1">都道府県</span>
                                <!-- /.title1 --></p>
                            <p class="itemEdit">
                                <span class="editInput">
                                    <?php write_html( $this->formSelect('prefId', $data['userShippingAddress']['pref_id'] ? $data['userShippingAddress']['pref_id'] : PHPParser::ACTION_FORM, array($disable=>$disable), $data['prefectures']))?>
                                    <!-- /.editInput --></span>
                                <span id="error_shipping_address_prefId" class="iconError1" style="display:none"></span>
                                <!-- /.itemEdit --></p>
                        </li>
                        <li>
                            <p class="title1">
                                <span class="require1">市区町村</span>
                                <!-- /.title1 --></p>
                            <p class="itemEdit">
                                <span class="editInput">
                                    <?php write_html( $this->formText('address1', $data['userShippingAddress']['address1'] ? $data['userShippingAddress']['address1'] : PHPParser::ACTION_FORM, array($disable=>$disable)))?>
                                    <!-- /.editInput --></span>
                                <span id="error_shipping_address_address1" class="iconError1" style="display:none"></span>
                                <!-- /.itemEdit --></p>
                        </li>
                        <li>
                            <p class="title1">
                                <span class="require1">番地</span>
                                <!-- /.title1 --></p>
                            <p class="itemEdit">
                                <span class="editInput">
                                    <?php write_html( $this->formText('address2', $data['userShippingAddress']['address2'] ? $data['userShippingAddress']['address2'] : PHPParser::ACTION_FORM, array($disable=>$disable)))?>
                                    <!-- /.editInput --></span>
                                <span id="error_shipping_address_address2" class="iconError1" style="display:none"></span>
                                <!-- /.itemEdit --></p>
                        </li>
                        <li>
                            <p class="title1">
                                建物
                                <!-- /.title1 --></p>
                            <p class="itemEdit">
                                <span class="editInput">
                                    <?php write_html( $this->formText('address3', $data['userShippingAddress']['address3'] ? $data['userShippingAddress']['address3'] : PHPParser::ACTION_FORM, array($disable=>$disable)))?>
                                    <!-- /.editInput --></span>
                                <span id="error_shipping_address_address3" class="iconError1" style="display:none"></span>
                                <!-- /.itemEdit --></p>
                        </li>
                    <?php endif;?>
                    <?php if($data['product_info']->postal_tel_flg):?>
                        <li>
                            <p class="title1">
                                <span class="require1">電話番号</span>
                                <!-- /.title1 --></p>
                            <p class="itemEdit">
                                <span class="editInput">
                                    <?php write_html( $this->formTel('telNo1', $data['userShippingAddress']['tel_no1'] ? $data['userShippingAddress']['tel_no1'] : PHPParser::ACTION_FORM, array('class' => 'inputNum', $disable=>$disable) ))?>－<?php write_html( $this->formTel('telNo2', $data['userShippingAddress']['tel_no2'] ? $data['userShippingAddress']['tel_no2'] : PHPParser::ACTION_FORM, array('class' => 'inputNum', $disable=>$disable) ))?>－<?php write_html( $this->formTel('telNo3', $data['userShippingAddress']['tel_no3'] ? $data['userShippingAddress']['tel_no3'] : PHPParser::ACTION_FORM, array('class' => 'inputNum',$disable=>$disable) ))?>
                                    <!-- /.editInput --></span>
                                <span class="supplement1">※半角数字</span>
                                <span id="error_shipping_address_telNo" class="iconError1" style="display:none"></span>
                                <!-- /.itemEdit --></p>
                        </li>
                    <?php endif;?>
                    <!-- /.commonTableList1 --></ul>
                <div class="messageFooter">
                    <ul class="btnSet">
                        <li class="btn3 jsSaveShippingAddress">
                            <?php if ($disable):?>
                                <span>送信完了</span>
                            <?php else:?>
                                <a href="javascript:void(0)" class="middle1" id="saveShippingAddressBtn">送信</a>
                            <?php endif;?>
                        </li>
                    </ul>
                </div>
            </form>
        </section>
        <?php endif;?>
        <section class="message_introduceCp">
            <h1 class="messageHd1">キャンペーンの紹介</h1>
            <div class="introduceInner">
                <?php if ($data['campaign']['finished']) :?>
                    <p class="attention1">このキャンペーンは終了しました</p>
                <?php endif ;?>
                <p><?php write_html($this->toHalfContentDeeply($data['campaign']['invite_description'])); ?></p>
                <div class="cpPrev">
                    <?php if ($data['campaign']['image_url']) :?>
                        <figure><img src="<?php assign($data['campaign']['image_url'])?>" alt=""></figure>
                    <?php endif;?>
                    <p>
                        <strong class="title"><?php assign($data['campaign']['title'])?></strong>
                        <small class="description"><?php write_html($data['campaign']['html_content'] ? strip_tags($data['campaign']['html_content']) : $this->toHalfContentDeeply($data['campaign']['text']));?></small>
                        <small class="account"><img src="<?php assign($data['brand']->getProfileImage());?>" width="10" height="10" alt="account name"><?php assign($data['brand']->name)?></small>
                    </p>
                    <!-- /.cpPrev --></div>
                <!-- /.introduceInner --></div>
            <ul class="btnSet">
                <?php if($data['campaign']['finished']) :?>
                    <li class="btn3"><span class="large2">キャンペーンを見る</span></li>
                <?php else :?>
                    <li class="btn3"><a href="<?php assign($data['campaign']['link'])?>" class="large2" target="_blank">キャンペーンを見る</a></li>
                <?php endif ;?>
            </ul>
            <!-- /.message_introduceCp --></section>

        <!-- /.messageWrap --></section>



    <p class="pageTop"><a href="#top"><span>ページTOPへ</span></a></p>
</article>
<?php if($product_flg):?>
    <script src="<?php assign(Util::getHttpProtocol()); ?>://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>
    <?php write_html($this->scriptTag('GiftSaveShippingAddressService'))?>
<?php endif;?>
<?php $data['pageStatus']['extend_tag'] = $data["action_info"]["cp"]["extend_tag"] ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $data['pageStatus'])); ?>