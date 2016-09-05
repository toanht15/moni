<?php
    $questionnaire_errors = $data['action_data']['cp_questionnaire_errors'];
    $action_form = $data['action_data']['ActionForm'];
    if($questionnaire_errors && $questionnaire_errors->getError('question_id_'.$data['question']->id)) {
        $has_error = true;
    }
    if($questionnaire_errors && $questionnaire_errors->getError('is_fan_list_page')) {
        $requirement_flg = $action_form['requirement_'.$data['question']->id];
        $question = $action_form['question_id_'.$data['question']->id];
    } elseif($data['question']->id < 0) {
        // 新規作成時のデフォルト値
        $requirement_flg = CpQuestionnaireService::QUESTION_REQUIRED;
    } else {
        $cp_questionnaire_service = new CpQuestionnaireService();
        $requirement_flg = $cp_questionnaire_service->getRelationByQuestionnaireActionIdAndQuestionId($data['action_data']['cp_questionnaire_action_id'], $data['question']->id)->requirement_flg;
        $question = $data['question']->question;
    }
    if($questionnaire_errors && $questionnaire_errors->getError('question_id_'.$data['question']->id) == 'NG_QUESTION') {
        $service_factory = new aafwServiceFactory();
        /** @var QuestionNgWordService $questionNgWordService */
        $questionNgWordService = $service_factory->create('QuestionNgWordService');
        $ngWord = $questionNgWordService->getNgWordInQuestion($data['question']->question,$data['brand_id']);
    }
?>
<?php $disable = ($data['action_data']['action_status'] == CpAction::STATUS_FIX) ? 'disabled' : '' ?>
    <li class="moduleEnqueteDetail1" data-question_id=<?php assign($data['question']->id)?> data-type=<?php assign($data['question']->id . '_' . $data['question']->type_id) ?>
        style="display:<?php assign($data['action_data']['add_question'] ? 'none' : 'list-item') ?>">
        <p class="title jsModuleContTitle <?php assign($has_error ? 'jsHasError' : 'close')?>"><small class="type">自由回答</small>
            <?php if($has_error): ?>
                <span class="iconError1"><?php assign(str_replace(array('<%ng_word>'), array($ngWord),$questionnaire_errors->getMessage('question_id_'.$data['question']->id)))?></span>
            <?php endif; ?>
            <?php write_html($this->formHidden('type_'.$data['question']->id, QuestionTypeService::FREE_ANSWER_TYPE)) ?>
            <label>
                <span class="num">Q<?php assign($data['action_data']['question_number']) ?>.</span><?php write_html($this->formText(
                    'question_id_'.$data['question']->id,
                    $question !== '' ? $question : PHPParser::ACTION_FORM,
                    array('maxlength'=>'1024', 'placeholder'=> '設問文を入力してください', $disable=>$disable)
                )); ?>
            </label>
        </p>
        <div class="detail jsModuleContTarget">
            <p class="supplement1">※メールアドレスなどの個人情報を取得することはできません。</p>
            <dl class="moduleEnqueteSetting">
                <dt>回答必須</dt>
                    <dd><a href="javascript:void(0)" data-switch_question_id="requirement_<?php assign($data['question']->id) ?>" data-disabled="<?php assign($disable) ?>"
                         class="switch <?php assign($requirement_flg == CpQuestionnaireService::QUESTION_NOT_REQUIRED ? "off" : "on") ?>" onclick="return false";>
                        <?php write_html($this->formHidden('requirement_'.$data['question']->id, $requirement_flg)) ?>
                    <span class="switchInner"><span class="selectON">ON</span><span class="selectOFF">OFF</span></span></a></dd>
            <!-- /.enqueteSetting --></dl>
            <p class="moduleItemDelete"><a href="javascript:void(0)" class="linkDelete" data-delete_type="Question" onclick="return false";>設問を削除する</a></p>
        <!-- /.jsModuleContTarget --></div>
    <!-- /.enqueteDetail1--></li>
