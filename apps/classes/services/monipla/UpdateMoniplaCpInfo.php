<?php
AAFW::import('jp.aainc.classes.services.monipla.UpdateCpInfoBase');

class UpdateMoniplaCpInfo extends UpdateCpInfoBase {

    const PROVIDER_TYPE_MONIPLA = 2;
    const PROVIDER_TYPE_BRANDCO = 3;

    private $api_endpoint_url = array(
        CpAction::TYPE_JOIN_FINISH  => '/v1/join_history',
        CpAction::TYPE_INSTANT_WIN  => '/v1/campaign_progress',
        CpAction::TYPE_ANNOUNCE     => '/v1/elected_campaign'
    );

    /**
     * @param $user_ids
     * @param $cp_action_id
     * @param $app_id
     * @param $module_type
     * @throws Exception
     */
    public function sendCpUserAnnounceStatus($user_ids, $cp_action_id, $app_id, $module_type) {
        try {
            if ($module_type != CpAction::TYPE_ANNOUNCE) {
                throw new Exception('Illegal Module Type | cp_action_id = ' . $cp_action_id);
            }

            $method = 'POST';
            $url = $this->getApiUrl($module_type);
            $params = $this->getCommonParams($cp_action_id, $module_type);

            $allied_ids = null;
            $users = $this->user_service->getUserByBrandcoUserIds($user_ids);
            foreach ($users as $user) {
                $allied_ids[] = $user->monipla_user_id;
            }

            if (!$allied_ids) {
                throw new Exception('Invalid User Id');
            }

            $params['allied_ids'] = implode(',', $allied_ids);
            $json_result = $this->request($method, $url, $params);
            $res = json_decode($json_result);

            if ($res->error) {
                throw new Exception('Api connection failed | ' . $json_result, $res->error->status);
            }
        } catch (Exception $e) {
            $this->saveResendLog(0, $app_id, $params['provider_campaign_key'], $module_type, $e);

            $this->logger->error('#sendCpUserAnnounceStatus error ' . $e);
            throw $e;
        }
    }

    /**
     * @param $cp_user_id
     * @param $cp_action_id
     * @param $app_id
     * @param $module_type
     * @param int $is_elected
     */
    public function sendCpUserStatus($cp_user_id, $cp_action_id, $app_id, $module_type, $is_elected = 0) {
        try {
            if (!$this->isLegalCpAction($module_type)) {
                throw new Exception('Illegal Module Type | cp_action_id = ' . $cp_action_id);
            }

            $method = 'POST';
            $url = $this->getApiUrl($module_type);
            $params = $this->getCommonParams($cp_action_id, $module_type);

            $cp_user = $this->cp_user_service->getCpUserById($cp_user_id);
            $user = $this->user_service->getUserByBrandcoUserId($cp_user->user_id);
            $params['allied_id'] = $user->monipla_user_id;

            if ($module_type == CpAction::TYPE_INSTANT_WIN) {
                $params['is_elected'] = $is_elected;
            }

            $json_result = $this->request($method, $url, $params);
            $res = json_decode($json_result);

            if ($res->error) {
                throw new Exception('Api connection failed | ' . $json_result, $res->error->status);
            }
        } catch (Exception $e) {
            $this->logger->error('#sendCpUserStatus error ' . $e);
            $this->saveResendLog($params['allied_id'], $app_id, $params['provider_campaign_key'], $module_type, $e);
        }
    }

    /**
     * @param $cp_action_id
     * @param $module_type
     * @param $app_id
     * @return array
     * @throws Exception
     */
    protected function getCommonParams($cp_action_id, $module_type, $app_id = null) {
        try {
            $cp_action = $this->cp_flow_service->getCpActionById($cp_action_id);
            if (!$cp_action) {
                throw new Exception('Invalid CpAction');
            }

            $params = array(
                'provider_id' => self::PROVIDER_TYPE_BRANDCO,
                'provider_campaign_key' => $cp_action->getCp()->id
            );

            return $params;
        } catch (Exception $e) {
            throw new Exception('UpdateMoniplaCpInfo#setParams error');
        }
    }

    /**
     * @param $module_type
     * @return bool
     */
    protected function isLegalCpAction($module_type) {
        $legal_cp_actions = array(
            CpAction::TYPE_JOIN_FINISH,
            CpAction::TYPE_INSTANT_WIN
        );

        return in_array($module_type, $legal_cp_actions);
    }

    /**
     * @param $module_type
     * @return string
     */
    protected function getApiUrl($module_type) {
        return 'http://' . config('Domain.media_api') . $this->api_endpoint_url[$module_type];
    }
}