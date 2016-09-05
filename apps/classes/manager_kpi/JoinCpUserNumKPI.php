<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.entities.Cp');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.entities.CpUserActionStatus');

class JoinCpUserNumKPI implements IManagerKPI {

    function doExecute() {
        // メンテDBから取得
        $db = aafwDataBuilder::newBuilder('maintedb');
        list($date, $brandId) = func_get_args();
        $logger = aafwLog4phpLogger::getDefaultLogger();

        $cp_user_num = 0;
        $count_filter = array(
            'updated_at_start' => date('Y-m-d', strtotime($date)),
            'updated_at_end'   => date('Y-m-d', strtotime($date . '+1 day')),
        );

        $cur_page = 1;
        $limit = 100;

        $ca_filter = array('action_type' => CpAction::$legal_opening_cp_actions);
        $pager = array('page' => $cur_page, 'count' => $limit);
        if ($brandId) {
            $ca_filter['brand_id'] = $brandId;
        }

        while (true) {
            try {
                $ca_actions = $db->getOpeningCpActionId($ca_filter, null, $pager, false);
                if (Util::isNullOrEmpty($ca_actions)) {
                    break;
                }

                $cp_action_ids = $this->fetchCpActionIds($ca_actions);
                if (count($cp_action_ids) === 0) {
                    break;
                }

                $count_filter['ca_ids'] = $cp_action_ids;
                list($result) = $db->getJoinCpUserNumSumKPI($count_filter);
                $cp_user_num += $result['numbers'];
            } catch (Exception $e) {
                $logger->error("Error: JoinCpUserNumKPI#doExecute");
                $logger->error($e);
            }

            $pager['page'] += 1;
        }

        return $cp_user_num;
    }

    /**
     * @param $cp_actions
     * @return array
     */
    public function fetchCpActionIds($cp_actions) {
        $cp_action_ids = array();

        foreach ($cp_actions as $cp_action) {
            $cp_action_ids[] = $cp_action['id'];
        }

        return $cp_action_ids;
    }
}