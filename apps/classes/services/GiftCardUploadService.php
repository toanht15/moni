<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.entities.GiftCardUpload');

class GiftCardUploadService extends aafwServiceBase {

    protected $gift_card_uploads;

    public function __construct() {
        $this->gift_card_uploads = $this->getModel('GiftCardUploads');
    }

    /**
     * グリーティングカードの画像を取得
     * @param $gift_card_config_id
     * @return array
     */
    public function getGiftCardUploads($gift_card_config_id) {
        if (!$gift_card_config_id) return null;
        $filter = array(
            'conditions' => array(
                'gift_card_config_id' => $gift_card_config_id,
            ),
        );
        return $this->gift_card_uploads->find($filter);
    }

    /**
     * IDより画像を取得する
     * @param $gift_card_upload_id
     * @return mixed
     */
    public function getGiftCardUploadById($gift_card_upload_id) {
        if (!$gift_card_upload_id) return null;
        $filter = array(
            'conditions' => array(
                'id' => $gift_card_upload_id,
            ),
        );
        return $this->gift_card_uploads->findOne($filter);
    }

    public function validCardUpload($gift_card_config_id, $image_url) {
        $isValid = false;
        $gift_card_upload = $this->getGiftCardUploads($gift_card_config_id);
        foreach ($gift_card_upload as $element) {
            $isValid = ($element->image_url == $image_url) ?: $isValid;
        }
        return $isValid;
    }
    /**
     * 画像をアップロードする
     * @param $gift_card_config_id
     * @param $image_url
     * @return mixed
     */
    public function createGiftCardUpload($gift_card_config_id, $image_url) {
        $gift_card_config                       = $this->createEmptyGiftCardUploadData();
        $gift_card_config->gift_card_config_id  = $gift_card_config_id;
        $gift_card_config->image_url            = $image_url;
        return $this->saveGiftCardUploadData($gift_card_config);
    }

    /**
     * グリーティングカードの画像を削除する
     * @param $gift_card_upload_id
     */
    public function deleteGiftCardUpload($gift_card_upload_id) {
        $gift_card_upload = $this->getGiftCardUploadById($gift_card_upload_id);
        if ($gift_card_upload) {
            $this->gift_card_uploads->delete($gift_card_upload);
        }
    }


    public function createEmptyGiftCardUploadData() {
        return $this->gift_card_uploads->createEmptyObject();
    }

    public function saveGiftCardUploadData($gift_card_upload_data) {
        return $this->gift_card_uploads->save($gift_card_upload_data);
    }

}

