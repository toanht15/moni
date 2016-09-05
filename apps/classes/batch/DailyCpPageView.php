<?php
AAFW::import('jp.aainc.classes.batch.GetCpPageViewBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

/**
 * Googleアナリティクスから日別のキャンペーンのページビュー数情報を取得する
 * Class DailyCpPageView
 */
class DailyCpPageView extends GetCpPageViewBase {

    /**
     * 日別の対象キャンペーン
     * @return mixed
     */
    public function getTargetCps() {
        $yesterday = date("Y-m-d", strtotime("-1 day"));

        $conditions = array(
            "begin_date" => self::CP_START_DATE_BEGIN,
            "start_date" => $yesterday
        );

        $order = array(
            "name"      => "start_date",
            "direction" => "ASC"
        );

        $db = aafwDataBuilder::newBuilder();
        $target_cps = $db->getDailyCpPageViewTarget($conditions, $order, array(), false, "Cp");

        return $target_cps;
    }

}