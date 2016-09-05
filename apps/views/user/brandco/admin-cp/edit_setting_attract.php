<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<?php $isDisableClientWhenFixAction = $data['status'] == Cp::SETTING_FIX && !(edit_setting_attract::canEditCp($data['CpStatus']) && $data['isManager']) ? 'disabled':'';  ?>
<?php $isDisableWhenFixAction = ($data['status'] == Cp::SETTING_FIX && !edit_setting_attract::canEditCp($data['CpStatus'])) ? 'disabled':'';  ?>
<?php $disable = $data['status'] == Cp::SETTING_FIX ? 'disabled':'';?>

<?php write_html($this->parseTemplate('CpPublicConditions.php', array('cp_id' => $data['cp_id']))) ?>

<article>
    <?php if ($data['cp']->status != Cp::STATUS_DRAFT): ?>
        <?php write_html($this->parseTemplate('ActionHeader.php',array(
            'cp_id' => $data['cp']->id,
            'action_id' => null,
            'user_list_page' => true,
            'pageStatus' => $data['pageStatus'],
            'enable_archive' => false,
            'isHideDemoFunction' => false,
        ))); ?>
    <?php else: ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('CreateCpActionHeader')->render(array('cp_id' => $data['cp_id'], 'setting_id'=>Cp::CP_SETTING_ATTRACT, 'mid'=>$this->params['mid']))) ?>
    <?php endif ?>
    <h1 class="hd1"><img src="<?php assign($this->setVersion('/img/module/attract1.png')) ?>" width="25" height="25" alt="集客設定" class="moduleIcon">集客設定</h1>

    <?php if ( $this->ActionError && !$this->ActionError->isValid('auth')): ?>
        <p class="attention1"><?php assign ( $this->ActionError->getMessage('auth') )?></p>
    <?php endif; ?>

    <form id="attractForm" name="attractForm" action="<?php assign(Util::rewriteUrl( 'admin-cp', 'save_setting_attract' )); ?>" method="POST" >
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('cp_id', $data['cp_id'])) ?>
        <?php if(!$data['pageStatus']['brand']->hasOption(BrandOptions::OPTION_TOP)): ?>
            <?php write_html( $this->formHidden('show_top_page_flg', Cp::FLAG_HIDE_VALUE) ); ?>
        <?php endif; ?>
        <section class="moduleEditWrap">
        <section class="attractSetting1">
            <ul>
                <li class="url"><label class="labelTitle">公開URL
                    </label><input type="text" value="<?php assign($data['cp_link']) ?>" readonly>
                    <p class="supplement1">キャンペーンを設計しなおす場合には同一のURLを使用することができません</p>
                </li>
                <?php if ($data['cp']->selection_method != CpCreator::ANNOUNCE_NON_INCENTIVE): ?>
                    <li>
                        <?php if (!$isDisableClientWhenFixAction && $data['status'] == Cp::SETTING_FIX): ?>
                            <span class="labelModeAllied"><?php write_html( $this->formCheckBox( 'show_monipla_com_flg', array($this->getActionFormValue('show_monipla_com_flg')), array($isDisableClientWhenFixAction=>$isDisableClientWhenFixAction), array('1' => '「キャンペーンメディア モニプラ」に掲載する'))); ?></span><a class="openNewWindow1" href = "https://cp.monipla.com/" target="_blank">掲載サイトを見る</a>
                        <?php else: ?>
                            <?php write_html( $this->formCheckBox( 'show_monipla_com_flg', array($this->getActionFormValue('show_monipla_com_flg')), array($isDisableClientWhenFixAction=>$isDisableClientWhenFixAction), array('1' => '「キャンペーンメディア モニプラ」に掲載する'))); ?><a class="openNewWindow1" href = "https://cp.monipla.com/" target="_blank">掲載サイトを見る</a>
                        <?php endif; ?>

                        <?php if($isDisableClientWhenFixAction)://disableの時は既存値をそのまま送る?>
                        <?php write_html( $this->formHidden( 'show_monipla_com_flg', $this->getActionFormValue('show_monipla_com_flg'))); ?>
                        <?php endif;?>

                    </li>
                <?php endif ?>
                <?php if($data['pageStatus']['brand']->hasOption(BrandOptions::OPTION_TOP)):?>
                <li>
                    <?php write_html( $this->formCheckBox( 'back_monipla_flg', array($this->getActionFormValue('back_monipla_flg')), array($isDisableWhenFixAction=>$isDisableWhenFixAction), array('1' => '参加完了時にブランドページトップへの導線を追加する'))); ?>
                </li>
                <?php endif; ?>
                <li>
                    <label>
                        <?php write_html( $this->formCheckBox( 'share_flg', array($this->getActionFormValue('share_flg')),  array($isDisableWhenFixAction=>$isDisableWhenFixAction),array(Cp::FLAG_SHOW_VALUE => 'キャンペーンを拡散するソーシャルボタンを表示する。'))); ?>
                        <span class="iconHelp">
                        <span class="text">ヘルプ</span>
                          <span class="textBalloon1">
                          <span>
                          キャンペーンページTOPに拡散用の<br>Facebook、Twitter、Google+のボタンが表示されます
                          </span>
                        <!-- /.textBalloon1 --></span>
                        <!-- /.iconHelp --></span>
                    </label>
                </li>
                <?php if($data['pageStatus']['brand']->hasOption(BrandOptions::OPTION_CRM)): ?>
                <li>
                    <label>
                        <?php write_html( $this->formCheckBox( 'send_mail_flg', array($this->getActionFormValue('send_mail_flg')), array($disable=>$disable), array('1' => '全てのファンにメールで通知する(キャンペーンの応募開始日時に送信されます) '))); ?>
                        <span class="iconHelp">
                        <span class="text">ヘルプ</span>
                        <span class="textBalloon1">
                          <span>
                            メール通知を拒否しているユーザーにはメールは届きません。<br>また、対象数により送信に時間を要する場合があります。
                          </span>
                        <!-- /.textBalloon1 --></span>
                        <!-- /.iconHelp --></span>
                    </label>
                </li>
                <?php endif; ?>
                <?php if($data['pageStatus']['brand']->hasOption(BrandOptions::OPTION_TOP)): ?>
                <li>
                    <label>
                        <?php write_html( $this->formCheckBox( 'show_top_page_flg', array($this->getActionFormValue('show_top_page_flg')),  array($isDisableWhenFixAction=>$isDisableWhenFixAction),array('1' => 'ブランドページトップに表示する'))); ?>
                    </label><a href="<?php assign(Util::getBaseUrl());?>" target="_blank" class="openNewWindow1">表示ページを見る</a>
                </li>
                <?php endif; ?>
            </ul>
            <!-- /.attractSetting1 --></section>
            <?php if ($data['CpStatus'] == Cp::CAMPAIGN_STATUS_DRAFT) : ?>
                <div class="moduleCheck">
                    <ul>
                        <?php if($data['status'] == Cp::SETTING_FIX): ?>
                            <li class="btn1"><a href="javascript:void(0)" id="editButton" data-action="cp_id=<?php assign($data['cp_id'])?>&setting=2"
                                                data-url= "<?php assign(Util::rewriteUrl('admin-cp','api_change_setting_status.json')) ?>">確定解除</a></li>
                        <?php else: ?>
                            <li class="btn2"><a href="javascript:void(0)" class="small1" id="submitDraft" data-action="<?php assign(Util::rewriteUrl('admin-cp','save_setting_attract', array(Cp::SETTING_DRAFT))) ?>">下書き保存</a></li>
                            <li class="btn3"><a href="javascript:void(0)" id="submit" data-action="<?php assign(Util::rewriteUrl('admin-cp', 'save_setting_attract', array(Cp::SETTING_FIX))); ?>">内容確定</a></li>
                        <?php endif; ?>
                    </ul>
                <!-- /.moduleCheck --></div>
            <?php elseif (edit_setting_attract::canEditCp($data['CpStatus'])): ?>
                <?php if(!$data['pageStatus']['isAgent']): ?>
                    <div class="moduleCheck">
                        <ul>
                            <li class="btn3"><a href="javascript:void(0)" id="submit" data-action="<?php assign(Util::rewriteUrl('admin-cp', 'save_setting_attract', array(Cp::SETTING_FIX))); ?>">更新</a></li>
                        </ul>
                    <!-- /.moduleCheck --></div>
                <?php endif; ?>
            <?php endif ?>
        <!-- /.moduleEditWrap --></section>
     </form>
<!-- /.wrap --></article>
<?php write_html($this->parseTemplate('MessageDeliveryConfirmBox.php', array(
    'reservation' => null,
    'cp_id' => $data['cp_id'],
    'pageStatus' => $data['pageStatus'],
))) ?>

<?php $script = array('admin-cp/EditSettingAttractService','admin-cp/CpMenuService'); ?>

<?php $param = array_merge($data['pageStatus'], array('script' => $script)) ?>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $param)); ?>
