<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.services.CpMessageDeliveryService');

class api_check_shipping_address_action_status extends BrandcoGETActionBase {

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $ContainerName = 'api_check_shipping_address_action_status';
    protected $AllowContent = array('JSON');

    private $cpFlowService;
    private $checkShippingAddressActionStatusService;

    const JOINED_ALL_SHPPING_ADDRESS_ACTION = 0;
    const NOT_JOINED_ALL_SHPPING_ADDRESS_ACTION = 1;

    public function doThisFirst() {
        ini_set('memory_limit', '256M');
        $this->cpFlowService = $this->createService('CpFlowService');
        $this->checkShippingAddressActionStatusService = $this->createService('CheckShippingAddressUserActionStatusService');
    }

    public function validate() {
        $this->brand = $this->getBrand();
        $cp_validator = new CpValidator($this->brand->id);
        if (!$cp_validator->isOwner($this->cp_id)) {
            $msg = '操作が失敗しました。';
            $this->sendErrorResponse($msg);

            return false;
        }
        if (!$cp_validator->isOwnerOfAction($this->action_id)) {
            $msg = '操作が失敗しました。';
            $this->sendErrorResponse($msg);
            return false;
        }
        if ($this->GET['update_type'] != CpMessageDeliveryService::RANDOM_TARGET) {
            if (!$this->user) {
                $msg = '対象ユーザを選択してください。';
                $this->sendErrorResponse($msg);
                return false;
            }
        }
        return true;
    }

    public function doAction(){

        $currentGroup = $this->cpFlowService->getCpActionById($this->action_id)->getCpActionGroup();

        $shippingAddressActionIds = $this->checkShippingAddressActionStatusService->getShippingAddressActionIdsInGroupBefore($this->cp_id, $currentGroup);

        if ($currentGroup->order_no == 1 || !$shippingAddressActionIds) {
            $jsonData = [
                'isNotInputShippingAddress' => self::JOINED_ALL_SHPPING_ADDRESS_ACTION,
            ];
            $this->assign('json_data', $this->createAjaxResponse('ok', $jsonData));
            return 'dummy.php';
        }

        if ($this->GET['select_all_users'] || $this->GET['update_type'] == CpMessageDeliveryService::RANDOM_TARGET) {
            // 全ファンから対象者をカウントする
            $searchQuery = $this->checkShippingAddressActionStatusService->getSearchQuery([
                'cp_id'     => $this->cp_id,
                'brand_id'  => $this->brand->id
            ],
            $this->getSearchConditionSession($this->cp_id)
            );
            $joinStatus = $this->checkShippingAddressActionStatusService->isNotJoinShippingAddressUserActionFromAllFan($searchQuery,$shippingAddressActionIds) ? self::NOT_JOINED_ALL_SHPPING_ADDRESS_ACTION : self::JOINED_ALL_SHPPING_ADDRESS_ACTION;
        } else {
            $targetUsers = [];
            foreach ($this->user as $user_id) {
                if (!is_numeric($user_id)) {
                    continue;
                }
                $targetUsers[] = $user_id;
            }
            $joinStatus = $this->checkShippingAddressActionStatusService->isNotJoinShippingAddressUserActionFromTargetFan($this->cp_id, implode(',', $targetUsers),$shippingAddressActionIds) ? self::NOT_JOINED_ALL_SHPPING_ADDRESS_ACTION : self::JOINED_ALL_SHPPING_ADDRESS_ACTION;
        }
        $jsonData = [
            'isNotInputShippingAddress' => $joinStatus ? self::NOT_JOINED_ALL_SHPPING_ADDRESS_ACTION : self::JOINED_ALL_SHPPING_ADDRESS_ACTION
        ];
        $this->assign('json_data', $this->createAjaxResponse('ok', $jsonData));
        return 'dummy.php';
    }

    private function sendErrorResponse($msg) {
        $errors['msg'] = $msg;
        $json_data = $this->createAjaxResponse('ng', array(), $errors);
        $this->assign('json_data', $json_data);
    }
}
