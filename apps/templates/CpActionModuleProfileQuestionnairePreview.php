<?php if (count($data['entry_questionnaires']) > 0): ?>
    <section class="message jsMessage">
        <h1 class="messageHd1">下記のアンケートにご回答ください。</h1>
        <ul class="commonTableList1">
            <?php
            $service_factory = new aafwServiceFactory();
            /** @var CpQuestionnaireService $questionnaire_service */
            $questionnaire_service = $service_factory->create('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);
            ?>
            <?php foreach($data['profile_questions_relations'] as $profile_question_relation): ?>
                <?php if (isset($data['entry_questionnaires'][$profile_question_relation->question_id])): ?>
                    <?php $profile_questionnaire = $questionnaire_service->getQuestionById($profile_question_relation->question_id) ?>
                    <li>
                        <p class="title1">
                            <span <?php if($profile_question_relation->requirement_flg) write_html('class="require1"') ?>><?php assign($profile_questionnaire->question) ?></span>
                            <!-- /.title1 --></p>

                        <?php if($profile_questionnaire->type_id == QuestionTypeService::CHOICE_ANSWER_TYPE || $profile_questionnaire->type_id == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE): ?>
                            <?php $question_requirement = $questionnaire_service->getRequirementByQuestionId($profile_questionnaire->id);
                            $choices = $questionnaire_service->getChoicesByQuestionId($profile_questionnaire->id)->toArray();
                            if ($question_requirement->random_order_flg) {
                                if (end($choices)->other_choice_flg) {
                                    $other_choice = end($choices);
                                    $choices = array_slice($choices, 0, count($choices) - 1);
                                    shuffle($choices);
                                    $choices[] = $other_choice;
                                } else {
                                    shuffle($choices);
                                }
                            }
                            ?>
                        <?php endif; ?>

                        <?php if($profile_questionnaire->type_id == QuestionTypeService::CHOICE_ANSWER_TYPE): ?>
                            <ul class="itemEdit">
                                <?php if ( $this->ActionError && !$this->ActionError->isValid('answer_'.$profile_questionnaire->id) ):?>
                                    <span class="iconError1"><?php assign ( $this->ActionError->getMessage('answer_'.$profile_questionnaire->id) )?></span>

                                <?php elseif ($this->ActionError && !$this->ActionError->isValid('other_answer_'.$profile_questionnaire->id)): ?>
                                    <span class="iconError1"><?php assign ( $this->ActionError->getMessage('other_answer_'.$profile_questionnaire->id) )?></span>
                                <?php endif; ?>

                                <?php if (!$question_requirement->multi_answer_flg): ?>

                                    <?php foreach ($choices as $choice): ?>
                                        <li><?php write_html($this->formRadio( 'answer_'.$profile_questionnaire->id, PHPParser::ACTION_FORM, array('class'=>'customRadio'), array($choice->id =>$choice->choice))); ?>
                                            <?php if ($choice->other_choice_flg): ?>
                                                <?php write_html($this->formTextArea('other_answer_'.$profile_questionnaire->id, PHPParser::ACTION_FORM, array('cols'=>30, 'rows'=>10))) ?>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <?php foreach($choices as $choice):?>
                                        <li><?php write_html($this->formCheckbox2('answer_'.$profile_questionnaire->id.'[]', $this->getActionFormValue('answer_'.$profile_questionnaire->id), array('class'=>'customCheck'), array($choice->id =>$choice->choice))) ?>
                                            <?php if ($choice->other_choice_flg): ?>
                                                <?php write_html($this->formTextArea('other_answer_'.$profile_questionnaire->id, PHPParser::ACTION_FORM, array('cols'=>30, 'rows'=>10))) ?>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach;?>

                                <?php endif; ?>
                            </ul>
                        <?php elseif($profile_questionnaire->type_id == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE): ?>
                            <p class="itemEdit">
                                <?php if ( $this->ActionError && !$this->ActionError->isValid('answer_'.$profile_questionnaire->id) ):?>
                                    <span class="iconError1"><?php assign ( $this->ActionError->getMessage('answer_'.$profile_questionnaire->id) )?></span>
                                <?php endif; ?>
                                <?php
                                $select_choices = array();
                                $select_choices[''] = '選択してください';
                                foreach($choices as $choice) {
                                    $select_choices[$choice->id] = $choice->choice;
                                }
                                ?>
                                <?php write_html($this->formSelect('answer_'.$profile_questionnaire->id, PHPParser::ACTION_FORM, array(), $select_choices)); ?>
                            </p>
                        <?php else: ?>
                            <p class="itemEdit">
                                <?php if ( $this->ActionError && !$this->ActionError->isValid('answer_'.$profile_questionnaire->id) ):?>
                                    <span class="iconError1"><?php assign ( $this->ActionError->getMessage('answer_'.$profile_questionnaire->id) )?></span>
                                <?php endif; ?>

                                <span class="editInput">
                                <?php write_html($this->formTextArea('answer_'.$profile_questionnaire->id, PHPParser::ACTION_FORM, array('cols'=>30, 'rows'=>10))) ?>
                            </span>
                            </p>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>

            <!-- /.commonTableList1 --></ul>
        <section class="ruleAreaWrap1">
            <div class="messageFooter">
                <p class="btnSet"><span class="btn1"><a href="javascript:;" id="submitEntry">回答する</a></span></p>
            </div>
        </section>
    </section>
<?php endif; ?>