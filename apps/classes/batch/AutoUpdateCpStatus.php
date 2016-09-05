<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.CacheManager');

class AutoUpdateCpStatus {

    public $logger;
    public $hipchat_logger;

    /** @var aafwServiceFactory $service_factory */
    public $service_factory;
    /** @var CpFlowService $cp_flow_service */
    public $cp_flow_service;
    /** @var CpInstagramHashtagActionService $cp_instagram_hashtag_action_service */
    public $cp_instagram_hashtag_action_service;

    public function __construct() {
        $this->loadLogger();
        $this->loadService();
    }

    public function doProcess() {
        $this->logger->info("AutoUpdateCpStatus#before_getCpsForBatch");
        $cps = $this->cp_flow_service->getCpsForBatch();
        $this->logger->info("AutoUpdateCpStatus#after_getCpsForBatch");

        $updated_cps = [];

        // 公開遅延を防ぐため、１分以内に終了しなければならない処理
        foreach ($cps as $cp) {
            if ($cp->status == Cp::STATUS_SCHEDULE) {
                if ($this->updateCpStatus($cp) && $cp->send_mail_flg == Cp::FLAG_SHOW_VALUE) {
                    // 公開時にメール送信するCPは、全てのCP公開処理が終わってから実施する
                    $updated_cps[] = $cp;
                }
            }
        }

        foreach ($updated_cps as $cp) {
            // cpを再取得して公開を確認する
            if ($this->cp_flow_service->getCpById($cp->id)->status == Cp::STATUS_FIX) {
                $this->insertMessageDelivery($cp);
            }
        }
    }


    /**
     * ロガーを読み込む
     */
    public function loadLogger() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    /**
     * サービスを読み込む
     */
    public function loadService() {
        $this->service_factory = new aafwServiceFactory();

        $this->cp_flow_service = $this->service_factory->create('CpFlowService');
        $this->cp_instagram_hashtag_action_service = $this->service_factory->create('CpInstagramHashtagActionService');
    }

    /**
     * @param $cp
     * @param $link_entry
     */
    private function addLinkToTop($cp, $link_entry) {

        try {
            $this->addLinkEntryToTop($cp, $link_entry);
        } catch (Exception $e) {
            $this->logger->error('AutoUpdateCpStatus addLinkEntryToTop Error. $cp_id = ' . $cp->id);
            $this->logger->error($e);
        }
    }

    /**
     * @param $date
     * @return bool
     */
    private function isPastDate($date) {
        return (strtotime($date) > time()) ? false : true;
    }


    /**
     * @param Cp $cp
     * @return mixed
     */
    public function createCpLinkEntry(Cp $cp) {
        /** @var LinkEntryService $link_entry_service */
        $link_entry_service = $this->service_factory->create("LinkEntryService");
        $link_entry = $link_entry_service->createEmptyEntry();
        $link_entry->brand_id = $cp->brand_id;
        $link_entry->title = "キャンペーン";
        $link_entry->body = $cp->getTitle();
        $link_entry->link = $cp->getUrl();
        $link_entry->image_url = $cp->getIcon();
        $link_entry->priority_flg = 1;
        $link_entry->created_at = date('Y-m-d H:i:s');
        $link_entry->pub_date = date('Y-m-d H:i:s');
        $link_entry_service->createEntry($link_entry);
        return $link_entry;
    }

    /**
     * @param Cp $cp
     * @param LinkEntry $link_entry
     */
    public function addLinkEntryToTop(Cp $cp, LinkEntry $link_entry) {
        /** @var TopPanelService $top_panel_service */
        $top_panel_service = $this->service_factory->create('TopPanelService');
        $top_panel_service->fixEntry($cp->getBrand(), $link_entry);
    }

    /**
     * @param $cp
     * @param $cp_action_id
     */
    public function createMessageDeliveryAndTarget($cp, $cp_action_id) {
        /** @var  CpMessageDeliveryService $message_delivery_service */
        $message_delivery_service = $this->service_factory->create('CpMessageDeliveryService');
        $reservation = $message_delivery_service->createReservation(
            $cp->brand_id,
            $cp_action_id,
            CpMessageDeliveryReservation::TYPE_ALL,
            $cp->start_date,
            null,
            null
        );
        $reservation->status = CpMessageDeliveryReservation::STATUS_SCHEDULED;
        $message_delivery_service->updateCpMessageDeliveryReservation($reservation);
    }

    /**
     * @param $cp
     * @return bool
     */
    public function updateCpStatus($cp) {
        $cp_store = aafwEntityStoreFactory::create('Cps');
        $link_entry = null;

        try {
            $cp_store->begin();

            // 公開日時を超えているかどうかチェック (保険)
            if (!$this->isPastDate($cp->public_date)) {
                return false;
            }

            // ステータスの変更
            $cp->status = Cp::STATUS_FIX;
            $this->cp_flow_service->updateCp($cp);

            // キャンペーンのリンクを作成し、パネルに表示
            if ($cp->show_top_page_flg == Cp::FLAG_SHOW_VALUE) {
                $link_entry = $this->createCpLinkEntry($cp);
                $this->logger->info("Created Link Entry entry_id = " . $link_entry->id);

                // キャッシュの削除
                $cache_manager = new CacheManager();
                $cache_manager->deletePanelCache($cp->brand_id);

            }
            $this->cp_instagram_hashtag_action_service->initializeInstagramHashtagByCpId($cp->id);

            $cp_store->commit();
        } catch (Exception $e) {
            $cp_store->rollback();
            $this->logger->error('AutoUpdateCpStatus Error. $cp_id = ' . $cp->id);
            $this->logger->error($e);
            $this->hipchat_logger->error('AutoUpdateCpStatus Error. $cp_id = ' . $cp->id);

            return false;
        }

        try {
            // トップパネルに移動（別トランザクション）
            // 失敗したらログにエラーを残す
            if ($link_entry) {
                $this->addLinkToTop($cp, $link_entry);
            }
        } catch (Exception $e) {
            $this->logger->error('AutoUpdateCpStatus#addLinkToTop Error. $cp_id = ' . $cp->id);
            $this->logger->error($e);
            $this->hipchat_logger->error('AutoUpdateCpStatus#addLinkToTop Error. $cp_id = ' . $cp->id);
        }

        return true;
    }

    public function insertMessageDelivery($cp) {
        $this->logger->warn('AutoUpdateCpStatus#insertMessageDelivery start (cp_id = ' . $cp->id. ')');
        $cp_store = aafwEntityStoreFactory::create('Cps');

        try {
            $cp_store->begin();

            $entry_action = $this->cp_flow_service->getFirstActionOfCp($cp->id);
            $this->createMessageDeliveryAndTarget($cp, $entry_action->id);
            $this->hipchat_logger->info("AutoUpdateCpStatus#createMessageDeliveryAndTarget Success brand_id = " . $cp->brand_id . ' entry_id = ' . $entry_action->id);
            $this->logger->info("AutoUpdateCpStatus#createMessageDeliveryAndTarget Success brand_id = " . $cp->brand_id . ' entry_id = ' . $entry_action->id);

            $cp_store->commit();

        } catch (Exception $e) {
            $cp_store->rollback();
            $this->logger->error('AutoUpdateCpStatus#sendMail Error. $cp_id = ' . $cp->id);
            $this->logger->error($e);
            $this->hipchat_logger->error('AutoUpdateCpStatus#sendMail Error. $cp_id = ' . $cp->id);
        }

        $this->logger->warn('AutoUpdateCpStatus#insertMessageDelivery end (cp_id = ' . $cp->id. ')');
    }
} 