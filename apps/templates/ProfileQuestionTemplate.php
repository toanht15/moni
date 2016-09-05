<?php $service_factory = new aafwServiceFactory();
/** @var QuestionNgWordService $questionNgWordService */
$questionNgWordService = $service_factory->create('QuestionNgWordService');
?>
<?php foreach ($data['question_list'] as $question_id): ?>
<?php $question_type = $data['question_type/'.$question_id] ? $data['question_type/'.$question_id] : QuestionTypeService::CHOICE_ANSWER_TYPE ?>
<li class="adminCustomProfile jsModuleContWrap" id="<?php assign($question_id.'_1') ?>" data-question-id="<?php assign($question_id) ?>" data-question-type="<?php assign($question_type)?>">
    <p class="label"><?php write_html($this->formCheckBox2('is_use/'.$question_id, $data['is_use/'.$question_id], array(), array('1' => 'フリー項目'))) ?></p>
    <div class="customProfileWrap">
        <p class="title jsModuleContTile close"
            ><?php if($data['UserSettingError'] && !$data['UserSettingError']->isValid('question/'.$question_id)): ?>
                  <?php
                    if($data['UserSettingError']->getError('question/'.$question_id) == 'NG_QUESTION') {
                        $ngWord = $questionNgWordService->getNgWordInQuestion($data['question/'.$question_id],$data['brand_id']);
                    }
                  ?>
                <span class="iconError1"><?php assign ( str_replace(array('<%ng_word>'), array($ngWord), $data['UserSettingError']->getMessage('question/'.$question_id)) )?></span>
            <?php endif; ?><label
            ><span class="num">Q.</span><?php write_html($this->formText('question/'.$question_id, $data['question/'.$question_id], array('placeholder' => '設問文を入力してください'))) ?></label></p>
        <div class="jsModuleContTarget">

            <?php if ($question_id && !$this->isNumeric($question_id)): ?>
                <ul class="itemType">
                    <li><?php write_html($this->formRadio('question_type/'.$question_id, $question_type, array('class' => 'question_type'), array(QuestionTypeService::CHOICE_ANSWER_TYPE => '選択回答（テキスト）'))) ?></li
                        ><li><?php write_html($this->formRadio('question_type/'.$question_id, $question_type, array('class' => 'question_type'), array(QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE => '選択回答（プルダウン）'))) ?></li
                        ><li><?php write_html($this->formRadio('question_type/'.$question_id, $question_type, array('class' => 'question_type'), array(QuestionTypeService::FREE_ANSWER_TYPE => '自由回答（文字入力）'))) ?></li>
                    <!-- /.itemType --></ul>
            <?php else: ?>
                <?php write_html($this->formHidden('question_type/'.$question_id, $question_type))?>
            <?php endif; ?>

            <?php write_html($this->formHidden('choice_order_'.$question_id, '')) ?>
            <?php if($question_type == QuestionTypeService::CHOICE_ANSWER_TYPE): ?>
                <?php $choice_num = 0; ?>
                <ul class="customProfileText" data-question-id="<?php assign($question_id) ?>">
                    <?php foreach ($data['choices'][$question_id] as $choice_id): ?>
                        <li id="<?php assign('choice_'.$question_id.'_'.$choice_id) ?>" data-choice-id="<?php assign($choice_id) ?>"
                            ><?php if($data['UserSettingError'] && !$data['UserSettingError']->isValid('choice/'.$question_id.'/'.$choice_id)): ?>
                                <span class="iconError1"><?php assign ( $data['UserSettingError']->getMessage('choice/'.$question_id.'/'.$choice_id) )?></span>
                            <?php endif; ?>
                        <label><span class="num">A<?php assign(++$choice_num) ?>.</span
                        ><?php write_html($this->formText('choice/'.$question_id.'/'.$choice_id, $data['choice/'.$question_id.'/'.$choice_id], array('placeholder'=>'選択肢を入力してください'))) ?><a href="javascript:void(0)" class="iconBtnDelete">削除する</a></label></li>
                    <?php endforeach; ?>
                    <li><a href="javascript:void(0)" class="linkAdd addChoice">選択肢を追加する</a></li>
                <!-- /.customProfileText --></ul>
                <?php if(preg_match("/^new_/",$question_id)): ?>
                    <p class="customProfilePulldown" style="display:none" data-question-id="<?php assign($question_id) ?>">
                        <?php write_html($this->formTextarea('textareaChoice/'.$question_id, $data['textareaChoice/'.$question_id], array())); ?>
                        <small>※選択肢を1行に1つずつ入力ください<br>※1つの選択肢は12文字までです</small>
                    <!-- /.customProfilePulldown --></p>
                <?php endif; ?>
            <?php endif; ?>

            <?php if($question_type == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE): ?>
                <?php if(($data['UserSettingError'] && !$data['UserSettingError']->isValid('textareaChoice/'.$question_id)) || !$data['choices'][$question_id] || preg_match('/^new_/', $question_id)): ?>
                    <p class="customProfilePulldown" data-question-id="<?php assign($question_id) ?>">
                        <?php if($data['UserSettingError'] && !$data['UserSettingError']->isValid('textareaChoice/'.$question_id)): ?>
                            <span class="iconError1"><?php assign($data['UserSettingError']->getMessage('textareaChoice/'.$question_id) )?></span>
                        <?php endif; ?>
                        <?php write_html($this->formTextarea('textareaChoice/'.$question_id, $data['textareaChoice/'.$question_id], array())); ?>
                        <small>※選択肢を1行に1つずつ入力ください<br>※1つの選択肢は12文字までです</small>
                    <!-- /.customProfilePulldown --></p>
                <?php endif; ?>
                <?php if($data['choices'][$question_id]): ?>
                    <ul class="customProfileText" <?php assign(($data['UserSettingError'] && !$data['UserSettingError']->isValid('textareaChoice/'.$question_id)) || preg_match('/^new_/', $question_id) ? 'style=display:none' : '') ?> data-question-id="<?php assign($question_id) ?>">
                        <?php foreach ($data['choices'][$question_id] as $choice_id): ?>
                            <li id="<?php assign('choice_'.$question_id.'_'.$choice_id) ?>" data-choice-id="<?php assign($choice_id) ?>"
                                ><?php if($data['UserSettingError'] && !$data['UserSettingError']->isValid('choice/'.$question_id.'/'.$choice_id)): ?>
                                    <span class="iconError1"><?php assign ( $data['UserSettingError']->getMessage('choice/'.$question_id.'/'.$choice_id) )?></span>
                                <?php endif; ?>
                            <label><?php write_html($this->formText('choice/'.$question_id.'/'.$choice_id, $data['choice/'.$question_id.'/'.$choice_id], array('placeholder'=>'選択肢を入力してください'))) ?><a href="javascript:void(0)" class="iconBtnDelete">削除する</a></label></li>
                        <?php endforeach; ?>
                        <li><a href="javascript:void(0)" class="linkAdd addChoice">選択肢を追加する</a></li>
                    <!-- /.customProfileText --></ul>
                <?php endif; ?>
            <?php endif; ?>
            <p class="supplement1" style="<?php assign($question_type == QuestionTypeService::FREE_ANSWER_TYPE ? '' : 'display:none')?>">※メールアドレスなどの個人情報を取得することはできません。</p>
            <dl class="customProfileSetting">
                <?php $is_multi_answer_style = $question_type == QuestionTypeService::CHOICE_ANSWER_TYPE ? '' : 'style=display:none'?>
                <dt <?php assign($is_multi_answer_style)?>>複数回答</dt>
                <dd <?php assign($is_multi_answer_style)?>>
                    <?php if ($data['is_multi_answer/'.$question_id]){
                        $class = 'switch on';
                    } else {
                        $class = 'switch off';
                        $data['is_multi_answer/'.$question_id] = "0";
                    }
                    write_html($this->formHidden('is_multi_answer/'.$question_id, $data['is_multi_answer/'.$question_id]))
                    ?><a href="javascript:void(0)" class="<?php assign($class) ?>"><span class="switchInner"><span class="selectON">ON</span><span class="selectOFF">OFF</span></span></a></dd>

                <dt class="fixChild">回答必須</dt>
                <dd class="fixChild"
                    ><?php if ($data['is_requirement/'.$question_id]){
                        $class = 'switch on';
                    } else {
                        $class = 'switch off';
                        $data['is_requirement/'.$question_id] = "0";
                    }
                    write_html($this->formHidden('is_requirement/'.$question_id, $data['is_requirement/'.$question_id]))
                    ?><a href="javascript:void(0)" class="<?php assign($class) ?>"><span class="switchInner"><span class="selectON">ON</span><span class="selectOFF">OFF</span></span></a></dd>

                <?php $is_random_choice_style = ($question_type == QuestionTypeService::CHOICE_ANSWER_TYPE || $question_type == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE) ? '' : 'style=display:none'?>
                <dt <?php assign($is_random_choice_style)?>>選択肢をランダムに表示する</dt>
                <dd <?php assign($is_random_choice_style)?>>
                    <?php if ($data['is_random_choice/'.$question_id]){
                        $class = 'switch on';
                    } else {
                        $class = 'switch off';
                        $data['is_random_choice/'.$question_id] = "0";
                    }
                    write_html($this->formHidden('is_random_choice/'.$question_id, $data['is_random_choice/'.$question_id]))
                    ?><a href="javascript:void(0)" class="<?php assign($class) ?>"><span class="switchInner"><span class="selectON">ON</span><span class="selectOFF">OFF</span></span></a></dd>

                <?php $is_use_other_style = $question_type == QuestionTypeService::CHOICE_ANSWER_TYPE ? '' : 'style=display:none'?>
                <dt <?php assign($is_use_other_style)?>>その他の選択肢を使用する</dt>
                <dd <?php assign($is_use_other_style)?>>
                    <?php if ($data['is_use_other/'.$question_id]){
                        $class = 'switch on';
                    } else {
                        $class = 'switch off';
                        $data['is_use_other/'.$question_id] = "0";
                    }
                    write_html($this->formHidden('is_use_other/'.$question_id, $data['is_use_other/'.$question_id]))
                    ?><a href="javascript:void(0)" class="<?php assign($class) ?>"><span class="switchInner"><span class="selectON">ON</span><span class="selectOFF">OFF</span></span></a></dd>
            <!-- /.customProfileSetting --></dl>
            <p class="customItemDelete"><a href="javascript:void(0)" class="linkDelete">設問を削除する</a></p>
        <!-- /.jsModuleContTarget --></div>
    <!-- /.customProfileWrap --></div>
<!-- /.adminCustomProfile--></li>
<?php endforeach; ?>