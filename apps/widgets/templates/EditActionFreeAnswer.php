<?php if($data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
<?php else: ?>
    <?php $disable = ($data['action']->status == CpAction::STATUS_FIX)?'disabled':'' ?>
<?php endif; ?>
    <section class="moduleEdit1">
    <?php write_html($this->parseTemplate('CpActionModuleTitle.php', array('disable'=>$disable))); ?>
    <?php write_html($this->parseTemplate('CpActionModuleImage.php', array('disable'=>$disable))); ?>
    <?php write_html($this->parseTemplate('CpActionModuleText.php', array('disable'=>$disable, 'cp_action'=>$data['action']))); ?>
        <section class="moduleCont1">
            <h1 class="editBtn1 jsModuleContTile">ボタン文言設定</h1>
            <div class="moduleSettingWrap jsModuleContTarget">
                <ul class="moduleSetting">
                    <li class="btn3Edit"><span>
                    <?php write_html( $this->formText( 'button_label', PHPParser::ACTION_FORM, array('maxlength'=>'80', 'id'=>'btn_text',$disable=>$disable))); ?>
                            <?php if ( $this->ActionError && !$this->ActionError->isValid('button_label')): ?>
                                <p class="attention1"><?php assign ( $this->ActionError->getMessage('button_label') )?></p>
                            <?php endif; ?>
                    </span></li>
                    <!-- /.moduleSetting --></ul>
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
                    <dl class="module">
                        <dt>
                            <p class="messageImg"><img src="" width="600" height="300" id="imagePreview"></p>
                            <section class="messageText""><span class="require1" id="textPreview"></span></section>
                        </dt>
                        <dd>
                            <textarea placeholder="自由記述"></textarea>
                        </dd>
                    </dl>

                    <div class="messageFooter">
                        <ul class="btnSet">
                            <li class="btn3"><a href="javascript:void(0)" class="large1" id="btnPreview">参加する</a></li>
                        </ul>
                    </div>

                </section>
            </section>
        </div>
    </section>
