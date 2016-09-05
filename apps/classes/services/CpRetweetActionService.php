<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.entities.CpRetweetAction');
AAFW::import('jp.aainc.classes.entities.RetweetPhotoConfig');

class CpRetweetActionService extends aafwServiceBase {
    protected $cp_retweet_actions;
    protected $retweet_photo_configs;

    public function __construct() {
        $this->cp_retweet_actions = $this->getModel('CpRetweetActions');
        $this->retweet_photo_configs = $this->getModel('RetweetPhotoConfigs');
    }

    /**
     * ツイートアクション取得
     * @param $cp_action_id
     * @return mixed
     */
    public function getCpRetweetAction($cp_action_id) {
        $filter = array(
            'conditions' => array(
                'cp_action_id' => $cp_action_id,
            ),
        );
        return $this->cp_retweet_actions->findOne($filter);
    }

    /**
     * ツイートアクション取得
     * @param $cp_retweet_action_id
     * @return mixed
     */
    public function getCpRetweetActionById($cp_retweet_action_id) {
        $filter = array(
            'id' => $cp_retweet_action_id,
        );
        return $this->cp_retweet_actions->findOne($filter);
    }

    /**
     * @param $cp_retweet_action_id
     * @return null
     */
    public function getRetweetPhotoConfig($cp_retweet_action_id) {
        if (!$cp_retweet_action_id) return null;
        $filter = array(
            'conditions' => array(
                'cp_retweet_action_id' => $cp_retweet_action_id,
            ),
        );
        return $this->retweet_photo_configs->find($filter);
    }

    /**
     * @param $cp_retweet_action_id
     * @return array
     */
    public function getRetweetPhotos($cp_retweet_action_id) {
        $image_urls = array();
        $retweet_photo_config = $this->getRetweetPhotoConfig($cp_retweet_action_id);
        foreach ($retweet_photo_config as $element) {
            $image_urls[] = $element->image_url;
        }
        return $image_urls;
    }

    public function createEmptyRetweetPhotoConfigData() {
        return $this->retweet_photo_configs->createEmptyObject();
    }

    public function saveRetweetPhotoConfigData($retweet_photo_config_data) {
        return $this->retweet_photo_configs->save($retweet_photo_config_data);
    }

    /**
     * @param $retweet_photo_config_id
     * @return null
     */
    public function getRetweetPhotoConfigById($retweet_photo_config_id) {
        if (!$retweet_photo_config_id) return null;
        $filter = array(
            'conditions' => array(
                'id' => $retweet_photo_config_id,
            ),
        );
        return $this->retweet_photo_configs->findOne($filter);
    }

    /**
     * @param $cp_retweet_action_id
     */
    public function deleteRetweetPhotoConfig($cp_retweet_action_id) {
        $retweet_photo_config = $this->getRetweetPhotoConfig($cp_retweet_action_id);
        foreach ($retweet_photo_config as $element) {
            $this->retweet_photo_configs->delete($element);
        }
    }

    /**
     * @param $cp_retweet_action_id
     * @param $image_url
     * @return mixed
     */
    public function createRetweetPhotoConfig($cp_retweet_action_id, $image_url) {
        $retweet_photo_config                           = $this->createEmptyRetweetPhotoConfigData();
        $retweet_photo_config->cp_retweet_action_id     = $cp_retweet_action_id;
        $retweet_photo_config->image_url                = $image_url;
        return $this->saveRetweetPhotoConfigData($retweet_photo_config);
    }
}