<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.lib.parsers.CSVParser');
AAFW::import('jp.aainc.actions.manager.dashboard.campaigns');

class csv_campaign_list extends BrandcoManagerGETActionBase {
    //hiddenで0を取れない対応
    const JOIN_LIMIT_OFF = "";
    protected $ContainerName = 'csv_campaign_list';

    public $NeedAdminLogin = true;
    public function doThisFirst() {
        ini_set('max_execution_time', 3600);
    }

    public function validate() {
        return true;
    }

    function doAction() {
        //hiddenで0を取れない対応
        if (in_array(self::JOIN_LIMIT_OFF, $this->range_type)) {
            $range = $this->range_type;
            array_push($range, "0");
            $this->range_type = $range;
        }
        //header　
        $data_csv = array();
        $data_csv[] = 'キャンペーンURL';
        if (in_array(campaigns::SEARCH_ID, $this->show)) {
            $data_csv[] = 'ID';
        }
        if (in_array(campaigns::SEARCH_STATUS, $this->show)) {
            $data_csv[] = 'ステータス';
        }
        if (in_array(campaigns::SEARCH_OPEN_RANGE, $this->show)) {
            $data_csv[] = '公開範囲';
        }
        if (in_array(campaigns::SEARCH_TITLE, $this->show)) {
            $data_csv[] = 'タイトル';
        }
        if (in_array(campaigns::SEARCH_BRAND, $this->show)) {
            $data_csv[] = 'ブランドID';
            $data_csv[] = 'ブランド';
        }
        if (in_array(campaigns::SEARCH_PUBLIC_DATE, $this->show)) {
            $data_csv[] = '公開（日時）';
            $data_csv[] = '公開（時間）';
        }
        if (in_array(campaigns::SEARCH_OPENING_DATE, $this->show)) {
            $data_csv[] = '開始（日時）';
            $data_csv[] = '開始（時間）';
        }
        if (in_array(campaigns::SEARCH_CLOSING_DATE, $this->show)) {
            $data_csv[] = '終了（日時）';
            $data_csv[] = '終了（時間）';
        }
        if (in_array(campaigns::SEARCH_ANNOUNCE_DATE, $this->show)) {
            $data_csv[] = '発表（日時）';
            $data_csv[] = '発表（時間）';
        }
        if (in_array(campaigns::SEARCH_COM, $this->show)) {
            $data_csv[] = '.com掲載';
        }
        if (in_array(campaigns::SEARCH_PARTICIPANT, $this->show)) {
            $data_csv[] = '当選者数';
        }
        if (in_array(campaigns::SEARCH_JOIN, $this->show)) {
            $data_csv[] = '参加';
        }
        if (in_array(campaigns::SEARCH_STEP, $this->show)) {
            for ($count = 1; $count <= 10; $count++) {
                $data_csv[] = $cp_action_detail = 'STEP' . $count;
            }
        }
        // Export csv
        $csv = new CSVParser();
        header("Content-type:" . $csv->getContentType());
        header($csv->getDisposition());
        $array_data = $csv->out(array('data' => $data_csv), null, true, true);
        print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');
        /** @var BrandService $brand_service */
        $brand_service = $this->createService('BrandService');
        $condition = array();
        $is_not_opened = false;
        $is_status = false;
        if (in_array(Cp::JOIN_LIMIT_ON, $this->range_type) || in_array(Cp::JOIN_LIMIT_OFF, $this->range_type)) {
            $is_status = true;
        if (in_array(CP::CAMPAIGN_STATUS_DRAFT, $this->status)) {
            $condition['status'][] = CP::CAMPAIGN_STATUS_DRAFT;
            $is_not_opened = true;
        }
        if (in_array(CP::CAMPAIGN_STATUS_SCHEDULE, $this->status)) {
            $condition['status'][] = CP::CAMPAIGN_STATUS_SCHEDULE;
            $is_not_opened = true;
        }
        if (!in_array(CP::CAMPAIGN_STATUS_OPEN, $this->status)) {
            $condition['NOT_STATUS_OPEN'] = '__ON__';
        }
        if (!in_array(CP::CAMPAIGN_STATUS_WAIT_ANNOUNCE, $this->status)) {
            $condition['NOT_STATUS_WAITING_ANNOUNCE'] = '__ON__';
        }
        if (!in_array(CP::CAMPAIGN_STATUS_CLOSE, $this->status)) {
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
        if ($this->brand_test == BrandService::COMPANY) {
            $condition['test_page'] = BrandService::COMPANY;
        }
        if ($condition['LIMIT_FLG'] && $condition['RANGE']) {
            $condition['OR'] = '__ON__';
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

        if ($this->brands > 0) {
            $condition['BRAND'] = '__ON__';
            $condition['brand_id'] = $this->brands;
        }

        if ($this->module ) {
            $condition['MODULE'] = '__ON__';
            $condition['module'] = $this->module;
        }
        if ($this->cp_status > campaigns::SEARCH_DEFAULT) {
            $from_date = date("Y-m-d 00:00:00", strtotime($this->from_date));
            $to_date = date("Y-m-d 23:59:59", strtotime($this->to_date));
            if ($this->cp_status == campaigns::SEARCH_PUBLIC) {
                if ($this->from_date) {
                    $condition['PUBLIC_DATE'] = '__ON__';
                    $condition['public_date_from'] = $from_date;
                }
                if ($this->to_date) {
                    $condition['PUBLIC_DATE'] = '__ON__';
                    $condition['public_date_to'] = $to_date;
                }
            }
            if ($this->cp_status == campaigns::SEARCH_OPENING) {
                if ($this->from_date) {
                    $condition['START_DATE'] = '__ON__';
                    $condition['start_date_from'] = $from_date;
                }
                if ($this->to_date) {
                    $condition['START_DATE'] = '__ON__';
                    $condition['start_date_to'] = $to_date;
                }
            }
            if ($this->cp_status == campaigns::SEARCH_END) {
                if ($this->from_date) {
                    $condition['END_DATE'] = '__ON__';
                    $condition['end_date_from'] = $from_date;
                }
                if ($this->to_date) {
                    $condition['END_DATE'] = '__ON__';
                    $condition['end_date_to'] = $to_date;
                }
            }
            if ($this->cp_status == campaigns::SEARCH_ANNOUNCE) {
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
        if ($this->cp_id != null) {
            $condition['CP'] = '__ON__';
            $condition['cp_id'] = $this->cp_id;
        }

        if ($this->brand_name != null) {
            $condition['BRAND_ID'] = '__ON__';
            $condition['brand_id'] = $this->brand_id;
        }

        if ($this->brand_name != null) {
            $condition['BRAND'] = '__ON__';
            $brand = $brand_service->getBrandByBrandName($this->brand_name);
            $condition['brand_id'] = $brand->id;
        }

        if ($this->winner_count_from) {
            $condition['WINNER_COUNT'] = '__ON__';
            $condition['winner_count_from'] = $this->winner_count_from;
        }
        if ($this->winner_count_to) {
            $condition['WINNER_COUNT'] = '__ON__';
            $condition['winner_count_to'] = $this->winner_count_to;
        }
        $condition['__NOFETCH__'] = true;
        $db = new aafwDataBuilder();
        $rs = $db->getCpsSearch($condition, null, null, true, 'Cp');
        while ($cp = $db->fetch($rs['list'])) {
            $cp_id = $cp->id;
            $cp->campaign_status = $cp->getStatus();
            $cp->name = $cp->getTitle();
            $brand = $brand_service->getBrandById($cp->brand_id);
            $actions = $cp_flow_service->getCpActionsByCpId($cp_id);
            $action = $cp_flow_service->getFirstActionOfCp($cp->id);
            $cp_member_count = $action->getMemberCount();
            $data_csv = array();

            switch ($cp->campaign_status) {
                case Cp::CAMPAIGN_STATUS_SCHEDULE:
                    $status = "公開予約";
                    if ($cp->join_limit_flg == Cp::JOIN_LIMIT_ON) {
                        $open_range = "限定キャンペーン";
                    } else {
                        $open_range = "公開キャンペーン";
                    }
                    break;
                case Cp::CAMPAIGN_STATUS_OPEN:
                    $status = "開催中";
                    if ($cp->join_limit_flg == Cp::JOIN_LIMIT_ON) {
                        $open_range = "限定キャンペーン";
                    } else {
                        $open_range = "公開キャンペーン";
                    }
                    break;
                case Cp::CAMPAIGN_STATUS_WAIT_ANNOUNCE:
                    $status = "当選発表待ち";
                    if ($cp->join_limit_flg == Cp::JOIN_LIMIT_ON) {
                        $open_range = "限定キャンペーン";
                    } else {
                        $open_range = "公開キャンペーン";
                    }
                    break;
                    break;
                case Cp::CAMPAIGN_STATUS_CLOSE:
                    $status = "終了";
                    if ($cp->join_limit_flg == Cp::JOIN_LIMIT_ON) {
                        $open_range = "限定キャンペーン";
                    } else {
                        $open_range = "公開キャンペーン";
                    }
                    break;
                case Cp::CAMPAIGN_STATUS_DEMO:
                    $status = "下書き";
                    $open_range = "デモ";
                    break;
                case Cp::CAMPAIGN_STATUS_CP_PAGE_CLOSED;
                    $status = "終了";
                    $open_range = 'クローズ';
                    break;
                default:
                    $status = "下書き";
                    if ($cp->join_limit_flg == Cp::JOIN_LIMIT_ON) {
                        $open_range = "限定キャンペーン";
                    } else {
                        $open_range = "公開キャンペーン";
                    }
            }
            if($cp->campaign_status == Cp::CAMPAIGN_STATUS_DEMO ){
                $data_csv[] = Util::constructBaseURL($brand->id, $brand->directory_name, true).'campaigns'.'/'.$cp->id.'?demo_token='.hash("sha256",  $cp->created_at);
            }else{
                $data_csv[] = Util::constructBaseURL($brand->id, $brand->directory_name, true).'campaigns'.'/'.$cp->id;
            }
            if (in_array(campaigns::SEARCH_ID, $this->show)) {
                $data_csv[] = intval($cp->id);
            }
            if (in_array(campaigns::SEARCH_STATUS, $this->show)) {
                $data_csv[] = $status;
            }
            if (in_array(campaigns::SEARCH_OPEN_RANGE, $this->show)) {
                $data_csv[] = $open_range;
            }
            if (in_array(campaigns::SEARCH_TITLE, $this->show)) {
                $data_csv[] = $cp->name;
            }
            if (in_array(campaigns::SEARCH_BRAND, $this->show)) {
                $data_csv[] = $brand->id;
                $data_csv[] = $brand->name;
            }
            if (in_array(campaigns::SEARCH_PUBLIC_DATE, $this->show)) {
                list($date, $time) = $this->splitDateTime($cp->public_date);
                $data_csv[] = $date;
                $data_csv[] = $time;
            }
            if (in_array(campaigns::SEARCH_OPENING_DATE, $this->show)) {
                list($date, $time) = $this->splitDateTime($cp->start_date);
                $data_csv[] = $date;
                $data_csv[] = $time;
            }
            if (in_array(campaigns::SEARCH_CLOSING_DATE, $this->show)) {
                list($date, $time) = $this->splitDateTime($cp->end_date);
                $data_csv[] = $date;
                $data_csv[] = $time;
            }
            if (in_array(campaigns::SEARCH_ANNOUNCE_DATE, $this->show)) {
                list($date, $time) = $this->splitDateTime($cp->announce_date);
                $data_csv[] = $date;
                $data_csv[] = $time;
            }
            if (in_array(campaigns::SEARCH_COM, $this->show)) {
                $data_csv[] = intval($cp->show_monipla_com_flg);
            }
            if (in_array(campaigns::SEARCH_PARTICIPANT, $this->show)) {
                $data_csv[] = intval($cp->winner_count);
            }
            if (in_array(campaigns::SEARCH_JOIN, $this->show)) {
                $data_csv[] = intval($cp_member_count['finish_action']);
            }
            if (in_array(campaigns::SEARCH_STEP, $this->show)) {
                $prev_count = -1;
                foreach ($actions as $action) {
                    $cp_member_count = $action->getMemberCount();
                    $cp_action_detail = '';

                    switch ($this->step_info){
                        case campaigns::STEP_INFO_DEFAULT: // デフォルト
                            $cp_action_detail = $action->getCpActionDetail()['title'];
                            break;
                        case campaigns::STEP_INFO_ALL_USER_COUNT: // 全ユーザ数
                            $cp_action_detail = $cp_member_count[CpUserService::CACHE_TYPE_FINISH_ACTION];
                            break;
                        case campaigns::STEP_INFO_PC_USER_COUNT: // PCユーザ数
                            $cp_action_detail = $cp_member_count[CpUserService::CACHE_TYPE_FINISH_ACTION_PC];
                            break;
                        case campaigns::STEP_INFO_SP_USER_COUNT: // SPユーザ数
                            $cp_action_detail = $cp_member_count[CpUserService::CACHE_TYPE_FINISH_ACTION_SP];
                            break;
                        case campaigns::STEP_INFO_NEW_USER_COUNT: // 新規全ユーザ数
                            $cp_action_detail = $cp_member_count[CpUserService::CACHE_TYPE_FINISH_ACTION_NEW_BR_USER];
                            break;
                        case campaigns::STEP_INFO_ALL_USER_CVR: // 全ユーザCVR
                            $new_count = $cp_member_count[CpUserService::CACHE_TYPE_FINISH_ACTION];
                            if(!$new_count) continue;
                            // CVR取得
                            $cp_action_detail = $this->calcCvr($new_count, $prev_count);
                            $prev_count = $new_count;
                            break;
                        case campaigns::STEP_INFO_PC_USER_CVR: // PCユーザCVR
                            $new_count = $cp_member_count[CpUserService::CACHE_TYPE_FINISH_ACTION_PC];
                            if(!$new_count) continue;
                            // CVR取得
                            $cp_action_detail = $this->calcCvr($new_count, $prev_count);
                            $prev_count = $new_count;
                            break;
                        case campaigns::STEP_INFO_SP_USER_CVR: // SPユーザCVR
                            $new_count = $cp_member_count[CpUserService::CACHE_TYPE_FINISH_ACTION_SP];
                            if(!$new_count) continue;
                            // CVR取得
                            $cp_action_detail = $this->calcCvr($new_count, $prev_count);
                            $prev_count = $new_count;
                            break;
                        case campaigns::STEP_INFO_NEW_USER_CVR: // 新規全ユーザCVR
                            $new_count = $cp_member_count[CpUserService::CACHE_TYPE_FINISH_ACTION_NEW_BR_USER];
                            if(!$new_count) continue;
                            // CVR取得
                            $cp_action_detail = $this->calcCvr($new_count, $prev_count);
                            $prev_count = $new_count;
                            break;
                        default:
                    }

                    $data_csv[] = $cp_action_detail;
                }
            }

            $array_data = $csv->out(array('data' => $data_csv), 1);
            print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");
        }
        exit();
    }

    /**
     * @param $new_count
     * @param int $prev_count
     * @return float|int
     */
    private function calcCvr($new_count, $prev_count =0){
        // 新モジュールカウント取得
        if ($prev_count == -1){
            if($new_count == 0){
                $cp_action_detail = 0;
            }else{
                $cp_action_detail = round($new_count/$new_count * 100);
            }
        }else{
            if($new_count == 0 || $prev_count == 0){
                $cp_action_detail = 0;
            }else{
                $cp_action_detail = round($new_count/$prev_count * 100);
            }
        }
        return $cp_action_detail;
    }

    private function splitDateTime($date_time) {
        $time = strtotime($date_time);
        if ($time < 0) {
            return array("0000/00/00", "00:00:00");
        } else {
            return array(date("Y/m/d", $time), date("H:i:s", $time));
        }

    }
}