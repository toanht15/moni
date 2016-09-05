<!-- 設問追加時は2つ作成しておく -->
    <div name="addChoiceDiv" style="display:none">
        <li data-choice_id=<?php assign($data['question']->id.'_'.$data['choice']->id) ?> name="moduleEnqueteChoice">
            <label>
                <?php if ($data['question']->type_id != QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE): ?>
                    <span class="num"></span><?php endif; ?><?php write_html( $this->formText(
                    'choice_id_'.$data['question']->id.'_'.$data['choice']->id,
                    $this->POST ? $this->POST['choice'] : PHPParser::ACTION_FORM,
                    array('maxlength'=>'512', 'placeholder'=> '選択肢を入力してください')
                )); ?>
                <a href="javascript:void(0)" class="iconBtnDelete" data-delete_type="Choice" onclick="return false";>削除する</a>
            </label>
            <?php if($data['question']->type_id == QuestionTypeService::CHOICE_IMAGE_ANSWER_TYPE): ?>
                <input type="file" name="choice_image_file_<?php assign($data['question']->id.'_'.$data['choice']->id) ?>">
                <?php write_html($this->formHidden('choice_image_url_'.$data['question']->id.'_'.$data['choice']->id, ''))?>
            <?php endif; ?>
        </li>
    </div>