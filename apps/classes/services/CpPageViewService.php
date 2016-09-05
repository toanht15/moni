<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

/**
 * Class CpPageViewService
 */
class CpPageViewService extends aafwServiceBase {
    protected $cp_page_view_store;

    public function __construct() {
        $this->cp_page_view_store = $this->getModel("CpPageViews");
    }

    /**
     * @return mixed
     */
    public function createEmptyObject() {
        return $this->cp_page_view_store->createEmptyObject();
    }

    /**
     * @param $cp_id
     * @param $date
     * @param $type
     * @param $total_view_count
     * @param $pc_view_count
     * @param $sp_view_count
     * @param $tablet_view_count
     * @param $user_count
     * @param $status
     */
    public function updateCpPageView($cp_id, $date, $type, $total_view_count, $pc_view_count, $sp_view_count, $tablet_view_count, $user_count, $status) {
        $cp_page_view = $this->getCpPageViewByCpIdAndDate($cp_id, $date, $type);
        if(!$cp_page_view) {
            $cp_page_view = $this->createEmptyObject();
        }

        $cp_page_view->cp_id = $cp_id;
        $cp_page_view->summed_date = $date;
        $cp_page_view->type = $type;
        $cp_page_view->total_view_count = $total_view_count;
        $cp_page_view->pc_view_count = $pc_view_count;
        $cp_page_view->sp_view_count = $sp_view_count;
        $cp_page_view->tablet_view_count = $tablet_view_count;
        $cp_page_view->user_count = $user_count;
        $cp_page_view->status = $status;

        $this->cp_page_view_store->save($cp_page_view);
    }

    /**
     * @param $cp_id
     * @param $date
     * @param $type
     * @return mixed
     */
    public function getCpPageViewByCpIdAndDate($cp_id, $date, $type) {
        $filter = array(
            "cp_id"         => $cp_id,
            "summed_date"   => $date,
            "type"          => $type
        );

        return $this->cp_page_view_store->findOne($filter);
    }
}