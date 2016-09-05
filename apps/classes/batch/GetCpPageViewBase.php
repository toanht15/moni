<?php

/**
 * Googleアナリティクスからキャンペーンページビュー数情報を取得するクラス
 * Class GetCpPageViewBase
 */
abstract class GetCpPageViewBase {

    const CP_START_DATE_BEGIN = "2016-01-01";       //2016年の開始キャンペーンがページビューを取得する
    const ANALYTICS_API_RATE_LIMIT = 8000;          //GoogleアナリティクスのAPIリクエストの制限

    protected $logger;
    protected $hipchat_loger;
    protected $service_factory;
    protected $execute_class;

    protected $request_count;           //APIリクエストカウント
    protected $execute_cp_count;        //実行キャンペーンカウント

    public function __construct() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_loger = aafwLog4phpLogger::getHipChatLogger();
        $this->service_factory = new aafwServiceFactory();
        $this->request_count = 0;
        $this->execute_cp_count = 0;
        $this->execute_class = get_class($this);
    }

    public function doProcess() {
        try {
            $this->logger->info("start batch: class=" . $this->execute_class);
            $start_time = date("Y-m-d H:i:s");
            $this->executeProcess();
            $end_time = date("Y-m-d H:i:s");

            $this->logger->info($this->execute_class . ' Status:Success Start_Time:' . $start_time . ' End_Time:' . $end_time . 'execute_cp_count=' . $this->execute_cp_count);
        } catch (Exception $e) {
            $end_time = date("Y-m-d H:i:s");
            $this->hipchat_loger->error($this->execute_class . " ERROR! " . $e);
            $this->logger->error($this->execute_class . ' Status:Error Start_Time:' . $start_time . ' End_Time:' . $end_time . ' Detail:' . $e);
        }
    }

    public function executeProcess() {
        //取得対象のキャンペーン
        $target_cps = $this->getTargetCps();

        $yesterday = date("Y-m-d 23:59:59", strtotime("-1 day"));

        /** @var CpPageViewService $cp_page_view_service */
        $cp_page_view_service = $this->service_factory->create("CpPageViewService");
        /** @var CpPageViewLogService $cp_page_view_log_service */
        $cp_page_view_log_service = $this->service_factory->create("CpPageViewLogService");

        foreach ($target_cps as $cp) {
            try {
                //取得完了したら、無視します。
                if ($cp_page_view_log_service->getCpPageViewLogByCpIdAndStatus($cp->id, CpPageViewLog::STATUS_FINISH)) {
                    continue;
                }

                $start_date = date("Y-m-d", strtotime($cp->start_date));
                $end_date = $cp->end_date > $yesterday ? date("Y-m-d", strtotime($yesterday)) : date("Y-m-d", strtotime($cp->end_date));

                //LPが設定されているかどうかチェックする
                $is_set_lp_cp = ($cp->reference_url && preg_match('/\/page\//', $cp->reference_url)) ? true : false;

                $date = $start_date;
                while ($date <= $end_date) {

                    //すでに取得したら、無視します。
                    $cp_page_view = $cp_page_view_service->getCpPageViewByCpIdAndDate($cp->id, $date, CpPageView::TYPE_CP_PAGE);
                    if ($cp_page_view && $cp_page_view->status == CpPageView::STATUS_SUCCESS) {
                        $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                        continue;
                    }

                    //キャンペーントップページビュー数を取得する
                    $cp_page_views = $this->getCpPageViewsByDate($date, $cp->getUrlPath());

                    if (!$cp_page_views) {
                        $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                        continue;
                    } else {
                        list($total_view_count, $pc_view_count, $mobile_view_count, $tablet_view_count, $user_count) = $cp_page_views;
                    }

                    $cp_page_view_service->updateCpPageView($cp->id, $date, CpPageView::TYPE_CP_PAGE, $total_view_count, $pc_view_count, $mobile_view_count, $tablet_view_count, $user_count, CpPageView::STATUS_SUCCESS);

                    //LPページビューを取得する
                    if ($is_set_lp_cp) {
                        $lp_page_views = $this->getCpPageViewsByDate($date, $cp->reference_url);

                        if (!$lp_page_views) {
                            $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                            continue;
                        } else {
                            list($total_view_count, $pc_view_count, $mobile_view_count, $tablet_view_count, $user_count) = $lp_page_views;
                        }

                        $cp_page_view_service->updateCpPageView($cp->id, $date, CpPageView::TYPE_LP_PAGE, $total_view_count, $pc_view_count, $mobile_view_count, $tablet_view_count, $user_count, CpPageView::STATUS_SUCCESS);
                    }

                    $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                }

                //キャンペーンの終了日なら、完了します。
                if ($cp->end_date <= $yesterday) {
                    $cp_page_view_log_service->updateCpPageViewLog($cp->id, CpPageViewLog::STATUS_FINISH);
                } else {
                    $cp_page_view_log_service->updateCpPageViewLog($cp->id, CpPageViewLog::STATUS_CRAWL);
                }

                $this->execute_cp_count++;

                //APIの制限なら、やめます
                if ($this->request_count >= self::ANALYTICS_API_RATE_LIMIT) {
                    exit();
                }

            } catch (Exception $e) {
                $cp_page_view_log_service->updateCpPageViewLog($cp->id, CpPageViewLog::STATUS_FAILED);
                $this->logger->error($this->execute_class . " error! cp_id=" . $cp->id);
                $this->logger->error($e);
            }
        }
    }

    /**
     * キャンペーントップページのページビューを取得する
     * @param $date
     * @param $cp_url
     * @return array
     */
    protected function getCpPageViewsByDate($date, $cp_url) {
        $analytics_client = new GoogleAnalyticsClient();
        $page_path_filter = $analytics_client->buildDimensionFilter("ga:pagePath", $cp_url, GoogleAnalyticsClient::FILTER_CONTAIN_MATCH);

        //ページビュー数（全体）
        $total_view_count = $analytics_client->getPageViewsByDate($date, array($page_path_filter));
        if (!$this->checkViewCount($total_view_count)) {
            return null;
        }

        //ページビュー数（PC）
        $pc_filter = $analytics_client->buildDimensionFilter("ga:deviceCategory", "desktop");
        $pc_view_count = $analytics_client->getPageViewsByDate($date, array($page_path_filter, $pc_filter));
        if (!$this->checkViewCount($total_view_count)) {
            return null;
        }

        //ページビュー数（モバイル）
        $mobile_filter = $analytics_client->buildDimensionFilter("ga:deviceCategory", "mobile");
        $mobile_view_count = $analytics_client->getPageViewsByDate($date, array($page_path_filter, $mobile_filter));
        if (!$this->checkViewCount($mobile_view_count)) {
            return null;
        }

        //ページビュー数（タブレット）
        $tablet_filter = $analytics_client->buildDimensionFilter("ga:deviceCategory", "tablet");
        $tablet_view_count = $analytics_client->getPageViewsByDate($date, array($page_path_filter, $tablet_filter));
        if (!$this->checkViewCount($tablet_view_count)) {
            return null;
        }

        //ユーザー数
        $user_count = $analytics_client->getUniqueUserByDate($date, array($page_path_filter));
        if (!$this->checkViewCount($user_count)) {
            return null;
        }

        $this->request_count += 5;
        return array($total_view_count, $pc_view_count, $mobile_view_count, $tablet_view_count, $user_count);
    }

    /**
     * ビュー数をチェックする
     * @param $view_count
     * @return bool
     */
    private function checkViewCount($view_count) {
        if (is_numeric($view_count)) {
            return true;
        }

        //エラーを発生されるとき、「Rate Limit」エラーかどうかチェックする
        if (is_array($view_count) && $view_count['reason'] == GoogleAnalyticsClient::RATE_LIMIT_REASON) {
            $this->hipchat_loger->error($this->execute_class . " ERROR: RATE LIMIT REACHED! ");
            exit();
        }

        return false;
    }

    abstract function getTargetCps();
}