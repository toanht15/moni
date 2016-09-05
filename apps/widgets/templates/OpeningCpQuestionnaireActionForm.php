<form class="openingCpActionForm" action="<?php assign(Util::rewriteUrl('messages', "api_pre_execute_questionnaire_action.json")); ?>" method="POST">
    <?php write_html($this->csrf_tag()); ?>
    <?php write_html($this->formHidden('cp_action_id', $data["cp_action"]->id)); ?>
    <dl class="module">

        <?php write_html($this->formHidden('cp_questionnaire_action_id', $data['questionnaire_action']->id)); ?>
        <?php foreach($data['questionnaire_question_relations'] as $relation): ?>
            <?php $question = $data['cp_questionnaire_service']->getQuestionById($relation->question_id)?>
            <dt data-questionId=<?php assign('question/' . $question->id); ?>>
                <span class="num">Q<?php assign($relation->number); ?></span>
                <span class="<?php assign($relation->requirement_flg == CpQuestionnaireService::QUESTION_REQUIRED ? 'require1' : ''); ?>"><?php write_html($this->toHalfContentDeeply($question->question, false)); ?></span>
            </dt>
            <?php if($question->type_id == QuestionTypeService::CHOICE_ANSWER_TYPE): ?>
                <dd>
                    <ul class="moduleItemList">
                        <?php
                        $requirement = $data['cp_questionnaire_service']->getRequirementByQuestionId($question->id);
                        $choices = $data['cp_questionnaire_service']->getChoicesExceptOtherChoiceByQuestionId($question->id)->toArray();
                        ?>
                        <?php if($requirement->random_order_flg == CpQuestionnaireService::RANDOM_ORDER) shuffle($choices); //設問のランダム表示 ?>
                        <?php if($requirement->multi_answer_flg != CpQuestionnaireService::MULTI_ANSWER): ?>
                            <?php foreach($choices as $choice): ?>
                                <li>
                                    <?php write_html($this->formRadio(
                                        'single_answer/' . $question->id,
                                        $data['join_status'] ? : PHPParser::ACTION_FORM,
                                        array('class'=>'customRadio', 'disabled' => $data['join_status'] ? 'disabled' : ''),
                                        array($choice->id => $choice->choice)
                                    ))?>
                                </li>
                            <?php endforeach;?>
                            <?php if($requirement->use_other_choice_flg == CpQuestionnaireService::USE_OTHER_CHOICE): ?>
                                <?php $other_choice = $data['cp_questionnaire_service']->getOtherChoice($question->id); ?>
                                <li>
                                    <?php write_html($this->formRadio(
                                        'single_answer/' . $question->id,
                                        $data['join_status'] ? : PHPParser::ACTION_FORM,
                                        array('class'=>'customRadio', 'disabled' => $data['join_status'] ? 'disabled' : ''),
                                        array($other_choice->id => $other_choice->choice)
                                    ))?>
                                    <?php write_html($this->formTextArea(
                                        'single_answer_othertext/' . $question->id,
                                        $data['join_status'] ? : PHPParser::ACTION_FORM,
                                        array('cols' => '30', 'rows' => '10', 'maxlength'=>'255', 'disabled' => $data['join_status'] ? 'disabled' : '')
                                    ))?>
                                </li>
                            <?php endif;?>
                        <?php else: ?>
                            <?php foreach($choices as $choice): ?>
                                <li>
                                    <?php write_html($this->formCheckbox(
                                        'multi_answer/' . $question->id . '/' . $choice->id,
                                        PHPParser::ACTION_FORM,
                                        array('class'=>'customCheck',
                                            'disabled' => $data['join_status'] ? 'disabled' : ''),
                                        array($choice->id => $choice->choice)
                                    ))?>
                                </li>
                            <?php endforeach;?>
                            <?php if($requirement->use_other_choice_flg == CpQuestionnaireService::USE_OTHER_CHOICE): ?>
                                <?php $other_choice = $data['cp_questionnaire_service']->getOtherChoice($question->id); ?>
                                <li>
                                    <?php write_html($this->formCheckbox(
                                        'multi_answer/' . $question->id . '/' . $other_choice->id,
                                        PHPParser::ACTION_FORM,
                                        array('class'=>'customCheck',
                                            'disabled' => $data['join_status'] ? 'disabled' : ''),
                                        array($other_choice->id => $other_choice->choice)
                                    ))?>
                                    <?php write_html($this->formTextArea(
                                        'multi_answer_othertext/' . $question->id . '/' . $other_choice->id,
                                        $data['join_status'] ? : PHPParser::ACTION_FORM,
                                        array('cols' => '30', 'rows' => '10', 'maxlength'=>'255', 'disabled' => $data['join_status'] ? 'disabled' : '')
                                    ))?>
                                </li>
                            <?php endif;?>
                        <?php endif;?>
                    </ul>
                </dd>
            <?php elseif($question->type_id == QuestionTypeService::FREE_ANSWER_TYPE): ?>
                <dd>
                    <?php write_html($this->formTextarea(
                        'free_answer/' . $question->id,
                        $data["message_info"]["action_status"]->status == CpUserActionStatus::JOIN ? : PHPParser::ACTION_FORM,
                        array('maxlength'=>'2048', 'disabled' => $data['join_status'] ? 'disabled' : '')
                    )) ?>
                </dd>
            <?php elseif($question->type_id == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE): ?>
                <dd>
                    <?php
                    $requirement = $data['cp_questionnaire_service']->getRequirementByQuestionId($question->id);
                    $choices = $data['cp_questionnaire_service']->getChoicesExceptOtherChoiceByQuestionId($question->id)->toArray();
                    if($requirement->random_order_flg == CpQuestionnaireService::RANDOM_ORDER) shuffle($choices);
                    ?>
                    <?php
                    $option = array();
                    $option[''] = '選択してください';
                    foreach($choices as $choice) {
                        $option[$choice->id] = $choice->choice;
                    } ?>
                    <?php write_html($this->formSelect(
                        'single_answer/' . $question->id,
                        $data['join_status'] ? : PHPParser::ACTION_FORM,
                        array('disabled' => $data['join_status'] ? 'disabled' : ''),
                        $option
                    )) ?>
                </dd>
            <?php else: ?>
                <dd>
                    <ul class="moduleItemImg">
                        <?php
                        $requirement = $data['cp_questionnaire_service']->getRequirementByQuestionId($question->id);
                        $choices = $data['cp_questionnaire_service']->getChoicesExceptOtherChoiceByQuestionId($question->id)->toArray();
                        $choice_count = count($choices);
                        $choice_no = 1
                        ?>
                        <?php if($requirement->random_order_flg == CpQuestionnaireService::RANDOM_ORDER) shuffle($choices); //設問のランダム表示 ?>
                        <?php if($requirement->multi_answer_flg != CpQuestionnaireService::MULTI_ANSWER): ?>
                            <?php foreach($choices as $choice): ?>
                                <?php $choice_no == 1 ? write_html('<li>') : write_html('--><li>') ?>
                                <input type="radio" class="customRadio" name="<?php assign('single_answer/'.$question->id) ?>" id="<?php assign('single_answer/'.$question->id.'_'.$choice->id)?>"
                                       value="<?php assign($choice->id) ?>" <?php assign($data['join_status'] ? 'disabled=disabled' : '') ?>
                                    <?php assign($data['join_status'] ? 'checked=checked' : '') ?>>
                                <label for="<?php assign('single_answer/'.$question->id.'_'.$choice->id)?>">
                                    <figure>
                                        <figcaption class="title" data-action_type="questionnaire">　</figcaption>
                                        <span class="img"><img src="<?php assign($choice->image_url);?>" alt="<?php assign($choice->choice);?>"></span>
                                    </figure>
                                </label>
                                <a href="javascript:void(0)" class="previwe">拡大表示する</a>
                                <?php $choice_no == $choice_count ? write_html('</li>') : write_html('</li><!--') ?>
                                <?php $choice_no += 1; ?>
                            <?php endforeach;?>
                        <?php else: ?>
                            <?php foreach($choices as $choice): ?>
                                <?php $choice_no == 1 ? write_html('<li>') : write_html('--><li>') ?>
                                <input type="checkbox" class="customCheck" name="<?php assign('multi_answer/'.$question->id.'/'.$choice->id) ?>" id="<?php assign('multi_answer/'.$question->id.'_'.$choice->id)?>"
                                       value="<?php assign($choice->id) ?>" <?php assign($data['join_status'] ? 'disabled=disabled' : '') ?>
                                    <?php assign($data['join_status'] ? 'checked=checked' : '')?>>
                                <label for="<?php assign('multi_answer/'.$question->id.'_'.$choice->id)?>">
                                    <figure>
                                        <figcaption class="title" data-action_type="questionnaire">　</figcaption>
                                        <span class="img"><img src="<?php assign($choice->image_url);?>" alt="<?php assign($choice->choice);?>"></span>
                                    </figure>
                                </label>
                                <a href="javascript:void(0)" class="previwe">拡大表示する</a>
                                <?php $choice_no == $choice_count ? write_html('</li>') : write_html('</li><!--') ?>
                                <?php $choice_no += 1; ?>
                            <?php endforeach;?>
                        <?php endif;?>
                    </ul>
                </dd>
            <?php endif; ?>
        <?php endforeach; ?>
    </dl>
</form>