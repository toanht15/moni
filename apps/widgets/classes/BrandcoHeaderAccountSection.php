<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

class BrandcoHeaderAccountSection extends aafwWidgetBase {

    public function doService($params = array()) {
        $contract = BrandInfoContainer::getInstance()->getBrandContract();
        $params['is_closed_brand'] = $params['brand']->isClosedBrand($contract) && !$params['manager']->id;
        return $params;
    }
}