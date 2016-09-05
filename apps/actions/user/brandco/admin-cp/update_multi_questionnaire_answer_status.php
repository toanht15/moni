<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class update_multi_questionnaire_answer_status extends BrandcoPOSTActionBase {
    protected $ContainerName = 'questionnaires';

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $Form = array(
        'package' => 'admin-cp',
        'action' => 'questionnaires/{action_id}'
    );

    public function validate() {
        if (!$this->POST['action_id'] || !$this->POST['bur_ids']) {
            return false;
        }
        return true;
    }

    public function doAction() {
        $user_answer_transaction = aafwEntityStoreFactory::create('QuestionnaireUserAnswers');
        $logger = aafwLog4phpLogger::getDefaultLogger();

        $qa_approval_status = $this->POST['multi_questionnaire_answer_approval_status'];
        $cp_action_id = $this->POST['action_id'];
        $bur_ids = $this->POST['bur_ids'];

        try {
            $user_answer_transaction->begin();

            if ($qa_approval_status == QuestionnaireUserAnswer::APPROVAL_STATUS_UNAPPROVED) {
                $user_answer_service = $this->getService('QuestionnaireUserAnswerService');
                foreach ($bur_ids as $bur_id) {
                    $user_answer_service->deletePhysicalUserAnswerByBurIdAndCpActionId($bur_id, $cp_action_id);
                }
            } else {
                $this->updateQuestionnaireUswerAnswer($cp_action_id, $bur_ids, $qa_approval_status);
            }

            $user_answer_transaction->commit();
        } catch (Exception $e) {
            $user_answer_transaction->rollback();

            $logger->error('update_multi_photo_status@doAction Error: ' . $e);
            return 'redirect: ' . Util::rewriteUrl('admin-cp', 'questionnaires', array($cp_action_id), array('mid' => 'failed'));
        }

        return 'redirect: ' . Util::rewriteUrl('admin-cp', 'questionnaires', array($cp_action_id), array('mid' => 'updated'));
    }

    public function updateQuestionnaireUswerAnswer($cp_action_id, $bur_ids, $qa_approval_status) {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        $next_action = $cp_flow_service->getCpNextActionByCpActionId($cp_action_id);

        // Check if user finished questionnaire or not
        $next_action_id = $next_action ? $next_action->cp_next_action_id : $cp_action_id;

        $db = aafwDataBuilder::newBuilder();
        $search_conditions = array(
            'next_action_id' => $next_action_id,
            'bur_ids' => $bur_ids
        );
        $user_answer_list = $db->getUserAnswerListForUpdate($search_conditions, null, null, false);

        if (!$user_answer_list) {
            throw new Exception('Invalid User');
        }

        // ユーザーがちゃんと回答したかどうか次のアクションステータスを見て判断する
        $query = "INSERT INTO questionnaire_user_answers(cp_action_id, brands_users_relation_id, finished_answer_id, approval_status, finished_answer_at, created_at, updated_at) VALUES ";

        foreach ($user_answer_list as $user_answer) {
            $query .= "(" . $cp_action_id . ", " . $user_answer['bur_id'] . ", " . $user_answer['finished_answer_id'] . ", " . $qa_approval_status . ", \"" . $user_answer['created_at'] . "\", NOW(), NOW()),";
        }

        $query = substr($query, 0, strlen($query) - 1);
        $query .= " ON DUPLICATE KEY UPDATE approval_status = VALUES(approval_status), updated_at = NOW()";

        $db->executeUpdate($query);
    }
}