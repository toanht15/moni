<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.entities.Cp');


class campaigns extends BrandcoManagerGETActionBase {

    public $NeedManagerLogin = true;
    public $ManagerPageId = Manager::MENU_CAMPAIGNS;

    //マネジャーキャンペン
    const SEARCH_ID = "1";//ID
    const SEARCH_STATUS = "2";//ステータス
    const SEARCH_OPEN_RANGE = "3"; //公開範囲
    const SEARCH_TITLE = "4"; //タイトル
    const SEARCH_BRAND = "5"; //ブランド
    const SEARCH_PUBLIC_DATE = "6"; //公開
    const SEARCH_OPENING_DATE = "7"; //開始
    const SEARCH_CLOSING_DATE = "8"; //終了
    const SEARCH_ANNOUNCE_DATE = "9"; //発表
    const SEARCH_COM = "10"; //.com掲載
    const SEARCH_PARTICIPANT = "11"; //当選者数
    const SEARCH_JOIN = "12"; //参加
    const SEARCH_STEP = "13"; //STEP

    const SEARCH_DEFAULT = "-1";//-
    const SEARCH_PUBLIC = "1";//公開日
    const SEARCH_OPENING = "2";//開始日
    const SEARCH_END = "3";//終了日
    const SEARCH_ANNOUNCE = "4";//発表日

    const STEP_INFO_DEFAULT = "0";//Default
    const STEP_INFO_ALL_USER_COUNT = "1";//全ユーザ数
    const STEP_INFO_ALL_USER_CVR = "2";//全ユーザCVR
    const STEP_INFO_NEW_USER_COUNT = "3";//新規ユーザ数
    const STEP_INFO_NEW_USER_CVR = "4";//新規ユーザCVR
    const STEP_INFO_PC_USER_COUNT = "5";//PCユーザ数
    const STEP_INFO_SP_USER_COUNT = "6";//SPユーザ数
    const STEP_INFO_PC_USER_CVR = "7";//PCユーザCVR
    const STEP_INFO_SP_USER_CVR = "8";//SPユーザCVR

    const SEARCH_TYPE = "0";//All
    const SEARCH_ALL_RANGE = "-1";
    const CSV_CAMPAIGN_URL = "-1";

    const SEARCH_ID_DESC = "1";//ID降順
    const SEARCH_PUBLIC_DESC ="2";//公開降順
    const SEARCH_WINNER_DESC ="3";//当選者数降順

    const CLOSE_FLG_OFF = "0";
    const CLOSE_FLG_ON = "1"; //クローズ

    public static $campaign_status =  [
        Cp::CAMPAIGN_STATUS_DRAFT => "下書き",
        Cp::CAMPAIGN_STATUS_SCHEDULE => "公開予約",
        Cp::CAMPAIGN_STATUS_OPEN => "開催中",
        Cp::CAMPAIGN_STATUS_WAIT_ANNOUNCE => "当選発表待ち",
        Cp::CAMPAIGN_STATUS_CLOSE => "終了",
    ];

    public static $search_checked =  [
        self::SEARCH_ID => "ID",
        self::SEARCH_STATUS => "ステータス",
        self::SEARCH_OPEN_RANGE => "公開範囲",
        self::SEARCH_TITLE => "タイトル",
        self::SEARCH_BRAND => "ブランド",
        self::SEARCH_PUBLIC_DATE => "公開",
        self::SEARCH_OPENING_DATE => "開始",
        self::SEARCH_CLOSING_DATE => "終了",
        self::SEARCH_ANNOUNCE_DATE => "発表",
        self::SEARCH_COM => ".com掲載",
        self::SEARCH_PARTICIPANT => "当選者数",
        self::SEARCH_JOIN => "参加",
        self::SEARCH_STEP => "STEP"
    ];

    public static $select_all =  [
        self::SEARCH_TYPE => "全選択/全解除",
    ];
    public static $brand_status = [
        BrandService::COMPANY => "テストアカウントを除く",
        BrandService::TEST => "テストアカウントを含む"
    ];

    public static $searchByCampaignStatus = [
        self::SEARCH_DEFAULT =>"-",
        self::SEARCH_PUBLIC => "公開日",
        self::SEARCH_OPENING => "開始日",
        self::SEARCH_END => "終了日",
        self::SEARCH_ANNOUNCE => "発表日"
    ];

    public static $download_choice = [
        self::STEP_INFO_DEFAULT => "モジュール名",
        self::STEP_INFO_ALL_USER_COUNT => "全ユーザ数",
        self::STEP_INFO_PC_USER_COUNT => "PCユーザ数",
        self::STEP_INFO_SP_USER_COUNT => "SPユーザ数",
        self::STEP_INFO_NEW_USER_COUNT => "新規ユーザ数",
        self::STEP_INFO_ALL_USER_CVR => "全ユーザCVR",
        self::STEP_INFO_PC_USER_CVR => "PCユーザCVR",
        self::STEP_INFO_SP_USER_CVR => "SPユーザCVR",
        self::STEP_INFO_NEW_USER_CVR => "新規ユーザCVR",
    ];

    public static $searchOpenRange = [
        Cp::CAMPAIGN_STATUS_DEMO => "デモ(ステータス:下書き)",
        Cp::CAMPAIGN_STATUS_CP_PAGE_CLOSED => "クローズ(ステータス:終了)",
        Cp::JOIN_LIMIT_ON => "限定キャンペーン",
        Cp::JOIN_LIMIT_OFF => "公開キャンペーン",
    ];

    public static $searchType = [
        self::SEARCH_TYPE => "All",
        Cp::TYPE_CAMPAIGN => "キャンペーン",
        Cp::TYPE_MESSAGE  => "メッセージ "
    ];

    public static $searchArrangement = [
        self::SEARCH_ID_DESC => "ID降順",
        self::SEARCH_PUBLIC_DESC => "公開降順",
        self::SEARCH_WINNER_DESC => "当選者数降順"
    ];

    protected $Form = array(
        'package' => 'dashboard',
        'action' => 'campaign',
    );

    public function validate() {
        return true;
    }

    function doAction() {
        $cp_action = new CpAction();
        $modules = $cp_action->getAvailableCampaignActions();
        foreach ($modules as $key => $val) {
            $this->Data['modules'][$key] = $val['title'];
            $module[] = $key;
        }
        if (!$this->module) {
            $this->module = $module;
            $this->checkAllModule = array_keys(self::$select_all);
            $this->Data['checkAllModule'] = $this->checkAllModule;
        }
        if (!$this->order) {
            $this->order = 1;
        }
        if(!$this->cp_type){
            $this->cp_type = self::SEARCH_TYPE;
        }
        if ($this->range_type == null) {
            $range_array = self::$searchOpenRange;
            foreach ($range_array as $key => $val) {
                $range_type[] = $key;
            }
            $this->range_type = $range_type;
        }
        $this->Data["type"] = self::$searchType;
        $this->Data['status'] = $this->status;
        $this->Data['range'] = self::$searchOpenRange;
        $this->Data['brand_test'] = $this->brand_test;
        $this->Data['show'] = $this->show;
        $this->Data['module'] = $this->module;
        $this->Data['order'] = $this->order;
        $this->Data['cp_type'] = $this->cp_type;
        $this->Data['range_type'] = $this->range_type;
        $this->Data['brands'] = $this->brands;
        $this->Data['brand_name'] = $this->brand_name;
        $this->Data['cp_status'] = self::$searchByCampaignStatus;
        $this->Data['search_contents'] = self::$search_checked;
        $this->Data["download"] = self::$download_choice;
        $this->Data["arrangment"] = self::$searchArrangement;
        for($step = 1;$step <= 10; $step ++){
            $step_count[self::SEARCH_STEP][] = "STEP".$step;
        }
        $this->Data['search_titles'] = array_replace($this->Data['search_contents'],$step_count);
        $this->Data['brand_test_data'] = self::$brand_status;
        $this->Data['status_data'] = self::$campaign_status;
        $this->Data['select_status_data'] = self::$select_all;
        if (!$this->status) {
            if(is_string($this->cp_id)){
            }else{
                $this->status = array_keys(self::$campaign_status);
                $this->checkAllStatus = array_keys(self::$select_all);
                $this->Data['status'] = $this->status;
                $this->Data['checkAllStatus'] = $this->checkAllStatus;
            }
        }
        if (!$this->show) {
            $this->show = array_keys($this->Data['search_contents']);
            $this->Data['show'] = $this->show;
            $this->checkAllShow = array_keys(self::$select_all);
            $this->Data['checkAllShow'] = $this->checkAllShow;
        }
        if($this->checkAllStatus == self::SEARCH_TYPE) {
            $this->checkAllStatus = array_keys(self::$select_all);
            $this->Data['checkAllStatus'] = $this->checkAllStatus;
        }
        if($this->checkAllShow == self::SEARCH_TYPE) {
            $this->checkAllShow = array_keys(self::$select_all);
            $this->Data['checkAllShow'] = $this->checkAllShow;
        }
        if($this->checkAllModule == self::SEARCH_TYPE) {
            $this->checkAllModule = array_keys(self::$select_all);
            $this->Data['checkAllModule'] = $this->checkAllModule;
        }
        if (!$this->brand_test) {
            $this->brand_test = BrandService::COMPANY;
            $this->Data['brand_test'] = $this->brand_test;
        }
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');
        /** @var BrandService $brand_service */
        $brand_service = $this->createService('BrandService');
        $brands = $brand_service->getAllBrands();
        foreach ($brands as $brand) {
            $this->Data['brand'][] = $brand;
        }
        $array_brand = $this->Data['brand'];
        array_unshift($array_brand, (object)array('id' => 0, 'name' => '-'));
        $this->Data['brand_name'] = array_combine(
            array_map(function ($array_brand) {
                return $array_brand->id;
            }, $array_brand),
            array_map(function ($array_brand) {
                return $array_brand->name;
            }, $array_brand)
        );
        if (!$this->limit || !$this->isNumeric($this->limit)) {
            $this->limit = 20;
        }
        if (!$this->p) {
            $this->p = 1;
        }
        $pager = array(
            'page' => $this->p,
            'count' => $this->limit,
        );
        $condition = array();
        $is_not_opened = false;
        $is_status = false;
        if (in_array(Cp::JOIN_LIMIT_ON, $this->range_type) || in_array(Cp::JOIN_LIMIT_OFF, $this->range_type)) {
            $is_status = true;
            if (in_array(Cp::CAMPAIGN_STATUS_DRAFT, $this->status)) {
                $condition['status'][] = Cp::CAMPAIGN_STATUS_DRAFT;
                $is_not_opened = true;
            }
            if (in_array(Cp::CAMPAIGN_STATUS_SCHEDULE, $this->status)) {
                $condition['status'][] = Cp::CAMPAIGN_STATUS_SCHEDULE;
                $is_not_opened = true;
            }
            if (!in_array(Cp::CAMPAIGN_STATUS_OPEN, $this->status)) {
                $condition['NOT_STATUS_OPEN'] = '__ON__';
            }
            if (!in_array(Cp::CAMPAIGN_STATUS_WAIT_ANNOUNCE, $this->status)) {
                $condition['NOT_STATUS_WAITING_ANNOUNCE'] = '__ON__';
            }
            if (!in_array(Cp::CAMPAIGN_STATUS_CLOSE, $this->status)) {
                $condition['NOT_STATUS_ANNOUNCE'] = '__ON__';
            }
            if (in_array(Cp::JOIN_LIMIT_ON, $this->range_type)) {
                $condition['LIMIT_FLG'] = '__ON__';
                $condition['join_limit'][] = Cp::JOIN_LIMIT_ON;
            }
            if (in_array(Cp::JOIN_LIMIT_OFF, $this->range_type)) {
                $condition['LIMIT_FLG'] = '__ON__';
                $condition['join_limit'][] = Cp::JOIN_LIMIT_OFF;
            }
        } else {
            if (in_array(Cp::CAMPAIGN_STATUS_DEMO, $this->range_type) && in_array(Cp::CAMPAIGN_STATUS_DRAFT, $this->status)) {
                $condition['RANGE'] = '__ON__';
                $condition['range'][] = Cp::STATUS_DEMO;
            }
            if (in_array(Cp::CAMPAIGN_STATUS_CP_PAGE_CLOSED, $this->range_type) && in_array(Cp::CAMPAIGN_STATUS_CLOSE, $this->status)) {
                $condition['RANGE'] = '__ON__';
                $condition['range'][] = Cp::STATUS_CLOSE;
            }
        }
        if (in_array(Cp::CAMPAIGN_STATUS_DEMO, $this->range_type)) {
            $condition['RANGE'] = '__ON__';
            $condition['range'][] = Cp::STATUS_DEMO;
        }
        if (in_array(Cp::CAMPAIGN_STATUS_CP_PAGE_CLOSED, $this->range_type)) {
            $condition['RANGE'] = '__ON__';
            $condition['range'][] = Cp::STATUS_CLOSE;
        }
        if ($condition['LIMIT_FLG'] && $condition['RANGE']) {
            $condition['OR'] = '__ON__';
        }
        if ($this->brand_test == BrandService::COMPANY) {
            $condition['test_page'] = BrandService::COMPANY;
        }
        if ($is_not_opened) {
            $condition['IS_NOT_OPENED'] = '__ON__';
        }
        if ($is_status) {
            $condition['IS_STATUS'] = '__ON__';
        }
        if ($this->cp_type) {
            $condition['cp_type'] = $this->cp_type;
        }
        if ($this->order == 2) {
            $condition['ORDER_PUBLIC'] = '__ON__';
        } elseif ($this->order == 3) {
            $condition['ORDER_WINNER'] = '__ON__';
        } else {
            $condition['ORDER_ID'] = '__ON__';
        }
        if ($this->cp_id != null) {
            $condition['CP'] = '__ON__';
            $condition['cp_id'] = $this->cp_id;
        }

        if ($this->brand_name != null) {
            $condition['BRAND'] = '__ON__';
            $brand = $brand_service->getBrandByBrandName($this->brand_name);
            $condition['brand_id'] = $brand->id;
        }

        if ($this->brands > 0) {
            $condition['BRAND'] = '__ON__';
            $condition['brand_id'] = $this->brands;
        }

        if ($this->module) {
            $condition['MODULE'] = '__ON__';
            $condition['module'] = $this->module;
        }

        if ($this->cp_status > self::SEARCH_DEFAULT) {
            $from_date = date("Y-m-d 00:00:00", strtotime($this->from_date));
            $to_date = date("Y-m-d 23:59:59", strtotime($this->to_date));
            if ($this->cp_status == self::SEARCH_PUBLIC) {
                if ($this->from_date) {
                    $condition['PUBLIC_DATE'] = '__ON__';
                    $condition['public_date_from'] = $from_date;
                }
                if ($this->to_date) {
                    $condition['PUBLIC_DATE'] = '__ON__';
                    $condition['public_date_to'] = $to_date;
                }
            }
            if ($this->cp_status == self::SEARCH_OPENING) {
                if ($this->from_date) {
                    $condition['START_DATE'] = '__ON__';
                    $condition['start_date_from'] = $from_date;
                }
                if ($this->to_date) {
                    $condition['START_DATE'] = '__ON__';
                    $condition['start_date_to'] = $to_date;
                }
            }
            if ($this->cp_status == self::SEARCH_END) {
                if ($this->from_date) {
                    $condition['END_DATE'] = '__ON__';
                    $condition['end_date_from'] = $from_date;
                }
                if ($this->to_date) {
                    $condition['END_DATE'] = '__ON__';
                    $condition['end_date_to'] = $to_date;
                }
            }
            if ($this->cp_status == self::SEARCH_ANNOUNCE) {
                if ($this->from_date) {
                    $condition['ANNOUNCE_DATE'] = '__ON__';
                    $condition['announce_date_from'] = $from_date;
                }
                if ($this->to_date) {
                    $condition['ANNOUNCE_DATE'] = '__ON__';
                    $condition['announce_date_to'] = $to_date;
                }
            }
        }
        if ($this->winner_count_from) {
            $condition['WINNER_COUNT'] = '__ON__';
            $condition['winner_count_from'] = $this->winner_count_from;
        }
        if ($this->winner_count_to) {
            $condition['WINNER_COUNT'] = '__ON__';
            $condition['winner_count_to'] = $this->winner_count_to;
        }

        $db = new aafwDataBuilder();
        $cps = $db->getCpsSearch($condition, null, $pager, true, 'Cp');
        $this->Data['allCpsCount'] = $cps['pager']['count'];

        foreach ($cps['list'] as $cp) {
            $cp->campaign_status = $cp->getStatus();
            $cp->title = $cp->getTitle();
            $cp = $cp->toArray();
            $cp['actions'] = $cp_flow_service->getCpActionsByCpId($cp['id']);
            $action = $cp_flow_service->getFirstActionOfCp($cp['id']);
            $cp_member_count = $action->getMemberCount();
            $status = $cp['campaign_status'];
            $cp['join_count'] = $cp_member_count['finish_action'];
            switch ($status) {
                case Cp::CAMPAIGN_STATUS_SCHEDULE:
                    $cp['campaign_status'] = "公開予約";
                    if ($cp['join_limit_flg'] == Cp::JOIN_LIMIT_ON) {
                        $cp['open_range'] = "限定キャンペーン";
                    } else {
                        $cp['open_range'] = "公開キャンペーン";
                    }
                    break;
                case Cp::CAMPAIGN_STATUS_OPEN:
                    $cp['campaign_status'] = "開催中";
                    if ($cp['join_limit_flg'] == Cp::JOIN_LIMIT_ON) {
                        $cp['open_range'] = "限定キャンペーン";
                    } else {
                        $cp['open_range'] = "公開キャンペーン";
                    }
                    break;
                case Cp::CAMPAIGN_STATUS_WAIT_ANNOUNCE:
                    $cp['campaign_status'] = "当選発表待ち";
                    if ($cp['join_limit_flg'] == Cp::JOIN_LIMIT_ON) {
                        $cp['open_range'] = "限定キャンペーン";
                    } else {
                        $cp['open_range'] = "公開キャンペーン";
                    }
                    break;
                case Cp::CAMPAIGN_STATUS_CLOSE:
                    $cp['campaign_status'] = "終了";
                    if ($cp['join_limit_flg'] == Cp::JOIN_LIMIT_ON) {
                        $cp['open_range'] = "限定キャンペーン";
                    } else {
                        $cp['open_range'] = "公開キャンペーン";
                    }
                    break;
                case Cp::CAMPAIGN_STATUS_DEMO:
                    $cp['campaign_status'] = "下書き";
                    $cp['open_range'] = "デモ";
                    break;
                case Cp::CAMPAIGN_STATUS_CP_PAGE_CLOSED;
                    $cp['campaign_status'] = '終了';
                    $cp['open_range'] = "クローズ";
                    break;
                default:
                    $cp['campaign_status'] = "下書き";
                    if ($cp['join_limit_flg'] == Cp::JOIN_LIMIT_ON) {
                        $cp['open_range'] = "限定キャンペーン";
                    } else {
                        $cp['open_range'] = "公開キャンペーン";
                    }
            }
            $this->Data['cps'][] = $cp;
        }

        // ページング$cp['campaign_status']
        $total_page = floor($this->Data['allCpsCount'] / $this->limit) + ($this->Data['allCpsCount'] % $this->limit > 0);
        $this->p = Util::getCorrectPaging($this->p, $total_page);
        $this->Data['limit'] = $this->limit;

        return 'manager/dashboard/campaigns.php';
    }

    /**
     * @param $cp_action_id
     * @return array
     */
    public function getEngagementLogStatusCount($cp_action_id) {
        /** @var EngagementLogService $engagementLogService */
        $engagementLogService = $this->getService('EngagementLogService');
        /** @var CpUserService $cpUserService */
        $cpUserService = $this->getService('CpUserService');

        $ret['unread'] = $engagementLogService->getEngagementLogCountByCpActionIdAndStatus($cp_action_id, EngagementLog::UNREAD_FLG);
        $ret['liked'] = $engagementLogService->getEngagementLogCountByCpActionIdAndStatus($cp_action_id, EngagementLog::LIKED_FLG);
        $ret['prev_liked'] = $engagementLogService->getEngagementLogCountByCpActionIdAndStatus($cp_action_id, EngagementLog::PREV_LIKED_FLG);
        $ret['skip_like'] = $engagementLogService->getEngagementLogCountByCpActionIdAndStatus($cp_action_id, EngagementLog::SKIP_FLG);
        $ret['action_read_count'] = $cpUserService->getSendMessageCount($cp_action_id);
        $ret['like_rate'] = $ret['liked'] * 100 / ($ret['action_read_count'] - $ret['prev_liked'] - $ret['unread']);
        return $ret;
    }
}