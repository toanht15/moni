<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class brand_mgr extends BrandcoManagerGETActionBase {

    const DEFAULT_TITLE = "キャンペーン告知";

    public $NeedManagerLogin = true;

    public function validate() {
        return true;
    }

    public function doAction() {
        if (!$this->limit || !$this->isNumeric($this->limit)) {
            $this->limit = 50;
        }
        if (!$this->p) {
            $this->p = 1;
        }
        $pager = array(
            'page' => $this->p,
            'count' => $this->limit,
            'offset' => ($this->p - 1) * $this->limit,
        );
        $db = aafwDataBuilder::newBuilder();

        $condition = $this->getSearchCondition();
        $brand_list = $db->getBrandListByManager($condition, null, $pager, true, 'Brand');
        $brands = $brand_list['list'];

        $this->Data['sales'] = $this->sales;
        $this->Data['consultant'] = $this->consultant;

        $this->Data['allBrandCount'] = $brand_list['pager']['count'];
        $total_page = floor($this->Data['allBrandCount'] / $this->limit) + ($this->Data['allBrandCount'] % $this->limit > 0);
        $this->p = Util::getCorrectPaging($this->p, $total_page);
        $this->Data['limit'] = $this->limit;

        $current_month = (integer)date('m');
        $last_year = (integer)date('Y') - 1;

        $last_month = $current_month - 1 == 0 ? 12 : ($current_month - 1);
        if ($current_month - 2 <= 0) {
            $two_month_ago = $current_month - 2 == 0 ? 12 : 11;
        } else {
            $two_month_ago = $current_month - 2;
        }
        $last_month_is_current_year = $last_month == 12 ? false : true;
        $two_month_ago_is_current_year = $two_month_ago >= 11 ? false : true;

        $this->Data['brand_list'] = array();
        /** @var BrandsUsersRelationService $brands_users_relation_service */
        $brands_users_relation_service = $this->createService('BrandsUsersRelationService');

        foreach ($brands as $brand) {
            $params = array(
                'brand_id' => $brand->id
            );
            $last_login = $db->getAdminLastLoginFromClientByBrandId($params);
            $last_login_date = $last_login[0]['last_login_date'] ?: '0000-00-00 00:00:00';

            $brand_info = array();

            $brand_info['brand_id'] = $brand->id;
            $brand_info['brand_name'] = $brand->name;
            $brand_info['directory_name'] = $brand->directory_name;
            $brand_info['last_login'] = $brands_users_relation_service->getLastLoginSummary($last_login_date);

            $brand_info['has_option'] = $brand->hasOption(BrandOptions::OPTION_TOP);
            $brand_info['brand_url'] = $brand->getUrl();

            //２ヶ月まで公開中キャンペーン数
            $brand_info['last_month_open_cp_count'] = $this->getPublicCpCountByBrandIdAndMonth($last_month, $brand->id, $last_month_is_current_year);
            $brand_info['two_month_ago_open_cp_count'] = $this->getPublicCpCountByBrandIdAndMonth($two_month_ago, $brand->id, $two_month_ago_is_current_year);

            //今月の公開中キャンペーン数
            $brand_info['current_month_open_cp_count'] = $this->getPublicCpCountByBrandIdAndMonth($current_month, $brand->id);

            //今月の下書きキャンペーン数
            $brand_info['current_month_draft_cp_count'] = $this->getCurrentMonthCountDraftCpsByBrandId($brand->id);

            // 今月のキャンペーン状態
            list($count_cp_schedule, $count_cp_demo, $count_cp_open, $count_cp_wait_announce, $count_cp_close, $count_cp_page_closed) = $this->getCpCountByStatus($brand->id);
            $brand_info['cp_status_schedule'] = $count_cp_schedule;
            $brand_info['cp_status_demo'] = $count_cp_demo;
            $brand_info['cp_status_open'] = $count_cp_open;
            $brand_info['cp_status_wait_announce'] = $count_cp_wait_announce;
            $brand_info['cp_status_close'] = $count_cp_close;
            $brand_info['cp_status_cp_page_close'] = $count_cp_page_closed;

            $this->Data['brand_list'][] = $brand_info;
        }

        $this->Data['current_month'] = $current_month . "月";
        $this->Data['last_month'] = $last_month == 12 ? $last_year . "/" . $last_month . "月" : $last_month . "月";
        $this->Data['two_month_ago'] = $two_month_ago >= 11 ? $last_year . "/" . $two_month_ago . "月" : $two_month_ago . "月";

        /** @var ManagerService $manager_service */
        $manager_service = $this->getService('ManagerService');
        $managers = $manager_service->getManagers('mail_address');
        $manager_list = array(0 => '指定なし');
        foreach ($managers as $manager) {
            $manager_list[$manager->id] = $manager->name;
        }
        $this->Data['select_list_manager'] = $manager_list;

        return 'manager/dashboard/brand_mgr.php';
    }

    /**
     * @return array
     */
    private function getSearchCondition() {
        $condition = array();

        // デフォルトで自分を運用担当にセット
        if (is_null($this->sales) && is_null($this->consultant)) {
            $this->consultant = $this->managerAccount->id;
        }

        if ($this->sales) {
            $condition['SALES'] = '__ON__';
            $condition['sales_manager_id'] = $this->sales;
        }
        if ($this->consultant) {
            $condition['CONSULTANT'] = '__ON__';
            $condition['consultants_manager_id'] = $this->consultant;
        }

        return $condition;
    }

    /**
     * @param $month
     * @param $brand_id
     * @param bool $current_year
     * @return mixed
     */
    private function getPublicCpCountByBrandIdAndMonth($month, $brand_id, $current_year = true) {
        $current_year = $current_year ? date("Y") : (date("Y") - 1);
        $start_date = date("{$current_year}-{$month}-01 00:00:00", strtotime("{$current_year}/{$month}/01"));
        $end_date = date("{$current_year}-{$month}-t 23:59:59", strtotime("{$current_year}/{$month}/01"));

        $params = array(
            'status' => Cp::STATUS_FIX,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'brand_id' => $brand_id
        );

        $db = aafwDataBuilder::newBuilder();
        $cps_count = $db->getCountPublishedCpsByBrandIdAndPeriod($params);

        return $cps_count[0]['COUNT(*)'];
    }

    /**
     * @param $brand_id
     * @return array
     */
    private function getCpCountByStatus($brand_id) {
        $current_date_time = date('Y-m-d H:i:s');

        $cp_store = aafwEntityStoreFactory::create('Cps');
        $cps = $cp_store->find(array("brand_id" => $brand_id));

        $count_cp_schedule = 0;
        $count_cp_demo = 0;
        $count_cp_open = 0;
        $count_cp_wait_announce = 0;
        $count_cp_close = 0;
        $count_cp_page_closed = 0;

        foreach ($cps as $cp) {
            if ($cp->status == Cp::STATUS_SCHEDULE) {
                $count_cp_schedule++;
            }
            if ($cp->status == Cp::STATUS_CLOSE) {
                $count_cp_page_closed++;
            }
            if ($cp->status == Cp::STATUS_DEMO) {
                $count_cp_demo++;
            }
            if ($cp->status == Cp::STATUS_FIX && ($cp->end_date >= $current_date_time || $cp->permanent_flg == Cp::PERMANENT_FLG_ON)) {
                $count_cp_open++;
            }
            if ($cp->status == Cp::STATUS_FIX && $cp->end_date <= $current_date_time && $cp->announce_date >= $current_date_time) {
                $count_cp_wait_announce++;
            }

            if ($cp->status == Cp::STATUS_FIX && $cp->announce_date <= $current_date_time) {
                $count_cp_close++;
            }
        }

        return array($count_cp_schedule, $count_cp_demo, $count_cp_open, $count_cp_wait_announce, $count_cp_close, $count_cp_page_closed);
    }

    /**
     * @param $brand_id
     * @return int
     */
    private function getCurrentMonthCountDraftCpsByBrandId($brand_id){
        $current_date_time = date('Y-m-d H:i:s');
        $current_month_start_date = date("Y-m-01 00:00:00");

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        $draft_cps = $cp_flow_service->getCpsByBrandIdAndUpdatedPeriod($brand_id, $current_month_start_date, $current_date_time, Cp::STATUS_DRAFT);

        $count = 0;
        foreach ($draft_cps as $cp){
            $first_cp_action = $cp_flow_service->getFirstActionOfCp($cp->id);
            $cp_title = $first_cp_action->getCpActionData()->title;

            if($cp_title != self::DEFAULT_TITLE){
                $count++;
            }
        }

        return $count;
    }

}