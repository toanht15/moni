<?php
$service_factory = new aafwServiceFactory();
if($data['condition_type'] == SegmentCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE) {
    $target_id = $data['target_id'];
    $name_key = 'search_profile_questionnaire/';
    $cp_questionnaire_service = $service_factory->create('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);
} elseif ($data['condition_type'] == SegmentCreateSqlService::SEARCH_QUESTIONNAIRE) {
    $split_key = explode('/', $data['target_id']);
    $target_id = $split_key[1];
    $name_key = 'search_questionnaire/';
    $cp_questionnaire_service = $service_factory->create('CpQuestionnaireService', CpQuestionnaireService::TYPE_CP_QUESTION);
}
$relation = $cp_questionnaire_service->getProfileQuestionRelationsById($target_id);
$question = $cp_questionnaire_service->getQuestionById($relation->question_id);
?>
<ul class="status">
    <?php write_html($this->formHidden('questionnaire_type/' . $target_id, $question->type_id)) ?>

    <?php $search_key = $name_key . $target_id ?>
    <?php if ($question->type_id == QuestionTypeService::FREE_ANSWER_TYPE): ?>
        <li>
            <?php write_html($this->formCheckbox(
                $search_key . '/' . CpCreateSqlService::ANSWERED_QUESTIONNAIRE,
                1,
                array('checked' => $data['condition_data'][$search_key . '/' . CpCreateSqlService::ANSWERED_QUESTIONNAIRE] ? 'checked' : ''),
                array('1' => '回答済')
            )) ?>
        </li>
        <li>
            <?php write_html($this->formCheckbox(
                $search_key . '/' . CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE,
                1,
                array('checked' => $data['condition_data'][$search_key . '/' . CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE] ? 'checked' : ''),
                array('1' => '未回答')
            )) ?>
        </li>
    <?php else: ?>
        <?php
        $requirement = $cp_questionnaire_service->getRequirementByQuestionId($question->id);
        $choices = $cp_questionnaire_service->getChoicesByQuestionId($question->id);
        ?>
        <?php foreach ($choices as $choice): ?>
            <li>
                <?php write_html($this->formCheckbox(
                    $search_key . '/' . $choice->id,
                    1,
                    array('checked' => $data['condition_data'][$search_key . '/' . $choice->id] ? 'checked' : ''),
                    array('1' => 'A' . $choice->choice_num . '.' . $choice->choice)
                )) ?>
            </li>
        <?php endforeach; ?>
        <li>
            <?php write_html($this->formCheckbox(
                $search_key . '/' . CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE,
                1,
                array('checked' => $data['condition_data'][$search_key . '/' . CpCreateSqlService::NOT_ANSWER_QUESTIONNAIRE] ? 'checked' : ''),
                array('1' => '未回答')
            )) ?>
        </li>
    <?php endif; ?>
    <!-- /.status --></ul>