<?php if($data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
<?php else: ?>
    <?php $disable = ($data['action']->status == CpAction::STATUS_FIX)?'disabled':''; ?>
<?php endif; ?>
<section class="moduleEdit1">
    <section class="moduleCont1">
        <h1 class="editTitle1 jsModuleContTile">タイトル</h1>
        <div class="moduleSettingWrap jsModuleContTarget">
            <?php if ( $this->ActionError && !$this->ActionError->isValid('title')): ?>
                <p class="iconError1"><?php assign ( $this->ActionError->getMessage('title') )?></p>
            <?php endif; ?>
            <p>
                <?php write_html( $this->formText( 'title', PHPParser::ACTION_FORM, array('id' => 'text_entry_title', 'maxlength'=>'80', 'disabled'=>$disable))); ?>
                <br><small class='textLimit' id='limitEntryTitle'></small>
            </p>
            <!-- /.moduleSettingWrap --></div>
        <!-- /.moduleCont1 --></section>
    <?php write_html($this->parseTemplate('CpActionModuleImage.php', array('disable'=>$disable))); ?>
    <?php write_html($this->parseTemplate('CpActionModuleText.php', array('disable'=>$disable))); ?>
    <?php write_html($this->parseTemplate('CpActionModuleButton.php', array('disable'=>$disable))); ?>
    <?php write_html($this->parseTemplate('CpActionModuleProfileQuestionnaire.php', $data)) ?>
    <!-- /.moduleEdit1 --></section>

<section class="modulePreview1">
    <header class="modulePreviewHeader">
        <p>スマートフォン<a href="#" class="toggle_switch left jsModulePreviewSwitch">toggle_switch</a>PC</p>
        <!-- /.modulePreviewHeader --></header>

    <div class="displaySP jsModulePreviewArea">
        <section class="messageWrap">
            <section class="message">
                <p class="messageImg"><img src="" width="600" height="300" id="imagePreview"></p>
                <section class="messageText" id="textPreview"></section>
                <div class="messageFooter">
                    <ul class="btnSet">
                        <li class="btn3"><a href="javascript:void(0)" class="large1" id="btnPreview">参加する</a></li>
                    </ul>
                </div>
            </section>
            <?php write_html($this->parseTemplate('CpActionModuleProfileQuestionnairePreview.php', $data)) ?>
        </section>
    </div>
    <!-- /.modulePreview --></section>