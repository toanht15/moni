<?php

AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class CpActionModuleDeadLine extends aafwWidgetBase {

    public function doService($params = array()) {

        // キャンペーン終了日
        $cp = $params['cp_action']->getCp();
        $params['is_permanent'] = $cp->isPermanent();
        if ($params['is_permanent']) {
            return $params;
        }

        if ($cp->end_date != '0000-00-00 00:00:00') {
            $cp_end_date = date_create($cp->end_date);
            $params['cp_end_date'] = $cp_end_date->format('Y/m/d H:i');
        } else {
            if ($cp->isCpTypeMessage()) {
                $params['cp_end_date'] = '※無期限';
            } else {
                $params['cp_end_date'] = '※キャンペーン形式の場合のみ';
            }
        }

        // 締め切り日(タイプ)
        // 未設定の場合は、デフォルト値を取得する
        $params['end_type'] = $params['cp_action']->getEndType();

        // 締め切り日(日付)
        if ($params['cp_action']->end_type == CpAction::END_TYPE_ORIGINAL) {
            $end_date = new Datetime($params['cp_action']->end_at);
            if (!array_key_exists('end_date', $params['ActionForm'])) {
                $params['ActionForm']['end_date'] = $end_date->format('Y/m/d');
            }
            if (!array_key_exists('end_hh', $params['ActionForm'])) {
                $params['ActionForm']['end_hh'] = $end_date->format('H');
            }
            if (!array_key_exists('end_mm', $params['ActionForm'])) {
                $params['ActionForm']['end_mm'] = $end_date->format('i');
            }
        }

        return $params;
    }
}
