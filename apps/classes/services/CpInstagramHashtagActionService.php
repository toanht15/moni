<?php
AAFW::import('jp.aainc.aafw.base.aafwServiceBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.vendor.instagram.Instagram');

class CpInstagramHashtagActionService extends aafwServiceBase {

    /** @var CpInstagramHashtagActions $cp_instagram_hashtag_actions */
    private $cp_instagram_hashtag_actions;

    /** @var CpInstagramHashtags $cp_instagram_hashtags */
    private $cp_instagram_hashtags;

    public function __construct() {
        $this->cp_instagram_hashtag_actions = $this->getModel("CpInstagramHashtagActions");
        $this->cp_instagram_hashtags = $this->getModel("CpInstagramHashtags");

        $this->data_builder = new aafwDataBuilder();
        $this->instagram = new Instagram();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    public function getCpInstagramHashtagActionByCpActionId($cp_action_id) {
        if (!$cp_action_id) return array();

        $filter = array(
            'cp_action_id' => $cp_action_id
        );
        return $this->cp_instagram_hashtag_actions->findOne($filter);
    }

    public function saveCpInstagramHashtagAction(CpInstagramHashtagAction $cp_instagram_hashtag_action) {
        return $this->cp_instagram_hashtag_actions->save($cp_instagram_hashtag_action);
    }

    public function initializeInstagramHashtagByCpId($cp_id) {
        if (!$cp_id) return;

        $params = array(
            '__NOFETCH__' => true,
            'cp_id' => $cp_id,
            'module_type' => CpAction::TYPE_INSTAGRAM_HASHTAG
        );

        $access_token = config('@instagram.User.ManagerAccessToken');
        if (!$access_token) return;

        $rs = $this->data_builder->getCpActionsByCpModuleType($params, array(), array(), false, 'CpAction');

        while ($cp_action = $this->data_builder->fetch($rs)) {
            $this->initializeCpInstagramHashtagByCpActionId($cp_action->id);
        }
    }

    public function initializeCpInstagramHashtagByCpActionId($cp_action_id) {
        if (!$cp_action_id) return;

        $access_token = config('@instagram.User.ManagerAccessToken');
        if (!$access_token) return;

        $cp_instagram_hashtag_action = $this->getCpInstagramHashtagActionByCpActionId($cp_action_id);

        if (!$cp_instagram_hashtag_action || !$cp_instagram_hashtag_action->isExistsCpInstagramHashtags()) return;

        $cp_instagram_hashtag_service = $this->getService('CpInstagramHashtagService');

        foreach ($cp_instagram_hashtag_service->getCpInstagramHashtagsOrderById($cp_instagram_hashtag_action->id) as $cp_instagram_hashtag) {

            if (!$cp_instagram_hashtag->total_media_count_start) {

                $object = $this->instagram->getTagInfo($cp_instagram_hashtag->hashtag, $access_token);

                if ($object->meta->code != Instagram::LEGAL_ACCESS_CODE) {
                    $this->hipchat_logger->error('CpInstagramHashtagActionService#initializeInstagramHashtag Illegal access code:' . $object->meta->code);
                    if ($object->meta->error_type) $this->logger->error('error_type:' . $object->meta->error_type);
                    if ($object->meta->error_message) $this->logger->error('error_message:' . $object->meta->error_message);
                    continue;
                }

                if ($object->data->media_count && $object->data->name == $cp_instagram_hashtag->hashtag) {
                    $cp_instagram_hashtag->total_media_count_start = $object->data->media_count;
                }
            }

            $this->cp_instagram_hashtags->save($cp_instagram_hashtag);
        }
    }
}
