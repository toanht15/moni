<?php
$service_factory = new aafwServiceFactory();
if($data['search_questionnaire_type'] == CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE) {
    $search_type = CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE;
    /** @var CpQuestionnaireService $cp_questionnaire_service */
    $cp_questionnaire_service = $service_factory->create('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);
    $name_key = 'search_profile_questionnaire/';
    $class_name = "refinementItem";
} else {
    $search_type = CpCreateSqlService::SEARCH_QUESTIONNAIRE;
    $cp_questionnaire_service = $service_factory->create('CpQuestionnaireService', CpQuestionnaireService::TYPE_CP_QUESTION);
    $name_key = 'search_questionnaire/';
    $class_name = "stepEnquete";
}
$relation = $cp_questionnaire_service->getProfileQuestionRelationsById($data['relation_id']);
$question = $cp_questionnaire_service->getQuestionById($relation->question_id);
?>
<div class="<?php assign($class_name) ?> jsSearchInputBlock">
    <p class="settingLabel"><?php assign('Q'.$relation->number.'.'.$question->question) ?></p>
    <form>
        <ul class="settingDetail">
            <?php $search_key = $search_type.'/'.$data['relation_id'] ?>
            <?php write_html($this->formHidden("search_type", $search_key)) ?>
            <?php if($question->type_id == QuestionTypeService::FREE_ANSWER_TYPE): ?>
                <li>
                    <?php write_html($this->formCheckbox(
                        $name_key.$data['relation_id'].'/'.CpCreateSqlService::ANSWERED_QUESTIONNAIRE.'/'.$data["search_no"],
                        $data['relation_id'].'/'.CpCreateSqlService::ANSWERED_QUESTIONNAIRE,
                        array('checked' => $data[$search_key][$name_key.$data['relation_id'].'/'.CpCreateSqlService::ANSWERED_QUESTIONNAIRE] ? 'checked' : ''),
                        array('1' => '回答済')
                    ))?>
                </li>
                <li>
                    <?php write_html($this->formCheckbox(
                        $name_key.$data['relation_id'].'/'.CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE.'/'.$data["search_no"],
                        $data['relation_id'].'/'.CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE,
                        array('checked' => $data[$search_key][$name_key.$data['relation_id'].'/'.CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE] ? 'checked' : ''),
                        array('1' => '未回答')
                    ))?>
                </li>
            <?php else: ?>
                <?php
                $requirement = $cp_questionnaire_service->getRequirementByQuestionId($question->id);
                $choices = $cp_questionnaire_service->getChoicesByQuestionId($question->id);
                ?>
                <?php foreach($choices as $choice): ?>
                    <li>
                        <?php write_html($this->formCheckbox(
                            $name_key.$data['relation_id'].'/'.$choice->id.'/'.$data["search_no"],
                            $data[$data['relation_id'].'/'.$choice->id],
                            array('checked' => $data[$search_key][$name_key.$data['relation_id'].'/'.$choice->id] ? 'checked' : ''),
                            array('1' => 'A'.$choice->choice_num.'.'.$choice->choice)
                        ))?>
                    </li>
                <?php endforeach;?>
                <li>
                    <?php write_html($this->formCheckbox(
                        $name_key.$data['relation_id'].'/'.CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE.'/'.$data["search_no"],
                        $data[$data['relation_id'].'/'.CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE],
                        array('checked' => $data[$search_key][$name_key.$data['relation_id'].'/'.CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE] ? 'checked' : ''),
                        array('1' => '未回答')
                    ))?>
                </li>
            <?php endif; ?>
            <?php write_html($this->formHidden('questionnaire_type/'.$search_key, $question->type_id)) ?>
        <!-- /.settingDetail --></ul>
    </form>
    <!-- /.refinementItem --></div>