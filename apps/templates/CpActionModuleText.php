<?php $service_factory = new aafwServiceFactory();
/** @var QuestionNgWordService $questionNgWordService */
$questionNgWordService = $service_factory->create('QuestionNgWordService');
?>
<section class="moduleCont1">
    <h1 class="editText1 jsModuleContTile">本文<small class="textLimit">（最大<?php assign(CpValidator::MAX_TEXT_LENGTH)?>文字）</small></h1>
    <?php if($data['is_show_send_text_mail_button']): ?>
        <?php write_html($this->parseTemplate('CpActionModuleSendTextMailButton.php', array('disable' => $data['disable']))); ?>
    <?php endif; ?>
    <div class="moduleSettingWrap jsModuleContTarget">
        <?php if($data['cp_action']->type == CpAction::TYPE_FREE_ANSWER): ?>
            <?php if($this->ActionError && !$this->ActionError->isValid('text')): ?>
                <?php if($this->ActionError->getError('text') == 'NG_QUESTION') {
                        $ngWord = $questionNgWordService->getNgWordInQuestion($this->getActionFormValue('ng_question'),$data['brand_id']);
                    }
                ?>
                <p class="iconError1"><?php assign(str_replace(array('<%ng_word>'), array($ngWord), $this->ActionError->getMessage('text')))?></p>
            <?php endif; ?>
            <?php write_html($this->formTextArea('text',($this->getActionFormValue('ng_question')) ? $this->getActionFormValue('ng_question') : PHPParser::ACTION_FORM, array('maxlength'=>CpValidator::MAX_TEXT_LENGTH, 'cols'=>25, 'rows'=>10,'id'=>'text_area',$data['disable']=>$data['disable']))); ?>
        <?php else: ?>
            <?php if($this->ActionError && !$this->ActionError->isValid('text')): ?>
                <p class="iconError1"><?php assign($this->ActionError->getMessage('text'))?></p>
            <?php endif; ?>
            <?php write_html($this->formTextArea('text',PHPParser::ACTION_FORM, array('maxlength'=>CpValidator::MAX_TEXT_LENGTH, 'cols'=>25, 'rows'=>10,'id'=>'text_area',$data['disable']=>$data['disable']))); ?>
        <?php endif; ?>
        <p>
            <!-- Campaign Status 1: STATUS_FIX, 2: DEFAULT -->
            <a href="javascript:void(0);"
               data-link="<?php assign(Util::rewriteUrl('admin-blog', 'file_list', array(), array('f_id' => BrandUploadFile::POPUP_FROM_TEXT_MODULE, 'stt' => ($data['disable'] ? 1 : 2)))) ?>"
               class="openNewWindow1 jsFileUploaderPopup">ファイル管理から本文に画像URL挿入</a>
            <br>
            <a href="javascript:;"
               class="openNewWindow1"
               id="markdown_rule_popup"
               data-link="<?php assign(Util::rewriteUrl('admin-cp', 'markdown_rule')); ?>" >
            文字や画像の装飾について</a>
        </p>
        <?php if($data['cp_action']->type == CpAction::TYPE_FREE_ANSWER): ?>
            <p class="supplement1">メールアドレスなどの個人情報を取得することはできません。</p>
        <?php endif; ?>
    <!-- /.moduleSettingWrap --></div>
<!-- /.moduleCont1 --></section>