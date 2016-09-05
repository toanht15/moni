<section class="moduleCont1">
    <h1 class="editText1 jsModuleContTile">プレースホルダー<small class="textLimit">（最大<?php assign(CpValidator::MAX_TWITTER_SHARE_TEXT)?>文字）</small></h1>
    <div class="moduleSettingWrap jsModuleContTarget">
        <?php if ( $this->ActionError && !$this->ActionError->isValid('placeholder')): ?>
            <p class="iconError1"><?php assign ( $this->ActionError->getMessage('placeholder') )?></p>
        <?php endif; ?>
        <p>
            <?php write_html( $this->formTextArea( 'placeholder', PHPParser::ACTION_FORM, array('maxlength'=>CpValidator::MAX_TWITTER_SHARE_TEXT, 'cols'=>25, 'rows'=>10, 'id'=>'share_text_area','class'=>'jsTweetText', $data['disable']=>$data['disable']))); ?>
        </p>
        <!-- /.moduleSettingWrap --></div>
    <!-- /.moduleCont1 --></section>