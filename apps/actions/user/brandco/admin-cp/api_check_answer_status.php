<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.CpMessageDeliveryService');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class api_check_answer_status extends BrandcoGETActionBase {
    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $ContainerName = 'api_check_answer_status';
    protected $AllowContent = array('JSON');
    protected $reservation;
    protected $message_delivery_service;
    protected $flow_service;

    /** @var aafwDataBuilder $data_builder  */
    protected $data_builder;

    public function doThisFirst() {
        ini_set('memory_limit', '256M');
        $this->data_builder = aafwDataBuilder::newBuilder();
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

        /** @var CpMessageDeliveryService $message_delivery_service */
        $this->message_delivery_service = $this->createService('CpMessageDeliveryService');
        $this->reservation = $this->message_delivery_service->getOrCreateCurrentReservation(
            $this->brand->id,
            $this->action_id
        );
        if (!$this->reservation) {
            $msg = '送信予約が設定されていません。';
            $this->sendErrorResponse($msg);

            return false;
        }

        return true;
    }

    public function doAction(){
        $this->flow_service = $this->createService('CpFlowService');
        $action_group = $this->getCurrentActionGroup();

        if($action_group->order_no == 1) {
            $json_info = [
                'title' => '',
                'unfinish_count' => 0,
            ];
            $this->assign('json_data', $this->createAjaxResponse('ok', $json_info));
            return 'dummy.php';
        }

        $prev_group = $this->flow_service->getCpActionGroupByCpIdAndOrderNo($this->cp_id, $action_group->order_no - 1);
        $prev_group_action = $this->flow_service->getCpActionsByCpActionGroupId($prev_group->id)->toArray();
        $prev_group_last_action = end($prev_group_action);
        $prev_action_id = $prev_group_last_action->id;
        $cp_id = $this->cp_id;
        $unfinish_count = 0;

        if ($this->GET['select_all_users'] ||
            $this->GET['update_type'] == CpMessageDeliveryService::RANDOM_TARGET) {
            // 全ファンから対象者をカウントする
            $search_query = $this->getSearchQuery([
                    'cp_id'     => $this->cp_id,
                    'brand_id'  => $this->brand->id
                ],
                $this->getSearchConditionSession($this->cp_id)
            );
            $unfinish_count = $this->getUnfinishedCountFromAllFans($search_query, $prev_action_id);
        } else {
            $target_users = [];
            foreach ($this->user as $user_id) {
                if (!is_numeric($user_id)) {
                    continue;
                }
                $target_users[] = $user_id;
            }
            $unfinish_count = $this->getUnfinishedCountFromTargetFans(
                $cp_id,
                $prev_action_id,
                implode(',', $target_users)
            );
        }

        $json_info = [
            'title' => $prev_group_last_action->getCpActionData()->title,
            'unfinish_count' => $unfinish_count,
        ];
        $this->assign('json_data', $this->createAjaxResponse('ok', $json_info));

        return 'dummy.php';
    }

    private function getCurrentActionGroup() {
        $cp_action = $this->flow_service->getCpActionById($this->action_id);
        $action_group = $this->flow_service->getCpActionGroupById($cp_action->cp_action_group_id);
        return $action_group;
    }

    private function getSearchQuery($page_info, $search_condition) {
        /** @var CpCreateSqlService $create_sql_service*/
        $create_sql_service = $this->createService('CpCreateSqlService');
        return $create_sql_service->getUserSql($page_info, $search_condition, null, null, null);
    }

    private function getUnfinishedCountFromAllFans($searchQuery, $prev_action_id) {
        $sql = "
            SELECT
                count(u.user_id) as num
            FROM (" . $searchQuery . ") u
            WHERE
                NOT EXISTS(
                    SELECT
                        m.id
                    FROM
                        cp_user_action_messages m
                    WHERE
                        m.cp_action_id = " . $this->action_id . " AND
                        m.cp_user_id = u.cp_user_id AND
                        m.del_flg = 0
                ) AND
                NOT EXISTS(
                    SELECT
                         a.id
                    FROM
                        cp_user_action_statuses a
                    WHERE
                        a.cp_action_id = " . $prev_action_id . " AND
                        a.cp_user_id = u.cp_user_id AND
                        a.status = 1 AND
                        a.del_flg = 0
                )
        ";
        $data = $this->data_builder->getBySQL($sql, []);

        return $data[0]['num'];
    }

    private function getUnfinishedCountFromTargetFans($cp_id, $prev_action_id, $user_ids) {
        $sql = "
            SELECT
                count(u.id) as num
            FROM
                users u
            WHERE
                NOT EXISTS(
                    SELECT
                        a.id
                    FROM
                        cp_user_action_statuses a
                    INNER JOIN
                        cp_users cu
                        ON a.cp_user_id = cu.id AND
                        a.status = 1 AND
                        a.del_flg = 0 AND
                        a.cp_action_id = " . $prev_action_id . "
                    WHERE
                        cu.user_id = u.id AND
                        cu.cp_id = " . $cp_id . " AND
                        cu.del_flg = 0
                )
            AND
               u.id in (" . $user_ids . ")
        ";
        $data = $this->data_builder->getBySQL($sql, []);

        return $data[0]['num'];
    }

    private function sendErrorResponse($msg) {
        $errors['msg'] = $msg;
        $json_data = $this->createAjaxResponse('ng', array(), $errors);
        $this->assign('json_data', $json_data);
    }
}
