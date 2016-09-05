<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class panel_clicks extends BrandcoManagerGETActionBase {

    public $NeedManagerLogin = true;
    private $limit = 30;
    private $order;
    private $pager;

    public function beforeValidate() {
        $this->search_mode = !$this->search_mode ? 'all' : $this->search_mode;
        $this->p = !$this->p ? '1' : $this->p;
    }

	public function validate () {
		return true;
	}

    function doAction() {

        $db = new aafwDataBuilder();
        $this->buildBaseFilter();

        $user_panel_click_store = $this->getModel('UserPanelClicks');

        if ($this->search_mode == 'date' && !$this->isDate($this->date)) return 'redirect: ' . Util::rewriteUrl('dashboard', 'panel_clicks', array(), array(), '', true);

        if ($this->search_mode == 'all') {
            $user_panel_clicks = $db->getUserPanelClicks(array(), $this->order, $this->pager, true, 'UserPanelClick');
            $this->Data['row_count'] = $user_panel_click_store->count(array());
        }else{
            $conditions = array(
                'start_date' => date("Y-m-d 00:00:00", strtotime($this->date)),
                'end_date' => date("Y-m-d 23:59:59", strtotime($this->date))
            );
            $user_panel_clicks = $db->getUserPanelClicks($conditions, $this->order, $this->pager, true, 'UserPanelClick');

            $conditions = array(
                'created_at:>' => date("Y-m-d 00:00:00", strtotime($this->date)),
                'created_at:<=' => date("Y-m-d 23:59:59", strtotime($this->date))
            );
            $this->Data['row_count'] = $user_panel_click_store->count($conditions);
        }

        $this->assign('user_panel_clicks', $user_panel_clicks['list']);

        // ページング
        $this->Data['total_count'] = $user_panel_clicks['pager']['count'];
        $total_page = floor ( $this->Data['total_count'] / $this->limit ) + ( $this->Data['total_count'] % $this->limit > 0 );
        $this->p = Util::getCorrectPaging($this->p, $total_page);
        $this->Data['limit'] = $this->limit;

        $this->Data['ActionForm']['date'] = $this->date;
        $this->Data['ActionForm']['search_mode'] = $this->search_mode;

        return 'manager/dashboard/panel_clicks.php';
    }

    private function buildBaseFilter() {
        $this->order = array(
            'name' => 'access_count',
            'direction' => 'desc'
        );

        $this->pager = array(
            'count' => $this->limit,
            'page' => $this->p
        );
    }
}
