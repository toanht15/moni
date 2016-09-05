<dt class="moduleSettingTitle jsModuleContTile">ギフトの渡し方</dt>
<dd class="moduleSettingDetail jsModuleContTarget jsIncentiveSetting">
    <ul class="moduleGiftType">
    <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_coupon_id')): ?>
        <li class="iconError1 giftTypeLabel"><?php assign ($this->ActionError->getMessage('gift_coupon_id'))?></li>
    <?php endif; ?>
        <li class="jsCouponSetting">
            <label class="giftTypeLabel"><?php write_html( $this->formRadio( 'incentive_type', PHPParser::ACTION_FORM, array($data['disable'] => $data['disable']), array('1' => 'クーポン'), array(), " ")); ?></label>
            <?php if (!$this->coupons): ?>
                <br>クーポンを<a href="<?php assign(Util::rewriteUrl('admin-coupon', 'create_coupon')) ?>">こちら</a>から作成して下さい。
            <?php else : ?>
                <?php $select_value  = array(''=>'未設定');
                    foreach ($this->coupons as $coupon) {
                        $select_value[$coupon->id] = $coupon->name;
                    }
                    if ($this->current_coupon) {
                        $select_value[$this->current_coupon->id] = $this->current_coupon->name;
                    }
                ?>
                <?php write_html($this->formSelect('gift_coupon_id', $this->current_coupon ? $this->current_coupon->id : PHPParser::ACTION_FORM, array($data['disable']=>$data['disable'], "class"=>"jsGiftCouponSetting") ,$select_value)); ?>
                <small>※プレビュー中のコードと期限は仮です。</small>
            <?php endif;?>
        </li>
        <li class="jsProductSetting">
            <p><label class="giftTypeLabel"><?php write_html( $this->formRadio( 'incentive_type', PHPParser::ACTION_FORM, array($data['disable'] => $data['disable']), array('2' => '商品'), array(), " ")); ?></label></p>
            <p>取得する配送先情報</p>
            <ul>
                <li><label><?php write_html( $this->formCheckBox( 'gift_product_postal_name_flg', array($this->postal_name_flg_default ? :$this->getActionFormValue('gift_product_postal_name_flg')), array($data['disable'] => $data['disable']), array('1' => 'お名前'))); ?></label>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_product_postal_name_flg')): ?>
                        <p class="attention1"><?php assign ( $this->ActionError->getMessage('gift_product_postal_name_flg') )?></p>
                    <?php endif; ?></li>
                <li><label><?php write_html( $this->formCheckBox( 'gift_product_postal_address_flg', array($this->postal_address_flg_default ? :$this->getActionFormValue('gift_product_postal_address_flg')), array($data['disable'] => $data['disable']), array('1' => '住所'))); ?></label>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_product_postal_address_flg')): ?>
                        <p class="attention1"><?php assign ( $this->ActionError->getMessage('gift_product_postal_address_flg') )?></p>
                    <?php endif; ?></li>
                <li><label><?php write_html( $this->formCheckBox( 'gift_product_postal_tel_flg', array($this->postal_tel_flg_default ? :$this->getActionFormValue('gift_product_postal_tel_flg')), array($data['disable'] => $data['disable']), array('1' => '電話番号'))); ?></label>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_product_postal_tel_flg')): ?>
                        <p class="attention1"><?php assign ( $this->ActionError->getMessage('gift_product_postal_tel_flg') )?></p>
                    <?php endif; ?></li>
            </ul>
            <p>
                配送先の入力期限<br>
                <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_product_expire_date')): ?>
                    <p class="iconError1"><?php assign ( $this->ActionError->getMessage('gift_product_expire_date') )?></p>
                <?php endif; ?>
                <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_product_expire_time_hh')): ?>
                    <p class="iconError1"><?php assign ( $this->ActionError->getMessage('gift_product_expire_time_hh') )?></p>
                <?php endif; ?>
                <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_product_expire_time_mm')): ?>
                    <p class="iconError1"><?php assign ( $this->ActionError->getMessage('gift_product_expire_time_mm') )?></p>
                <?php endif; ?>
                <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_product_expire_datetime')): ?>
                    <p class="iconError1"><?php assign ( str_replace('<%time>', '期限日時', $this->ActionError->getMessage('gift_product_expire_datetime')) )?></p>
                <?php endif; ?>
                <?php write_html($this->formText( 'gift_product_expire_date', $this->currentDate ? : PHPParser::ACTION_FORM, array($data['disable']=>$data['disable'], 'class' => 'jsDate inputDate', 'placeholder'=>'年/月/日'))); ?>
                <?php write_html($this->formSelect('gift_product_expire_time_hh', $this->currentTimeHH ? : PHPParser::ACTION_FORM, array($data['disable']=>$data['disable'], "class"=>"inputTime") ,$this->expireTimeHH)); ?>
                <?php write_html($this->formSelect('gift_product_expire_time_mm', $this->currentTimeMM ? : PHPParser::ACTION_FORM, array($data['disable']=>$data['disable'], "class"=>"inputTime") ,$this->expireTimeMM)); ?>
            </p>
        </li>
    <!-- /.moduleGiftType --></ul>
        <dl>
            <dt>クーポンの説明/配送商品について</dt>
            <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_description')): ?>
                <dd class="iconError1"><?php assign ($this->ActionError->getMessage('gift_description'))?></dd>
            <?php endif; ?>
            <?php $gift_description = ($this->getActionFormValue('incentive_type') == CpGiftAction::INCENTIVE_TYPE_PRODUCT ? $this->gift_product_config->product_text : $this->gift_coupon_config->message);?>
            <dd>
                <?php write_html( $this->formTextArea( 'gift_description', $gift_description != '' ? $gift_description : PHPParser::ACTION_FORM, array('cols'=>30, 'rows'=>10, 'maxlength'=>2000, 'class'=>'jsGiftIncentiveDescription', 'id'=>'gift_incentive_description', $data['disable']=>$data['disable']))); ?>
                <a href="javascript:void(0);" data-link="<?php assign(Util::rewriteUrl('admin-blog', 'file_list', array(), array('f_id' => BrandUploadFile::POPUP_FROM_GIFT_INCENTIVE_SETTING, 'stt' => ($data['disable'] ? 1 : 2)))) ?>" class="openNewWindow1 jsFileUploaderPopup">ファイル管理から本文に画像URL挿入</a>
                <br><a href="javascript:void(0);" class="openNewWindow1" id="markdown_rule_popup" data-link="<?php assign(Util::rewriteUrl('admin-cp', 'markdown_rule')); ?>">文字や画像の装飾について</a>
            </dd>
        </dl>
    <!-- /.moduleSettingDetail --></dd>
<dt class="moduleSettingTitle jsModuleContTile">受け取る側へのキャンペーンの説明</dt>
<dd class="moduleSettingDetail jsModuleContTarget jsCampaignDescriptionSetting">
    <?php if ( $this->ActionError && !$this->ActionError->isValid('receiver_text')): ?>
        <p class="iconError1"><?php assign ( $this->ActionError->getMessage('receiver_text') )?></p>
    <?php endif; ?>
    <?php write_html( $this->formTextArea( 'receiver_text', $this->getActionFormValue('receiver_text'), array('cols'=>30, 'rows'=>10, 'maxlength'=>2000, 'class' => 'jsReceiverCampaignDetail', $data['disable']=>$data['disable']))); ?>
    <!-- /.moduleSettingDetail --></dd>