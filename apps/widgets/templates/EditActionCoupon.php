<?php if($data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
<?php else: ?>
    <?php $disable = ($data['action']->status == CpAction::STATUS_FIX) ? 'disabled' :'' ?>
<?php endif; ?>
    <section class="moduleEdit1">

        <?php write_html($this->parseTemplate('CpActionModuleTitle.php', array('disable'=>$disable))); ?>
        <?php write_html($this->parseTemplate('CpActionModuleImage.php', array('disable'=>$disable))); ?>
        <?php write_html($this->parseTemplate('CpActionModuleText.php', array('disable'=>$disable))); ?>

        <section class="moduleCont1">
            <h1 class="editCoupon1 jsModuleContTile">クーポン選択</h1>
            <div class="moduleSettingWrap jsModuleContTarget">
                <?php if (!$data['coupons']): ?>
                    クーポンを<a href="<?php assign(Util::rewriteUrl('admin-coupon', 'create_coupon')) ?>">こちら</a>から作成して下さい。
                <?php endif; ?>

                <?php if ( $this->ActionError && !$this->ActionError->isValid('coupon_id')): ?>
                    <p class="iconError1"><?php assign ( $this->ActionError->getMessage('coupon_id') )?></p>
                <?php endif; ?>
                <p class="moduleSetting">
                    <?php $select_value  = array('0' => '未設定');
                        foreach ($data['coupons'] as $coupon) {
                            $select_value[$coupon->id] = $coupon->name . '(' . $coupon->description . ')';
                        }
                        if ($data['current_coupon']) {
                            $select_value[$data['current_coupon']->id] = $data['current_coupon']->name . '(' . $data['current_coupon']->description . ')';
                        }
                    ?>
                    <?php $coupon_disable = ($data['action']->status == CpAction::STATUS_FIX) ? 'disabled' : '' ?>

                    <?php if ($coupon_disable == 'disabled'): ?>
                        <?php write_html($this->formHidden('coupon_id', PHPParser::ACTION_FORM)) ?>
                    <?php endif; ?>

                    <?php write_html($this->formSelect('coupon_id', PHPParser::ACTION_FORM, array('disabled' => $coupon_disable, 'id' => 'couponSelection'), $select_value)); ?>
                <!-- /.moduleSetting --></p>
                <p><small>※プレビュー中のコードと期限は仮です。</small></p>
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
            <p>スマートフォン<a href="#" class="toggle_switch left jsModulePreviewSwitch">toggle_switch</a>PC</p>
            <!-- /.modulePreviewHeader --></header>

        <div class="displaySP jsModulePreviewArea">
            <section class="messageWrap">

                <section class="message">
                    <p class="messageImg"><img src="" width="600" height="300" id="imagePreview"></p>
                    <div class="messageCoupon">
                        <p class="couponName" id="couponName">クーポン名</p>
                        <p class="couponNum"><strong>123456789-123456789</strong></p>
                        <p class="couponLimit">0000年00月00日 23:59まで</p>
                    </div>
                    <section class="messageText" id="textPreview"></section>
                    
                    <!-- /.message --></section>
            </section>
        </div>

        <!-- /.modulePreview --></section>
