<?php $question_errors = $data['action_data']['cp_questionnaire_errors']?>
<?php $disable = ($data['action_data']['action_status'] == CpAction::STATUS_FIX) ? 'disabled' : '' ?>
    <li class="moduleEnqueteDetail1" data-question_id=<?php assign($data['question']->id) ?> data-type=<?php assign($data['question']->id . '_' . $data['question']->type_id) ?> style="display:<?php assign($data['action_data']['add_question'] ? 'none' : 'list-item') ?>">
        <p class="title jsModuleContTitle <?php assign($data['has_error'] ? 'jsHasError' : 'close')?>">
            <small class="type">
                <?php switch ($data['question']->type_id) {
                    case QuestionTypeService::CHOICE_ANSWER_TYPE:
                        $text = '選択式（テキスト）';
                        $type = QuestionTypeService::CHOICE_ANSWER_TYPE;
                        break;
                    case QuestionTypeService::CHOICE_IMAGE_ANSWER_TYPE :
                        $text = '選択式（画像）';
                        $type = QuestionTypeService::CHOICE_IMAGE_ANSWER_TYPE;
                        break;
                    case QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE:
                        $text = '選択式（プルダウン）';
                        $type = QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE;
                        break;
                    default :
                        $text = '選択式（テキスト）';
                        $type = QuestionTypeService::CHOICE_ANSWER_TYPE;
                        break;
                } ?>
                <?php assign($text)?>
            </small>
            <?php if($data['errors']['question_id_'.$data['question']->id]): ?>
                <span class="iconError1"><?php assign($question_errors->getMessage('question_id_'.$data['question']->id))?></span>
            <?php endif; ?>
            <?php write_html($this->formHidden('type_'.$data['question']->id, $type)); ?>
            <label>
                <span class="num">Q<?php assign($data['action_data']['question_number']) ?>.</span><?php write_html($this->formText(
                    'question_id_'.$data['question']->id,
                    $data['question']->question !== '' ? $data['question']->question : PHPParser::ACTION_FORM,
                    array('maxlength'=>'1024', 'placeholder'=> '設問文を入力してください', $disable=>$disable)
                )); ?>
            </label>
        </p>
        <div class="detail jsModuleContTarget">
            <ul class="moduleEnqueteSelect">
                <?php if($data['action_data']['add_question'] || ($type == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE && !$data['choices'])): ?>
                    <?php if ($type == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE): ?>

                        <?php if($data['errors']['pulldown_choice_'.$data['question']->id]): ?>
                            <span class="iconError1"><?php assign($question_errors->getMessage('pulldown_choice_'.$data['question']->id))?></span>
                        <?php endif; ?>

                        <?php write_html($this->formTextarea('pulldown_choice_'.$data['question']->id, $data['action_data']['ActionForm']['pulldown_choice_'.$data['question']->id])) ?>

                        <small>※選択肢を1行に1つずつ入力ください<br>※1つの選択肢は12文字までです</small>
                    <?php else : ?>
                        <!-- 設問追加時は2つ作成しておく -->
                        <?php for($i=1; $i<=2; $i++): ?>
                            <li data-choice_id=<?php assign($data['question']->id.'_a'.$i) ?> name="moduleEnqueteChoice">
                                <label>
                                    <span class="num">A<?php assign($i) ?>.</span><?php write_html($this->formText(
                                        'choice_id_'.$data['question']->id.'_a'.$i,
                                        $this->POST ? $this->POST['choice'] : PHPParser::ACTION_FORM,
                                        array('maxlength'=>'512', 'placeholder'=> '選択肢を入力してください', $disable=>$disable)
                                    )); ?>
                                    <a href="javascript:void(0)" class="iconBtnDelete" data-delete_type="Choice" onclick="return false";>削除する</a>
                                </label>
                                <?php if($data['question']->type_id == QuestionTypeService::CHOICE_IMAGE_ANSWER_TYPE): ?>
                                    <input type="file" name="choice_image_file_<?php assign($data['question']->id.'_a'.$i) ?>">
                                    <?php write_html($this->formHidden('choice_image_url_'.$data['question']->id.'_a'.$i, ''))?>
                                <?php endif; ?>
                            </li>
                        <?php endfor;?>
                    <?php endif; ?>
                <?php else:?>
                    <?php foreach ($data['choices'] as $choice): ?>
                        <?php if($choice->other_choice_flg != CpQuestionnaireService::USE_OTHER_CHOICE): ?>
                            <?php if ($data['errors']['choice_id_'.$data['question']->id.'_'.$choice->id]): ?>
                                <li data-choice_id=<?php assign($data['question']->id.'_'.$choice->id) ?> name="moduleEnqueteChoice"><span class="iconError1" data-choice_id=<?php assign($data['question']->id.'_'.$choice->id) ?>><?php assign($question_errors->getMessage('choice_id_'.$data['question']->id.'_'.$choice->id))?></span>
                            <?php elseif($data['errors']['choice_image_file_'.$data['question']->id.'_'.$choice->id]): ?>
                                <li data-choice_id=<?php assign($data['question']->id.'_'.$choice->id) ?> name="moduleEnqueteChoice"><span class="iconError1" data-choice_id=<?php assign($data['question']->id.'_'.$choice->id) ?>><?php assign($question_errors->getMessage('choice_image_file_'.$data['question']->id.'_'.$choice->id))?></span>
                            <?php else: ?>
                                <li data-choice_id=<?php assign($data['question']->id.'_'.$choice->id) ?> name="moduleEnqueteChoice">
                            <?php endif; ?>
                                <label>
                                    <?php if ($data['question']->type_id != QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE): ?>
                                        <span class="num">A<?php assign($choice->choice_num) ?>.</span><?php endif; ?><?php $choice_label = $data['action_data']['ActionForm']['choice_id_'.$data['question']->id.'_'.$choice->id] ?><?php write_html( $this->formText(
                                        'choice_id_'.$data['question']->id.'_'.$choice->id,
                                        (!is_numeric($choice_label) && $choice_label != '') ? $choice_label : $choice->choice,
                                        array('maxlength'=>'512', 'placeholder'=> '選択肢を入力してください', $disable=>$disable)
                                    )); ?>
                                   <a href="javascript:void(0)" class="iconBtnDelete" data-delete_type="Choice" onclick="return false";>削除する</a>
                                </label>
                                <?php if($data['question']->type_id == QuestionTypeService::CHOICE_IMAGE_ANSWER_TYPE): ?>
                                    <input type="file" name="choice_image_file_<?php assign($data['question']->id.'_'.$choice->id) ?>" <?php assign($disable) ?>>
                                    <?php write_html($this->formHidden('choice_image_url_'.$data['question']->id.'_'.$choice->id, $choice->image_url))?>
                                <?php endif; ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach;?>
                <?php endif;?>

                <?php if (!($type == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE && !$data['choices'])): ?>
                    <li><a href="javascript:void(0)" class="linkAdd" data-question_id="<?php assign($data['question']->id) ?>" data-add_choice_url="<?php assign(Util::rewriteUrl('admin-cp', 'api_add_choice.json')); ?>" data-question_type="<?php assign($data['question']->type_id) ?>" onclick="return false";>選択肢を追加する</a></li>
                <?php endif ?>
            <!-- /.enqueteText --></ul>

            <dl class="moduleEnqueteSetting">
                <dt>回答必須</dt>
                <dd><a href="javascript:void(0)" data-switch_question_id="requirement_<?php assign($data['question']->id) ?>" data-disabled="<?php assign($disable) ?>"
                   class="switch <?php assign($data['relation']->requirement_flg == CpQuestionnaireService::QUESTION_NOT_REQUIRED ? "off" : "on") ?>" onclick="return false";>
                    <?php write_html($this->formHidden('requirement_'.$data['question']->id, $data['relation']->requirement_flg)) ?>
                    <span class="switchInner"><span class="selectON">ON</span><span class="selectOFF">OFF</span></span></a></dd>

                <?php if ($type != QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE): ?>
                    <dt>複数回答</dt>
                    <dd><a href="javascript:void(0)" data-switch_question_id="multianswer_<?php assign($data['question']->id) ?>" data-disabled="<?php assign($disable) ?>"
                        class="switch <?php assign($data['requirement']->multi_answer_flg == CpQuestionnaireService::SINGLE_ANSWER ? "off" : "on") ?>" onclick="return false";>
                        <?php write_html($this->formHidden('multi_answer_'.$data['question']->id, $data['requirement']->multi_answer_flg)) ?>
                    <span class="switchInner"><span class="selectON">ON</span><span class="selectOFF">OFF</span></span></a></dd>
                <?php endif; ?>

                <dt>選択肢をランダムに表示する</dt>
                <dd><a href="javascript:void(0)" data-switch_question_id="random_<?php assign($data['question']->id) ?>" data-disabled="<?php assign($disable) ?>"
                     class="switch <?php assign($data['requirement']->random_order_flg == CpQuestionnaireService::NOT_RANDOM_ORDER ? "off" : "on") ?>" onclick="return false";>
                    <?php write_html($this->formHidden('random_order_'.$data['question']->id, $data['requirement']->random_order_flg)) ?>
                <span class="switchInner"><span class="selectON">ON</span><span class="selectOFF">OFF</span></span></a></dd>

                <?php if($data['question']->type_id == QuestionTypeService::CHOICE_ANSWER_TYPE): ?>
                    <dt>選択肢に自由回答を追加する</dt>
                    <dd><a href="javascript:void(0)" data-switch_question_id="otherchoice_<?php assign($data['question']->id) ?>" data-disabled="<?php assign($disable) ?>"
                         class="switch <?php assign($data['requirement']->use_other_choice_flg == CpQuestionnaireService::NOT_USE_OTHER_CHOICE ? "off" : "on") ?>" onclick="return false";>
                        <?php write_html($this->formHidden('use_other_choice_'.$data['question']->id, $data['requirement']->use_other_choice_flg)) ?>
                    <span class="switchInner"><span class="selectON">ON</span><span class="selectOFF">OFF</span></span></a></dd>
                <?php endif; ?>

            <!-- /.enqueteSetting --></dl>
            <p class="moduleItemDelete"><a href="javascript:void(0)" class="linkDelete" data-delete_type="Question" onclick="return false";>設問を削除する</a></p>
        <!-- /.jsModuleContTarget --></div>
    <!-- /.enqueteDetail1--></li>
