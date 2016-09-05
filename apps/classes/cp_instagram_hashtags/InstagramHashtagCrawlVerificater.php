<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.cp_instagram_hashtags.InstagramHashtagVeriticateDao');
AAFW::import('jp.aainc.classes.cp_instagram_hashtags.AbstractInstagramHashtagVerificater');
AAFW::import('jp.aainc.vendor.instagram.Instagram');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class InstagramHashtagCrawlVerificater extends AbstractInstagramHashtagVerificater {
    const MAX_CRAWL_POST_COUNT = 200;

    protected $crawl_post_count;

    public function __construct() {
        parent::__construct();
    }

    function initialize($cp_instagram_hashtag) {

        if (!$cp_instagram_hashtag) throw new Exception();

        $this->crawl_post_count = 0;
        
        $this->dao->resetNextMinId();

        if (json_decode($cp_instagram_hashtag->pagination)->next_min_id) {
            $this->dao->setNextMinId(json_decode($cp_instagram_hashtag->pagination)->next_min_id);
        }

        if ($this->dao->getNextMinId()) {
            $this->dao->setInstagramObject($this->instagram->getTagMedia($cp_instagram_hashtag->hashtag, $this->dao->getAccessToken(), array('min_tag_id' => $this->dao->getNextMinId())));
            $this->api_call_count++;
        } else {
            $this->dao->setInstagramObject($this->instagram->getTagMedia($cp_instagram_hashtag->hashtag, $this->dao->getAccessToken(), array()));
            $this->api_call_count++;
        }

        if ($this->dao->getInstagramObject() === null) {
            $this->hipchat_logger->error('InstagramHashtagCrawlVerificater#verify() error. Instagram api result is null: cp_id=' . $this->dao->getCp()->id . ' cp_instagram_hashtag_action_id=' . $this->dao->getCpInstagramHashtagAction()->id);
            throw new Exception();
        }

        if ($this->dao->getInstagramObject()->meta->code != Instagram::LEGAL_ACCESS_CODE) {
            $this->hipchat_logger->error('InstagramHashtagCrawlVerificater#verify() error. Illegal access code:' . $this->dao->getInstagramObject()->meta->code.  " cp_instagram_hashtag_id: " . $cp_instagram_hashtag->id);
            if ($this->dao->getInstagramObject()->meta->error_type) $this->logger->error('error_type:' . $this->dao->getInstagramObject()->meta->error_type);
            if ($this->dao->getInstagramObject()->meta->error_message) $this->logger->error('error_message:' . $this->dao->getInstagramObject()->meta->error_message);
            throw new Exception();
        }
        
        if($this->dao->getInstagramObject()->pagination->next_min_id){
            //新しいPaginationがあれば
            $this->dao->setNextPagination($this->dao->getInstagramObject()->pagination);
        }else{
            //新しいPaginationがない
            $this->dao->setNextPagination(json_decode($cp_instagram_hashtag->pagination));
        }
    }

    function verifyAll() {

        // 参加がない場合はなにもしない
        if (!$this->instagram_hashtag_user_service->countInstagramHashtagUserByCpActionId($this->dao->getCpInstagramHashtagAction()->cp_action_id)) return;

        while (true) {

            if($this->crawl_post_count > self::MAX_CRAWL_POST_COUNT){
                break;
            }

            // 一つのオブジェクトですべてのユーザを検索
            foreach ($this->dao->getInstagramObject()->data as $tag_info) {

                $this->crawl_post_count++;

                $this->dao->setTagInfo($tag_info);

                // CP開始時刻と投稿時刻チェック
                if ($this->dao->getCp()->status != Cp::STATUS_DEMO) {
                    if ($this->dao->getTagInfo()->created_time < strtotime($this->dao->getCp()->start_date)) continue;

                    if (!$this->dao->getCp()->isPermanent() && strtotime('-3 month', $this->dao->getTagInfo()->created_time) > strtotime($this->dao->getCp()->announce_date)) continue;
                }

                // ユーザ名一致チェック
                $instagram_hashtag_users = $this->instagram_hashtag_user_service->getInstagramHashtagUsersByCpActionIdAndInstagramUserName($this->dao->getCpInstagramHashtagAction()->cp_action_id, $this->dao->getTagInfo()->user->username);

                if (!$instagram_hashtag_users) continue;

                foreach ($instagram_hashtag_users as $instagram_hashtag_user) {

                    // タグ一致チェック
                    $is_match = Util::isIncludeArray($this->dao->getHashtags(), $this->dao->getTagInfo()->tags);

                    if (mb_strtolower($this->dao->getTagInfo()->user->username) == mb_strtolower($instagram_hashtag_user->instagram_user_name) && $is_match) {
                        $instagram_hashtag_user_post = $this->saveInstagramHashtagUserPost($instagram_hashtag_user);
                        $this->saveCpInstagramHashtagEntry($instagram_hashtag_user_post);
                    }
                }
            }

            // next_urlがあれば継続
            if ($this->dao->getInstagramObject()->pagination->next_url) {

                $next_url = $this->dao->getInstagramObject()->pagination->next_url;

                if($this->dao->getNextMinId()){
                    // 最初のmin_tag_idを指定して検索
                    $next_url .= '&min_tag_id=' . $this->dao->getNextMinId();
                }

                $instagram_object = $this->instagram->executeGETRequest($next_url);

                if ($instagram_object->meta->code != Instagram::LEGAL_ACCESS_CODE) {
                    $this->hipchat_logger->error('InstagramHashtagCrawlVerificater#verify() error. Illegal access code:' . $instagram_object->meta->code);
                    if ($instagram_object->meta->error_type) $this->hipchat_logger->error('error_type:' . $instagram_object->meta->error_type);
                    if ($instagram_object->meta->error_message) $this->hipchat_logger->error('error_message:' . $instagram_object->meta->error_message);
                    break;
                }

                $this->dao->setInstagramObject($instagram_object);

                $this->api_call_count++;
            }else {
                break;
            }
        }
    }

    function saveCpInstagramHashtag($cp_instagram_hashtag) {
        if (!$cp_instagram_hashtag) return;

        if($this->dao->getNextPagination()){
            $cp_instagram_hashtag->pagination = json_encode($this->dao->getNextPagination());
        }

        $tag_info = $this->getTagInfo($cp_instagram_hashtag);

        if ($tag_info) {
            $cp_instagram_hashtag->total_media_count_end = $tag_info->data->media_count;
            $cp_instagram_hashtag->cp_media_count_summary = $this->instagram_hashtag_user_service->countInstagramHashtagUserPostByActionId($this->dao->getCpInstagramHashtagAction()->cp_action_id);
        }

        $this->cp_instagram_hashtag_service->saveCpInstagramHashtag($cp_instagram_hashtag);
    }

    private function saveInstagramHashtagUserPost($instagram_hashtag_user) {
        if (!$instagram_hashtag_user) return;

        $instagram_hashtag_user_post = $this->instagram_hashtag_user_post_service->getInstagramHashtagUserPostByInstagramHashtagUserIdAndObjectId($instagram_hashtag_user->id, $this->dao->getTagInfo()->id);
        if ($instagram_hashtag_user_post) return;

        // 投稿時系列チェック
        if ($instagram_hashtag_user->isValidPostTime($this->dao->getTagInfo()->created_time)) {
            $instagram_hashtag_user_post = $this->fillInstagramHahstagUserPost($instagram_hashtag_user);
            $this->instagram_hashtag_user_post_service->saveInstagramHashtagUserPost($instagram_hashtag_user_post);
        } else {
            $instagram_hashtag_user_post = $this->fillInstagramHahstagUserPost($instagram_hashtag_user, true);
            $this->instagram_hashtag_user_post_service->saveInstagramHashtagUserPost($instagram_hashtag_user_post);
        }

        // 重複ユーザの他の投稿チェック ユーザ名が重複している場合の投稿は必ずすべて非承認となる
        if ($instagram_hashtag_user->duplicate_flg) {
            $duplicate_posts = $this->instagram_hashtag_user_service->getInstagramHashtagUserPostsByCpActionIdAndObjectId($instagram_hashtag_user->cp_action_id, $this->dao->getTagInfo()->id);
            if ($duplicate_posts) {
                foreach ($duplicate_posts as $duplicate_instagram_hashtag_user_post) {
                    $duplicate_instagram_hashtag_user_post->approval_status = InstagramHashtagUserPost::APPROVAL_STATUS_REJECT;
                    $this->instagram_hashtag_user_post_service->saveInstagramHashtagUserPost($duplicate_instagram_hashtag_user_post);
                }
            }
        }

        return $instagram_hashtag_user_post;
    }

    private function fillInstagramHahstagUserPost($instagram_hashtag_user, $reverse_post_time_flg = false) {
        $instagram_hashtag_user_post = $this->instagram_hashtag_user_post_service->createEmptyObject();
        $instagram_hashtag_user_post->instagram_hashtag_user_id = $instagram_hashtag_user->id;
        $instagram_hashtag_user_post->object_id = $this->dao->getTagInfo()->id;
        $instagram_hashtag_user_post->link = $this->dao->getTagInfo()->link;
        $instagram_hashtag_user_post->type = $this->dao->getTagInfo()->type;
        $instagram_hashtag_user_post->user_name = $this->dao->getTagInfo()->user->username;
        $instagram_hashtag_user_post->user_account_id = $this->dao->getTagInfo()->user->id;
        $instagram_hashtag_user_post->post_text = json_encode($this->dao->getTagInfo()->caption->text);
        $instagram_hashtag_user_post->low_resolution = $this->dao->getTagInfo()->images->low_resolution->url;
        $instagram_hashtag_user_post->thumbnail = $this->dao->getTagInfo()->images->thumbnail->url;
        $instagram_hashtag_user_post->standard_resolution = $this->dao->getTagInfo()->images->standard_resolution->url;
        $instagram_hashtag_user_post->detail_data = json_encode($this->dao->getTagInfo());

        if ($this->dao->getCpInstagramHashtagAction()->approval_flg) {
            $instagram_hashtag_user_post->approval_status = InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT;
        } else {
            $instagram_hashtag_user_post->approval_status = InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE;
        }

        // 投稿時間逆チェック
        if ($reverse_post_time_flg) {
            $instagram_hashtag_user_post->approval_status = InstagramHashtagUserPost::APPROVAL_STATUS_REJECT;
            $instagram_hashtag_user_post->reverse_post_time_flg = 1;
        }

        if ($instagram_hashtag_user->duplicate_flg) {
            $instagram_hashtag_user_post->approval_status = InstagramHashtagUserPost::APPROVAL_STATUS_REJECT;
        }

        return $instagram_hashtag_user_post;
    }

    private function saveCpInstagramHashtagEntry($instagram_hashtag_user_post) {
        if (!$instagram_hashtag_user_post) return;

        $brand = $this->dao->getCp()->getBrand();
        if (!$brand) return;

        $cp_instagram_hashtag_stream = $this->cp_instagram_hashtag_stream_service->getStreamByBrandId($brand->id);
        if (!$cp_instagram_hashtag_stream) return;

        $cp_instagram_hashtag_entry = $this->cp_instagram_hashtag_stream_service->getCpInstagramHashtagEntryByInstagramHashtagUserPostIdAndStreamId($instagram_hashtag_user_post->id, $cp_instagram_hashtag_stream->id);
        if ($cp_instagram_hashtag_entry) return;

        $panel_hidden_flg = $this->cp_instagram_hashtag_stream_service->getCpInstagramHashtagPanelHiddenFlg($this->dao->getCpInstagramHashtagAction()->approval_flg, $cp_instagram_hashtag_stream->panel_hidden_flg);

        $cp_instagram_hashtag_entry = $this->cp_instagram_hashtag_stream_service->createEmptyEntry();
        $cp_instagram_hashtag_entry->stream_id = $cp_instagram_hashtag_stream->id;
        $cp_instagram_hashtag_entry->instagram_hashtag_user_post_id = $instagram_hashtag_user_post->id;
        $cp_instagram_hashtag_entry->top_hidden_flg = $panel_hidden_flg;
        $cp_instagram_hashtag_entry->pub_date = date('Y-m-d H:i:s');
        $this->cp_instagram_hashtag_stream_service->updateEntry($cp_instagram_hashtag_entry);
    }
}