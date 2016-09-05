<p class="batchAction">
    <span style="margin-right: 25px;border-right: 1px solid #9d9d9d"><?php write_html($this->formCheckbox2('questionnaire_answer_check_all', null, array('class' => 'jsQuestionnaireAnswerCheckAll', 'data-questionnaire_answer_check_class' => 'jsQuestionnaireAnswerCheck'), array('1' => '全選択'))) ?></span>
    <?php write_html($this->formRadio('multi_questionnaire_answer_approval_status_' . $data['menu_order'],
        QuestionnaireUserAnswer::APPROVAL_STATUS_APPROVE,
        array('class' => 'jsMultiQuestionnaireAnswerApprovalStatus'),
        array(QuestionnaireUserAnswer::APPROVAL_STATUS_APPROVE => '承認', QuestionnaireUserAnswer::APPROVAL_STATUS_REJECT => '非承認', QuestionnaireUserAnswer::APPROVAL_STATUS_UNAPPROVED => '未承認'))); ?>
    <span class="btn3"><a href="javascript:void(0);" class="small1 jsQuestionnaireAnswerActionFormSubmit<?php assign($data['menu_order']) ?>">適用</a></span>
    <!-- /.batchAction --></p>

