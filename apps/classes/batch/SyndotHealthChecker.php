<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpInstantWinActionManager');
AAFW::import('jp.aainc.classes.services.instant_win.InstantWinPrizeService');

/*
 * 現在公開中のSyn.キャンペーンのhealthCheck
 * 管理画面からは挿入できないため、DBに直接値を入れなければならないところがある
 * その部分が勝手に更新されたことを検知する
 */
class SyndotHealthChecker extends BrandcoBatchBase{

    const ATTRIBUTE_SYN_MENU_CLASS = "class=\"jsClickMenu\""; //連続チャレンジのAPIを叩くトリガーになるHTMLのclass名

    function executeProcess() {

        $cp = $this->findCurrentSynCp();
        if(!$cp){
            $warnMessage = "syndotのredirectionが設定されていません！確認してください";
            $this->hipchat_logger->warn($warnMessage);
            return;
        }

        $cpInstantWinPrizeStatusStay = $this->findCpInstantWinPrizeStatusStay($cp->id);

        if(!$this->isExistsAttributeSynMenuId($cpInstantWinPrizeStatusStay->html_content)){
            $warnMessage = "instant_win_prizes : id = ".$cpInstantWinPrizeStatusStay->id
                ."\nhtml_contentに必要な情報が含まれてません"
                ."\naタグに" .self::ATTRIBUTE_SYN_MENU_CLASS ."が含まれてるか確認してください";
            $this->hipchat_logger->warn($warnMessage);
        }
    }

    /**
     * @param $htmlContent
     * @return bool
     */
    public function isExistsAttributeSynMenuId($htmlContent){
        return strpos($htmlContent, self::ATTRIBUTE_SYN_MENU_CLASS) !== false;
    }

    /**
     * @param $cpId
     * @return InstantWinPrize
     */
    public function findCpInstantWinPrizeStatusStay($cpId){
        /** @var CpFlowService $cpFlowService */
        $cpFlowService = $this->service_factory->create('CpFlowService');
        $cpAction = $cpFlowService->getCpActionsByCpIdAndActionType($cpId,CpAction::TYPE_INSTANT_WIN)[0];

        /** @var CpInstantWinActionManager $cpInstantWinActionManager */
        $cpInstantWinActionManager = $this->service_factory->create('CpInstantWinActionManager');
        $cpInstantWinAction = $cpInstantWinActionManager->getConcreteAction($cpAction);

        /** @var InstantWinPrizeService $instantWinPrizeService */
        $instantWinPrizeService =$this->service_factory->create('InstantWinPrizeService');
        $cpInstantWinPrize = $instantWinPrizeService->getInstantWinPrizeByPrizeStatus($cpInstantWinAction->id,InstantWinPrizes::PRIZE_STATUS_STAY);

        return $cpInstantWinPrize;
    }

    /**
     * @return Cp
     */
    public function findCurrentSynCp() {
        $now = date("Y-m-d H:i:s", time());
        $synCpRedirectDuration = aafwEntityStoreFactory::create('SynCpRedirectDurations')->findOne(
            array('start_at:<=' => $now, 'end_at:>' => $now)
        );
        if( !$synCpRedirectDuration ) {
            return null;
        }
        $synCp = $synCpRedirectDuration->getSynCp();
        return $synCp->getCp();
    }
}