<?php

require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

// hotfix
class FixPersonalInfoFlg{

    public $logger;
    public $hipchatLogger;
    public $serviceFactory;
    /** @var BrandsUsersRelationService $brands_users_relation_service */
    public $brands_users_relation_service;

    public function __construct() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchatLogger = aafwLog4phpLogger::getHipChatLogger();

        $this->serviceFactory = new aafwServiceFactory();
        $this->brands_users_relation_service = $this->serviceFactory->create('BrandsUsersRelationService');
    }

    public function doProcess() {
        $brands_users_relations = $this->getBrandsUsersRelations();
        foreach ($brands_users_relations as $bur) {
            $pqa = $this->getProfileQuestionnaireAnswersByBURId($bur['id']);
            if (!$pqa) {
                $pu = $this->getPreUserByUserId($bur['user_id']);
                if ($pu) {
                    $this->brands_users_relation_service->updatePersonalInfo($bur['id'], 0);
                } else {
                    $pqqr = $this->getProfileQuestionnairesQuestionsRelationByBrandId($bur['brand_id']);
                    if ($pqqr) {
                        $this->brands_users_relation_service->updatePersonalInfo($bur['id'], 0);
                    }
                }
            }
        }
    }


    public function getBrandsUsersRelations() {
        $sql = "SELECT id, brand_id, user_id FROM brands_users_relations WHERE id >= 2911749";

        $db = aafwDataBuilder::newBuilder();
        return $db->getBySQL($sql, array());
    }

    public function getPreUserByUserId($user_id) {
        $sql = "SELECT 1 FROM pre_users WHERE user_id = {$user_id}";

        $db = aafwDataBuilder::newBuilder();
        return $db->getBySQL($sql, array());
    }

    public function getProfileQuestionnaireAnswersByBURId($brands_users_relation_id) {
        $sql = "SELECT 1 FROM profile_questionnaire_answers WHERE relation_id = {$brands_users_relation_id}";

        $db = aafwDataBuilder::newBuilder();
        return $db->getBySQL($sql, array());
    }

    public function getProfileQuestionnairesQuestionsRelationByBrandId($brand_id) {
        $sql = "SELECT 1 FROM profile_questionnaires_questions_relations WHERE brand_id = {$brand_id} AND public = 1 AND del_flg = 0";

        $db = aafwDataBuilder::newBuilder();
        return $db->getBySQL($sql, array());
    }
}
