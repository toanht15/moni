
<?php $service_factory = new aafwServiceFactory();
$cp_flow_service = $service_factory->create('CpFlowService');
$manager_service = $service_factory->create('ManagerService');
?>

    <div class="campaignPhotoSearch">
        <ul class="tablink1">
            <?php foreach($data['questionnaire_actions'] as $questionnaire_action): ?>
                <?php $min_step_no = $cp_flow_service->getMinOrderOfActionInGroup($questionnaire_action->cp_action_group_id); ?>
                <?php if ($questionnaire_action->id == $data['action_id']): ?>
                    <li class="current"><span>STEP <?php assign($min_step_no + $questionnaire_action->order_no) ?>：<?php assign($questionnaire_action->getCpActionData()->title) ?></span></li>
                <?php else: ?>
                    <li><a href="<?php write_html(Util::rewriteUrl('admin-cp', 'questionnaires', array($questionnaire_action->id))) ?>">STEP <?php assign($min_step_no + $questionnaire_action->order_no) ?>：<?php assign($questionnaire_action->getCpActionData()->title) ?></a></li>
                <?php endif ?>
            <?php endforeach; ?>
            <!-- /.tablink1 --></ul>
        <div class="itemsSortingDetail">
            <dl>
                <dt>検閲</dt>
                <dd><?php write_html($this->formRadio('panel_hidden_flg', $data['cur_questionnaire_action']->panel_hidden_flg, array('class' => 'jsQuestionnairePanelHiddenFlg'), array(CpQuestionnaireAction::PANEL_TYPE_HIDDEN => 'あり', CpQuestionnaireAction::PANEL_TYPE_AVAILABLE => 'なし<small>（投稿と同時に承認となりAPIに出力されます）</small>'), array(),'',false)); ?></dd>
            </dl>
            <p class="btnSet"><span class="btn3"><a href="javascript:void(0);" class="small1 jsQuestionnairePanelHiddenConfirm">適用</a></span></p>
            <!-- /.itemsSortingDetail --></div>
        <div class="itemsSortingDetail">
            <dl>
                <dt>絞り込み</dt>
                <dd><?php write_html($this->formRadio('approval_status', $data['approval_status'] ? $data['approval_status'] : 1, array('class' => 'jsQuestionnaireAnswerApprovalStatus'), array('1' => '全て', '2' => '承認', '3' => '非承認', '4' => '未承認'))); ?></dd>
                <dt>並び替え</dt>
                <dd>
                    <?php write_html($this->formSelect('order_kind', $data['order_kind'] ? $data['order_kind'] : 1, array('class' => 'jsQuestionnaireAnswerOrderKind'), array('1' => '投稿順', '2' => 'ユーザーID順'))); ?>
                    <?php write_html($this->formRadio('order_type', $data['order_type'] ? $data['order_type'] : 1, array('class' => 'jsQuestionnaireAnswerOrderType'), array('1' => '[A-Z↓] 昇順', '2' => '[Z-A↑] 降順'))); ?>
                </dd>
                <dt>表示件数</dt>
                <dd>
                    <?php write_html($this->formSelect('page_limit', $data['page_limit'], array('class' => 'jsQuestionnaireAnswerPageLimit'), array('10' => '10', '20' => '20', '50' => '50'))); ?>件
                </dd>
            </dl>
            <p class="btnSet"><span class="btn2"><a href="javascript:void(0);" class="small1 jsQuestionnaireAnswerSearchReset">リセット</a></span><span class="btn3"><a href="javascript:void(0);" class="small1 jsQuestionnaireAnswerSearchBtn">適用</a></span></p>
            <!-- /.itemsSortingDetail --></div>

        <!-- /.campaignQuestionnaireAnswerSearch --></div>

    <div class="outputApi">
        <div class="labelModeAllied">
            <dl class="outputApiWrap">
                <dt>出力対象</dt>
                <dd>
                    <?php foreach ($data['qqs'] as $q_no => $qq): ?>
                        <label class="surveyText">
                            <input type="checkbox" name="export_question_ids[]" class="jsExportingQuestion" value="<?php assign($qq['data']['id']) ?>"<?php if ($qq['data']['exporting']) write_html(' checked="checked"') ?>>Q<?php assign($q_no) ?><span class="textBalloon1"><span>【<?php assign($qq['data']['type_text']) ?>】<?php assign($qq['data']['question']) ?></span><!-- /.textBalloon1 --></span></label>
                    <?php endforeach ?>
                </dd>
                <dt class="confirmed">承認済<strong><?php assign($data['approved_count']) ?></strong>件</dt>
                <dd>
                    <?php if ($data['api_url'] != ''): ?>
                        <span class="btn2 jsExportAPIBtn"><a href="javascript:void(0);" class="large2 jsExportAPI">出力対象の更新</a></span>
                        <span class="url jsExportAPIUrl">URL：<?php assign($data['api_url']) ?></span>
                    <?php else: ?>
                        <span class="btn2 jsExportAPIBtn"><a href="javascript:void(0);" class="large2 jsExportAPI">外部出力APIのURL作成</a></span>
                        <span class="url jsExportAPIUrl">URL：なし</span>
                    <?php endif ?>
                </dd>
            </dl>
            <!-- /.labelModeAllied --></div>
        <!-- /.outputApi --></div>

    <?php write_html($this->parseTemplate('BrandcoQuestionnaireAnswerActionMenu.php',  array('menu_order' => '1'))) ?>

    <p class="viewLimit"><span class="viewLimitTitle">表示する質問</span>
        <?php foreach ($data['qqs'] as $q_no => $qq): ?>
            <label class="surveyText">
                <input type="checkbox" name="target_question_ids[]" class="jsTargetingQuestion" value="<?php assign($qq['data']['id']) ?>" <?php if ($qq['data']['targeted']) write_html('checked="checked"') ?>>Q<?php assign($q_no) ?>
                <span class="textBalloon1"><span>【<?php assign($qq['data']['type_text']) ?>】<?php assign($qq['data']['question']) ?></span><!-- /.textBalloon1 --></span>
            </label>
        <?php endforeach ?>
    </p>

    <form method="POST" name="questionnaire_answer_action_form" action="<?php assign(Util::rewriteUrl('admin-cp', 'update_multi_questionnaire_answer_status')) ?>">
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('multi_questionnaire_answer_approval_status', QuestionnaireUserAnswer::APPROVAL_STATUS_APPROVE)) ?>
        <?php write_html($this->formHidden('action_id', $data['action_id'])); ?>

        <ul class="campaignSurvey">
            <?php if (count($data['qas'])): ?>
                <?php foreach($data['qas'] as $bur_id => $qa): ?>
                    <div class="survey">
                        <div class="surveyMeta">
                            <p class="labels">
                                <input type="checkbox" class="jsQuestionnaireAnswerCheck" name="bur_ids[]" value="<?php assign($bur_id) ?>">
                                <span class="<?php assign(QuestionnaireUserAnswer::getApprovalStatusClass($qa['user_info']['approval_status'])) ?>"><?php assign(QuestionnaireUserAnswer::getApprovalStatus($qa['user_info']['approval_status'])) ?></span>
                                <span class="user"><img src="<?php assign($qa['user_info']['profile_image_url']) ?>" width="30" height="30" alt="<?php assign($qa['user_info']['profile_image_url']); ?>" onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';"><span class="name"><?php assign($this->cutLongText($qa['user_info']['name'], 15)); ?></span></span>
                                <!-- /.labels --></p>
                            <p class="postDate"><?php assign($qa['user_info']['created_at']) ?><span class="number">(No.<?php assign($qa['user_info']['bur_no']) ?>)</span></p>
                            <!-- /.surveyMeta --></div>

                        <ul class="answerList">
                            <?php ksort($qa['answer_data']) ?>
                            <?php foreach ($qa['answer_data'] as $q_no => $answers): ?>
                                <li>
                                    <span class="surveyText">
                                        Q<?php assign($q_no) ?>
                                        <span class="textBalloon1">
                                            <span>【<?php assign($data['qqs'][$q_no]['data']['type_text']) ?>】<?php assign($data['qqs'][$q_no]['data']['question']) ?></span>
                                            <!-- /.textBalloon1 --></span></span>
                                    <?php
                                        if($data['qqs'][$q_no]['data']['type'] == QuestionTypeService::FREE_ANSWER_TYPE) {
                                            assign($answers['answer_text']);
                                        } else {
                                            ksort($answers);

                                            if ($data['qqs'][$q_no]['data']['type'] == QuestionTypeService::CHOICE_IMAGE_ANSWER_TYPE) {
                                                foreach ($answers as $answer) {
                                                    write_html('<span class="thumb"><span><img src="' . $answer['image_url'] . '" alt="" data-pin-nopin="true"></span></span>' . $answer['choice'] . '　　');
                                                }
                                            } else {
                                                $answer_arr = array();
                                                foreach ($answers as $answer) {
                                                    if ($answer['other_text']) $answer['choice'] .= '（' . $answer['other_text'] . '）';
                                                    $answer_arr[] = $answer['choice'];
                                                }
                                                assign(join("、", $answer_arr));
                                            }
                                        }
                                    ?>
                                </li>
                            <?php endforeach ?>
                        </ul>
                        <!-- /.survey --></div>
            <?php endforeach; ?>
            <?php endif; ?>
            <!-- /.campaignQuestionnaireAnswer --></ul>
    </form>

<?php write_html($this->parseTemplate('BrandcoQuestionnaireAnswerActionMenu.php',  array('menu_order' => '2'))) ?>

<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoCpDataListPager')->render(array(
    'TotalCount' => $data['total_count'],
    'CurrentPage' => $data['page'],
    'Count' => $data['page_limit'],
))) ?>
