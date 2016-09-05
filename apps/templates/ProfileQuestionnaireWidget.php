<?php if ($data['profile_questionnaires']): ?>
    <?php $i=1; ?>
    <?php foreach($data['profile_questionnaires'] as $questionnaire): ?>
        <li class="jsCheckToggleWrap <?php if($i==1) assign('questionnaire_template') ?>">
            <label class="questionnaire_title"><input type="checkbox" class="jsCheckToggle" name="item_<?php assign($i)?>" <?php if($questionnaire->public_flg) assign('checked') ?>>フリー項目<?php assign($i) ?></label><a href="javascript:void(0)" class="openEditBox">【編集】</a>
            <div class="customItemWrap jsCheckToggleTarget">
                <p class="itemTitle"><strong class="itemTitleStrong">フリー項目<?php assign($i) ?>の設定</strong>
                    <?php write_html($this->formText("question_".$i, $questionnaire->question, array("class"=>"question", 'placeholder'=>'質問文　[例]この商品を知っていましたか？'))) ?><br>
                </p>
                <ul>
                    <li><label><input type="radio" class="requirement" name="requirement_<?php assign($i) ?>" value="<?php assign(ProfileQuestionnaireService::QUESTIONNAIRE_NOT_REQUIRED) ?>" <?php if($questionnaire->requirement_flg == ProfileQuestionnaireService::QUESTIONNAIRE_NOT_REQUIRED) assign('checked') ?>>任意</label></li>
                    <li><label><input type="radio" class="requirement" name="requirement_<?php assign($i) ?>" value="<?php assign(ProfileQuestionnaireService::QUESTIONNAIRE_REQUIRED) ?>" <?php if($questionnaire->requirement_flg == ProfileQuestionnaireService::QUESTIONNAIRE_REQUIRED) assign('checked') ?>>必須</label></li>
                </ul>
                <ul>
                    <li><label><input type="radio" class="questionnaire_type" name="questionnaire_type_<?php assign($i) ?>" value="<?php assign(ProfileQuestionnaireService::QUESTIONNAIRE_TYPE_RADIO) ?>" <?php if($questionnaire->type == ProfileQuestionnaireService::QUESTIONNAIRE_TYPE_RADIO) assign('checked'); ?>>単一回答</label><img src="<?php assign($this->setVersion('/img/setting/imgRadioArea1.png'))?>" width="281" height="134" alt="単一回答" class="sampleImg"></li>
                    <li><label><input type="radio" class="questionnaire_type" name="questionnaire_type_<?php assign($i) ?>" value="<?php assign(ProfileQuestionnaireService::QUESTIONNAIRE_TYPE_CHECKBOX) ?>" <?php if($questionnaire->type == ProfileQuestionnaireService::QUESTIONNAIRE_TYPE_CHECKBOX) assign('checked'); ?>>複数回答</label><img src="<?php assign($this->setVersion('/img/setting/imgCheckArea1.png'))?>" width="281" height="134" alt="複数回答" class="sampleImg"></li>
                    <li><label><input type="radio" class="questionnaire_type" name="questionnaire_type_<?php assign($i) ?>" value="<?php assign(ProfileQuestionnaireService::QUESTIONNAIRE_TYPE_FREE) ?>" <?php if($questionnaire->type == ProfileQuestionnaireService::QUESTIONNAIRE_TYPE_FREE) assign('checked'); ?>>自由回答</label><img src="<?php assign($this->setVersion('/img/setting/imgFreeArea1.png'))?>" width="281" height="108" alt="自由回答" class="sampleImg"></li>
                </ul>
                <?php if($questionnaire->type == ProfileQuestionnaireService::QUESTIONNAIRE_TYPE_FREE) {
                    $showAnswer = 'display: none';
                }else {
                    $showAnswer = '';
                } ?>
                <p class="answerBox" style="<?php assign($showAnswer)?>">
                    <?php write_html($this->formTextArea("answer_".$i, str_replace("<br />", "\r\n", $questionnaire->choices), array("class"=>"answer",'cols'=>30, 'rows'=>10, 'placeholder'=> 'はい
いいえ'))) ?>
                    <small>※選択肢を改行区切りで入力ください</small></p>
                <?php write_html($this->formHidden("question_id_".$i, $questionnaire->id, array("class"=>"question_id"))) ?>
            </div>
        </li>
        <?php ++$i ?>
    <?php endforeach;?>
<?php else: ?>
    <?php for($i=1; $i <= 2 ; $i++): ?>
        <li class="jsCheckToggleWrap <?php if($i==1) assign('questionnaire_template') ?>">
            <label class="questionnaire_title"><input type="checkbox" class="jsCheckToggle" name="item_<?php assign($i)?>" >フリー項目<?php assign($i) ?></label><a href="javascript:void(0)" class="openEditBox">【編集】</a>
            <div class="customItemWrap jsCheckToggleTarget">
                <p class="itemTitle"><strong class="itemTitleStrong">フリー項目<?php assign($i) ?>の設定</strong>
                    <?php write_html($this->formText("question_".$i, "", array("class"=>"question", 'placeholder'=>'質問文　[例]この商品を知っていましたか？'))) ?><br>
                </p>
                <ul>
                    <li><label><input type="radio" class="requirement" name="requirement_<?php assign($i) ?>" value="<?php assign(ProfileQuestionnaireService::QUESTIONNAIRE_NOT_REQUIRED) ?>" checked>任意</label></li>
                    <li><label><input type="radio" class="requirement" name="requirement_<?php assign($i) ?>" value="<?php assign(ProfileQuestionnaireService::QUESTIONNAIRE_REQUIRED) ?>">必須</label></li>
                </ul>
                <ul>
                    <li><label><input type="radio" class="questionnaire_type" name="questionnaire_type_<?php assign($i) ?>" value="<?php assign(ProfileQuestionnaireService::QUESTIONNAIRE_TYPE_RADIO) ?>" checked>単一回答</label><img src="<?php assign($this->setVersion('/img/setting/imgRadioArea1.png'))?>" width="281" height="134" alt="単一回答" class="sampleImg"></li>
                    <li><label><input type="radio" class="questionnaire_type" name="questionnaire_type_<?php assign($i) ?>" value="<?php assign(ProfileQuestionnaireService::QUESTIONNAIRE_TYPE_CHECKBOX) ?>">複数回答</label><img src="<?php assign($this->setVersion('/img/setting/imgCheckArea1.png'))?>" width="281" height="134" alt="複数回答" class="sampleImg"></li>
                    <li><label><input type="radio" class="questionnaire_type" name="questionnaire_type_<?php assign($i) ?>" value="<?php assign(ProfileQuestionnaireService::QUESTIONNAIRE_TYPE_FREE) ?>">自由回答</label><img src="<?php assign($this->setVersion('/img/setting/imgFreeArea1.png'))?>" width="281" height="108" alt="自由回答" class="sampleImg"></li>
                </ul>
                <p class="answerBox">
                    <?php write_html($this->formTextArea("answer_".$i, "", array("class"=>"answer", 'cols'=>30, 'rows'=>10, 'placeholder'=> 'はい
いいえ'))) ?>
                    <small>※選択肢を改行区切りで入力ください</small></p>
                <?php write_html($this->formHidden("question_id_".$i, -1, array("class"=>"question_id"))) ?>
            </div>
        </li>
    <?php endfor;?>
<?php endif; ?>
