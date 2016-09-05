<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class InitializeCpInstagramHashtag extends BrandcoBatchBase {

    protected $data_builder;

    /** @var CpInstagramHashtagActionService $cp_instagram_hashtag_action_service */
    protected $cp_instagram_hashtag_action_service;

    public function __construct($argv = null) {
        parent::__construct($argv);
        $this->data_builder = aafwDataBuilder::newBuilder();
        $this->cp_instagram_hashtag_action_service = $this->service_factory->create('CpInstagramHashtagActionService');
    }

    public function executeProcess() {

        $conditions = array(
            'status' => array(Cp::STATUS_FIX, Cp::STATUS_DEMO),
            'end_date' => date('Y/m/d H:i:s', strtotime('-1 day')), // CP終了後1日後まで
            'module_type' => array(CpAction::TYPE_INSTAGRAM_HASHTAG),
            '__NOFETCH__' => true,
        );

        $rs = $this->data_builder->getCpActionsByCpModuleType($conditions, array(), array(), false, 'CpAction');

        while ($cp_action = $this->data_builder->fetch($rs)) {

            if (!$cp_action->id) return;

            try{
                $this->cp_instagram_hashtag_action_service->initializeCpInstagramHashtagByCpActionId($cp_action->id);
                
            }catch (Exception $e) {
                $this->logger->error('InitialiseCpInstagramHashtag#executeProcess()' . $e);
            }
        }
    }
}
