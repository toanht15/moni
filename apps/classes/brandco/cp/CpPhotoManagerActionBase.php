<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.entities.PhotoUser');
AAFW::import('jp.aainc.classes.entities.PhotoEntry');

abstract class CpPhotoManagerActionBase extends BrandcoPOSTActionBase {
    protected $ContainerName = 'photo_campaign';

    protected $photo_approval_status;
    protected $photo_top_status;

    protected $logger;
    protected $photo_user_service;
    protected $photo_stream_service;
    protected $photo_entry_transaction;

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function doThisFirst() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->photo_user_service = $this->createService('PhotoUserService');
        $this->photo_stream_service = $this->createSErvice('PhotoStreamService');
        $this->photo_entry_transaction = aafwEntityStoreFactory::create('PhotoEntries');
    }

    public function validate() {
        return true;
    }

    /**
     * @param $photo_user_id
     * @throws Exception
     */
    public function updatePhotoCampaign($photo_user_id) {
        try {
            $this->photo_entry_transaction->begin();

            $photo_entry = $this->photo_stream_service->getPhotoEntryByPhotoUserId($photo_user_id);
            $photo_user = $this->photo_user_service->getPhotoUserById($photo_user_id);

            if ($photo_user->approval_status != $this->photo_approval_status) {
                $photo_user->approval_status = $this->photo_approval_status;
                $this->photo_user_service->updatePhotoUser($photo_user);
            }

            if ($this->photo_top_status != $photo_entry->hidden_flg) {
                $cache_manager = new CacheManager();
                $cache_manager->deletePanelCache($this->getBrand()->id);

                if ($photo_entry->priority_flg) {
                    $panel_service = $this->createService('TopPanelService');
                } else {
                    $panel_service = $this->createService('NormalPanelService');
                }

                if ($this->photo_top_status == PhotoEntry::TOP_STATUS_AVAILABLE) {
                    $panel_service->addEntry($this->getBrand(), $photo_entry);
                } else {
                    $panel_service->deleteEntry($this->getBrand(), $photo_entry);
                }
            }

            $this->photo_entry_transaction->commit();
        } catch (Exception $e) {
            $this->photo_entry_transaction->rollback();
            throw $e;
        }
    }
}