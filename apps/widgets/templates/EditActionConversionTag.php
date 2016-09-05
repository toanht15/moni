<?php if($data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
<?php else: ?>
    <?php $disable = ($data['action']->status == CpAction::STATUS_FIX) ? 'disabled' : '' ?>
<?php endif; ?>

<section class="moduleEdit1">
    <?php write_html($this->parseTemplate('CpActionModuleTitle.php', array('disable' => $disable))); ?>
    <section class="moduleCont1">
        <h1 class="editCvtag1 jsModuleContTile">タグ設置スペース</h1>
        <div class="moduleSettingWrap jsModuleContTarget">
            <?php if ($data['cp']->type == Cp::TYPE_CAMPAIGN): ?>
                <p class="supplement1" style="margin-bottom:10px;">※デモ公開を利用し、タグが正常に稼動しているかの確認を必ず行ってください。</p>
            <?php else : ?>
                <p class="supplement1" style="margin-bottom:10px;">※自身のアカウントへテスト送信をし、タグが正常に稼動しているかの確認を必ず行ってください。</p>
            <?php endif; ?>
            <?php if ( $this->ActionError && !$this->ActionError->isValid('script_code')): ?>
                <p class="iconError1"><?php assign ( $this->ActionError->getMessage('script_code') )?></p>
            <?php endif; ?>
            <p>
                <?php write_html( $this->formTextArea( 'script_code', $this->getActionFormValue('script_code'), array('maxlength'=>CpValidator::CV_TAG_MAX_LENGTH, 'cols'=>25, 'rows'=>400, 'class'=>'jsScriptCode',$disable=>$disable))); ?>
            </p>
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
                <p class="messageImg"><img src="<?php assign($this->setVersion('/img/message/bgConversion1.png'));?>"></p>
            </section>
        </section>
    </div>
    <!-- /.modulePreview --></section>
