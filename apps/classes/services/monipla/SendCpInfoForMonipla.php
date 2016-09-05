<?php
AAFW::import('jp.aainc.classes.services.monipla.UpdateCpInfoBase');

class SendCpInfoForMonipla extends UpdateCpInfoBase {
    const STATUS_ENTRY = 1;
    const STATUS_JOIN_FINISH = 2;
    const STATUS_ANNOUNCE = 3;

    /**
     * @param $cp_user_id
     * @param $cp_action_id
     * @param $app_id
     * @param $module_type
     * @param int $is_elected
     */
    public function sendCpUserStatus($cp_user_id, $cp_action_id, $app_id, $module_type, $is_elected = 0) {
        try {
            $method = 'POST';
            $url = $this->getApiUrl($module_type);
            $params = $this->getCommonParams($cp_action_id, $module_type, $app_id);

            $cp_user = $this->cp_user_service->getCpUserById($cp_user_id);
            $user = $this->user_service->getUserByBrandcoUserId($cp_user->user_id);
            $params['platform_user_id'] = $user->monipla_user_id;

            $json_result = $this->request($method, $url, $params);
            $res = json_decode($json_result);

            if ($res->data->status != 'success') {
                throw new Exception('API connection failed | ' . $json_result, 500);
            }
        } catch (Exception $e) {
            $this->logger->error('#sendCpUserStatus error ' . $e );
            $this->saveResendLog($params['platform_user_id'], $app_id, $params['campaign_id'], $module_type, $e);
        }
    }

    /**
     * @param $cp_action_id
     * @param $module_type
     * @param $app_id
     * @return array
     * @throws Exception
     */
    protected function getCommonParams($cp_action_id, $module_type, $app_id) {
        try {
            $cp_action = $this->cp_flow_service->getCpActionById($cp_action_id);
            if (!$cp_action) {
                throw new Exception('Invalid CpAction');
            }

            if ($app_id == ApplicationService::BRANDCO) {
                $service_name = ApplicationService::CLIENT_ID_BRANDCO;
            } elseif ($app_id == ApplicationService::MONIPLA) {
                $service_name = ApplicationService::CLIENT_ID_COMCAMPAIGN;
            } else {
                $service_name = ApplicationService::CLIENT_ID_BRANDCO;
            }

            if ($module_type == CpAction::TYPE_ENTRY || $module_type == CpAction::TYPE_QUESTIONNAIRE) {
                $status = self::STATUS_ENTRY;
            } elseif ($module_type == CpAction::TYPE_JOIN_FINISH || $module_type == CpAction::TYPE_INSTANT_WIN) {
                $status = self::STATUS_JOIN_FINISH;
            } elseif ($module_type == CpAction::TYPE_ANNOUNCE) {
                $status = self::STATUS_ANNOUNCE;
            } else {
                throw new Exception('Illegal Module Type | cp_action_id = ' . $cp_action_id);
            }

            $params = array(
                'service_name' => $service_name,
                'campaign_id' => $cp_action->getCp()->id,
                'status' => $status
            );

            return $params;
        } catch (Exception $e) {
            throw new Exception('SendCpInfoForMonipla#setParams error | ' . $e->getMessage());
        }
    }

    /**
     * @param $module_type
     */
    protected function isLegalCpAction($module_type) {}

    /**
     * @param null $module_type
     * @return string
     */
    protected function getApiUrl($module_type) {
        return 'http://' . config('Domain.old_monipla_api') . '/private_api/api_update_platform_campaign_users.php';
    }
}