<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.vendor.cores.MoniplaCore');

class CriteoTracker extends aafwWidgetBase {

    public function doService($params = array()) {

        if( !is_numeric($params['platform_user_id']) || $params['platform_user_id'] <= 0 ) {
            return array();
        }

        $returnParam = array();
        $userInfo = $this->getPlatformUserInfo($params['platform_user_id']);
        if( $userInfo->result->status === Thrift_APIStatus::SUCCESS && $userInfo->mailAddress ) {
            $returnParam['md5MailAddress'] = Hash::doHashMd5Email($userInfo->mailAddress);
        }
        return $returnParam;
    }

    public function getPlatformUserInfo($alliedId){
        return $this->getBrandAuthService()->getUserInfoByQuery($alliedId);
    }

    /**
     * @return BrandcoAuthService
     */
    public function getBrandAuthService() {
        return $this->getService('BrandcoAuthService');
    }

}
