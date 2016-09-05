<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class public_cps extends BrandcoGETActionBase{

    protected $ContainerName = 'public_cps';

    public $NeedOption = array(BrandOptions::OPTION_CP, BrandOptions::OPTION_CRM);
    public $NeedAdminLogin = true;
    private $pageLimited = 15;
    private $cp_flow_service;

    public function validate() {
        $brand = $this->getBrand();

        /** @var CpFlowService $cp_flow_service */
        $this->cp_flow_service = $this->createService('CpFlowService');

        if ($this->archive) {
            $archive = Cp::ARCHIVE_ON;
        } else {
            $archive = Cp::ARCHIVE_OFF;
        }

        if ($this->isValidType($this->type)) {
            $type = $this->type;
        } else {
            return 'redirect: ' . Util::rewriteUrl('admin-cp', 'public_cps', array(), array('type' => Cp::TYPE_CAMPAIGN));
        }

        $this->Data['totalEntriesCount'] = $this->cp_flow_service->getCpsNotDraftCountByBrandId($brand->id, $type, $archive);
        $total_page = floor ( $this->Data['totalEntriesCount'] / $this->pageLimited ) + ( $this->Data['totalEntriesCount'] % $this->pageLimited > 0 );
        $this->p = Util::getCorrectPaging($this->p, $total_page);

        $this->Data['type'] = $this->type;
        $public_cps = $this->cp_flow_service->getCpsNotDraftByBrandIdAndArchiveFlg($brand->id, $type, $this->p, $this->pageLimited, $archive);
        $this->Data['pageLimited'] = $this->pageLimited;
        
        /** @var CpListService $cp_list_service */
        $cp_list_service = $this->createService('CpListService');
        $cp_ids = array();
        foreach($public_cps as $cp) {
            $cp_ids[] = $cp->id;
        }
        if($cp_ids) {
            $this->Data['cps'] = $cp_list_service->getListPublicCp($cp_ids);
        }

        return true;
    }

    function doAction() {
        return 'user/brandco/admin-cp/public_cps.php';
    }

    private function isValidType($type) {
        if ($type == Cp::TYPE_CAMPAIGN) {
            return $this->getBrand()->hasOption(BrandOptions::OPTION_CP, BrandInfoContainer::getInstance()->getBrandOptions());
        } else if ($type == Cp::TYPE_MESSAGE) {
            return $this->getBrand()->hasOption(BrandOptions::OPTION_CRM, BrandInfoContainer::getInstance()->getBrandOptions());
        }

        return false;
    }
}