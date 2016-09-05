<?php
/**
 * Created by PhpStorm.
 * User: katoriyusuke
 * Date: 15/09/01
 * Time: 15:52
 */

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.validator.user.CampaignPageValidator');
AAFW::import('jp.aainc.lib.db.aafwRedisManager');
AAFW::import('jp.aainc.classes.CpLPInfoContainer');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

class closed_campaigns extends BrandcoGETActionBase {

    public $NeedOption   = array(BrandOptions::OPTION_CP);
    public $Secure       = false;
    public $NeedRedirect = true;

    public function doThisFirst() {
        $this->Data['cp_id'] = $this->GET['exts'][0];
    }

    public function beforeValidate() {
        $cpFlowService = $this->createService('CpFlowService');
        $this->Data['cp']       = $cpFlowService->getCpById($this->Data['cp_id']);
        $this->Data['userInfo'] = $this->getBrandsUsersRelation() ? $this->getBrandsUsersRelation()->getUser() : null;
    }

    public function validate() {
        $validator = new CampaignPageValidator($this->Data['cp_id'], $this->Data['userInfo'], $this->getBrand()->id, $this->Data['cp']);
        $validator->validate();

        return $validator->isValid() ? true : '404';
    }

    public function doAction() {
        $container = new CpLPInfoContainer();
        $lpInfo = $container->getCpLPInfo($this->Data["cp"], BrandInfoContainer::getInstance()->getBrand());
        $ogInfo = $lpInfo[CpLPInfoContainer::KEY_OG_INFO];
        $this->Data['pageStatus']['og'] = array(
            'url'         => $ogInfo['url'],
            'image'       => $ogInfo['image'],
            'title'       => $ogInfo['title'],
            'description' => $ogInfo['description'],
        );
        $this->Data['brand'] = $this->brand;

        return 'user/brandco/campaigns/closed_campaigns.php';
    }
}