<?php $disable = ($data['action_status'] == CpAction::STATUS_FIX) ? 'disabled' : '' ?>
    <?php if($data['cp_questionnaire_errors']): ?>
        <?php if($data['cp_questionnaire_errors']->getError('questionnaire')): ?>
            <p class="attention1"><?php assign ($data['cp_questionnaire_errors']->getMessage('questionnaire'))?></p>
        <?php else: ?>
            <?php foreach($data['cp_questionnaire_errors']->getErrors() as $key=>$error): ?>
                <?php if(preg_match('/^choice_id_|^question_id_|^choice_image_file_/',$key)): ?>
                    <p class="iconError1">アンケートの設定が完了していません</p>
                    <?php break; ?>
                <?php endif; ?>
            <?php endforeach;?>
        <?php endif; ?>
    <?php endif; ?>

    <?php $data['add_question'] = false;?>
    <?php $parser = new PHPParser(); ?>
    <?php $data['question_number'] = 1; ?>
    <?php foreach($data['question_list'] as $question): ?>
        <?php write_html($parser->parseTemplate(
            'CpQuestionnaireQuestion.php',
            array(
                'question' => $question,
                'action_data' => $data,
            )
        )); ?>
        <?php $data['question_number'] += 1; ?>
    <?php endforeach;?>
    <li class="addQuestion jsFirstType"><a href="javascript:void(0)" class="linkAdd" data-type=<?php assign(QuestionTypeService::FREE_ANSWER_TYPE) ?> data-add_question_url=<?php assign(Util::rewriteUrl('admin-cp', 'api_add_question.json')); ?> onclick="return false";>自由回答設問を追加</a></li>
    <li class="addQuestion"><a href="javascript:void(0)" class="linkAdd" data-type=<?php assign(QuestionTypeService::CHOICE_ANSWER_TYPE) ?> data-add_question_url=<?php assign(Util::rewriteUrl('admin-cp', 'api_add_question.json')); ?> onclick="return false";>選択式設問（テキスト）を追加</a></li>
    <li class="addQuestion"><a href="javascript:void(0)" class="linkAdd" data-type=<?php assign(QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE) ?> data-add_question_url=<?php assign(Util::rewriteUrl('admin-cp', 'api_add_question.json')); ?> onclick="return false";>選択式設問（プルダウン）を追加</a></li>
    <li class="addQuestion"><a href="javascript:void(0)" class="linkAdd" data-type=<?php assign(QuestionTypeService::CHOICE_IMAGE_ANSWER_TYPE) ?> data-add_question_url=<?php assign(Util::rewriteUrl('admin-cp', 'api_add_question.json')); ?> onclick="return false";>選択式設問（画像）を追加</a></li>

    <?php write_html($this->formHidden('static_url', config('Static.Url'))) ?>
    <div class="modal2 jsModal" id="modal3">
        <section class="modalCont-small jsModalCont">
            <h1>確認</h1>
            <p><span class="attention1">この設問を削除しますか？</span></p>
            <p class="btnSet"><span class="btn2"><a href="javascript:void(0)" data-close_modal_type="3" class="middle1" onclick="return false";>キャンセル</a></span><span class="btn4"><a id="deleteQuestionPush" href="javascript:void(0)" class="middle1" onclick="return false";>削除する</a></span></p>
        </section>
    </div>
    <div class="modal2 jsModal" id="modal4">
        <section class="modalCont-small jsModalCont">
            <h1>確認</h1>
            <p><span class="attention1">この選択肢を削除しますか？</span></p>
            <p class="btnSet"><span class="btn2"><a href="javascript:void(0)" data-close_modal_type="4" class="middle1"  onclick="return false";>キャンセル</a></span><span class="btn4"><a id="deleteChoicePush" href="javascript:void(0)" class="middle1" onclick="return false";>削除する</a></span></p>
        </section>
    </div>

<?php write_html($this->scriptTag('CreateQuestionnaireService')) ?>
