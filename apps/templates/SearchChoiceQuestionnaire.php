<?php
    $service_factory = new aafwServiceFactory();
    if($data['search_questionnaire_type'] == CpQuestionnaireService::TYPE_PROFILE_QUESTION) {
        $search_type = CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE;
        /** @var CpQuestionnaireService $cp_questionnaire_service */
        $cp_questionnaire_service = $service_factory->create('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);
        $name_key = 'search_profile_questionnaire/';
    } else {
        $search_type = CpCreateSqlService::SEARCH_QUESTIONNAIRE;
        $cp_questionnaire_service = $service_factory->create('CpQuestionnaireService', CpQuestionnaireService::TYPE_CP_QUESTION);
        $name_key = 'search_questionnaire/';
    }
    $requirement = $cp_questionnaire_service->getRequirementByQuestionId($data['question_id']);
    $choices = $cp_questionnaire_service->getChoicesByQuestionId($data['question_id']);
?>
<div class="sortBox jsAreaToggleTarget" data-search_type="<?php assign($search_type.'/'.$data['relation_id'])?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
        <ul>
            <?php foreach($choices as $choice): ?>
                <li>
                    <?php write_html($this->formCheckbox(
                        $name_key.$data['relation_id'].'/'.$choice->id.'/'.$this->search_no,
                        $this->POST ? PHPParser::ACTION_FORM : $data['search_questionnaire'][$data['relation_id'].'/'.$choice->id],
                        array('checked' => $data['search_questionnaire'][$name_key.$data['relation_id'].'/'.$choice->id] ? 'checked' : ''),
                        array('1' => $choice->choice)
                    ))?>
                </li>
            <?php endforeach;?>
            <li>
                <?php write_html($this->formCheckbox(
                    $name_key.$data['relation_id'].'/'.CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE.'/'.$this->search_no,
                    $this->POST ? PHPParser::ACTION_FORM : $data['search_questionnaire'][$data['relation_id'].'/'.CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE],
                    array('checked' => $data['search_questionnaire'][$name_key.$data['relation_id'].'/'.CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE] ? 'checked' : ''),
                    array('1' => '未回答')
                ))?>
            </li>
        </ul>
        <?php write_html($this->formHidden('questionnaire_type/'.$search_type.'/'.$data['relation_id'], QuestionTypeService::CHOICE_ANSWER_TYPE)) ?>
        <?php if($requirement->multi_answer_flg == CpQuestionnaireService::MULTI_ANSWER): ?>
            <?php write_html($this->formHidden('switch_type/'.$search_type.'/'.$data['relation_id'], $data['search_questionnaire']['switch_type/'.$search_type.'/'.$data['relation_id']]==CpCreateSqlService::QUERY_TYPE_AND ? CpCreateSqlService::QUERY_TYPE_AND : CpCreateSqlService::QUERY_TYPE_OR))?>
            <p class="switchWrap">and<a href="javascript:void(0)" class="toggle_switch <?php assign($data['search_questionnaire']['switch_type/'.$search_type.'/'.$data['relation_id']] == CpCreateSqlService::QUERY_TYPE_AND ? 'left' : 'right')?>"
                data-switch_type="<?php assign($search_type.'/'.$data['relation_id'])?>">toggle_switch</a>or</p>
        <?php endif;?>
        <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-clear_type="<?php assign($search_type.'/'.$data['relation_id'])?>" data-search_no="<?php assign($this->search_no)?>">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign($search_type.'/'.$data['relation_id'])?>" data-search_no="<?php assign($this->search_no)?>">絞り込む</a></span>
    </p>
<!-- /.sortBox --></div>
