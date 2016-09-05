<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class r extends BrandcoGETActionBase {

    public $NeedOption = array();

    public function validate () {
        return true;
    }

    function doAction() {

        /** @var RedirectorService $redirector_service */
        $redirector_service = $this->createService('RedirectorService');

        $user_id = null;
        if($brands_users_relation = $this->getBrandsUsersRelation()) {
            $user_id = $brands_users_relation->user_id;
        }

        $url = $redirector_service->redirectTo($this->GET['exts'][0], $this->getBrand()->id, $user_id);

        if(!$url) {
            return '404';
        }

        return 'redirect :'.$url;
    }
}
