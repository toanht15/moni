<?php
AAFW::import('jp.aainc.aafw.base.aafwServiceBase');

class CpInstagramHashtagService extends aafwServiceBase {

    /** @var CpInstagramHashtags $cp_instagram_hashtags */
    private $cp_instagram_hashtags;

    public function __construct() {
        $this->cp_instagram_hashtags = $this->getModel("CpInstagramHashtags");
    }

    public function saveCpInstagramHashtag(CpInstagramHashtag $cp_instagram_hashtag) {
        $this->cp_instagram_hashtags->save($cp_instagram_hashtag);
    }

    /**
     * ハッシュタグをすべて更新する
     * @param $cp_instagram_hashtag_action_id
     * @param array $hashtags
     */
    public function refreshCpInstagramHashtagsByCpActionIdAndHashtags($cp_instagram_hashtag_action_id, $hashtags = array()) {
        if (!$cp_instagram_hashtag_action_id || !count($hashtags)) return;

        // cp_instagram_hashtag一覧取得
        $cp_instagram_hashtags = $this->getCpInstagramHashtagsByCpInstagramHashtagActionId($cp_instagram_hashtag_action_id);

        // 削除
        if ($cp_instagram_hashtags) {
            foreach ($cp_instagram_hashtags as $cp_instagram_hashtag) {
                // POSTのハッシュタグ一覧になかったら削除
                if (!in_array($cp_instagram_hashtag->hashtag, $hashtags)) {
                    $this->cp_instagram_hashtags->deletePhysicalByCpInstagramHashtag($cp_instagram_hashtag);
                }
            }
        }

        // 更新
        foreach ($hashtags as $hashtag) {
            $this->saveCpInstagramHashtagByCpActionIdAndHashtag($cp_instagram_hashtag_action_id, $hashtag);
        }
    }

    public function saveCpInstagramHashtagByCpActionIdAndHashtag($cp_instagram_hashtag_action_id, $hashtag) {
        if (!$cp_instagram_hashtag_action_id || $hashtag === null) return array();

        // 存在チェック
        $cp_hashtag = $this->getCpInstagramHashtagByCpInstagramHashtagActionIdAndHashtag($cp_instagram_hashtag_action_id, $hashtag);

        if (!$cp_hashtag) {
            $cp_hashtag = $this->cp_instagram_hashtags->createEmptyObject();
        }

        $cp_hashtag->cp_instagram_hashtag_action_id = $cp_instagram_hashtag_action_id;
        $cp_hashtag->hashtag = $hashtag;

        return $this->cp_instagram_hashtags->save($cp_hashtag);
    }

    public function getCpInstagramHashtagByCpInstagramHashtagActionIdAndHashtag($cp_instagram_hashtag_action_id, $hashtag) {
        if (!$cp_instagram_hashtag_action_id || $hashtag === null) return array();

        $filter = array(
            'cp_instagram_hashtag_action_id' => $cp_instagram_hashtag_action_id,
            'hashtag' => $hashtag
        );
        return $this->cp_instagram_hashtags->findOne($filter);
    }

    public function getCpInstagramHashtagsByCpInstagramHashtagActionId($cp_instagram_hashtag_action_id) {
        if (!$cp_instagram_hashtag_action_id) return array();

        $filter = array(
            'cp_instagram_hashtag_action_id' => $cp_instagram_hashtag_action_id
        );
        return $this->cp_instagram_hashtags->find($filter);
    }

    public function getCpInstagramHashtagsByCpInstagramHashtagAction(CpInstagramHashtagAction $cp_instagram_hashtag_action) {
        if (!$cp_instagram_hashtag_action || !$cp_instagram_hashtag_action->isExistsCpInstagramHashtags()) return;

        $hashtags = array();

        foreach ($this->getCpInstagramHashtagsOrderById($cp_instagram_hashtag_action->id) as $cp_instagram_hashtag) {
            $hashtags[] = $cp_instagram_hashtag->hashtag;
        }

        return $hashtags;
    }

    public function getCpInstagramHashtagsOrderById($cp_instagram_hashtag_action_id) {
        if (!$cp_instagram_hashtag_action_id) return array();

        $filter = array(
            'conditions' => array(
                'cp_instagram_hashtag_action_id' => $cp_instagram_hashtag_action_id
            ),
            'order' => array(
                'name' => 'id'
            )
        );

        return $this->cp_instagram_hashtags->find($filter);
    }
}
