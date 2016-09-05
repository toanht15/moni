<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.lib.parsers.CSVParser');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class csv_winner_shipping_list extends BrandcoGETActionBase {
    protected $ContainerName = 'csv_winner_shipping_list';

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    private $is_instant_win_cp;

    public function doThisFirst() {

        $this->Data['cp_id'] = $this->GET['exts'][0];
        $this->Data['action_id'] = $this->GET['exts'][1];
        $this->Data['brand'] = $this->getBrand();

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');
        $this->is_instant_win_cp = $cp_flow_service->checkInstantWinCpByCpId($this->Data['cp_id']);
    }

    public function validate() {

        //社内のIPからはダウンロードできない
        if(!Util::isAcceptRemote() && !Util::isPersonalMachine()){
            return false;
        }

        // 代理店はダウンロードできたらダメ
        $manager = $this->getManager();
        if ($manager && $manager->authority == Manager::AGENT) {
            return false;
        }

        $cp_validator = new CpValidator($this->Data['brand']->id);
        if (!$cp_validator->isOwner($this->Data['cp_id'])) {
            return false;
        }
        if (!$cp_validator->isOwnerOfAction($this->Data['action_id'])) {
            return false;
        }
        if (!$cp_validator->isIncludedInCp($this->Data['cp_id'], $this->Data['action_id'])) {
            return false;
        }

        if ($this->is_instant_win_cp) {
            /** @var CpFlowService $cp_flow_service */
            $cp_flow_service = $this->createService('CpFlowService');
            $this->action = $cp_flow_service->getCpActionById($this->Data['action_id']);
            if (!($this->action->type == CpAction::TYPE_ANNOUNCE || $this->action->type == CpAction::TYPE_ANNOUNCE_DELIVERY)) {
                return false;
            }
        } else {
            /** @var CpMessageDeliveryService $cp_message_delivery_service */
            $cp_message_delivery_service = $this->createService('CpMessageDeliveryService');
            if (!$cp_message_delivery_service->checkExistFixedTargetByCpActionId($this->Data['action_id'])) {
                return false;
            }
        }

        return true;
    }

    function doAction() {
        if ($this->is_instant_win_cp) {
            /** @var CpAnnounceActionService $cp_announce_action_service */
            $cp_announce_action_service = $this->createService('CpAnnounceActionService');
            $brands_users_data = $cp_announce_action_service->getAnnouncedUserByCpActionId($this->Data['action_id']);

            $winner_user_ids = array();
            foreach($brands_users_data as $brands_users) {
                $winner_user_ids[] = intval($brands_users->user_id);
            }
        } else {
            /** @var CpMessageDeliveryService $cp_message_delivery_service */
            $cp_message_delivery_service = $this->createService('CpMessageDeliveryService');
            $winner_user_ids = $cp_message_delivery_service->getFixedTargetUserIdByCpActionId($this->Data['action_id']);
        }

        // 万が一対象者がいなければ処理しない
        if (!$winner_user_ids) exit();

        // Export csv
        $csv = new CSVParser();
        $dt = new DateTime();
        header("Content-type:" . $csv->getContentType());

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');
        $cp_action_group = $cp_flow_service->getCpActionGroupByAction($this->Data['action_id']);
        $dl_action = $cp_flow_service->getCpActionByCpActionGroupIdAndType($cp_action_group->id, CpAction::TYPE_ANNOUNCE);
        if (!$dl_action) {
            $dl_action = $cp_flow_service->getCpActionByCpActionGroupIdAndType($cp_action_group->id, CpAction::TYPE_ANNOUNCE_DELIVERY);
        }
        $step_no = $dl_action->getStepNo();
        $csv->setCSVFileName($dt->format('YmdHis').'_'.$this->Data['cp_id'] . '_step' . $step_no);
        header($csv->getDisposition());

        $db = new aafwDataBuilder();

        $param = array(
            '__NOFETCH__' => true,
            'cp_id' => $this->Data['cp_id'],
            'brand_id' => $this->brand->id,
            'user_ids' => $winner_user_ids
        );

        $rs = $db->getCpShippingAddressUser($param);

        $data_csv['header'] = [
            '会員番号', '名字', '名前', '名字(カナ)', '名前(カナ)', '郵便番号',
            '都道府県', '市区町村', '番地', '建物', '電話番号', '取得日時'
        ];
        while ($address = $db->fetch($rs)) {
            $data_csv['list'][] = $address;
        }
        print mb_convert_encoding($csv->out($data_csv, 1), 'Shift_JIS', "UTF-8");
        exit();
    }
}
