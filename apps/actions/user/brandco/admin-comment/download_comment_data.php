<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.brandco.cp.trait.DownloadDataTrait');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.lib.parsers.CSVParser');

class download_comment_data extends BrandcoGETActionBase {
    use DownloadDataTrait;

    const CHUNK_SIZE = 1048576; // チャンクサイズ (bytes)

    public $NeedOption = array(BrandOptions::OPTION_COMMENT);
    public $NeedAdminLogin = true;

    private $brand_user_relation_service;
    private $comment_user_service;
    private $user_service;
    private $data_builder;

    private $comment_plugin;
    private $page_url_list;

    public function doThisFirst() {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 3600);

        $this->data_builder = aafwDataBuilder::newBuilder();
        /** @var CommentUserService comment_user_service */
        $this->comment_user_service = $this->getService('CommentUserService');
        /** @var UserService $user_service */
        $this->user_service = $this->getService('UserService');
        /** @var BrandsUsersRelationService $brand_user_relation_service */
        $this->brand_user_relation_service = $this->getService('BrandsUsersRelationService');

    }

    public function validate() {
        $comment_plugin_id = $this->GET['comment_plugin_id'];

        if (Util::isNullOrEmpty($comment_plugin_id)) {
            return true;
        }

        $comment_plugin_validator = new CommentPluginValidator($comment_plugin_id, $this->getBrand()->id);
        $comment_plugin_validator->validate();

        if (!$comment_plugin_validator->isValid()) {
            return false;
        }

        $this->comment_plugin = $comment_plugin_validator->getCommentPlugin();
        return true;
    }

    public function doAction() {
        if (Util::isNullOrEmpty($this->comment_plugin)) {
            /** @var CommentPluginService $comment_plugin_service */
            $comment_plugin_service = $this->getService('CommentPluginService');
            $comment_plugins = $comment_plugin_service->getCommentPluginsByBrandId($this->getBrand()->id);
        } else {
            $comment_plugins = array($this->comment_plugin);
        }

        $search_conditions = array(
            'status'        => $this->GET['status'] ?: CommentUserRelation::COMMENT_USER_RELATION_STATUS_ALL,
            'discard_flg'   => $this->GET['discard_flg'],
            'note_status'   => $this->GET['note_status'],
            'order_type'    => $this->GET['order_type'],
            'sns_share'     => $this->GET['sns_share'],
            'order_kind'    => $this->GET['order_kind'],
            'nickname'      => trim($this->GET['nickname']),
            'comment_content' => trim($this->GET['comment_content']),
            'bur_no'        => $this->GET['bur_no'],
            'from_date'     => $this->GET['from_date'],
            'to_date'       => $this->GET['to_date'],
        );

        $this->fetchPageUrlList($comment_plugins);

        // Export csv
        $csv = new CSVParser();
        $csv_header = array('投稿No', 'プラグイン名', '投稿日時', '会員No', 'fid', 'いいね数', 'コメント数', 'Facebookへのシェア', 'Facebook友達数', 'Twitterへのシェア', 'Twitter友達数', '投稿内容', 'ステータス', 'URL');

        header("Content-type:" . $csv->getContentType());
        header($csv->getDisposition());
        $array_data = $csv->out(array('data' => $csv_header), null, true, true);
        print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");

        foreach ($comment_plugins as $comment_plugin) {
            $search_conditions['brand_id'] = $comment_plugin->brand_id;
            $search_conditions['comment_plugin_ids'] = $comment_plugin->id;

            //検索条件を取得する
            $filter_search_conditions = $this->comment_user_service->filterSearchCondition($search_conditions);

            $comment_list = $this->data_builder->getCommentList($filter_search_conditions, null, null, false, 'CommentUserRelation');

            foreach ($comment_list as $comment) {
                $comment_data = $this->fetchCommentData($comment, $comment_plugin);

                $array_data = $csv->out(array('data' => $comment_data), 1);
                print mb_convert_encoding($array_data, 'Shift_JIS', "UTF-8");
            }
        }

        exit;
    }

    /**
     * @param $comment
     * @param $comment_plugin
     * @return array
     */
    public function fetchCommentData($comment, $comment_plugin) {
        $comment_data = array();

        $comment_data[] = $comment->no;
        $comment_data[] = $comment_plugin->title;
        $comment_data[] = date('Y/m/d H:i', strtotime($comment->created_at));

        $comment_data[] = $comment->isAnonymousUser() ? "" : $comment->bur_no;
        $comment_data[] = $comment->fid;

        $comment_data[] = $this->comment_user_service->countCommentUserLike($comment->id);
        $comment_data[] = $comment->object_type == CommentUserRelation::OBJECT_TYPE_COMMENT ? $this->comment_user_service->countCommentUserRepliesByCommentUserId($comment->object_id) : "";
        $share_sns_list = $this->comment_user_service->getCommentUserShareSnsList($comment->id);

        $comment_data[] = in_array(SocialAccountService::SOCIAL_MEDIA_FACEBOOK, $share_sns_list) ? '◯' : '';
        $sa_friend_count = 'sa_friend_count_' . SocialAccountService::SOCIAL_MEDIA_FACEBOOK;
        $comment_data[] = $comment->$sa_friend_count ?: '';
        $comment_data[] = in_array(SocialAccountService::SOCIAL_MEDIA_TWITTER, $share_sns_list) ? '◯' : '';
        $sa_friend_count = 'sa_friend_count_' . SocialAccountService::SOCIAL_MEDIA_TWITTER;
        $comment_data[] = $comment->$sa_friend_count ?: '';

        $comment_text = $this->comment_user_service->decodeComment($comment->extra_data);
        $comment_data[] = $this->comment_user_service->parseTextForSnsSharing($comment_text);
        $comment_data[] = $comment->isDiscard() ? 'ユーザーによって削除された' : CommentUserRelation::$comment_user_relation_statuses[$comment->status];
        $comment_data[] = $this->page_url_list[$comment->comment_plugin_id] . '#cur_id_' . $comment->id;

        return $comment_data;
    }

    /**
     * @param $comment_plugins
     * @return array
     */
    public function fetchPageUrlList($comment_plugins) {
        $this->page_url_list = array();
        /** @var StaticHtmlEntryService $static_html_entry_service */
        $static_html_entry_service = $this->getService('StaticHtmlEntryService');

        foreach ($comment_plugins as $comment_plugin) {
            if (Util::isNullOrEmpty($comment_plugin->static_html_entry_id) || $comment_plugin->static_html_entry_id == 0) {
                continue;
            }

            $static_html_entry = $static_html_entry_service->getEntryById($comment_plugin->static_html_entry_id);
            $this->page_url_list[$comment_plugin->id] = Util::rewriteUrl('', 'page', array($static_html_entry->page_url));
        }

        return $this->page_url_list;
    }
}