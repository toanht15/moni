<?php
    $gcc = $this->gift_card_config;
?>
<link rel="stylesheet" href="<?php assign($this->setVersion('/css/farbtastic.css'))?>">
<script src="<?php assign($this->setVersion('/js/min/farbtastic-all.min.js'))?>"></script>
    <dt class="moduleSettingTitle jsModuleContTile <?php assign_js($this->getActionFormValue('card_required') ? '' : 'close')?>"><label><?php write_html( $this->formCheckBox( 'card_required', array($this->getActionFormValue('card_required')), array($data['disable']=>$data['disable'], 'class' => 'jsGiftCardConfigRequired'), array('1' => 'グリーティングカードをつける'))); ?></label></dt>
    <dd class="moduleSettingDetail jsModuleContTarget jsGiftCardSetting" style="pointer-events: <?php assign_js($this->getActionFormValue('card_required') ? 'auto' : 'none')?>">
        <dl>
            <dt>画像</dt>
            <dd>
                <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_card_upload')): ?>
                    <p class="iconError1"><?php assign ( $this->ActionError->getMessage('gift_card_upload') )?></p>
                <?php endif; ?>
                <ul class="moduleGiftCard" id="uploadPreview">
                    <?php foreach ($this->gift_card_uploads as $key=>$value): ?>
                        <li id="<?php assign_js($key)?>"><?php write_html($this->formHidden('gift_card_uploaded[]', $value->id)) ?><img src="<?php assign_js($value->image_url);?>" alt="image title"><a href="javascript:void(0)" class="iconBtnDelete jsIconBtnDelete">削除する</a></li>
                    <?php endforeach;?>
                    <!-- /.moduleGiftCard --></ul>
                <ul class="moduleGiftCard" id="uploadInput">
                    <li class="upload"><a href="javascript:void(0)" class="linkAdd jsUploadPhotoLink" >新規追加 / アップ済ファイルから選択</a></li>
                    <!-- /.moduleGiftCard --></ul>
            </dd>
            <dt>メッセージの設定</dt>
            <dd>
                <ul class="moduleGiftScale">
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_text_color')): ?>
                        <p class="iconError1">文字色：<?php assign ( $this->ActionError->getMessage('gift_text_color') )?></p>
                    <?php endif; ?>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_to_x')): ?>
                        <p class="iconError1">宛先・横位置：<?php assign ( $this->ActionError->getMessage('gift_to_x') )?></p>
                    <?php endif; ?>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_to_y')): ?>
                        <p class="iconError1">宛先・縦位置：<?php assign ( $this->ActionError->getMessage('gift_to_y') )?></p>
                    <?php endif; ?>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_to_text_size')): ?>
                        <p class="iconError1">宛先・文字サイズ：<?php assign ( $this->ActionError->getMessage('gift_to_text_size') )?></p>
                    <?php endif; ?>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_to_size')): ?>
                        <p class="iconError1">宛先・サイズ：<?php assign ( $this->ActionError->getMessage('gift_to_size') )?></p>
                    <?php endif; ?>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_content_x')): ?>
                        <p class="iconError1">本文・横位置：<?php assign ( $this->ActionError->getMessage('gift_content_x') )?></p>
                    <?php endif; ?>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_content_y')): ?>
                        <p class="iconError1">本文・縦位置：<?php assign ( $this->ActionError->getMessage('gift_content_y') )?></p>
                    <?php endif; ?>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_content_width')): ?>
                        <p class="iconError1">本文・横サイズ：<?php assign ( $this->ActionError->getMessage('gift_content_width') )?></p>
                    <?php endif; ?>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_content_height')): ?>
                        <p class="iconError1">本文・縦サイズ：<?php assign ( $this->ActionError->getMessage('gift_content_height') )?></p>
                    <?php endif; ?>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_content_text_size')): ?>
                        <p class="iconError1">本文・文字サイズ：<?php assign ( $this->ActionError->getMessage('gift_content_text_size') )?></p>
                    <?php endif; ?>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_from_x')): ?>
                        <p class="iconError1">送り主・横位置：<?php assign ( $this->ActionError->getMessage('gift_from_x') )?></p>
                    <?php endif; ?>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_from_y')): ?>
                        <p class="iconError1">送り主・縦位置：<?php assign ( $this->ActionError->getMessage('gift_from_y') )?></p>
                    <?php endif; ?>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_from_text_size')): ?>
                        <p class="iconError1">送り主・文字サイズ：<?php assign ( $this->ActionError->getMessage('gift_from_text_size') )?></p>
                    <?php endif; ?>
                    <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_from_size')): ?>
                        <p class="iconError1">送り主・サイズ：<?php assign ( $this->ActionError->getMessage('gift_from_size') )?></p>
                    <?php endif; ?>
                    <li class="setColor">
                        <strong>文字色</strong><?php write_html( $this->formText( 'gift_text_color', $gcc->text_color != '' ? $gcc->text_color : PHPParser::ACTION_FORM, array($data['disable']=>$data['disable'], 'class' => 'text_color colorPicker jsColorInput'))); ?><div id="pickerColorMain" class="jsFarbtastic" style="top: inherit; left: 200px;"></div>
                    </li>
                    <li>
                        <strong>宛先</strong>
                        <label>横位置<?php write_html( $this->formText( 'gift_to_x', $gcc->to_x != '' ? $gcc->to_x : PHPParser::ACTION_FORM, array($data['disable']=>$data['disable'], 'class' => 'to_x'))); ?>px</label><label>縦位置<?php write_html( $this->formText( 'gift_to_y', $gcc->to_y != '' ? $gcc->to_y : PHPParser::ACTION_FORM, array($data['disable']=>$data['disable'], 'class' => 'to_y'))); ?>px</label><label>文字サイズ<?php write_html( $this->formText( 'gift_to_text_size', $gcc->to_text_size != '' ? $gcc->to_text_size : PHPParser::ACTION_FORM, array($data['disable']=>$data['disable'], 'class' => 'to_text_size'))); ?>px</label><label>サイズ<?php write_html( $this->formText( 'gift_to_size', $gcc->to_size != '' ? $gcc->to_size : PHPParser::ACTION_FORM, array($data['disable']=>$data['disable'], 'class' => 'to_size'))); ?>px</label>
                    </li>
                    <li>
                        <strong>本文</strong>
                        <label>横位置<?php write_html( $this->formText( 'gift_content_x', $gcc->content_x != '' ? $gcc->content_x : PHPParser::ACTION_FORM, array($data['disable']=>$data['disable'], 'class' => 'content_x'))); ?>px</label><label>縦位置<?php write_html( $this->formText( 'gift_content_y', $gcc->content_y != '' ? $gcc->content_y : PHPParser::ACTION_FORM, array($data['disable']=>$data['disable'], 'class' => 'content_y'))); ?>px</label><label>横サイズ<?php write_html( $this->formText( 'gift_content_width', $gcc->content_width != '' ? $gcc->content_width : PHPParser::ACTION_FORM, array($data['disable']=>$data['disable'], 'class' => 'content_width'))); ?>px</label><label>縦サイズ<?php write_html( $this->formText( 'gift_content_height', $gcc->content_height != '' ? $gcc->content_height : PHPParser::ACTION_FORM, array($data['disable']=>$data['disable'], 'class' => 'content_height'))); ?>px</label><label>文字サイズ<?php write_html( $this->formText( 'gift_content_text_size', $gcc->content_text_size != '' ? $gcc->content_text_size : PHPParser::ACTION_FORM, array($data['disable']=>$data['disable'], 'class' => 'content_text_size'))); ?>px</label><small class="supplement1">※最大:300文字/6行</small>
                    </li>
                    <li>
                        <strong>送り主</strong>
                        <label>横位置<?php write_html( $this->formText( 'gift_from_x', $gcc->from_x != '' ? $gcc->from_x : PHPParser::ACTION_FORM, array($data['disable']=>$data['disable'], 'class' => 'from_x'))); ?>px</label><label>縦位置<?php write_html( $this->formText( 'gift_from_y', $gcc->from_y != '' ? $gcc->from_y : PHPParser::ACTION_FORM, array($data['disable']=>$data['disable'], 'class' => 'from_y'))); ?>px</label><label>文字サイズ<?php write_html( $this->formText( 'gift_from_text_size', $gcc->from_text_size != '' ? $gcc->from_text_size : PHPParser::ACTION_FORM, array($data['disable']=>$data['disable'], 'class' => 'from_text_size'))); ?>px</label><label>サイズ<?php write_html( $this->formText( 'gift_from_size', $gcc->from_size != '' ? $gcc->from_size : PHPParser::ACTION_FORM, array($data['disable']=>$data['disable'], 'class' => 'from_size'))); ?>px</label>
                    </li>
                    <li><small class="supplement1">※テキストボックスの位置は左上が基準（0/0）です</small></li>
                    <!-- /.moduleGiftScale --></ul>
            </dd>
            <dt>プリセットテキスト</dt>
            <?php if ( $this->ActionError && !$this->ActionError->isValid('gift_content_default_text')): ?>
                <p class="iconError1"><?php assign ( $this->ActionError->getMessage('gift_content_default_text') )?></p>
            <?php endif; ?>
            <dd><?php write_html( $this->formTextArea( 'gift_content_default_text', $gcc->content_default_text != '' ? $gcc->content_default_text : PHPParser::ACTION_FORM, array('cols'=>30, 'rows'=>10, 'maxlength'=>2000, 'class'=>'content_default_text',$data['disable']=>$data['disable']))); ?></dd>
        </dl>
<!-- /.moduleSettingDetail --></dd>

