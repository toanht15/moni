<?php if($data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
<?php else: ?>
    <?php $disable = ($data['action']->status == CpAction::STATUS_FIX)? 'disabled' : '' ?>
<?php endif; ?>

    <section class="moduleEdit1">
        <?php write_html($this->parseTemplate('CpActionModuleTitle.php', array('disable'=>$disable))); ?>
        <?php write_html($this->parseTemplate('CpActionModuleImage.php', array('disable'=>$disable))); ?>
        <?php write_html($this->parseTemplate('CpActionModuleText.php', array(
            'disable'=>$disable,
            'is_show_send_text_mail_button' => $data['is_show_send_text_mail_button']
        ))); ?>
        <?php write_html($this->parseTemplate('CpActionModuleManualStepButton.php', array('disable' => $disable, 'is_last' => $data['is_last_action_in_group']))); ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('CpActionModuleDeadLine')->render([
            'ActionForm'       => $data['ActionForm'],
            'ActionError'      => $data['ActionError'],
            'cp_action'        => $data['action'],
            'is_login_manager' => $data['pageStatus']['isLoginManager'],
            'disable'          => $disable,
        ])); ?>
        <!-- /.moduleEdit --></section>

    <section class="modulePreview1">
        <header class="modulePreviewHeader">
            <p>スマートフォン<a href="#" class="toggle_switch left jsModulePreviewSwitch">toggle_switch</a>PC</p>
            <!-- /.modulePreviewHeader --></header>

        <?php if ($data['is_entry_message_action'] && !$data['is_last_action_in_group']) : ?>
            <ul class="tablink1">
                <li class="current jsTab" data-login="1"><span>ログイン時</span></li>
                <li class="jsTab" data-login="0"><span>非ログイン時</span></li>
                <!-- /.tablink1 --></ul>
        <?php endif; ?>

        <div class="displaySP jsModulePreviewArea">
            <section class="messageWrap">
                <section class="message">
                    <p class="messageImg"><img src="" width="600" height="300" id="imagePreview"></p>
                    <section class="messageText" id="textPreview"></section>
                    <div class="messageFooter">
                        <?php // ログイン時 ?>
                        <ul class="btnSet jsNextBtn" style="<?php assign(($this->ActionForm['manual_step_flg']) ? "display: block;" : "display: none;") ?>">
                            <li class="btn3"><a href="javascript:void(0)" class="large1" id="btnPreview">次へ</a></li>
                        </ul>

                        <?php // 非ログイン時 ?>
                        <ul class="btnSet jsLoginBtn" style="display: none;">
                            <li class="btn3"><a href="javascript:void(0)" class="large3"><small>ログインして</small>続きをみる</a></li>
                        </ul>
                    </div>
                </section>
            </section>
        </div>

        <!-- /.modulePreview --></section>
