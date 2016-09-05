<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class StampRallyCpList extends aafwWidgetBase {

    public function doService($params = array()) {

        $service_factory = new aafwServiceFactory();

        /** @var StaticHtmlStampRallyService $staticHtmlStampRallyService */
        $params['staticHtmlStampRallyService'] = $service_factory->create('StaticHtmlStampRallyService');

        return $params;
    }
}
