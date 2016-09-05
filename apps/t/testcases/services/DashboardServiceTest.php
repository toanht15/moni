<?php

AAFW::import('jp.aainc.classes.services.DashboardService');
AAFW::import('jp.aainc.classes.services.UserAttributeService');
AAFW::import('jp.aainc.t.helpers.adapters.UserProfileTestHelper');

class DashboardServiceTest extends BaseTest {

    protected $brand;
    protected $service;

    public function setup() {
        $this->brand = $this->entity("Brands", array("created_at" => "2014-09-16 00:00:00"));
        $this->service = new DashboardService($this->brand);
        $this->profile_helper = new UserProfileTestHelper();
    }

    public function testGetSummaryDateType01_typeToday() {
        $summary_date_type = DashboardService::SUMMARY_TODAY;
        $summary_date = null;

        $this->assertEquals(array("2014-09-16 00:00:00", date('Y-m-d H:i:s', strtotime('today 23:59:59'))),
            $this->service->getSummaryDate($summary_date_type, $summary_date));
    }

    public function testGetSummaryDateType02_typeYesterday() {
        $summary_date_type = DashboardService::SUMMARY_YESTERDAY;
        $summary_date = null;

        $this->assertEquals(array("2014-09-16 00:00:00", date('Y-m-d H:i:s', strtotime('yesterday 23:59:59'))),
            $this->service->getSummaryDate($summary_date_type, $summary_date));
    }

    public function testGetSummaryDateType03_existsTypeCustom() {
        $summary_date_type = DashboardService::SUMMARY_CUSTOM;
        $summary_date = '2015/02/01';

        $this->assertEquals(array("2014-09-16 00:00:00", '2015-02-01 23:59:59'),
            $this->service->getSummaryDate($summary_date_type, $summary_date));
    }

    public function testGetTermDateType01_typeToday() {
        $term_date_type = DashboardService::TERM_TODAY;
        $term_from_date = null;
        $term_to_date = null;

        $this->assertEquals(array(date('Y-m-d H:i:s', strtotime('today 00:00:00')), date('Y-m-d H:i:s', strtotime('today 23:59:59'))),
            $this->service->getTermDate($term_date_type, $term_from_date, $term_to_date));
    }

    public function testGetTermDateType02_typeYesterday() {
        $term_date_type = DashboardService::TERM_YESTERDAY;
        $term_from_date = null;
        $term_to_date = null;

        $this->assertEquals(array(date('Y-m-d H:i:s', strtotime('yesterday 00:00:00')), date('Y-m-d H:i:s', strtotime('yesterday 23:59:59'))),
            $this->service->getTermDate($term_date_type, $term_from_date, $term_to_date));
    }

    //テスト実行日により、getTermDateの中の分岐が変わるため、現状ではカバレッジが100%にならない
    public function testGetTermDateType03_typeLastWeek() {
        $term_date_type = DashboardService::TERM_LAST_WEEK;
        $term_from_date = null;
        $term_to_date = null;
        if (date('l') == 'Sunday') {
            $from_date = date('Y-m-d H:i:s', strtotime('Sunday previous week 00:00:00'));
        } else {
            $from_date = date('Y-m-d H:i:s', strtotime('2 weeks ago Sunday 00:00:00'));
        }

        $this->assertEquals(array($from_date, date('Y-m-d H:i:s', strtotime('Saturday previous week 23:59:59'))),
            $this->service->getTermDate($term_date_type, $term_from_date, $term_to_date));
    }

    public function testGetTermDateType04_typeLastMonth() {
        $term_date_type = DashboardService::TERM_LAST_MONTH;
        $term_from_date = null;
        $term_to_date = null;

        $this->assertEquals(array(date('Y-m-d H:i:s', strtotime('first day of -1 month 00:00:00')), date('Y-m-d H:i:s', strtotime('last day of -1 month 23:59:59'))),
            $this->service->getTermDate($term_date_type, $term_from_date, $term_to_date));
    }

    public function testGetTermDateType05_typeLastSevenDays() {
        $term_date_type = DashboardService::TERM_LAST_SEVEN_DAYS;
        $term_from_date = null;
        $term_to_date = null;

        $this->assertEquals(array(date('Y-m-d H:i:s', strtotime('-7 day 00:00:00')), date('Y-m-d H:i:s', strtotime('-1 day 23:59:59'))),
            $this->service->getTermDate($term_date_type, $term_from_date, $term_to_date));
    }

    public function testGetTermDateType06_typeLastThirtyDays() {
        $term_date_type = DashboardService::TERM_LAST_THIRTY_DAYS;
        $term_from_date = null;
        $term_to_date = null;

        $this->assertEquals(array(date('Y-m-d H:i:s', strtotime('-30 day 00:00:00')), date('Y-m-d H:i:s', strtotime('-1 day 23:59:59'))),
            $this->service->getTermDate($term_date_type, $term_from_date, $term_to_date));
    }

    public function testGetTermDateType07_typeCustom() {
        $term_date_type = DashboardService::TERM_CUSTOM;
        $term_from_date = '2015/02/01';
        $term_to_date = '2015/03/01';

        $this->assertEquals(array(date('Y-m-d H:i:s', strtotime($term_from_date . ' 00:00:00')), date('Y-m-d H:i:s', strtotime($term_to_date . ' 23:59:59'))),
            $this->service->getTermDate($term_date_type, $term_from_date, $term_to_date));
    }

    public function testGetSummaryDateError01_Absent() {
        $summary_date = null;

        $this->assertEquals('カスタムを選択時は日付を入力してください。', $this->service->getSummaryDateError($summary_date));
    }

    public function testGetSummaryDateError02_Empty() {
        $summary_date = '';

        $this->assertEquals('カスタムを選択時は日付を入力してください。', $this->service->getSummaryDateError($summary_date));
    }

    public function testGetSummaryDateError03_Exists() {
        $summary_date = date('Y/m/d', strtotime('2014-12-18'));

        $this->assertNull($this->service->getSummaryDateError($summary_date));
    }

    public function testGetSummaryDateError04_FormatIncorrent() {
        $summary_date = '2015/02/';

        $this->assertEquals('日付形式(年/月/日)で入力してください。', $this->service->getSummaryDateError($summary_date));
    }

    public function testGetSummaryDateError05_NextDay() {
        $summary_date = date('Y/m/d', strtotime('+1 day'));

        $this->assertEquals('ページの開設日〜本日の範囲で入力してください。', $this->service->getSummaryDateError($summary_date));
    }

    public function testGetSummaryDateError06_PreviousDay() {
        $summary_date = date('Y/m/d', strtotime('2014-09-15'));

        $this->assertEquals('ページの開設日〜本日の範囲で入力してください。', $this->service->getSummaryDateError($summary_date));
    }

    public function testGetTermDateError01_absentAndAbsent() {
        $term_from_date = null;
        $term_to_date = null;

        $this->assertEquals('カスタムを選択時は日付を入力してください。', $this->service->getTermDateError($term_from_date, $term_to_date));
    }

    public function testGetTermDateError02_emptyAndAbsent() {
        $term_from_date = '';
        $term_to_date = null;

        $this->assertEquals('カスタムを選択時は日付を入力してください。', $this->service->getTermDateError($term_from_date, $term_to_date));
    }

    public function testGetTermDateError03_emptyAndEmpty() {
        $term_from_date = '';
        $term_to_date = '';

        $this->assertEquals('カスタムを選択時は日付を入力してください。', $this->service->getTermDateError($term_from_date, $term_to_date));
    }

    public function testGetTermDateError04_FormatIncorrentAndFormatCorrent() {
        $term_from_date = '2015/02/';
        $term_to_date = date('Y/m/d', strtotime('2015-02-15'));

        $this->assertEquals('日付形式(年/月/日)で入力してください。', $this->service->getTermDateError($term_from_date, $term_to_date));
    }

    public function testGetTermDateError05_PreviousDayAndFormatCorrent() {
        $term_from_date = '2014/09/15';
        $term_to_date = date('Y/m/d', strtotime('2015-02-15'));

        $this->assertEquals('ページの作成日〜本日の範囲で入力してください。', $this->service->getTermDateError($term_from_date, $term_to_date));
    }

    public function testGetTermDateError06_FormatCorrentAndNextDay() {
        $term_from_date = '2014/09/16';
        $term_to_date = date('Y/m/d', strtotime('+1 day'));

        $this->assertEquals('ページの作成日〜本日の範囲で入力してください。', $this->service->getTermDateError($term_from_date, $term_to_date));
    }

    public function testGetTermDateError07_OrderReverse() {
        $term_from_date = '2014/12/10';
        $term_to_date = '2014/11/10';

        $this->assertEquals('日付の指定順序が正しくありません。', $this->service->getTermDateError($term_from_date, $term_to_date));
    }

    public function testGetSummaryTitleDate01_typeToday() {
        $summary_date_type = DashboardService::SUMMARY_TODAY;
        $summary_date = null;

        $this->assertEquals(array('今日(' . date('Y-m-d', strtotime('today')) . ')', '開設 から 今日(' . date('Y-m-d', strtotime('today')) . ') までの累計'),
            $this->service->getSummaryTitleDate($summary_date_type, $summary_date));
    }

    public function testGetSummaryTitleDate02_typeYesterday() {
        $summary_date_type = DashboardService::SUMMARY_YESTERDAY;
        $summary_date = null;

        $this->assertEquals(array('昨日(' . date('Y-m-d', strtotime('yesterday')) . ')', '開設 から 昨日(' . date('Y-m-d', strtotime('yesterday')) . ') までの累計'),
            $this->service->getSummaryTitleDate($summary_date_type, $summary_date));
    }

    public function testGetSummaryTitleDate03_typeCustom() {
        $summary_date_type = DashboardService::SUMMARY_CUSTOM;
        $summary_date = '2015-02-01';

        $this->assertEquals(array(date('Y-m-d', strtotime($summary_date)), '開設 から ' . date('Y-m-d', strtotime($summary_date)) . ' までの累計'),
            $this->service->getSummaryTitleDate($summary_date_type, $summary_date));
    }

    public function testGetTermTitleDate01_typeToday() {
        $term_date_type = DashboardService::TERM_TODAY;
        $from_date = null;
        $to_date = null;

        $this->assertEquals(array('今日(' . date('Y-m-d', strtotime('today')) . ')', '今日(' . date('Y-m-d', strtotime('today')) . ') の新規登録'),
            $this->service->getTermTitleDate($term_date_type, $from_date, $to_date));
    }

    public function testGetTermTitleDate02_typeYesterday() {
        $term_date_type = DashboardService::TERM_YESTERDAY;
        $from_date = null;
        $to_date = null;

        $this->assertEquals(array('昨日(' . date('Y-m-d', strtotime('yesterday')) . ')', '昨日(' . date('Y-m-d', strtotime('yesterday')) . ') の新規登録'),
            $this->service->getTermTitleDate($term_date_type, $from_date, $to_date));
    }

    public function testGetTermTitleDate03_typeLastThirtyDays() {
        $term_date_type = DashboardService::TERM_LAST_THIRTY_DAYS;
        $from_date = null;
        $to_date = null;

        $this->assertEquals(array(date('Y-m-d', strtotime($from_date)) . ' 〜 ' . date('Y-m-d', strtotime($to_date)),
                date('Y-m-d', strtotime($from_date)) . ' 〜 ' . date('Y-m-d', strtotime($to_date)) . ' の新規登録'),
            $this->service->getTermTitleDate($term_date_type, $from_date, $to_date));
    }

    public function testGetTermTitleDate04_typeCustom() {
        $term_date_type = DashboardService::TERM_CUSTOM;
        $from_date = '2015-02-01';
        $to_date = '2015-03-01';

        $this->assertEquals(array(date('Y-m-d', strtotime($from_date)) . ' 〜 ' . date('Y-m-d', strtotime($to_date)),
                date('Y-m-d', strtotime($from_date)) . ' 〜 ' . date('Y-m-d', strtotime($to_date)) . ' の新規登録'),
            $this->service->getTermTitleDate($term_date_type, $from_date, $to_date));
    }

    public function testGetSummaryElementStyle01_typeToday() {
        $summary_date_type = DashboardService::SUMMARY_TODAY;
        $this->assertEquals(array("", "display:none", "display:none"), $this->service->getSummaryElementStyle($summary_date_type));
    }

    public function testGetSummaryElementStyle02_typeCustom() {
        $summary_date_type = DashboardService::SUMMARY_CUSTOM;
        $this->assertEquals(array("", "display:none", ""), $this->service->getSummaryElementStyle($summary_date_type));
    }

    public function testGetTermElementStyle01_typeToday() {
        $term_date_type = DashboardService::TERM_TODAY;
        $this->assertEquals(array("display:none", "", "display:none"), $this->service->getTermElementStyle($term_date_type));
    }

    public function testGetTermElementStyle02_typeCustom() {
        $term_date_type = DashboardService::TERM_CUSTOM;
        $this->assertEquals(array("display:none", "", ""), $this->service->getTermElementStyle($term_date_type));
    }

    public function testGetSummaryDatePicker01_createdAtToday() {
        $brand = $this->entity("Brands", array("created_at" => date('Y-m-d', strtotime('today 12:00:00'))));
        $service = new DashboardService($brand);
        $this->assertEquals(array(DashboardService::SUMMARY_TODAY => '今日まで',
                DashboardService::SUMMARY_CUSTOM => 'カスタム'),
            $service->getSummaryDatePicker());
    }

    public function testGetSummaryDatePicker02_createdAtYesterday() {
        $brand = $this->entity("Brands", array("created_at" => date('Y-m-d', strtotime('yesterday 23:59:59'))));
        $service = new DashboardService($brand);
        $this->assertEquals(array(DashboardService::SUMMARY_TODAY => '今日まで',
                DashboardService::SUMMARY_YESTERDAY => '昨日まで',
                DashboardService::SUMMARY_CUSTOM => 'カスタム'),
            $service->getSummaryDatePicker());
    }

    public function testGetTermDatePicker01_createdAt31DaysBefore() {
        $brand = $this->entity("Brands", array("created_at" => date('Y-m-d H:i:s', strtotime('-31 day 00:00:00'))));
        $service = new DashboardService($brand);
        $result = array(DashboardService::TERM_TODAY => '今日',
            DashboardService::TERM_YESTERDAY => '昨日',
            DashboardService::TERM_LAST_WEEK => '前週',
            DashboardService::TERM_LAST_MONTH => '先月',
            DashboardService::TERM_LAST_SEVEN_DAYS => '過去7日間',
            DashboardService::TERM_LAST_THIRTY_DAYS => '過去30日間',
            DashboardService::TERM_CUSTOM => 'カスタム');
        $this->assertEquals($result, $service->getTermDatePicker());
    }

    public function testGetTermDatePicker02_createdAt30DaysBefore() {
        $brand = $this->entity("Brands", array("created_at" => date('Y-m-d H:i:s', strtotime('-30 day 23:59:59'))));
        $service = new DashboardService($brand);
        $result = array(DashboardService::TERM_TODAY => '今日',
            DashboardService::TERM_YESTERDAY => '昨日',
            DashboardService::TERM_LAST_WEEK => '前週',
            DashboardService::TERM_LAST_SEVEN_DAYS => '過去7日間',
            DashboardService::TERM_LAST_THIRTY_DAYS => '過去30日間',
            DashboardService::TERM_CUSTOM => 'カスタム');
        if ($brand->created_at <= date('Y-m-d H:i:s', strtotime('last day of -1 month 23:59:59'))) {
            $result += array(DashboardService::TERM_LAST_MONTH => '先月');
        }
        $this->assertEquals($result, $service->getTermDatePicker());
    }

    //テスト実行日により、getTermDateの中の分岐が変わるため、現状ではカバレッジが100%にならない
    public function testGetTermDatePicker03_createdAt8DaysBefore() {
        $brand = $this->entity("Brands", array("created_at" => date('Y-m-d H:i:s', strtotime('-8 day 00:00:00'))));
        $service = new DashboardService($brand);
        $result = array(DashboardService::TERM_TODAY => '今日',
            DashboardService::TERM_YESTERDAY => '昨日',
            DashboardService::TERM_LAST_SEVEN_DAYS => '過去7日間',
            DashboardService::TERM_CUSTOM => 'カスタム');
        if (date('l') == 'Sunday') {
            $term_from_date = 'Sunday previous week';
        } else {
            $term_from_date = '2 weeks ago Sunday';
        }
        if ($brand->created_at <= date('Y-m-d H:i:s', strtotime($term_from_date . ' 23:59:59'))) {
            $result += array(DashboardService::TERM_LAST_WEEK => '前週');
        }
        if ($brand->created_at <= date('Y-m-d H:i:s', strtotime('last day of -1 month 23:59:59'))) {
            $result += array(DashboardService::TERM_LAST_MONTH => '先月');
        }
        $this->assertEquals($result, $service->getTermDatePicker());
    }

    public function testGetTermDatePicker04_createdAt7DaysBefore() {
        $brand = $this->entity("Brands", array("created_at" => date('Y-m-d H:i:s', strtotime('-7 day 23:59:59'))));
        $service = new DashboardService($brand);
        $result = array(DashboardService::TERM_TODAY => '今日',
            DashboardService::TERM_YESTERDAY => '昨日',
            DashboardService::TERM_LAST_SEVEN_DAYS => '過去7日間',
            DashboardService::TERM_CUSTOM => 'カスタム');
        if (date('l') == 'Sunday') {
            $term_from_date = 'Sunday previous week';
        } else {
            $term_from_date = '2 weeks ago Sunday';
        }
        if ($brand->created_at <= date('Y-m-d H:i:s', strtotime($term_from_date . ' 23:59:59'))) {
            $result += array(DashboardService::TERM_LAST_WEEK => '前週');
        }
        if ($brand->created_at <= date('Y-m-d H:i:s', strtotime('last day of -1 month 23:59:59'))) {
            $result += array(DashboardService::TERM_LAST_MONTH => '先月');
        }
        $this->assertEquals($result, $service->getTermDatePicker());
    }

    public function testGetTermDatePicker05_createdAtLastMonthMinusOneDayBefore() {
        $brand = $this->entity("Brands", array("created_at" => date('Y-m-d H:i:s', strtotime('last day of -1 month 23:59:59'))));
        $service = new DashboardService($brand);
        $result = array(DashboardService::TERM_TODAY => '今日',
            DashboardService::TERM_YESTERDAY => '昨日',
            DashboardService::TERM_LAST_MONTH => '先月',
            DashboardService::TERM_CUSTOM => 'カスタム');
        if (date('l') == 'Sunday') {
            $term_from_date = 'Sunday previous week';
        } else {
            $term_from_date = '2 weeks ago Sunday';
        }
        if ($brand->created_at <= date('Y-m-d H:i:s', strtotime($term_from_date . ' 23:59:59'))) {
            $result += array(DashboardService::TERM_LAST_WEEK => '前週');
        }
        if ($brand->created_at <= date('Y-m-d H:i:s', strtotime('-7 day 23:59:59'))) {
            $result += array(DashboardService::TERM_LAST_SEVEN_DAYS => '過去7日間');
        }
        if ($brand->created_at <= date('Y-m-d H:i:s', strtotime('-30 day 23:59:59'))) {
            $result += array(DashboardService::TERM_LAST_THIRTY_DAYS => '過去30日間');
        }
        $this->assertEquals($result, $service->getTermDatePicker());
    }

    public function testGetTermDatePicker06_createdAtFirstDayOfMonth() {
        $brand = $this->entity("Brands", array("created_at" => date('Y-m-01 00:00:00')));
        $service = new DashboardService($brand);
        $result = array(DashboardService::TERM_TODAY => '今日',
            DashboardService::TERM_CUSTOM => 'カスタム');

        if ($brand->created_at <= date('Y-m-d H:i:s', strtotime('yesterday 23:59:59'))) {
            $result += array(DashboardService::TERM_YESTERDAY => '昨日');
        }

        if (date('l') == 'Sunday') {
            $term_from_date = 'Sunday previous week';
        } else {
            $term_from_date = '2 weeks ago Sunday';
        }

        if ($brand->created_at <= date('Y-m-d H:i:s', strtotime($term_from_date . ' 23:59:59'))) {
            $result += array(DashboardService::TERM_LAST_WEEK => '前週');
        }
        if ($brand->created_at <= date('Y-m-d H:i:s', strtotime('-7 day 23:59:59'))) {
            $result += array(DashboardService::TERM_LAST_SEVEN_DAYS => '過去7日間');
        }
        if ($brand->created_at <= date('Y-m-d H:i:s', strtotime('-30 day 23:59:59'))) {
            $result += array(DashboardService::TERM_LAST_THIRTY_DAYS => '過去30日間');
        }
        $this->assertEquals($result, $service->getTermDatePicker());
    }

    public function testGetTermDatePicker07_createdAtLastWeek() {
        if (date('l') == 'Sunday') {
            $created_at = 'Sunday previous week 23:59:59';
        } else {
            $created_at = '2 weeks ago Sunday 23:59:59';
        }
        $brand = $this->entity("Brands", array("created_at" => date('Y-m-d H:i:s', strtotime($created_at))));
        $service = new DashboardService($brand);
        $result = array(DashboardService::TERM_TODAY => '今日',
            DashboardService::TERM_YESTERDAY => '昨日',
            DashboardService::TERM_LAST_WEEK => '前週',
            DashboardService::TERM_LAST_SEVEN_DAYS => '過去7日間',
            DashboardService::TERM_CUSTOM => 'カスタム');
        if ($brand->created_at <= date('Y-m-d H:i:s', strtotime('last day of -1 month 23:59:59'))) {
            $result += array(DashboardService::TERM_LAST_MONTH => '先月');
        }
        $this->assertEquals($result, $service->getTermDatePicker());
    }

    public function testGetTermDatePicker08_createdAtLastWeekPlusOneDay() {
        if (date('l') == 'Sunday') {
            $created_at = 'Sunday previous week +1 day 00:00:00';
        } else {
            $created_at = '2 weeks ago Sunday +1 day 00:00:00';
        }
        $brand = $this->entity("Brands", array("created_at" => date('Y-m-d H:i:s', strtotime($created_at))));
        $service = new DashboardService($brand);
        $result = array(DashboardService::TERM_TODAY => '今日',
            DashboardService::TERM_YESTERDAY => '昨日',
            DashboardService::TERM_CUSTOM => 'カスタム');
        if ($brand->created_at <= date('Y-m-d H:i:s', strtotime('-7 day 23:59:59'))) {
            $result += array(DashboardService::TERM_LAST_SEVEN_DAYS => '過去7日間');
        }
        if ($brand->created_at <= date('Y-m-d H:i:s', strtotime('last day of -1 month 23:59:59'))) {
            $result += array(DashboardService::TERM_LAST_MONTH => '先月');
        }
        $this->assertEquals($result, $service->getTermDatePicker());
    }

    public function testGetTermDatePicker09_createdAtToday() {
        $brand = $this->entity("Brands", array("created_at" => date('Y-m-d H:i:s', strtotime('today 00:00:00'))));
        $service = new DashboardService($brand);
        $result = array(DashboardService::TERM_TODAY => '今日',
            DashboardService::TERM_CUSTOM => 'カスタム');
        $this->assertEquals($result, $service->getTermDatePicker());
    }

    public function testGetTermDatePicker10_createdAtYesterday() {
        $brand = $this->entity("Brands", array("created_at" => date('Y-m-d H:i:s', strtotime('yesterday 00:00:00'))));
        $service = new DashboardService($brand);
        $result = array(DashboardService::TERM_TODAY => '今日',
            DashboardService::TERM_YESTERDAY => '昨日',
            DashboardService::TERM_CUSTOM => 'カスタム');
        if(date('l') == 'Sunday') {
            $term_from_date = 'Sunday previous week';
        } else {
            $term_from_date = '2 weeks ago Sunday';
        }
        if($brand->created_at <= date('Y-m-d H:i:s', strtotime($term_from_date.' 23:59:59'))) {
            $result += array(DashboardService::TERM_LAST_WEEK => '前週');
        }
        if ($brand->created_at <= date('Y-m-d H:i:s', strtotime('last day of -1 month 23:59:59'))) {
            $result += array(DashboardService::TERM_LAST_MONTH => '先月');
        }
        $this->assertEquals($result, $service->getTermDatePicker());
    }

    public function testGetBrandPvCount01_Yesterday() {
        $brand_kpi_column = $this->findOne("ManagerBrandKpiColumns", array("import" => 'jp.aainc.classes.manager_brand_kpi.BrandsPV'));
        if(!$brand_kpi_column) {
            $brand_kpi_column = $this->entity("ManagerBrandKpiColumns", array("import" => 'jp.aainc.classes.manager_brand_kpi.BrandsPV'));
        }

        $this->entities('ManagerBrandKpiValues', array(
            array('column_id' => $brand_kpi_column->id,
                'brand_id' => $this->brand->id,
                'value' => 11000,
                'summed_date' => '2015-04-30'),
            array('column_id' => $brand_kpi_column->id,
                'brand_id' => $this->brand->id,
                'value' => 12500,
                'summed_date' => '2015-05-01'),
            array('column_id' => $brand_kpi_column->id,
                'brand_id' => $this->brand->id,
                'value' => 10050,
                'summed_date' => '2015-05-02')
        ));

        $result = $this->service->getDashboardInfo(
            DashboardService::DATE_TERM,
            DashboardService::BRAND_PV_COUNT,
            '2015-05-01 00:00:00',
            '2015-05-02 23:59:59',
            100
        );

        $expect_brand_pv_info = array(
            '2015/05/01' => array(
                0 => 12500,
                1 => '+1,500'
            ),
            '2015/05/02' => array(
                0 => 10050,
                1 => '-2,450'
            )
        );
        $this->assertEquals($expect_brand_pv_info, $result);
    }

    public function testGetBrandPvCount02_today() {
        $brand = $this->entity("Brands", array("created_at" => date('Y-m-d', strtotime('today 12:00:00'))));
        $service = new DashboardService($brand);

        $brand_kpi_column = $this->findOne("ManagerBrandKpiColumns", array("import" => 'jp.aainc.classes.manager_brand_kpi.BrandsPV'));
        if(!$brand_kpi_column) {
            $this->entity("ManagerBrandKpiColumns", array("import" => 'jp.aainc.classes.manager_brand_kpi.BrandsPV'));
        }

        $result = $service->getDashboardInfo(
            DashboardService::DATE_TERM,
            DashboardService::BRAND_PV_COUNT,
            date('Y-m-d H:i:s', strtotime('today 00:00:00')),
            date('Y-m-d H:i:s', strtotime('today 23:59:59')),
            100
        );
        $this->assertEquals(key($result), date('Y/m/d', strtotime('today')));
    }

    public function testGetAllFanCount01() {
        $user1 = $this->newUser();
        $user2 = $this->newUser();
        $user3 = $this->newUser();

        $this->entities('BrandsUsersRelations', array(
            array('user_id' => $user1->id,
                'brand_id' => $this->brand->id,
                'created_at' => date('Y-m-d', strtotime('2015-04-30'))),
            array('user_id' => $user2->id,
                'brand_id' => $this->brand->id,
                'created_at' => date('Y-m-d', strtotime('2015-05-01'))),
            array('user_id' => $user3->id,
                'brand_id' => $this->brand->id,
                'created_at' => date('Y-m-d', strtotime('2015-05-02'))),
        ));

        $result = $this->service->getAllFanCount(
            date('Y-m-d H:i:s', strtotime('2015-05-01 00:00:00')),
            date('Y-m-d H:i:s', strtotime('2015-05-02 23:59:59'))
        );
        $this->assertEquals(2, $result);
    }

    public function testGetQuestionHeight01_MultiAnswerChoiceCountThree() {
        $brand = $this->entity("Brands", array("created_at" => "2014-09-16 00:00:00"));
        $service = new DashboardService($brand);

        $condition = array(
            'number' => 1,
            'question_type' => QuestionTypeService::CHOICE_ANSWER_TYPE,
            'multi_answer_flg' => CpQuestionnaireService::MULTI_ANSWER
        );
        list($questionnaire_relation, $question, $choice_requirement, $choices) =
            $this->profile_helper->newProfileQuestionnaireByBrand($brand, $condition);
        $question_height = 140;

        // デフォルトはchoiceが2つだけなので、1つ追加
        $this->entity('ProfileQuestionChoices',
            array(
                'question_id' => $question->id,
                'choice_num' => 3,
                'choice' => 'テスト選択肢3',
                'other_choice_flg' => CpQuestionnaireService::NOT_USE_OTHER_CHOICE
            )
        );

        $this->assertEquals($question_height, $service->getQuestionHeight($choice_requirement));
    }

    public function testGetQuestionHeight02_MultiAnswerChoiceCountFour() {
        $brand = $this->entity("Brands", array("created_at" => "2014-09-16 00:00:00"));
        $service = new DashboardService($brand);

        $condition = array(
            'number' => 1,
            'question_type' => QuestionTypeService::CHOICE_ANSWER_TYPE,
            'multi_answer_flg' => CpQuestionnaireService::MULTI_ANSWER
        );
        list($questionnaire_relation, $question, $choice_requirement, $choices) =
            $this->profile_helper->newProfileQuestionnaireByBrand($brand, $condition);

        // デフォルトはchoiceが2つだけなので、1つ追加
        $this->entities('ProfileQuestionChoices',array(
            array(
                'question_id' => $question->id,
                'choice_num' => 3,
                'choice' => 'テスト選択肢3',
                'other_choice_flg' => CpQuestionnaireService::NOT_USE_OTHER_CHOICE
            ),
            array(
                'question_id' => $question->id,
                'choice_num' => 4,
                'choice' => 'テスト選択肢4',
                'other_choice_flg' => CpQuestionnaireService::NOT_USE_OTHER_CHOICE
            ),
        ));
        $question_height = 171;

        $this->assertEquals($question_height, $service->getQuestionHeight($choice_requirement));
    }

    public function testGetQuestionHeight03_SingleAnswerChoiceCountFive() {
        $brand = $this->entity("Brands", array("created_at" => "2014-09-16 00:00:00"));
        $service = new DashboardService($brand);

        $condition = array(
            'number' => 1,
            'question_type' => QuestionTypeService::CHOICE_ANSWER_TYPE,
            'multi_answer_flg' => CpQuestionnaireService::SINGLE_ANSWER
        );
        list($questionnaire_relation, $question, $choice_requirement, $choices) =
            $this->profile_helper->newProfileQuestionnaireByBrand($brand, $condition);

        // デフォルトはchoiceが2つだけなので、1つ追加
        $this->entities('ProfileQuestionChoices',array(
            array(
                'question_id' => $question->id,
                'choice_num' => 3,
                'choice' => 'テスト選択肢3',
                'other_choice_flg' => CpQuestionnaireService::NOT_USE_OTHER_CHOICE
            ),
            array(
                'question_id' => $question->id,
                'choice_num' => 4,
                'choice' => 'テスト選択肢4',
                'other_choice_flg' => CpQuestionnaireService::NOT_USE_OTHER_CHOICE
            ),
            array(
                'question_id' => $question->id,
                'choice_num' => 5,
                'choice' => 'テスト選択肢5',
                'other_choice_flg' => CpQuestionnaireService::NOT_USE_OTHER_CHOICE
            ),
        ));
        $question_height = 150;

        $this->assertEquals($question_height, $service->getQuestionHeight($choice_requirement));
    }

    public function testGetQuestionHeight04_SingleAnswerChoiceCountSix() {
        $brand = $this->entity("Brands", array("created_at" => "2014-09-16 00:00:00"));
        $service = new DashboardService($brand);

        $condition = array(
            'number' => 1,
            'question_type' => QuestionTypeService::CHOICE_ANSWER_TYPE,
            'multi_answer_flg' => CpQuestionnaireService::SINGLE_ANSWER
        );
        list($questionnaire_relation, $question, $choice_requirement, $choices) =
            $this->profile_helper->newProfileQuestionnaireByBrand($brand, $condition);

        // デフォルトはchoiceが2つだけなので、1つ追加
        $this->entities('ProfileQuestionChoices',array(
            array(
                'question_id' => $question->id,
                'choice_num' => 3,
                'choice' => 'テスト選択肢3',
                'other_choice_flg' => CpQuestionnaireService::NOT_USE_OTHER_CHOICE
            ),
            array(
                'question_id' => $question->id,
                'choice_num' => 4,
                'choice' => 'テスト選択肢4',
                'other_choice_flg' => CpQuestionnaireService::NOT_USE_OTHER_CHOICE
            ),
            array(
                'question_id' => $question->id,
                'choice_num' => 5,
                'choice' => 'テスト選択肢5',
                'other_choice_flg' => CpQuestionnaireService::NOT_USE_OTHER_CHOICE
            ),
            array(
                'question_id' => $question->id,
                'choice_num' => 6,
                'choice' => 'テスト選択肢6',
                'other_choice_flg' => CpQuestionnaireService::NOT_USE_OTHER_CHOICE
            ),
        ));
        $question_height = 175;

        $this->assertEquals($question_height, $service->getQuestionHeight($choice_requirement));
    }

    public function testGetDateBrandFanInfo01_term() {
        $brand = $this->entity('Brands', array('enterprise_id' => 1, 'created_at' => '2014-09-16 00:00:00'));
        $service = new DashboardService($brand);

        $user1 = $this->newUser();
        $user2 = $this->newUser();
        $user3 = $this->newUser();
        $user4 = $this->newUser();
        $user5 = $this->newUser();
        $user6 = $this->newUser();
        $user7 = $this->newUser();

        $relations = $this->entities('BrandsUsersRelations', array(
            array('user_id' => $user1->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-29 12:00:00'))),
            array('user_id' => $user2->id,
                'brand_id' => $brand->id,
                'withdraw_flg' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-30 12:00:00'))),
            array('user_id' => $user3->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-30 12:00:00'))),
            array('user_id' => $user4->id,
                'brand_id' => $brand->id,
                'withdraw_flg' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 12:00:00'))),
            array('user_id' => $user5->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-02 12:00:00'))),
            array('user_id' => $user6->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-02 12:00:00'))),
            array('user_id' => $user7->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-03 12:00:00'))),
        ));

        $this->entities('WithdrawLogs', array(
            array('brand_user_relation_id' => $relations[1]->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 13:00:00'))),
            array('brand_user_relation_id' => $relations[3]->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 13:00:00'))),
        ));

        $result = $service->getDashboardInfo(
            DashboardService::DATE_TERM,
            DashboardService::DATE_BRAND_FAN_COUNT,
            '2015-04-30 00:00:00',
            '2015-05-02 23:59:59',
            100
        );

        $expect_result = array(
            '2015/04/30' => array(3, '+2'),
            '2015/05/01' => array(2, '-1'),
            '2015/05/02' => array(4, '+2'),
        );

        $this->assertEquals($expect_result, $result);
    }

    public function testGetDateBrandFanInfo02_summary() {
        $brand = $this->entity("Brands", array('enterprise_id' => 1, "created_at" => "2015-04-29 12:00:00"));
        $service = new DashboardService($brand);

        $user1 = $this->newUser();
        $user2 = $this->newUser();
        $user3 = $this->newUser();
        $user4 = $this->newUser();
        $user5 = $this->newUser();
        $user6 = $this->newUser();
        $user7 = $this->newUser();

        $relations = $this->entities('BrandsUsersRelations', array(
            array('user_id' => $user1->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-29 12:00:00'))),
            array('user_id' => $user2->id,
                'brand_id' => $brand->id,
                'withdraw_flg' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-30 12:00:00'))),
            array('user_id' => $user3->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-30 12:00:00'))),
            array('user_id' => $user4->id,
                'brand_id' => $brand->id,
                'withdraw_flg' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 12:00:00'))),
            array('user_id' => $user5->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-02 12:00:00'))),
            array('user_id' => $user6->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-02 12:00:00'))),
            array('user_id' => $user7->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-03 12:00:00'))),
        ));

        $this->entities('WithdrawLogs', array(
            array('brand_user_relation_id' => $relations[1]->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 13:00:00'))),
            array('brand_user_relation_id' => $relations[3]->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 13:00:00'))),
        ));

        $result = $service->getDashboardInfo(
            DashboardService::DATE_SUMMARY,
            DashboardService::DATE_BRAND_FAN_COUNT,
            '2015-04-29 00:00:00',
            '2015-05-02 23:59:59',
            100
        );

        $expect_result = array(
            '2015/04/29' => array(1, '+1'),
            '2015/04/30' => array(3, '+2'),
            '2015/05/01' => array(2, '-1'),
            '2015/05/02' => array(4, '+2'),
        );

        $this->assertEquals($expect_result, $result);
    }

    public function testGetSnsFanInfo01_existsGdo() {
        $brand = $this->entity("Brands", array('enterprise_id' => 1, "created_at" => "2014-09-16 00:00:00"));
        $service = new DashboardService($brand);

        $this->entity('BrandGlobalSettings', array(
            'brand_id' => $brand->id,
            'name' => 'original_sns_accounts',
            'content' => 6
        ));

        $user1 = $this->newUser();
        $user2 = $this->newUser();
        $user3 = $this->newUser();
        $user4 = $this->newUser();

        $this->entities('BrandsUsersRelations', array(
            array('user_id' => $user1->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-30 12:00:00'))),
            array('user_id' => $user2->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 12:00:00'))),
            array('user_id' => $user3->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-02 12:00:00'))),
            array('user_id' => $user4->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-02 12:00:00'))),
        ));

        $this->entities('SocialAccounts', array(
            array('user_id' => $user1->id,
                'social_media_id' => 1,
                'social_media_account_id' => 1,
                'name' => 'test1',
                'validated' => 1),
            array('user_id' => $user1->id,
                'social_media_id' => 3,
                'social_media_account_id' => 1,
                'name' => 'test1',
                'validated' => 1),
            array('user_id' => $user1->id,
                'social_media_id' => 4,
                'social_media_account_id' => 1,
                'name' => 'test1',
                'validated' => 1),
            array('user_id' => $user2->id,
                'social_media_id' => 1,
                'social_media_account_id' => 1,
                'name' => 'test2',
                'validated' => 1),
            array('user_id' => $user2->id,
                'social_media_id' => 5,
                'social_media_account_id' => 1,
                'name' => 'test2',
                'validated' => 1),
            array('user_id' => $user3->id,
                'social_media_id' => 1,
                'social_media_account_id' => 1,
                'name' => 'test3',
                'validated' => 1),
            array('user_id' => $user4->id,
                'social_media_id' => 1,
                'social_media_account_id' => 1,
                'name' => 'test4',
                'validated' => 1),
            array('user_id' => $user4->id,
                'social_media_id' => 4,
                'social_media_account_id' => 1,
                'name' => 'test4',
                'validated' => 1),
            array('user_id' => $user4->id,
                'social_media_id' => 6,
                'social_media_account_id' => 1,
                'name' => 'test4',
                'validated' => 1),
            array('user_id' => $user4->id,
                'social_media_id' => 7,
                'social_media_account_id' => 1,
                'name' => 'test4',
                'validated' => 1),
        ));

        $result = $service->getDashboardInfo(
            DashboardService::DATE_SUMMARY,
            DashboardService::SNS_FAN_COUNT,
            '2015-04-30 00:00:00',
            '2015-05-02 23:59:59',
            4
        );
        $expect_result = array(
            '-1' => array(
                'cnt' => 0,
                'ratio' => '0'
            ),
            '1' => array(
                'cnt' => 4,
                'ratio' => 100
            ),
            '2' => array(
                'cnt' => 1,
                'ratio' => '25.0'
            ),
            '3' => array(
                'cnt' => 0,
                'ratio' => '0'
            ),
            '4' => array(
                'cnt' => 1,
                'ratio' => '25.0'
            ),
            '5' => array(
                'cnt' => 1,
                'ratio' => '25.0'
            ),
            '6' => array(
                'cnt' => 2,
                'ratio' => '50.0'
            ),
            '7' => array(
                'cnt' => 1,
                'ratio' => '25.0'
            ),
        );
        $this->assertEquals($expect_result, $result);
    }

    public function testGetSnsFanInfo02_AbsentGdo() {
        $brand = $this->entity("Brands", array('enterprise_id' => 1, "created_at" => "2014-09-16 00:00:00"));
        $service = new DashboardService($brand);

        $user1 = $this->newUser();
        $user2 = $this->newUser();
        $user3 = $this->newUser();
        $user4 = $this->newUser();

        $this->entities('BrandsUsersRelations', array(
            array('user_id' => $user1->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-30 12:00:00'))),
            array('user_id' => $user2->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 12:00:00'))),
            array('user_id' => $user3->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-02 12:00:00'))),
            array('user_id' => $user4->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-02 12:00:00'))),
        ));

        $this->entities('SocialAccounts', array(
            array('user_id' => $user1->id,
                'social_media_id' => 1,
                'social_media_account_id' => 1,
                'name' => 'test1',
                'validated' => 1),
            array('user_id' => $user1->id,
                'social_media_id' => 3,
                'social_media_account_id' => 1,
                'name' => 'test1',
                'validated' => 1),
            array('user_id' => $user1->id,
                'social_media_id' => 4,
                'social_media_account_id' => 1,
                'name' => 'test1',
                'validated' => 1),
            array('user_id' => $user2->id,
                'social_media_id' => 1,
                'social_media_account_id' => 1,
                'name' => 'test2',
                'validated' => 1),
            array('user_id' => $user2->id,
                'social_media_id' => 5,
                'social_media_account_id' => 1,
                'name' => 'test2',
                'validated' => 1),
            array('user_id' => $user3->id,
                'social_media_id' => 1,
                'social_media_account_id' => 1,
                'name' => 'test3',
                'validated' => 1),
            array('user_id' => $user4->id,
                'social_media_id' => 1,
                'social_media_account_id' => 1,
                'name' => 'test4',
                'validated' => 1),
            array('user_id' => $user4->id,
                'social_media_id' => 4,
                'social_media_account_id' => 1,
                'name' => 'test4',
                'validated' => 1),
            array('user_id' => $user4->id,
                'social_media_id' => 6,
                'social_media_account_id' => 1,
                'name' => 'test4',
                'validated' => 1),
        ));

        $result = $service->getDashboardInfo(
            DashboardService::DATE_SUMMARY,
            DashboardService::SNS_FAN_COUNT,
            '2015-04-30 00:00:00',
            '2015-05-02 23:59:59',
            4
        );
        $expect_result = array(
            '1' => array(
                'cnt' => 4,
                'ratio' => 100
            ),
            '2' => array(
                'cnt' => 1,
                'ratio' => '25.0'
            ),
            '3' => array(
                'cnt' => 0,
                'ratio' => 0
            ),
            '4' => array(
                'cnt' => 0,
                'ratio' => 0
            ),
            '5' => array(
                'cnt' => 1,
                'ratio' => '25.0'
            ),
            '6' => array(
                'cnt' => 2,
                'ratio' => '50.0'
            ),
            '-1' => array(
                'cnt' => 0,
                'ratio' => '0'
            ),
        );
        $this->assertEquals($expect_result, $result);
    }

    public function testGetSnsFanInfo03_existsPlatform() {
        $brand = $this->entity("Brands", array('enterprise_id' => 1, "created_at" => "2014-09-16 00:00:00"));
        $service = new DashboardService($brand);

        $user1 = $this->newUser();

        $this->entity('BrandGlobalSettings', array(
            'brand_id' => $brand->id,
            'name' => 'original_sns_accounts',
            'content' => 6
        ));

        $this->entities('BrandsUsersRelations', array(
            array('user_id' => $user1->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-30 12:00:00'))),
        ));

        $result = $service->getDashboardInfo(
            DashboardService::DATE_SUMMARY,
            DashboardService::SNS_FAN_COUNT,
            '2015-04-30 00:00:00',
            '2015-05-02 23:59:59',
            1
        );
        $expect_result = array(
            '1' => array(
                'cnt' => 0,
                'ratio' => 0
            ),
            '2' => array(
                'cnt' => 0,
                'ratio' => 0
            ),
            '3' => array(
                'cnt' => 0,
                'ratio' => 0
            ),
            '4' => array(
                'cnt' => 0,
                'ratio' => 0
            ),
            '5' => array(
                'cnt' => 0,
                'ratio' => 0
            ),
            '6' => array(
                'cnt' => 0,
                'ratio' => 0
            ),
            '7' => array(
                'cnt' => 0,
                'ratio' => 0
            ),
            '-1' => array(
                'cnt' => 1,
                'ratio' => 100
            ),
        );
        $this->assertEquals($expect_result, $result);
    }

    public function testGetSexFanInfo01_existsUnknown() {
        $brand = $this->entity("Brands", array('enterprise_id' => 1, "created_at" => "2014-09-16 00:00:00"));
        $service = new DashboardService($brand);

        $user1 = $this->newUser();
        $user2 = $this->newUser();
        $user3 = $this->newUser();

        $this->entities('BrandsUsersRelations', array(
            array('user_id' => $user1->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-30 12:00:00'))),
            array('user_id' => $user2->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 12:00:00'))),
            array('user_id' => $user3->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-02 12:00:00'))),
        ));

        $this->entities('UserSearchInfos', array(
            array('user_id' => $user1->id,
                'sex' => 'm'),
            array('user_id' => $user2->id,
                'sex' => 'f'),
            array('user_id' => $user3->id,
                'sex' => ''),
        ));

        $result = $service->getDashboardInfo(
            DashboardService::DATE_SUMMARY,
            DashboardService::SEX_FAN_COUNT,
            '2015-04-30 00:00:00',
            '2015-05-02 23:59:59',
            4
        );
        $expect_result = array(
            'f' => array(
                'cnt' => 1,
                'ratio' => '25.0'
            ),
            'm' => array(
                'cnt' => 1,
                'ratio' => '25.0'
            ),
            'n' => array(
                'cnt' => 2,
                'ratio' => '50.0'
            ),
        );
        $this->assertEquals($expect_result, $result);
    }

    public function testGetSexFanInfo02_onlyMan() {
        $brand = $this->entity("Brands", array('enterprise_id' => 1, "created_at" => "2014-09-16 00:00:00"));
        $service = new DashboardService($brand);

        $user1 = $this->newUser();
        $user2 = $this->newUser();

        $this->entities('BrandsUsersRelations', array(
            array('user_id' => $user1->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-30 12:00:00'))),
            array('user_id' => $user2->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 12:00:00'))),
        ));

        $this->entities('UserSearchInfos', array(
            array('user_id' => $user1->id,
                'sex' => 'm'),
            array('user_id' => $user2->id,
                'sex' => 'm'),
        ));

        $result = $service->getDashboardInfo(
            DashboardService::DATE_SUMMARY,
            DashboardService::SEX_FAN_COUNT,
            '2015-04-30 00:00:00',
            '2015-05-02 23:59:59',
            2
        );
        $expect_result = array(
            'f' => array(
                'cnt' => 0,
                'ratio' => '0'
            ),
            'm' => array(
                'cnt' => 2,
                'ratio' => '100'
            ),
            'n' => array(
                'cnt' => 0,
                'ratio' => '0'
            ),
        );
        $this->assertEquals($expect_result, $result);
    }

    public function testGetSexFanInfo03_onlyWoman() {
        $brand = $this->entity("Brands", array('enterprise_id' => 1, "created_at" => "2014-09-16 00:00:00"));
        $service = new DashboardService($brand);

        $user1 = $this->newUser();
        $user2 = $this->newUser();

        $this->entities('BrandsUsersRelations', array(
            array('user_id' => $user1->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-30 12:00:00'))),
            array('user_id' => $user2->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 12:00:00'))),
        ));

        $this->entities('UserSearchInfos', array(
            array('user_id' => $user1->id,
                'sex' => 'f'),
            array('user_id' => $user2->id,
                'sex' => 'f'),
        ));

        $result = $service->getDashboardInfo(
            DashboardService::DATE_SUMMARY,
            DashboardService::SEX_FAN_COUNT,
            '2015-04-30 00:00:00',
            '2015-05-02 23:59:59',
            2
        );
        $expect_result = array(
            'f' => array(
                'cnt' => 2,
                'ratio' => '100'
            ),
            'm' => array(
                'cnt' => 0,
                'ratio' => '0'
            ),
            'n' => array(
                'cnt' => 0,
                'ratio' => '0'
            ),
        );
        $this->assertEquals($expect_result, $result);
    }

    public function testGetAgeFanInfo01_onlyTeens() {
        $brand = $this->entity("Brands", array('enterprise_id' => 1, "created_at" => "2014-09-16 00:00:00"));
        $service = new DashboardService($brand);

        $user1 = $this->newUser();
        $user2 = $this->newUser();

        $this->entities('BrandsUsersRelations', array(
            array('user_id' => $user1->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-30 12:00:00'))),
            array('user_id' => $user2->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 12:00:00'))),
        ));

        $this->entities('UserSearchInfos', array(
            array('user_id' => $user1->id,
                'birthday' => date('Y-m-d', strtotime('today -18 year'))),
            array('user_id' => $user2->id,
                'birthday' => date('Y-m-d', strtotime('today -20 year +1 day'))),
        ));

        $result = $service->getDashboardInfo(
            DashboardService::DATE_SUMMARY,
            DashboardService::AGE_FAN_COUNT,
            '2015-04-30 00:00:00',
            '2015-05-02 23:59:59',
            2
        );

        $expect_result = array(
            '1' => array('cnt' => 2, 'ratio' => '100', 'summary_name' => '20才未満', 'name' => '20才未満'),
            '2' => array('cnt' => 0, 'ratio' => 0, 'summary_name' => '20-29才', 'name' => '20-29才'),
            '3' => array('cnt' => 0, 'ratio' => 0, 'summary_name' => '30-39才', 'name' => '30-39才'),
            '4' => array('cnt' => 0, 'ratio' => 0, 'summary_name' => '40-49才', 'name' => '40-49才'),
            '5' => array('cnt' => 0, 'ratio' => 0, 'summary_name' => '50才以上', 'name' => '50才以上'),
            '-1' => array('cnt' => 0, 'ratio' => 0, 'summary_name' => '未登録', 'name' => '未登録'),
        );
        $this->assertEquals($expect_result, $result);
    }

    public function testGetAgeFanInfo02_TeensAndTwenties() {
        $brand = $this->entity("Brands", array('enterprise_id' => 1, "created_at" => "2014-09-16 00:00:00"));
        $service = new DashboardService($brand);

        $user1 = $this->newUser();
        $user2 = $this->newUser();

        $this->entities('BrandsUsersRelations', array(
            array('user_id' => $user1->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-30 12:00:00'))),
            array('user_id' => $user2->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 12:00:00'))),
        ));

        $this->entities('UserSearchInfos', array(
            array('user_id' => $user1->id,
                'birthday' => date('Y-m-d', strtotime('today -18 year'))),
            array('user_id' => $user2->id,
                'birthday' => date('Y-m-d', strtotime('today -20 year -1 day'))),
        ));

        $result = $service->getDashboardInfo(
            DashboardService::DATE_SUMMARY,
            DashboardService::AGE_FAN_COUNT,
            '2015-04-30 00:00:00',
            '2015-05-02 23:59:59',
            2
        );

        $expect_result = array(
            '1' => array('cnt' => 1, 'ratio' => '50.0', 'summary_name' => '20才未満', 'name' => '20才未満'),
            '2' => array('cnt' => 1, 'ratio' => '50.0', 'summary_name' => '20-29才', 'name' => '20-29才'),
            '3' => array('cnt' => 0, 'ratio' => 0, 'summary_name' => '30-39才', 'name' => '30-39才'),
            '4' => array('cnt' => 0, 'ratio' => 0, 'summary_name' => '40-49才', 'name' => '40-49才'),
            '5' => array('cnt' => 0, 'ratio' => 0, 'summary_name' => '50才以上', 'name' => '50才以上'),
            '-1' => array('cnt' => 0, 'ratio' => 0, 'summary_name' => '未登録', 'name' => '未登録'),
        );
        $this->assertEquals($expect_result, $result);
    }

    public function testGetAgeFanInfo03_onlyNotRegister() {
        $brand = $this->entity("Brands", array('enterprise_id' => 1, "created_at" => "2014-09-16 00:00:00"));
        $service = new DashboardService($brand);

        $user1 = $this->newUser();
        $user2 = $this->newUser();

        $this->entities('BrandsUsersRelations', array(
            array('user_id' => $user1->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-30 12:00:00'))),
            array('user_id' => $user2->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 12:00:00'))),
        ));

        $result = $service->getDashboardInfo(
            DashboardService::DATE_SUMMARY,
            DashboardService::AGE_FAN_COUNT,
            '2015-04-30 00:00:00',
            '2015-05-02 23:59:59',
            2
        );
        $expect_result = array(
            '1' => array('cnt' => 0, 'ratio' => 0, 'summary_name' => '20才未満', 'name' => '20才未満'),
            '2' => array('cnt' => 0, 'ratio' => 0, 'summary_name' => '20-29才', 'name' => '20-29才'),
            '3' => array('cnt' => 0, 'ratio' => 0, 'summary_name' => '30-39才', 'name' => '30-39才'),
            '4' => array('cnt' => 0, 'ratio' => 0, 'summary_name' => '40-49才', 'name' => '40-49才'),
            '5' => array('cnt' => 0, 'ratio' => 0, 'summary_name' => '50才以上', 'name' => '50才以上'),
            '-1' => array('cnt' => 2, 'ratio' => 100, 'summary_name' => '未登録', 'name' => '未登録'),
        );
        $this->assertEquals($expect_result, $result);
    }

    public function testGetAreaFanInfo01_onlyOnePrefectures() {
        $brand = $this->entity("Brands", array('enterprise_id' => 1, "created_at" => "2014-09-16 00:00:00"));
        $service = new DashboardService($brand);

        $user1 = $this->newUser();
        $user2 = $this->newUser();

        $this->entities('BrandsUsersRelations', array(
            array('user_id' => $user1->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-30 12:00:00'))),
            array('user_id' => $user2->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 12:00:00'))),
        ));

        $this->entities('ShippingAddresses', array(
            array('user_id' => $user1->id,
                'pref_id' => 2),
            array('user_id' => $user2->id,
                'pref_id' => 2),
        ));

        $result = $service->getDashboardInfo(
            DashboardService::DATE_SUMMARY,
            DashboardService::AREA_FAN_COUNT,
            '2015-04-30 00:00:00',
            '2015-05-02 23:59:59',
            2
        );
        $expect_result = array(
            '0' => array('cnt' => 2, 'ratio' => 100, 'name' => '青森県'), '1' => array('cnt' => 0, 'ratio' => 0, 'name' => '北海道'),
            '2' => array('cnt' => 0, 'ratio' => 0, 'name' => '岩手県'), '3' => array('cnt' => 0, 'ratio' => 0, 'name' => '宮城県'),
            '4' => array('cnt' => 0, 'ratio' => 0, 'name' => '秋田県'), '5' => array('cnt' => 0, 'ratio' => 0, 'name' => '山形県'),
            '6' => array('cnt' => 0, 'ratio' => 0, 'name' => '福島県'), '7' => array('cnt' => 0, 'ratio' => 0, 'name' => '茨城県'),
            '8' => array('cnt' => 0, 'ratio' => 0, 'name' => '栃木県'), '9' => array('cnt' => 0, 'ratio' => 0, 'name' => '群馬県'),
            '10' => array('cnt' => 0, 'ratio' => 0, 'name' => '埼玉県'), '11' => array('cnt' => 0, 'ratio' => 0, 'name' => '千葉県'),
            '12' => array('cnt' => 0, 'ratio' => 0, 'name' => '東京都'), '13' => array('cnt' => 0, 'ratio' => 0, 'name' => '神奈川県'),
            '14' => array('cnt' => 0, 'ratio' => 0, 'name' => '新潟県'), '15' => array('cnt' => 0, 'ratio' => 0, 'name' => '富山県'),
            '16' => array('cnt' => 0, 'ratio' => 0, 'name' => '石川県'), '17' => array('cnt' => 0, 'ratio' => 0, 'name' => '福井県'),
            '18' => array('cnt' => 0, 'ratio' => 0, 'name' => '山梨県'), '19' => array('cnt' => 0, 'ratio' => 0, 'name' => '長野県'),
            '20' => array('cnt' => 0, 'ratio' => 0, 'name' => '岐阜県'), '21' => array('cnt' => 0, 'ratio' => 0, 'name' => '静岡県'),
            '22' => array('cnt' => 0, 'ratio' => 0, 'name' => '愛知県'), '23' => array('cnt' => 0, 'ratio' => 0, 'name' => '三重県'),
            '24' => array('cnt' => 0, 'ratio' => 0, 'name' => '滋賀県'), '25' => array('cnt' => 0, 'ratio' => 0, 'name' => '京都府'),
            '26' => array('cnt' => 0, 'ratio' => 0, 'name' => '大阪府'), '27' => array('cnt' => 0, 'ratio' => 0, 'name' => '兵庫県'),
            '28' => array('cnt' => 0, 'ratio' => 0, 'name' => '奈良県'), '29' => array('cnt' => 0, 'ratio' => 0, 'name' => '和歌山県'),
            '30' => array('cnt' => 0, 'ratio' => 0, 'name' => '鳥取県'), '31' => array('cnt' => 0, 'ratio' => 0, 'name' => '島根県'),
            '32' => array('cnt' => 0, 'ratio' => 0, 'name' => '岡山県'), '33' => array('cnt' => 0, 'ratio' => 0, 'name' => '広島県'),
            '34' => array('cnt' => 0, 'ratio' => 0, 'name' => '山口県'), '35' => array('cnt' => 0, 'ratio' => 0, 'name' => '徳島県'),
            '36' => array('cnt' => 0, 'ratio' => 0, 'name' => '香川県'), '37' => array('cnt' => 0, 'ratio' => 0, 'name' => '愛媛県'),
            '38' => array('cnt' => 0, 'ratio' => 0, 'name' => '高知県'), '39' => array('cnt' => 0, 'ratio' => 0, 'name' => '福岡県'),
            '40' => array('cnt' => 0, 'ratio' => 0, 'name' => '佐賀県'), '41' => array('cnt' => 0, 'ratio' => 0, 'name' => '長崎県'),
            '42' => array('cnt' => 0, 'ratio' => 0, 'name' => '熊本県'), '43' => array('cnt' => 0, 'ratio' => 0, 'name' => '大分県'),
            '44' => array('cnt' => 0, 'ratio' => 0, 'name' => '宮崎県'), '45' => array('cnt' => 0, 'ratio' => 0, 'name' => '鹿児島県'),
            '46' => array('cnt' => 0, 'ratio' => 0, 'name' => '沖縄県'), '47' => array('cnt' => 0, 'ratio' => 0, 'name' => '未登録'),
        );
        $this->assertEquals($expect_result, $result);
    }

    public function testGetAreaFanInfo02_twoPrefectures() {
        $brand = $this->entity("Brands", array('enterprise_id' => 1, "created_at" => "2014-09-16 00:00:00"));
        $service = new DashboardService($brand);

        $user1 = $this->newUser();
        $user2 = $this->newUser();

        $this->entities('BrandsUsersRelations', array(
            array('user_id' => $user1->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-30 12:00:00'))),
            array('user_id' => $user2->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 12:00:00'))),
        ));

        $this->entities('ShippingAddresses', array(
            array('user_id' => $user1->id,
                'pref_id' => 1),
            array('user_id' => $user2->id,
                'pref_id' => 2),
        ));

        $result = $service->getDashboardInfo(
            DashboardService::DATE_SUMMARY,
            DashboardService::AREA_FAN_COUNT,
            '2015-04-30 00:00:00',
            '2015-05-02 23:59:59',
            2
        );
        $expect_result = array(
            '0' => array('cnt' => 1, 'ratio' => '50.0', 'name' => '北海道'), '1' => array('cnt' => 1, 'ratio' => '50.0', 'name' => '青森県'),
            '2' => array('cnt' => 0, 'ratio' => 0, 'name' => '岩手県'), '3' => array('cnt' => 0, 'ratio' => 0, 'name' => '宮城県'),
            '4' => array('cnt' => 0, 'ratio' => 0, 'name' => '秋田県'), '5' => array('cnt' => 0, 'ratio' => 0, 'name' => '山形県'),
            '6' => array('cnt' => 0, 'ratio' => 0, 'name' => '福島県'), '7' => array('cnt' => 0, 'ratio' => 0, 'name' => '茨城県'),
            '8' => array('cnt' => 0, 'ratio' => 0, 'name' => '栃木県'), '9' => array('cnt' => 0, 'ratio' => 0, 'name' => '群馬県'),
            '10' => array('cnt' => 0, 'ratio' => 0, 'name' => '埼玉県'), '11' => array('cnt' => 0, 'ratio' => 0, 'name' => '千葉県'),
            '12' => array('cnt' => 0, 'ratio' => 0, 'name' => '東京都'), '13' => array('cnt' => 0, 'ratio' => 0, 'name' => '神奈川県'),
            '14' => array('cnt' => 0, 'ratio' => 0, 'name' => '新潟県'), '15' => array('cnt' => 0, 'ratio' => 0, 'name' => '富山県'),
            '16' => array('cnt' => 0, 'ratio' => 0, 'name' => '石川県'), '17' => array('cnt' => 0, 'ratio' => 0, 'name' => '福井県'),
            '18' => array('cnt' => 0, 'ratio' => 0, 'name' => '山梨県'), '19' => array('cnt' => 0, 'ratio' => 0, 'name' => '長野県'),
            '20' => array('cnt' => 0, 'ratio' => 0, 'name' => '岐阜県'), '21' => array('cnt' => 0, 'ratio' => 0, 'name' => '静岡県'),
            '22' => array('cnt' => 0, 'ratio' => 0, 'name' => '愛知県'), '23' => array('cnt' => 0, 'ratio' => 0, 'name' => '三重県'),
            '24' => array('cnt' => 0, 'ratio' => 0, 'name' => '滋賀県'), '25' => array('cnt' => 0, 'ratio' => 0, 'name' => '京都府'),
            '26' => array('cnt' => 0, 'ratio' => 0, 'name' => '大阪府'), '27' => array('cnt' => 0, 'ratio' => 0, 'name' => '兵庫県'),
            '28' => array('cnt' => 0, 'ratio' => 0, 'name' => '奈良県'), '29' => array('cnt' => 0, 'ratio' => 0, 'name' => '和歌山県'),
            '30' => array('cnt' => 0, 'ratio' => 0, 'name' => '鳥取県'), '31' => array('cnt' => 0, 'ratio' => 0, 'name' => '島根県'),
            '32' => array('cnt' => 0, 'ratio' => 0, 'name' => '岡山県'), '33' => array('cnt' => 0, 'ratio' => 0, 'name' => '広島県'),
            '34' => array('cnt' => 0, 'ratio' => 0, 'name' => '山口県'), '35' => array('cnt' => 0, 'ratio' => 0, 'name' => '徳島県'),
            '36' => array('cnt' => 0, 'ratio' => 0, 'name' => '香川県'), '37' => array('cnt' => 0, 'ratio' => 0, 'name' => '愛媛県'),
            '38' => array('cnt' => 0, 'ratio' => 0, 'name' => '高知県'), '39' => array('cnt' => 0, 'ratio' => 0, 'name' => '福岡県'),
            '40' => array('cnt' => 0, 'ratio' => 0, 'name' => '佐賀県'), '41' => array('cnt' => 0, 'ratio' => 0, 'name' => '長崎県'),
            '42' => array('cnt' => 0, 'ratio' => 0, 'name' => '熊本県'), '43' => array('cnt' => 0, 'ratio' => 0, 'name' => '大分県'),
            '44' => array('cnt' => 0, 'ratio' => 0, 'name' => '宮崎県'), '45' => array('cnt' => 0, 'ratio' => 0, 'name' => '鹿児島県'),
            '46' => array('cnt' => 0, 'ratio' => 0, 'name' => '沖縄県'), '47' => array('cnt' => 0, 'ratio' => 0, 'name' => '未登録'),
        );
        $this->assertEquals($expect_result, $result);
    }

    public function testGetAreaFanInfo03_onlyNotRegister() {
        $brand = $this->entity("Brands", array('enterprise_id' => 1, "created_at" => "2014-09-16 00:00:00"));
        $service = new DashboardService($brand);

        $user1 = $this->newUser();
        $user2 = $this->newUser();

        $this->entities('BrandsUsersRelations', array(
            array('user_id' => $user1->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-30 12:00:00'))),
            array('user_id' => $user2->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 12:00:00'))),
        ));

        $this->entities('ShippingAddresses', array(
            array('user_id' => $user1->id,
                'pref_id' => 0),
        ));

        $result = $service->getDashboardInfo(
            DashboardService::DATE_SUMMARY,
            DashboardService::AREA_FAN_COUNT,
            '2015-04-30 00:00:00',
            '2015-05-02 23:59:59',
            2
        );
        $expect_result = array(
            '0' => array('cnt' => 0, 'ratio' => 0, 'name' => '北海道'), '1' => array('cnt' => 0, 'ratio' => 0, 'name' => '青森県'),
            '2' => array('cnt' => 0, 'ratio' => 0, 'name' => '岩手県'), '3' => array('cnt' => 0, 'ratio' => 0, 'name' => '宮城県'),
            '4' => array('cnt' => 0, 'ratio' => 0, 'name' => '秋田県'), '5' => array('cnt' => 0, 'ratio' => 0, 'name' => '山形県'),
            '6' => array('cnt' => 0, 'ratio' => 0, 'name' => '福島県'), '7' => array('cnt' => 0, 'ratio' => 0, 'name' => '茨城県'),
            '8' => array('cnt' => 0, 'ratio' => 0, 'name' => '栃木県'), '9' => array('cnt' => 0, 'ratio' => 0, 'name' => '群馬県'),
            '10' => array('cnt' => 0, 'ratio' => 0, 'name' => '埼玉県'), '11' => array('cnt' => 0, 'ratio' => 0, 'name' => '千葉県'),
            '12' => array('cnt' => 0, 'ratio' => 0, 'name' => '東京都'), '13' => array('cnt' => 0, 'ratio' => 0, 'name' => '神奈川県'),
            '14' => array('cnt' => 0, 'ratio' => 0, 'name' => '新潟県'), '15' => array('cnt' => 0, 'ratio' => 0, 'name' => '富山県'),
            '16' => array('cnt' => 0, 'ratio' => 0, 'name' => '石川県'), '17' => array('cnt' => 0, 'ratio' => 0, 'name' => '福井県'),
            '18' => array('cnt' => 0, 'ratio' => 0, 'name' => '山梨県'), '19' => array('cnt' => 0, 'ratio' => 0, 'name' => '長野県'),
            '20' => array('cnt' => 0, 'ratio' => 0, 'name' => '岐阜県'), '21' => array('cnt' => 0, 'ratio' => 0, 'name' => '静岡県'),
            '22' => array('cnt' => 0, 'ratio' => 0, 'name' => '愛知県'), '23' => array('cnt' => 0, 'ratio' => 0, 'name' => '三重県'),
            '24' => array('cnt' => 0, 'ratio' => 0, 'name' => '滋賀県'), '25' => array('cnt' => 0, 'ratio' => 0, 'name' => '京都府'),
            '26' => array('cnt' => 0, 'ratio' => 0, 'name' => '大阪府'), '27' => array('cnt' => 0, 'ratio' => 0, 'name' => '兵庫県'),
            '28' => array('cnt' => 0, 'ratio' => 0, 'name' => '奈良県'), '29' => array('cnt' => 0, 'ratio' => 0, 'name' => '和歌山県'),
            '30' => array('cnt' => 0, 'ratio' => 0, 'name' => '鳥取県'), '31' => array('cnt' => 0, 'ratio' => 0, 'name' => '島根県'),
            '32' => array('cnt' => 0, 'ratio' => 0, 'name' => '岡山県'), '33' => array('cnt' => 0, 'ratio' => 0, 'name' => '広島県'),
            '34' => array('cnt' => 0, 'ratio' => 0, 'name' => '山口県'), '35' => array('cnt' => 0, 'ratio' => 0, 'name' => '徳島県'),
            '36' => array('cnt' => 0, 'ratio' => 0, 'name' => '香川県'), '37' => array('cnt' => 0, 'ratio' => 0, 'name' => '愛媛県'),
            '38' => array('cnt' => 0, 'ratio' => 0, 'name' => '高知県'), '39' => array('cnt' => 0, 'ratio' => 0, 'name' => '福岡県'),
            '40' => array('cnt' => 0, 'ratio' => 0, 'name' => '佐賀県'), '41' => array('cnt' => 0, 'ratio' => 0, 'name' => '長崎県'),
            '42' => array('cnt' => 0, 'ratio' => 0, 'name' => '熊本県'), '43' => array('cnt' => 0, 'ratio' => 0, 'name' => '大分県'),
            '44' => array('cnt' => 0, 'ratio' => 0, 'name' => '宮崎県'), '45' => array('cnt' => 0, 'ratio' => 0, 'name' => '鹿児島県'),
            '46' => array('cnt' => 0, 'ratio' => 0, 'name' => '沖縄県'), '47' => array('cnt' => 2, 'ratio' => 100, 'name' => '未登録'),
        );
        $this->assertEquals($expect_result, $result);
    }

    public function testGetProfileQuestionnaireInfo01_onlyOneAnswer() {
        $brand = $this->entity("Brands", array('enterprise_id' => 1, "created_at" => "2014-09-16 00:00:00"));
        $service = new DashboardService($brand);

        $user1 = $this->newUser();
        $user2 = $this->newUser();
        $user3 = $this->newUser();

        $relations = $this->entities('BrandsUsersRelations', array(
            array('user_id' => $user1->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-30 12:00:00'))),
            array('user_id' => $user2->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 12:00:00'))),
            array('user_id' => $user3->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-02 12:00:00'))),
        ));

        $condition = array(
            'number' => 1,
            'question_type' => QuestionTypeService::CHOICE_ANSWER_TYPE,
            'multi_answer_flg' => CpQuestionnaireService::SINGLE_ANSWER,
            'public' => 1
        );
        list($questionnaire_relation, $question, $choice_requirement, $choices) =
            $this->profile_helper->newProfileQuestionnaireByBrand($brand, $condition);

        $choices[2] = $this->entity('ProfileQuestionChoices',
            array(
                'question_id' => $question->id,
                'choice_num' => 3,
                'choice' => '選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3'
            )
        );

        $this->entities('ProfileQuestionChoiceAnswers', array(
            array(
                'choice_id' => $choices[0]->id,
                'questionnaires_questions_relation_id' => $questionnaire_relation->id,
                'brands_users_relation_id' => $relations[0]->id
            ),
            array(
                'choice_id' => $choices[0]->id,
                'questionnaires_questions_relation_id' => $questionnaire_relation->id,
                'brands_users_relation_id' => $relations[1]->id
            ),
            array(
                'choice_id' => $choices[0]->id,
                'questionnaires_questions_relation_id' => $questionnaire_relation->id,
                'brands_users_relation_id' => $relations[2]->id
            ),
        ));

        $result = $service->getDashboardInfo(
            DashboardService::DATE_SUMMARY,
            DashboardService::PROFILE_QUESTIONNAIRE_FAN_COUNT.'/'.$questionnaire_relation->id,
            '2015-04-30 00:00:00',
            '2015-05-02 23:59:59',
            3
        );
        $expect_result = array(
            1 => array(
                'cnt' => 3,
                'ratio' => 100,
                'summary_name' => 'テスト選択肢1',
                'name' => 'テスト選択肢1'
            ),
            2 => array(
                'cnt' => 0,
                'ratio' => 0,
                'summary_name' => 'テスト選択肢2',
                'name' => 'テスト選択肢2'
            ),
            3 => array(
                'cnt' => 0,
                'ratio' => 0,
                'summary_name' => '選択肢3選択肢3選択肢3選...',
                'name' => '選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3'
            ),
            '-1' => array(
                'cnt' => 0,
                'ratio' => 0,
                'summary_name' => '未回答',
                'name' => '未回答'
            ),
        );
        $this->assertEquals($expect_result, $result);
    }

    public function testGetProfileQuestionnaireInfo02_TwoAnswers() {
        $brand = $this->entity("Brands", array('enterprise_id' => 1, "created_at" => "2014-09-16 00:00:00"));
        $service = new DashboardService($brand);

        $user1 = $this->newUser();
        $user2 = $this->newUser();
        $user3 = $this->newUser();

        $relations = $this->entities('BrandsUsersRelations', array(
            array('user_id' => $user1->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-30 12:00:00'))),
            array('user_id' => $user2->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 12:00:00'))),
            array('user_id' => $user3->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-02 12:00:00'))),
        ));

        $condition = array(
            'number' => 1,
            'question_type' => QuestionTypeService::CHOICE_ANSWER_TYPE,
            'multi_answer_flg' => CpQuestionnaireService::SINGLE_ANSWER,
            'public' => 1
        );
        list($questionnaire_relation, $question, $choice_requirement, $choices) =
            $this->profile_helper->newProfileQuestionnaireByBrand($brand, $condition);

        $choices[2] = $this->entity('ProfileQuestionChoices',
            array(
                'question_id' => $question->id,
                'choice_num' => 3,
                'choice' => '選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3'
            )
        );

        $this->entities('ProfileQuestionChoiceAnswers', array(
            array(
                'choice_id' => $choices[0]->id,
                'questionnaires_questions_relation_id' => $questionnaire_relation->id,
                'brands_users_relation_id' => $relations[0]->id
            ),
            array(
                'choice_id' => $choices[0]->id,
                'questionnaires_questions_relation_id' => $questionnaire_relation->id,
                'brands_users_relation_id' => $relations[1]->id
            ),
            array(
                'choice_id' => $choices[1]->id,
                'questionnaires_questions_relation_id' => $questionnaire_relation->id,
                'brands_users_relation_id' => $relations[2]->id
            ),
        ));

        $result = $service->getDashboardInfo(
            DashboardService::DATE_SUMMARY,
            DashboardService::PROFILE_QUESTIONNAIRE_FAN_COUNT.'/'.$questionnaire_relation->id,
            '2015-04-30 00:00:00',
            '2015-05-02 23:59:59',
            3
        );

        $expect_result = array(
            1 => array(
                'cnt' => 2,
                'ratio' => 66.7,
                'summary_name' => 'テスト選択肢1',
                'name' => 'テスト選択肢1'
            ),
            2 => array(
                'cnt' => 1,
                'ratio' => 33.3,
                'summary_name' => 'テスト選択肢2',
                'name' => 'テスト選択肢2'
            ),
            3 => array(
                'cnt' => 0,
                'ratio' => 0,
                'summary_name' => '選択肢3選択肢3選択肢3選...',
                'name' => '選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3'
            ),
            '-1' => array(
                'cnt' => 0,
                'ratio' => 0,
                'summary_name' => '未回答',
                'name' => '未回答'
            ),
        );
        $this->assertEquals($expect_result, $result);
    }

    public function testGetProfileQuestionnaireInfo03_onlyNotAnswer() {
        $brand = $this->entity("Brands", array('enterprise_id' => 1, "created_at" => "2014-09-16 00:00:00"));
        $service = new DashboardService($brand);

        $user1 = $this->newUser();
        $user2 = $this->newUser();
        $user3 = $this->newUser();

        $relations = $this->entities('BrandsUsersRelations', array(
            array('user_id' => $user1->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-30 12:00:00'))),
            array('user_id' => $user2->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 12:00:00'))),
            array('user_id' => $user3->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-02 12:00:00'))),
        ));

        $condition = array(
            'number' => 1,
            'question_type' => QuestionTypeService::CHOICE_ANSWER_TYPE,
            'multi_answer_flg' => CpQuestionnaireService::SINGLE_ANSWER,
            'public' => 1
        );
        list($questionnaire_relation, $question, $choice_requirement, $choices) =
            $this->profile_helper->newProfileQuestionnaireByBrand($brand, $condition);

        $choices[2] = $this->entity('ProfileQuestionChoices',
            array(
                'question_id' => $question->id,
                'choice_num' => 3,
                'choice' => '選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3'
            )
        );

        $result = $service->getDashboardInfo(
            DashboardService::DATE_SUMMARY,
            DashboardService::PROFILE_QUESTIONNAIRE_FAN_COUNT.'/'.$questionnaire_relation->id,
            '2015-04-30 00:00:00',
            '2015-05-02 23:59:59',
            3
        );
        $expect_result = array(
            1 => array(
                'cnt' => 0,
                'ratio' => 0,
                'summary_name' => 'テスト選択肢1',
                'name' => 'テスト選択肢1'
            ),
            2 => array(
                'cnt' => 0,
                'ratio' => 0,
                'summary_name' => 'テスト選択肢2',
                'name' => 'テスト選択肢2'
            ),
            3 => array(
                'cnt' => 0,
                'ratio' => 0,
                'summary_name' => '選択肢3選択肢3選択肢3選...',
                'name' => '選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3'
            ),
            '-1' => array(
                'cnt' => 3,
                'ratio' => 100,
                'summary_name' => '未回答',
                'name' => '未回答'
            ),
        );
        $this->assertEquals($expect_result, $result);
    }

    public function testGetProfileQuestionnaireInfo04_TwoAnswersAndNotAnswer() {
        $brand = $this->entity("Brands", array('enterprise_id' => 1, "created_at" => "2014-09-16 00:00:00"));
        $service = new DashboardService($brand);

        $user1 = $this->newUser();
        $user2 = $this->newUser();
        $user3 = $this->newUser();

        $relations = $this->entities('BrandsUsersRelations', array(
            array('user_id' => $user1->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-04-30 12:00:00'))),
            array('user_id' => $user2->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-01 12:00:00'))),
            array('user_id' => $user3->id,
                'brand_id' => $brand->id,
                'created_at' => date('Y-m-d H:i:s', strtotime('2015-05-02 12:00:00'))),
        ));

        $condition = array(
            'number' => 1,
            'question_type' => QuestionTypeService::CHOICE_ANSWER_TYPE,
            'multi_answer_flg' => CpQuestionnaireService::SINGLE_ANSWER,
            'public' => 1
        );
        list($questionnaire_relation, $question, $choice_requirement, $choices) =
            $this->profile_helper->newProfileQuestionnaireByBrand($brand, $condition);

        $choices[2] = $this->entity('ProfileQuestionChoices',
            array(
                'question_id' => $question->id,
                'choice_num' => 3,
                'choice' => '選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3'
            )
        );

        $this->entities('ProfileQuestionChoiceAnswers', array(
            array(
                'choice_id' => $choices[0]->id,
                'questionnaires_questions_relation_id' => $questionnaire_relation->id,
                'brands_users_relation_id' => $relations[0]->id
            ),
            array(
                'choice_id' => $choices[1]->id,
                'questionnaires_questions_relation_id' => $questionnaire_relation->id,
                'brands_users_relation_id' => $relations[2]->id
            ),
        ));

        $result = $service->getDashboardInfo(
            DashboardService::DATE_SUMMARY,
            DashboardService::PROFILE_QUESTIONNAIRE_FAN_COUNT.'/'.$questionnaire_relation->id,
            '2015-04-30 00:00:00',
            '2015-05-02 23:59:59',
            3
        );
        $expect_result = array(
            1 => array(
                'cnt' => 1,
                'ratio' => 33.3,
                'summary_name' => 'テスト選択肢1',
                'name' => 'テスト選択肢1'
            ),
            2 => array(
                'cnt' => 1,
                'ratio' => 33.3,
                'summary_name' => 'テスト選択肢2',
                'name' => 'テスト選択肢2'
            ),
            3 => array(
                'cnt' => 0,
                'ratio' => 0,
                'summary_name' => '選択肢3選択肢3選択肢3選...',
                'name' => '選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3選択肢3'
            ),
            '-1' => array(
                'cnt' => 1,
                'ratio' => 33.3,
                'summary_name' => '未回答',
                'name' => '未回答'
            ),
        );
        $this->assertEquals($expect_result, $result);
    }

}