<?php
$service_factory = new aafwServiceFactory();
if($data['search_questionnaire_type'] == CpQuestionnaireService::TYPE_PROFILE_QUESTION) {
    $search_type = CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE;
    $name_key = 'search_profile_questionnaire/';
} else {
    $search_type = CpCreateSqlService::SEARCH_QUESTIONNAIRE;
    $name_key = 'search_questionnaire/';
}
?>
<div class="sortBox jsAreaToggleTarget" data-search_type="<?php assign($search_type.'/'.$data['relation_id'])?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
        <ul>
            <li>
                <?php write_html($this->formCheckbox(
                    $name_key.$data['relation_id'].'/'.CpCreateSqlService::ANSWERED_QUESTIONNAIRE.'/'.$this->search_no,
                    $this->POST ? PHPParser::ACTION_FORM : $data['search_questionnaire'][$data['relation_id'].'/'.CpCreateSqlService::ANSWERED_QUESTIONNAIRE],
                    array('checked' => $data['search_questionnaire'][$name_key.$data['relation_id'].'/'.CpCreateSqlService::ANSWERED_QUESTIONNAIRE] ? 'checked' : ''),
                    array('1' => '回答済')
                ))?>
            </li>
            <li>
                <?php write_html($this->formCheckbox(
                    $name_key.$data['relation_id'].'/'.CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE.'/'.$this->search_no,
                    $this->POST ? PHPParser::ACTION_FORM : $data['search_questionnaire'][$data['relation_id'].'/'.CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE],
                    array('checked' => $data['search_questionnaire'][$name_key.$data['relation_id'].'/'.CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE] ? 'checked' : ''),
                    array('1' => '未回答')
                ))?>
            </li>
        </ul>
        <?php write_html($this->formHidden('questionnaire_type/'.$search_type.'/'.$data['relation_id'], QuestionTypeService::FREE_ANSWER_TYPE)) ?>
        <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-clear_type="<?php assign($search_type.'/'.$data['relation_id'])?>" data-search_no="<?php assign($this->search_no)?>">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign($search_type.'/'.$data['relation_id'])?>" data-search_no="<?php assign($this->search_no)?>">絞り込む</a></span>
    </p>
<!-- /.sortBox --></div>
