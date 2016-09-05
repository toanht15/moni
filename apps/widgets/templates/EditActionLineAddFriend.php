<?php if($data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
<?php else: ?>
    <?php $disable = ($data['action']->status == CpAction::STATUS_FIX) ? 'disabled' : '' ?>
<?php endif; ?>

<section class="moduleEdit1">
    <section class="moduleCont1" id="line">
        <h1 class="editLineFollow1 jsModuleContTile">LINE 友だち追加設定</h1>
        <div class="moduleSettingWrap jsModuleContTarget">
            <dl class="moduleSettingList">
                <dt class="moduleSettingTitle jsModuleContTile">アカウント設定</dt>
                <dd class="moduleSettingDetail jsModuleContTarget">
                    <ul class="moduleLineAccount">
                        <li>
                            <?php if ( $this->ActionError && !$this->ActionError->isValid('line_account_name')): ?>
                                <p class="iconError1"><?php assign ( $this->ActionError->getMessage('line_account_name') )?></p>
                            <?php endif; ?>
                            アカウント名<span><?php write_html( $this->formText('line_account_name', PHPParser::ACTION_FORM, array('id' => 'line_account_name', 'maxlength'=>'255', $disable => $disable))); ?></span></li>
                        <li>
                            <?php if ( $this->ActionError && !$this->ActionError->isValid('line_account_id')): ?>
                                <p class="iconError1"><?php assign ( $this->ActionError->getMessage('line_account_id') )?></p>
                            <?php endif; ?>
                            アカウントID名<span>@<?php write_html( $this->formText('line_account_id', PHPParser::ACTION_FORM, array('id' => 'line_account_id', 'maxlength'=>'50', $disable => $disable))); ?></span></li>
                    </ul>
                    <!-- /.moduleSettingDetail --></dd>
                <dt class="moduleSettingTitle jsModuleContTile">コメント</dt>
                <dd class="moduleSettingDetail jsModuleContTarget">
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('comment')): ?>
                        <p class="iconError1"><?php assign ( $this->ActionError->getMessage('comment') )?></p>
                    <?php endif; ?>
                    <?php write_html( $this->formTextArea('comment', PHPParser::ACTION_FORM, array('maxlength'=>CpValidator::SHORT_TEXT_LENGTH,
                        'cols'=>30, 'rows'=>10,'class' => 'line', $disable => $disable))); ?>
                    <!-- /.moduleSettingDetail --></dd>
                <!-- /.moduleSettingList --></dl>
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
        <section class="message_engagement">
            <h1 class="messageHd1">「<span id="lineAccountName"><?php assign($this->ActionForm['line_account_name'] ? : 'アカウント名'); ?></span>」のLINEアカウントを友だちに追加しよう！</h1>
            <div class="engagementLineInner">
               <p class="engagementLn">
                    <span class="lineBtn"><a href="http://line.me/ti/p/%40yfq0144w" target="_blank"><img height="36" border="0" alt="友だち追加" src="http://biz.line.naver.jp/line_business/img/btn/addfriends_ja.png"></a></span>
                    <span class="descriptionText" id="lineAddFriendActionComment"><?php assign($this->ActionForm['comment']); ?></span>
               </p>
                <!-- /.engagementInner --></div>
            <div class="messageFooter">
                <p class="skip"><a href="javascript:void(0)"><small>友だちを追加せず次へ</small></a></p>
                <!-- /.messageFooter --></div>
            <!-- /.message_engagement --></section>
    </div>
<!-- /.modulePreview --></section>