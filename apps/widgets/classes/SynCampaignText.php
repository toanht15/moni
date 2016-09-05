<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.services.CpFlowService');
AAFW::import('jp.aainc.classes.services.instant_win.SynInstantWinService');

class SynCampaignText extends aafwWidgetBase {

    public function doService($params = array()) {
        /** @var Cp $cp */
        $cp = $params['cp'];
        $returnParams = array();
        $returnParams['isForSyndotOnly'] = $cp->isForSyndotOnly();
        if( $returnParams['isForSyndotOnly'] ){
            $returnParams['electedCount'] = $this->getElectedCount($cp->id);
        }
        return $returnParams;
    }

    public function getElectedCount($cpId){
        /** @var $serviceFactory aafwServiceFactory */
        $serviceFactory = new aafwServiceFactory();
        /** @var CpUserService $cpUserService */
        $cpUserService = $serviceFactory->create('CpUserService');
        return $cpUserService->getFinishActionCount($this->searchAnnounceCpActionId($cpId));
    }

    public function searchAnnounceCpActionId($cpId){
        $cpFlowService = new CpFlowService();
        list($announceActionId) = $cpFlowService->getCpActionIdsByCpIdAndType($cpId,CpAction::TYPE_ANNOUNCE);
        return $announceActionId;
    }
}
