<?php
/**
 * Created by PhpStorm.
 * User: katoriyusuke
 * Date: 15/09/01
 * Time: 12:24
 */

require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.CacheManager');

class AutoUpdateCpStatusToClosed {

    public $logger;
    public $hipchatLogger;
    public $serviceFactory;

    public function __construct() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchatLogger = aafwLog4phpLogger::getHipChatLogger();
        $this->serviceFactory = new aafwServiceFactory();
    }

    public function doProcess() {
        $cpFlowService     = $this->serviceFactory->create('CpFlowService');
        $linkEntryService  = $this->serviceFactory->create("LinkEntryService");
        $nomalPanelService = $this->serviceFactory->create("NormalPanelService");
        $topPanelService   = $this->serviceFactory->create("TopPanelService");

        //クロースするキャンペーンの取得
        $cpPageClosedCps = $cpFlowService->getCpPageClosingCps();
        $cacheManager = new CacheManager();

        foreach($cpPageClosedCps as $closedCp) {
            $cpStore = aafwEntityStoreFactory::create('Cps');

            try {
                $cpStore->begin();

                //キャンペーンステータスをクローズに更新
                $closedCp->status = Cp::STATUS_CLOSE;
                //クローズしたキャンペーン画像の差し替え
                $closedCp->image_url = Cp::getClosedCampaignImage();
                $closedCp->image_rectangle_url = Cp::getClosedCampaignImage();

                $brand = $closedCp->getBrand();
                $linkEntry = $linkEntryService->getEntryByBrandIdAndPageUrl($brand->id, $closedCp->getUrl());

                //クローズしたキャンペーンのパネルを削除
                if ($linkEntry) {
                    $cacheManager->deletePanelCache($brand->id);
                    //対象のパネル削除
                    if (!$nomalPanelService->deleteEntry($brand, $linkEntry)) {
                        $topPanelService->deleteEntry($brand, $linkEntry);
                    }
                    $topPanelService->deleteLogicalEntry($linkEntry);
                }

                $cpFlowService->updateCp($closedCp);

                $cpStore->commit();

            } catch (Exception $e) {
                $cpStore->rollback();
                $this->logger->error('AutoUpdateCpStatusToClosed Error. $cp_id = ' . $closedCp->id);
                $this->logger->error($e);
                $this->hipchat_logger->error('AutoUpdateCpStatusToClosed Error. $cp_id = ' . $closedCp->id);
                continue;
            }

        }
    }
}