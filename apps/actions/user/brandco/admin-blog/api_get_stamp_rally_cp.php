<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.StaticHtmlStampRallyService');
AAFW::import('jp.aainc.aafw.parsers.PHPParser');

class api_get_stamp_rally_cp extends BrandcoGETActionBase {

    protected $ContainerName = 'api_get_stamp_rally_cp';

    public $NeedOption = array();
    protected $AllowContent = array('JSON');

    public function validate() {
        return true;
    }

    function doAction() {

        $condition = $this->getCondition();
        $pager = $this->getPager();
        $order = $this->getOrder();

        /** @var StaticHtmlStampRallyService $staticHtmlStampRallyService */
        $staticHtmlStampRallyService = $this->createService('StaticHtmlStampRallyService');
        $cpList = $staticHtmlStampRallyService->getCpList($condition,$order,$pager);

        $parser = new PHPParser();

        $html = Util::sanitizeOutput($parser->parseTemplate(
            'StampRallyCpSetting.php', array(
                'cp_list' => $cpList,
                'pager' => $pager,
                'search_condition'  => $condition,
                'orders'  => $order,
                'cp_status_joined_image' => $this->cp_status_joined_image,
                'cp_status_finished_image' => $this->cp_status_finished_image
            )
        ));

        $json_data = $this->createAjaxResponse("ok", array(), array(), $html);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    private function getPager(){

        if (!$this->limit || !in_array($this->limit, StaticHtmlStampRallyService::$display_items_range)) {
            $limit = StaticHtmlStampRallyService::DISPLAY_20_ITEMS;
        }else{
            $limit = $this->limit;
        }

        $pager = array(
            'page'  => $this->page ? $this->page : '1',
            'count' => $limit
        );

        return $pager;
    }

    private function getOrder(){

        if($this->orders){

            $orders = array(
                'name' => $this->orders['name'],
                'direction' => $this->orders['direction'] == StaticHtmlStampRallyService::ORDER_ASC ? 'ASC' : 'DESC',
            );

        }else{
            $orders = array(
                'name' => 'id',
                'direction' => 'DESC',
            );
        }

        return $orders;
    }

    private function getCondition(){

        $conditions = array();

        $conditions['brand_id'] = $this->getBrand()->id;

        if(!$this->search_conditions){
            $conditions['BY_STATUS'] = '__ON__';
            $conditions['statuses'] = array(Cp::STATUS_DEMO, CP::STATUS_DRAFT, CP::STATUS_SCHEDULE);
        }else{

            if($this->search_conditions['get_select_cp']){
                $conditions['BY_ID'] = '__ON__';
                $conditions['ids'] = $this->search_conditions['select_cp'] ? $this->search_conditions['select_cp'] : array();
            }

            if($this->search_conditions['status']){
                $conditions['BY_STATUS'] = '__ON__';
                $conditions['statuses'] = $this->search_conditions['status'];
            }

            if(!$conditions['statuses'] && !$conditions['ids']){
                $conditions['BY_STATUS'] = '__ON__';
                $conditions['statuses'] = array(Cp::STATUS_DEMO, CP::STATUS_DRAFT, CP::STATUS_SCHEDULE);
            }
        }

        return $conditions;
    }
}