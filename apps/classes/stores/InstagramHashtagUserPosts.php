<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');
class InstagramHashtagUserPosts extends aafwEntityStoreBase {
    protected $_TableName = "instagram_hashtag_user_posts";
    protected $_EntityName = "InstagramHashtagUserPost";

    public function deletePhysicalByInstagramHashtagUser($instagram_hashtag_user, $with_related_entity) {
        if (!$instagram_hashtag_user) return;

        if (!$instagram_hashtag_user->isExistsInstagramHashtagUserPosts()) return;

        $cp_instagram_hashtag_entres = $this->getModel('CpInstagramHashtagEntries');

        foreach ($instagram_hashtag_user->getInstagramHashtagUserPosts() as $instagram_hashtag_user_post) {

            if ($with_related_entity) {
                if ($instagram_hashtag_user_post->isExistsCpInstagramHashtagEntries()) {
                    foreach ($instagram_hashtag_user_post->getCpInstagramHashtagEntries() as $cp_instagram_hashtag_entry) {
                        $cp_instagram_hashtag_entres->deletePhysical($cp_instagram_hashtag_entry);
                    }
                }
            }

            $this->deletePhysical($instagram_hashtag_user_post);
        }
    }
}
