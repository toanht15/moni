<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

class BrandcoTopMainTag extends aafwWidgetBase {

    public function doService($params = array()){
        $params['page_settings'] = BrandInfoContainer::getInstance()->getBrandPageSetting();
        return $params;
    }
}