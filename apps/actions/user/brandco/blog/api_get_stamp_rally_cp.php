<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.services.StaticHtmlStampRallyService');

class api_get_stamp_rally_cp extends BrandcoPOSTActionBase {

    public $NeedOption = array();
    
    protected $ContainerName = 'api_get_stamp_rally_cp';
    protected $AllowContent = array('JSON');
    public $CsrfProtect = true;

    private $cpCount;
    private $cpIds;
    private $stampStatusComingSoonImage;
    private $currentActiveCp;

    private $parser;

    public function doThisFirst() {
        $this->cpCount = $this->POST['cp_count'];
        $this->cpIds = $this->POST['cp_ids'];
        $this->stampStatusComingSoonImage = $this->POST['stamp_status_coming_soon_image'];

        $this->parser = new PHPParser();
    }

    public function validate() {

        if(!$this->cpCount || !is_numeric($this->cpCount)){
            return false;
        }

        if(!$this->cpIds){
            return false;
        }

        return true;
    }

    function doAction() {
        $responseContent = $this->createResponseContent();
        $this->assign('json_data', $responseContent);
        return 'dummy.php';
    }

    private function createResponseContent(){

        $cpIds = json_decode($this->cpIds);

        $html = "";

        sort($cpIds);

        $listCpHasStartAndEndDate = $this->getListCpHasStartAndEndDate($cpIds);

        $activedCps = $listCpHasStartAndEndDate['activedCps'];
        $activedCps = $this->sortCpByEndDate($activedCps);

        $notActivedCps = $listCpHasStartAndEndDate['notActivedCps'];
        $notActivedCps = $this->sortCpByEndDate($notActivedCps);

        $html .= $this->createActivedCpItemList($activedCps);

        $html .= $this->createNotActivedCpItemList($notActivedCps);

        $prepareCpCount = $this->cpCount-count($activedCps)-count($notActivedCps);
        $html .= $this->createPrepareCpItemList($prepareCpCount);

        $brandGlobalSettingService = $this->getService('BrandGlobalSettingService');
        $showCurrentStampRallyCp = $brandGlobalSettingService->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::SHOW_CURRENT_STAMP_RALLY_CP);

        if (!Util::isNullOrEmpty($showCurrentStampRallyCp) && $this->currentActiveCp) {
            $data = array('cur_active_cp_url' => $this->currentActiveCp->getUrl());
        }

        $responseContent = $this->createAjaxResponse("ok", $data, array(), $html);

        return $responseContent;
    }

    private function getListCpHasStartAndEndDate($cpIds){

        $activedCps = array();
        $notActivedCps = array();

        foreach($cpIds as $cpId){
            $cp = CpInfoContainer::getInstance()->getCpById($cpId);
            if($cp->isActivedCp()){
                $activedCps[] = $cp;
            } elseif($cp->hasStartEndDate()){
                $notActivedCps[] = $cp;
            }
        }

        $cps = array(
            'activedCps' => $activedCps,
            'notActivedCps' => $notActivedCps
        );

        return $cps;
    }

    private function sortCpByEndDate($cps){

        usort($cps, function($cp1,$cp2){
            return strtotime($cp1->start_date) > strtotime($cp2->start_date);
        });

        return $cps;
    }

    private function createPrepareCpItemList($prepareItemCount){

        $html = '';

        for($i = 0; $i < $prepareItemCount; $i++){
            $html .= $this->parser->parseTemplate('StampRallyCpItem.php',array(
                'stamp_status' => StaticHtmlStampRallyService::STAMP_STATUS_COMING_SOON,
                'cp_image' => $this->stampStatusComingSoonImage
            ));
        }

        return $html;
    }

    private function createNotActivedCpItemList($cps){

        $html = '';

        foreach($cps as $cp){

            $endDate = new DateTime($cp->end_date);

            $startDate = new DateTime($cp->start_date);
            
            $html .= $this->parser->parseTemplate('StampRallyCpItem.php',array(
                'stamp_status' => StaticHtmlStampRallyService::STAMP_STATUS_COMING_SOON,
                'cp_image' => $this->stampStatusComingSoonImage,
                'start_date' => $startDate,
                'end_date' => $endDate
            ));
        }

        return $html;
    }

    private function createActivedCpItemList($cps){
        /** @var CpUserService $cp_user_service */
        $cp_user_service = $this->getService('CpUserService');

        $html = '';
        $user_id = $this->getSession('pl_monipla_userId');

        foreach($cps as $cp){

            if ($this->isLogin() && $cp_user_service->isJoinFinish($cp->id, $user_id)) {

                $html .= $this->createActivedCpItem($cp,StaticHtmlStampRallyService::STAMP_STATUS_JOINED);

            }else{

                if ($cp->canEntry()) {

                    if (!$this->currentActiveCp || strtotime($this->currentActiveCp->public_date) > strtotime($cp->public_date)) {
                        $this->currentActiveCp = $cp;
                    }

                    $html .= $this->createActivedCpItem($cp,StaticHtmlStampRallyService::STAMP_STATUS_OPENING);

                } elseif($cp->getStatus() == Cp::CAMPAIGN_STATUS_CP_PAGE_CLOSED) {

                    $html .= $this->createActivedCpItem($cp,StaticHtmlStampRallyService::STAMP_STATUS_CLOSED);

                } else{

                    $html .= $this->createActivedCpItem($cp,StaticHtmlStampRallyService::STAMP_STATUS_FINISHED);
                }
            }

        }

        return $html;
    }

    private function createActivedCpItem($cp, $status){

        $endDate = new DateTime($cp->end_date);
        $startDate = new DateTime($cp->start_date);

        $html = $this->parser->parseTemplate('StampRallyCpItem.php', array(
            'stamp_status' => $status,
            'cp_image' => $cp->getCpRectangleImage(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'cp_url' => $cp->getUrl()
        ));

        return $html;
    }
}