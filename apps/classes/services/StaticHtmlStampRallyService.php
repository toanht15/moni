<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class StaticHtmlStampRallyService extends aafwServiceBase {

    private $cps;
    private $stampRallyCampaigns;

    public function __construct(){
        $this->cps = $this->getModel('Cps');
        $this->stampRallyCampaigns = $this->getModel('StaticHtmlStampRallyCampaigns');
    }

    const DISPLAY_10_ITEMS = 10;
    const DISPLAY_20_ITEMS = 20;
    const DISPLAY_50_ITEMS = 50;

    public static $display_items_range = array(self::DISPLAY_10_ITEMS, self::DISPLAY_20_ITEMS, self::DISPLAY_50_ITEMS);

    public static $cp_status_labels = array(
        Cp::CAMPAIGN_STATUS_WAIT_ANNOUNCE  => array('class' => 'label1', 'label' => '当選発表待ち'),
        Cp::CAMPAIGN_STATUS_CLOSE => array('class' => 'label2', 'label' => '終了'),
        Cp::CAMPAIGN_STATUS_OPEN  => array('class' => 'label3', 'label' => '開催中'),
        Cp::CAMPAIGN_STATUS_SCHEDULE  => array('class' => 'label4', 'label' => '公開予約'),
        Cp::CAMPAIGN_STATUS_DRAFT  => array('class' => 'label5', 'label' => '下書き'),
        Cp::CAMPAIGN_STATUS_DEMO  => array('class' => 'label6', 'label' => 'デモ公開中')
    );

    const SEARCH_BY_SELECTED_CP = 1;
    const SEARCH_BY_CP_STATUS = 2;
    const SEARCH_BY_CP_OPEN_DATE = 3;
    const SEARCH_BY_CP_FINISH_DATE = 4;

    const ORDER_ASC  = 1;
    const ORDER_DESC = 2;

    const STAMP_STATUS_CLOSED = 1;
    const STAMP_STATUS_FINISHED = 2;
    const STAMP_STATUS_JOINED = 3;
    const STAMP_STATUS_OPENING = 4;
    const STAMP_STATUS_COMING_SOON = 5;

    public static $stamp_status_classes = array(
        self::STAMP_STATUS_CLOSED => 'stampStatusClosed',
        self::STAMP_STATUS_FINISHED => 'stampStatusFinished',
        self::STAMP_STATUS_JOINED => 'stampStatusJoined',
        self::STAMP_STATUS_OPENING => '',
        self::STAMP_STATUS_COMING_SOON => 'stampStatusComingsoon'
    );


    public function getCpList($condition,$orders, $pager) {

        $db = aafwDataBuilder::newBuilder();
        $cp_list = $db->getStampRallyCps($condition, $orders, $pager, true, 'Cp');

        return $cp_list;
    }

    public function convertDate($sourceDate){

        if ($sourceDate == '0000-00-00 00:00:00') {
            return '-月-日';
        }

        return Util::getFormatDateTimeString($sourceDate);
    }

    public function deleteStampRallyCampaignByCpId($cpId){
        $stampRallyCps = $this->stampRallyCampaigns->find(
            array(
                'campaign_id' => $cpId
            )
        );
        foreach($stampRallyCps as $stampRallyCp){
            $this->stampRallyCampaigns->delete($stampRallyCp);
        }
    }

    public function getStampRallyCampaignsByBrandId($brand_id, $start_date, $end_date) {
        if (!$brand_id) {
            return null;
        }

        $db = aafwDataBuilder::newBuilder();
        $condition = array(
            "brand_id" => $brand_id,
            "start_date" => $start_date,
            "end_date" => $end_date
        );

        return $db->getStampRallyCampaignsByBrandId($condition);
    }
}
