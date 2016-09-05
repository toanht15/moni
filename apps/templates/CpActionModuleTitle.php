<section class="moduleCont1">
    <h1 class="editTitle1 jsModuleContTile">タイトル</h1>
    <div class="moduleSettingWrap jsModuleContTarget">
        <?php if ( $this->ActionError && !$this->ActionError->isValid('title')): ?>
            <p class="iconError1"><?php assign ( $this->ActionError->getMessage('title') )?></p>
        <?php endif; ?>
        <p>
            <?php write_html( $this->formText( 'title', PHPParser::ACTION_FORM, array('id' => 'text_title', 'maxlength'=>'50', $data['disable']=>$data['disable']))); ?>
            <br><small class='textLimit' id='limitTitle'></small>
        </p>
        <!-- /.moduleSettingWrap --></div>
    <!-- /.moduleCont1 --></section>