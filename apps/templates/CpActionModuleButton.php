<section class="moduleCont1">
    <h1 class="editBtn1 jsModuleContTile">ボタン文言設定</h1>
    <div class="moduleSettingWrap jsModuleContTarget">
        <?php if ( $this->ActionError && !$this->ActionError->isValid('button_label_text')): ?>
            <p class="iconError1"><?php assign ( $this->ActionError->getMessage('button_label_text') )?></p>
        <?php endif; ?>
        <ul class="moduleSetting">
            <li class="btn3Edit">
                <span><?php write_html( $this->formText( 'button_label_text', PHPParser::ACTION_FORM, array('maxlength'=>'80', 'id'=>'btn_text',$data['disable']=>$data['disable']))); ?></span>
            </li>
            <!-- /.moduleSetting --></ul>
        <!-- /.moduleSettingWrap --></div>
    <!-- /.moduleCont1 --></section>
