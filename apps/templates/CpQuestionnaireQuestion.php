    <?php if($data['question']->type_id == QuestionTypeService::FREE_ANSWER_TYPE): ?>
        <?php write_html($this->parseTemplate('CpQuestionnaireFree.php', array(
            'question'=>$data['question'],
            'action_data'=>$data['action_data']
        ))) ?>
    <?php else: ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('CpQuestionnaireChoice')->render(array(
            'question'=>$data['question'],
            'action_data'=>$data['action_data']
        ))) ?>
    <?php endif; ?>
