<?php write_html(aafwWidgets::getInstance()->loadWidget("BrandcoHeader")->render($data["pageStatus"])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget("BrandcoAccountHeader")->render($data["pageStatus"])) ?>

<article>
    <h1 class="hd1">SNS広告との連携データ管理</h1>
    <div class="customaudienceWrap">
        <form id="create_ads_audience" name="save_audience_form" action="<?php assign(Util::rewriteUrl( 'admin-fan', 'save_ads_audience'))?>" method="post">
            <?php write_html($this->formHidden('brand_user_relation_id' , $data['brand_user_relation_id'])); ?>
            <?php write_html($this->csrf_tag()); ?>
            <?php write_html($this->formHidden('save_type')) ?>
            <dl class="customaudienceDataSetting">
                <dt>広告アカウント</dt>
                <dd class="spreadWidth">
                    <ul class="addAcountList">
                        <?php foreach($data['ads_accounts'] as $account): ?>
                            <li><label>
                                    <input type="checkbox" name="ads_account_ids[]" value="<?php assign($account->id)?>">
                                    <span class="<?php assign(AdsAccount::$sns_icon_class_1[$account->social_app_id])?>"><?php assign($account->account_name)?></span>
                                </label>
                            </li>
                        <?php endforeach; ?>
                        <!-- /.addAcountList --></ul>
                    <span class="iconError1 jsAdsAccountError" style="display: none;"></span>
                    <p class="addAcount"><a href="#selectAdsSnSType" class="jsOpenModal">新規にアカウントを連携する</a><!-- /.addAcount --></p>
                </dd>
                <dt class="require1"><label>タイトル</label>
                    <span class="iconHelp">
                        <span class="text">ヘルプ</span>
                        <span class="textBalloon1"><span>タイトルを変更すると、<br>Facebookビジネスマネジャーの<br>オーディエンス名も更新されます</span><!-- /.textBalloon1 --></span>
                    <!-- /.iconHelp --></span>
                </dt>
                <dd>
                    <?php write_html($this->formText('audience_name', PHPParser::ACTION_FORM, array('class' => 'jsNameInput', 'placeholder' => 'カスタムオーディエンスのタイトル', 'maxlength' => 255))) ?>
                    <span class="iconError1 jsNameInputError" style="display: none;"></span>
                    <span class="jsCheckToggleWrap">
                        <span class="sub">
                                <?php write_html($this->formCheckBox('description_flg', array($this->getActionFormValue('description_flg')), array('class' => 'jsCheckToggle'), array(AdsAudience::DESCRIPTION_FLG_ON => 'メモ'))) ?>
                                <?php $attr_array = array('class' => 'jsCheckToggleTarget', 'maxlength' => '255');
                                if ($this->getActionFormValue('description_flg') == AdsAudience::DESCRIPTION_FLG_OFF) {
                                    $attr_array['style'] = 'display:none';
                                } ?>
                                <?php write_html($this->formText('audience_description', PHPParser::ACTION_FORM, $attr_array)); ?>
                                <span class="iconError1 jsDescriptionInputError" style="display: none;"></span>
                        </span>
                    </span>
                </dd>
                <dt>送信対象</dt>
                <dd class="spreadWidth">
                    <p class="segmentTotal"><strong><span class="jsTargetCount">---</span><span>名</span></strong></p>
                    <ul class="segmentList jsSegmentProvisionList">
                        <?php write_html($this->parseTemplate("ads/DefaultProvisionContainer.php", array(
                            'search_history' => null,
                            'brand_id' => $data['brand_id'],
                        ))); ?>
                    </ul>
                </dd>
            <!-- /.customaudienceDataSetting --></dl>
            </form>
            <ul class="btnSet">
                <li class="btn1"><a href="javascript:void(0)" class="large1 jsCreateDraftAudience">下書き保存</a></li>
                <li class="btn3"><a href="javascript:void(0)" class="large1 jsCreateAudienceAndSendTarget">データの送信</a></li>
            </ul>
        <section class="backPage">
            <p><a href="<?php write_html(Util::rewriteUrl('admin-fan', 'ads_list')) ?>" class="iconPrev1">広告一覧</a></p>
            <!-- /.backPage --></section>
        <!-- /.customaudienceWrap --></div>
</article>

<div class="modal1 jsModal" id="segmentProvisionConditionSelector">
    <!-- /.modal1 --></div>

<?php write_html($this->parseTemplate('ads/AddAdsAccountModal.php', array('callback_url' => Util::rewriteUrl('admin-fan','create_ads_audience')))) ?>

<?php write_html($this->parseTemplate('segment/JsEmbedList.php')) ?>

<?php $script = array('admin-fan/AdsUpdateTargetService','admin-fan/AdsSearchTargetService') ?>
<?php $param = array_merge($data['pageStatus'], array('script' => $script)) ?>
<?php write_html($this->parseTemplate("BrandcoFooter.php", $param)); ?>