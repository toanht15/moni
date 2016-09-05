<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class OpeningCpPaymentActionForm extends aafwWidgetBase {

    public function doService($params = array()) {

        // 決済モジュールに必要な情報を表示する
        $params['product'] = $this->getModel('Products')->findOne(array('cp_action_id' => $params['cp_action']->id));
        $params['product_item'] = $params['product']->getProductItem();

        return $params;
    }
}