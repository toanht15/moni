<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');
class CpInstagramHashtags extends aafwEntityStoreBase {
    protected $_TableName = "cp_instagram_hashtags";
    protected $_EntityName = "CpInstagramHashtag";

    public function deletePhysicalByCpInstagramHashtag(CpInstagramHashtag $cp_instagram_hashtag) {
        $this->deletePhysical($cp_instagram_hashtag);
    }
}
