<?php

AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');
AAFW::import('jp.aainc.classes.CacheManager');

use Michelf\Markdown;

/**
 * Class CpEntryActionManager
 * TODO トランザクション
 */
class CpEntryActionManager extends aafwObject implements CpActionManager {

    use CpActionTrait;

    protected $cp_entry_actions;
    protected $logger;

    function __construct() {
        parent::__construct();
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_entry_actions = $this->getModel("CpEntryActions");
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $cp_action_id
     * @return array|mixed
     */
    public function getCpActions($cp_action_id) {
        $cp_action = $this->getCpActionById($cp_action_id);
        if ($cp_action === null) {
            $entry_action = null;
        } else {
            $entry_action = $this->getCpEntryActionByCpAction($cp_action);
        }
        return array($cp_action, $entry_action);

    }

    /**
     * @param $cp_action_group_id
     * @param $type
     * @param $status
     * @param $order_no
     * @return mixed
     */
    public function createCpActions($cp_action_group_id, $type, $status, $order_no) {
        $cp_action = $this->createCpAction($cp_action_group_id, $type, $status, $order_no);
        $entry_action = $this->createConcreteAction($cp_action);
        $service_factory = new aafwServiceFactory();
        /** @var CpTransactionService $transaction_service */
        $transaction_service = $service_factory->create('CpTransactionService');
        $transaction_service->createCpTransaction($cp_action->id);
        return array($cp_action, $entry_action);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function deleteCpActions(CpAction $cp_action) {
        $this->deleteConcreteAction($cp_action);
        $this->deleteCpAction($cp_action);
    }

    /**
     * @param CpAction $cp_action
     * @param $data
     * @return mixed
     */
    public function updateCpActions(CpAction $cp_action, $data) {
        $this->updateCpAction($cp_action);
        $this->updateConcreteAction($cp_action, $data);

        $cp = $cp_action->getCp();
        $cache_manager = new CacheManager();
        $cache_manager->clearCampaignLPInfo($cp->id);
    }


    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function getConcreteAction(CpAction $cp_action) {
        return $this->getCpEntryActionByCpAction($cp_action);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function createConcreteAction(CpAction $cp_action) {
        $entry_action = $this->cp_entry_actions->createEmptyObject();
        $entry_action->cp_action_id = $cp_action->id;
        $entry_action->title = "キャンペーン告知";
        $entry_action->text = "【キャンペーン概要】  " . PHP_EOL;
        $entry_action->text .= "○○の発売を記念してプレゼントキャンペーンを開催！  " . PHP_EOL;
        $entry_action->text .= "ぜひ奮ってご参加ください！" . PHP_EOL . PHP_EOL;
        $entry_action->text .= "【賞品】  " . PHP_EOL;
        $entry_action->text .= "○○セット　100名様  " . PHP_EOL;
        $entry_action->text .= "○○セット　100名様";

        $entry_action->button_label_text = "参加する";
        $this->cp_entry_actions->save($entry_action);
        return $entry_action;
    }

    /**
     * @param CpAction $cp_action
     * @param $data
     * @return mixed|void
     */
    public function updateConcreteAction(CpAction $cp_action, $data) {
        $entry_action = $this->getCpEntryActionByCpAction($cp_action);
        $entry_action->image_url = $data["image_url"];
        $entry_action->text = $data["text"];
        $entry_action->html_content = Markdown::defaultTransform($data["text"]);
        $entry_action->title = $data['title'];
        $entry_action->button_label_text = $data["button_label_text"];
        $entry_action->del_flg = 0;
        $this->cp_entry_actions->save($entry_action);

        $cp = $cp_action->getCp();
        $cache_manager = new CacheManager();
        $cache_manager->clearCampaignLPInfo($cp->id);
    }

    /**
     * @param $cp_action
     * @return mixed|void
     */
    public function deleteConcreteAction(CpAction $cp_action) {
        $entry_action = $this->getCpEntryActionByCpAction($cp_action);
        $entry_action->del_flg = 1;
        $this->cp_entry_actions->save($entry_action);
    }

    /**
     * @param $cp_action
     * @return mixed
     */
    private function getCpEntryActionByCpAction(CpAction $cp_action) {
        return $this->cp_entry_actions->findOne(array("cp_action_id" => $cp_action->id));
    }

    /**
     * @param $old_cp_action
     * @param $new_cp_action_id
     * @return mixed
     */
    public function copyConcreteAction(CpAction $old_cp_action, $new_cp_action_id) {
        $old_concrete_action = $this->getConcreteAction($old_cp_action);
        $new_concrete_action = $this->cp_entry_actions->createEmptyObject();
        $new_concrete_action->cp_action_id = $new_cp_action_id;
        $new_concrete_action->image_url = $old_concrete_action->image_url;
        $new_concrete_action->button_label_text = $old_concrete_action->button_label_text;
        $new_concrete_action->text = $old_concrete_action->text;
        $new_concrete_action->title = $old_concrete_action->title;
        $new_concrete_action->html_content = $old_concrete_action->html_content;
        $this->cp_entry_actions->save($new_concrete_action);

        /** @var CpEntryProfileQuestionnaireService $cp_entry_questionnaire_service */
        $cp_entry_questionnaire_service = $this->_ServiceFactory->create('CpEntryProfileQuestionnaireService');
        $old_profile_questionnaires = $cp_entry_questionnaire_service->getQuestionnairesByCpActionId($old_cp_action->id);
        foreach ($old_profile_questionnaires as $old_profile_questionnaire) {
            $cp_entry_questionnaire_service->copyQuestionnaire($old_profile_questionnaire, $new_cp_action_id);
        }
    }

    /**
     * CpAction に関連するデータを削除する
     */
    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false) {

        if (!$cp_action || !$cp_action->id) {
            throw new Exception("CpEntryActionManager#deletePhysicalRelatedCpActionData cp_action_id=".$cp_action->id);
        }

        if ($with_concrete_actions) {
            $cp_concrete_action = $this->getCpEntryActionByCpAction($cp_action);
            $this->cp_entry_actions->deletePhysical($cp_concrete_action);
        }
    }

    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user) {
        //ユーザーと関係情報がないので実施しない
    }
}

