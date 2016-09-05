<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.brandco.plugin.trait.CommentUserLikeTrait');
AAFW::import('jp.aainc.classes.brandco.plugin.trait.CommentUserShareTrait');
AAFW::import('jp.aainc.classes.brandco.plugin.trait.CommentUserMentionTrait');
AAFW::import('jp.aainc.classes.brandco.plugin.trait.CommentUserHiddenLogTrait');
AAFW::import('jp.aainc.classes.brandco.plugin.trait.CommentUserRelationTrait');

class CommentUserService extends aafwServiceBase {

    use CommentUserLikeTrait;
    use CommentUserShareTrait;
    use CommentUserMentionTrait;
    use CommentUserHiddenLogTrait;
    use CommentUserRelationTrait;

    const ANCHOR_PREFIX = '#cur_id_';

    const LINE_WIDTH_PC = 650;
    const LINE_WIDTH_SP = 250;

    const COMMENT_LIMIT_PER_PAGE    = 20;
    const REPLY_LIMIT_PER_PAGE      = 9;

    const DISPLAY_20_ITEMS  = 20;
    const DISPLAY_50_ITEMS  = 50;
    const DISPLAY_100_ITEMS = 100;

    const COMMENT_KEYWORD_SEARCH_TYPE_NICKNAME  = 1;
    const COMMENT_KEYWORD_SEARCH_TYPE_CONTENT   = 2;

    public static $comment_user_relation_keyword_label = array(
        self::COMMENT_KEYWORD_SEARCH_TYPE_NICKNAME => 'ニックネーム',
        self::COMMENT_KEYWORD_SEARCH_TYPE_CONTENT => '投稿内容'
    );

    protected $logger;
    protected $data_builder;
    protected $hipchat_logger;

    protected $comment_users;
    protected $comment_user_replies;
    protected $comment_free_text_users;

    public function __construct() {
        $this->comment_user_likes = $this->getModel('CommentUserLikes');
        $this->comment_user_shares = $this->getModel('CommentUserShares');
        $this->comment_user_mentions = $this->getModel('CommentUserMentions');
        $this->comment_user_hidden_logs = $this->getModel('CommentUserHiddenLogs');
        $this->comment_user_relations = $this->getModel('CommentUserRelations');

        $this->comment_users = $this->getModel('CommentUsers');
        $this->comment_user_replies = $this->getModel('CommentUserReplies');
        $this->comment_free_text_users = $this->getModel('CommentFreeTextUsers');

        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->data_builder = aafwDataBuilder::newBuilder();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();
    }

    /***********************************************************
     * Comment User
     ***********************************************************/

    /**
     * @param $comment_plugin_id
     * @param $prev_min_id
     * @return mixed
     */
    public function countPublicCommentUsers($comment_plugin_id, $prev_min_id) {
        $search_condition = array(
            'comment_plugin_id' => $comment_plugin_id
        );

        if ($prev_min_id != 0) {
            $search_condition['prev_min_id'] = $prev_min_id;
        }

        list($result) = $this->data_builder->countPublicCommentUsers($search_condition);
        return $result['comment_count'];
    }

    /**
     * @param $search_condition
     * @return mixed
     */
    public function countComment($search_condition) {
        if($this->isEmpty($search_condition['comment_plugin_ids'])) {
            return 0;
        }

        $search_condition = $this->filterSearchCondition($search_condition);
        list($result) = $this->data_builder->countCommentList($search_condition);

        return $result['comment_count'];
    }

    /**
     * @param $comment_plugin_id
     * @return mixed
     */
    public function getLastCommentUser($comment_plugin_id) {
        $filter = array(
            'conditions' => array(
                'comment_plugin_id' => $comment_plugin_id
            ),
            'order' => array(
                'name' => 'id',
                'direction' => 'desc'
            )
        );

        return $this->comment_users->findOne($filter);
    }

    /**
     * @param $search_condition
     * @param null $pager
     * @param null $order
     * @return mixed
     */
    public function getCommentList($search_condition, $pager = null, $order = null) {
        if($this->isEmpty($search_condition['comment_plugin_ids'])) {
            return array();
        }

        $search_condition = $this->filterSearchCondition($search_condition);

        if (Util::isNullOrEmpty($order)) {
            $order = array(
                'name' => 'updated_at',
                'direction' => 'desc'
            );
        }

        if (Util::isNullOrEmpty($pager)) {
            $pager = array(
                'page' => 1,
                'count' => self::DISPLAY_20_ITEMS
            );
        }

        return $this->data_builder->getCommentList($search_condition, $order, $pager, false, 'CommentUserRelation');
    }

    /**
     * @param $comment_plugin_id
     * @param int $prev_min_id
     * @param null $exclude_id
     * @return mixed
     */
    public function getPublicCommentUsers($comment_plugin_id, $prev_min_id = 0, $exclude_id = null) {
        $search_condition = array(
            'comment_plugin_id' => $comment_plugin_id
        );

        if ($prev_min_id != 0) {
            $search_condition['prev_min_id'] = $prev_min_id;
        }

        if (!Util::isNullOrEmpty($exclude_id)) {
            $search_condition['exclude_id'] = $exclude_id;
        }

        $order = array(
            'name' => 'id',
            'direction' => 'desc'
        );

        $pager = array(
            'page' => 1,
            'count' => self::COMMENT_LIMIT_PER_PAGE
        );

        return $this->data_builder->getPublicCommentUsers($search_condition, $order, $pager, false, 'CommentUser');
    }

    /**
     * @param $comment_user_id
     */
    public function getCommentUserById($comment_user_id) {
        if (Util::isNullOrEmpty($comment_user_id)) {
            return;
        }

        return $this->comment_users->findOne($comment_user_id);
    }

    /**
     * @param $comment_data
     */
    public function createCommentUser($comment_data) {
        // Create Comment User
        $comment_user = $this->comment_users->createEmptyObject();
        $comment_user->comment_plugin_id = $comment_data['comment_plugin_id'];
        $this->comment_users->save($comment_user);

        // Create Comment Free Text User
        $comment_free_text_user = $this->comment_free_text_users->createEmptyObject();

        $comment_free_text_user->comment_user_id = $comment_user->id;
        $comment_free_text_user->comment_action_id = $comment_data['comment_action_id'];
        $comment_free_text_user->text = $comment_data['text'];
        $comment_free_text_user->extra_data = $this->encodeComment($comment_data['comment_text']);

        $this->comment_free_text_users->save($comment_free_text_user);

        return $comment_user;
    }

    /**
     * @param $search_condition
     * @return mixed
     */
    public function filterSearchCondition($search_condition) {
        if ($search_condition['status'] == CommentUserRelation::COMMENT_USER_RELATION_STATUS_ALL) {
            unset($search_condition['status']);
        }

        if (Util::isNullOrEmpty($search_condition['bur_no'])) {
            unset($search_condition['bur_no']);
        } else {
            $search_condition['bur_no'] = explode(',', $search_condition['bur_no']);
        }

        if (Util::isNullOrEmpty($search_condition['discard_flg'])) {
            if ($search_condition['status'] == CommentUserRelation::COMMENT_USER_RELATION_STATUS_REJECTED) { // Searching rejected comment post (omit discarded post)
                $search_condition['discard_flg'] = CommentUserRelation::DISCARD_FLG_OFF;
            } else {
                unset($search_condition['discard_flg']);
            }
        }

        if ($search_condition['note_status'] == CommentUserRelation::NOTE_STATUS_VALID) {
            $search_condition['NOTE_STATUS_VALID'] = '__ON__';
        } elseif ($search_condition['note_status'] == CommentUserRelation::NOTE_STATUS_INVALID) {
            $search_condition['NOTE_STATUS_INVALID'] = '__ON__';
        }
        unset($search_condition['note_status']);

        if ($search_condition['sns_share'] == CommentUserRelation::USE_SNS_SHARE) {
            $search_condition['USE_SNS_SHARE'] = '__ON__';
        } elseif ($search_condition['sns_share'] == CommentUserRelation::NOT_USE_SNS_SHARE) {
            $search_condition['NOT_USE_SNS_SHARE'] = '__ON__';
        }
        unset($search_condition['sns_share']);

        if (Util::isNullOrEmpty($search_condition['nickname'])) {
            unset($search_condition['nickname']);
        } else {
            $search_condition['nickname'] = "%" . $search_condition['nickname'] . "%";
            $search_condition['NICKNAME_SEARCH'] = '__ON__';
        }

        if (Util::isNullOrEmpty($search_condition['comment_content'])) {
            unset($search_condition['comment_content']);
        } else {
            $search_condition['comment_content'] = "%" . $search_condition['comment_content'] . "%";
            $search_condition['CONTENT_SEARCH'] = '__ON__';
        }

        if($search_condition['get_new_record']) {
            $search_condition['IS_NEW_FLG'] = '__ON__';
            unset($search_condition['get_new_record']);
        }

        if($search_condition['get_saved_no_record']) {
            $search_condition['SAVED_NO_FLG'] = '__ON__';
            unset($search_condition['get_saved_no_record']);
        }

        if (!Util::isNullOrEmpty($search_condition['from_date']) && !Util::isNullOrEmpty($search_condition['to_date'])) {
            $search_condition['PERIOD_SEARCH'] = '__ON__';
            $search_condition['from_date'] = $this->getFromDateFormat($search_condition['from_date']);
            $search_condition['to_date'] = $this->getToDateFormat($search_condition['to_date']);
        } else if (!Util::isNullOrEmpty($search_condition['from_date'])) {
            $search_condition['FROM_DATE_SEARCH'] = '__ON__';
            $search_condition['from_date'] = $this->getFromDateFormat($search_condition['from_date']);
            unset($search_condition['to_date']);
        } else if (!Util::isNullOrEmpty($search_condition['to_date'])) {
            $search_condition['TO_DATE_SEARCH'] = '__ON__';
            $search_condition['to_date'] = $this->getToDateFormat($search_condition['to_date']);
            unset($search_condition['from_date']);
        } else {
            unset($search_condition['to_date']);
            unset($search_condition['from_date']);
        }

        return $search_condition;
    }

    /**
     * @param $date
     * @return mixed
     */
    private function getFromDateFormat($date) {
        $date_format = date('Y-m-d H:i:s', strtotime($date.' 00:00:00'));
        return $this->escape($date_format);
    }

    /**
     * @param $date
     * @return mixed
     */
    private function getToDateFormat($date) {
        $date_format = date('Y-m-d H:i:s', strtotime($date.' 23:59:59'));
        return $this->escape($date_format);
    }

    /**
     * @param $value
     * @return mixed
     */
    private function escape($value){
        return $this->comment_users->escapeForSQL($value);
    }

    /***********************************************************
     * Comment Free Text User
     ***********************************************************/

    /**
     * @param $comment_free_text_user
     */
    public function updateCommentFreeTextUser($comment_free_text_user) {
        $this->comment_free_text_users->save($comment_free_text_user);
    }

    /**
     * @param $comment_user_id
     */
    public function getCommentFreeTextUser($comment_user_id) {
        if (Util::isNullOrEmpty($comment_user_id)) {
            return;
        }

        $filter = array(
            'comment_user_id' => $comment_user_id
        );

        return $this->comment_free_text_users->findOne($filter);
    }

    /***********************************************************
     * Comment User Reply
     ***********************************************************/

    /**
     * @param $comment_user_id
     * @param int $prev_min_id
     * @param null $exclude_id
     * @return mixed
     */
    public function countRemainingCommentUserReplies($comment_user_id, $prev_min_id = 0, $exclude_id = null) {
        $search_condition = array(
            'comment_user_id' => $comment_user_id
        );

        if ($prev_min_id != 0) {
            $search_condition['prev_min_id'] = $prev_min_id;
        }

        if (!Util::isNullOrEmpty($exclude_id)) {
            $search_condition['exclude_id'] = $exclude_id;
        }

        list($result) = $this->data_builder->countRemainingCommentUserReplies($search_condition);
        return $result['reply_count'];
    }

    /**
     * @param $comment_user_reply
     */
    public function updateCommentUserReply($comment_user_reply) {
        $this->comment_user_replies->save($comment_user_reply);
    }

    /**
     * @param $comment_user_reply_id
     */
    public function getCommentUserReplyById($comment_user_reply_id) {
        if (Util::isNullOrEmpty($comment_user_reply_id)) {
            return;
        }

        return $this->comment_user_replies->findOne($comment_user_reply_id);
    }

    /**
     * @param $comment_user_id
     * @return mixed
     */
    public function getCommentUserRepliesByCommentUserId($comment_user_id) {
        $filter = array(
            'comment_user_id' => $comment_user_id
        );

        return $this->comment_user_replies->find($filter);
    }

    /**
     * @param $comment_user_id
     * @param null $exclude_id
     * @param $prev_min_id
     * @return mixed
     */
    public function getPublicCommentUserReplies($comment_user_id, $exclude_id = null, $prev_min_id) {
        $search_condition = array(
            'comment_user_id' => $comment_user_id
        );

        if ($prev_min_id != 0) {
            $search_condition['prev_min_id'] = $prev_min_id;
        }

        if (!Util::isNullOrEmpty($exclude_id)) {
            $search_condition['exclude_id'] = $exclude_id;
        }

        $order = array(
            'name' => 'id',
            'direction' => 'desc'
        );

        $pager = array(
            'page' => 1,
            'count' => self::REPLY_LIMIT_PER_PAGE
        );

        return $this->data_builder->getPublicCommentUserReplies($search_condition, $order, $pager, false, 'CommentUserReply');
    }

    /**
     * @param $comment_user_id
     * @return mixed
     */
    public function countCommentUserRepliesByCommentUserId($comment_user_id) {
        $filter = array(
            'comment_user_id' => $comment_user_id
        );

        return $this->comment_user_replies->count($filter);
    }

    /**
     * @param $comment_data
     */
    public function createCommentUserReply($comment_data) {
        $comment_user_reply = $this->comment_user_replies->createEmptyObject();

        $comment_user_reply->comment_user_id = $comment_data['comment_user_id'];
        $comment_user_reply->text = $comment_data['text'];
        $comment_user_reply->extra_data = $this->encodeComment($comment_data['comment_text']);

        $this->comment_user_replies->save($comment_user_reply);

        return $comment_user_reply;
    }

    /***********************************************************
     * Common Function
     ***********************************************************/

    /**
     * @param $user_info
     * @param $target_user_id
     * @return bool
     */
    public function isOwner($user_info, $target_user_id) {
        if (Util::isNullOrEmpty($user_info)) {
            return false;
        }

        if ($user_info->id == $target_user_id) {
            return true;
        }

        return false;
    }

    /**
     * @param $user_info
     * @param $comment_user_relation_id
     * @return array
     */
    public function getLikeData($user_info, $comment_user_relation_id) {
        $like_data = array();

        $comment_user_like_count = $this->countCommentUserLike($comment_user_relation_id);
        $comment_user_like = $this->getCommentUserLike($user_info->id, $comment_user_relation_id);

        $like_data['like_count'] = $comment_user_like_count;
        $like_data['is_liked'] = !Util::isNullOrEmpty($comment_user_like);

        return $like_data;
    }

    /**
     * @param $dom_element
     * @return string
     */
    public function getElementText($dom_element) {
        if ($dom_element instanceof \DOMText) {
            return htmlentities($dom_element->nodeValue);
        }

        // Support mention span tag
        if ($dom_element->getAttribute('class') === 'mention' && $dom_element->hasAttribute('contenteditable')) {
            return '<span class="mention" contenteditable="false">' . htmlentities($dom_element->nodeValue) . '</span>';
        }

        // Remove auto generated span tag
        if ($dom_element->tagName === 'span' && $dom_element->hasAttribute('style')) {
            return htmlentities($dom_element->nodeValue);
        }

        $outer_html = $dom_element->ownerDocument->saveHTML($dom_element);
        return htmlentities($outer_html);
    }

    /**
     * @param $paragraph_element
     * @return string
     */
    public function getParagraphText($paragraph_element) {
        $element_node_value = "";
        foreach($paragraph_element->childNodes as $node) {
            $element_node_value .= $this->getElementText($node);
        }

        // Replace multiple default space
        $element_node_value = preg_replace("/\s+/", ' ', trim($element_node_value));

        if (Util::isNullOrEmpty($element_node_value) || ctype_space($element_node_value) || $this->ctypeIdeographicSpace($element_node_value)) {
            return "";
        }

        return '<p>' . $element_node_value . '</p>';
    }

    /**
     * Reformat text
     * @param $text
     * @return mixed
     */
    public function trimText($text) {
        // Remove all html whitespace
        $text = preg_replace('/&nbsp;|<br>/', '', $text);

        // Convert to paragraph format
        $text = preg_replace('/div/', 'p', $text);

        $trimming_text = "";
        $dom = new DOMDocument();
        $dom->loadHTML(mb_convert_encoding($text, 'HTML-ENTITIES', 'UTF-8'));
        $dom->preserveWhiteSpace = false;

        foreach($dom->getElementsByTagName('p') as $element ){
            $trimming_text .= $this->getParagraphText($element);
        }

        return $trimming_text;
    }

    /**
     * @param $text
     * @return string
     */
    public function getTextContent($text) {
        $dom = new DOMDocument();
        $dom->loadHTML(mb_convert_encoding($text, 'HTML-ENTITIES', 'UTF-8'));
        $dom->preserveWhiteSpace = false;

        $text_content = $this->stripEmoji($dom->textContent);
        return $text_content;
    }

    /**
     * @param $text
     * @return mixed
     */
    public function stripEmoji($text) {
        $clean_text = $text;

        // Match Emoticons
        $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clean_text = preg_replace($regexEmoticons, '', $clean_text);

        // Match Miscellaneous Symbols and Pictographs
        $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clean_text = preg_replace($regexSymbols, '', $clean_text);

        // Match Transport And Map Symbols
        $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clean_text = preg_replace($regexTransport, '', $clean_text);

        // Match Miscellaneous Symbols
        $regexMisc = '/[\x{2600}-\x{26FF}]/u';
        $clean_text = preg_replace($regexMisc, '', $clean_text);

        // Match Dingbats
        $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
        $clean_text = preg_replace($regexDingbats, '', $clean_text);

        return $clean_text;
    }

    /**
     * @param $comment_text
     * @return string
     */
    public function parseTextForSnsSharing($comment_text) {
        $share_paragraphs = array();
        $paragraphs = $this->explodeParagraph($comment_text);

        foreach ($paragraphs as $paragraph) {
            if (Util::isNullOrEmpty($paragraph)) {
                continue;
            }

            $detaching = $this->detachMentionName($paragraph);

            if (is_array($detaching) && !Util::isNullOrEmpty($detaching[2])) {
                $share_paragraphs[] = $detaching[1] . ' ' . $detaching[2] . ' ' . $detaching[3];
            } else {
                $share_paragraphs[] = $detaching;
            }
        }

        return implode(chr(13), $share_paragraphs);
    }

    /**
     * @param $comment_text
     * @param bool $is_html
     * @param string $link_text
     * @param int $max_line
     * @return string
     */
    public function cutTextByLine($comment_text, $is_html = true, $link_text = "more", $max_line = 5) {
        $line_max_width = Util::isSmartPhone() ? self::LINE_WIDTH_SP : self::LINE_WIDTH_PC;
        $max_width = $max_line * $line_max_width;
        $paragraphs = $this->explodeParagraph($comment_text);

        $text_width = 0;
        $exposed_text = "";
        $need_exposed = false;
        $max_paragraph_key = max(array_keys($paragraphs));

        foreach ($paragraphs as $key => $paragraph) {
            $paragraph_width = $text_width;
            if (Util::isNullOrEmpty($paragraph)) {
                continue;
            }

            $parsed_text = "";
            $is_exposed_paragraph = $need_exposed;

            $result = $this->detachMentionName($paragraph);

            if (is_array($result) && !Util::isNullOrEmpty($result[2])) {                 // Check if mention is available
                $this->calcTextWidth($result[1], $max_width, $text_width, $need_exposed, $parsed_text, $link_text);
                $parsed_text .= $is_html ? ('<span class="mention" contenteditable="false">' . $result[2] . '</span>') : ('＠' . $result[2] . " ");
                $this->calcTextWidth($result[3], $max_width, $text_width, $need_exposed, $parsed_text, $link_text);
            } else {
                $this->calcTextWidth($paragraph, $max_width, $text_width, $need_exposed, $parsed_text, $link_text);
            }

            if ($need_exposed && strpos($parsed_text, '<br>') !== false) {
                $parsed_text = str_replace('<br>', '<span class="jsSeeMore exposed_text_hide">… <a href="javascript:void(0);">' . $link_text . '</a></span><span class="exposed_text_show" style="display: none;">', $parsed_text);
                $parsed_text .= '</span>';
            }

            $paragraph_width = $text_width - $paragraph_width;
            $paragraph_max_width = (floor($paragraph_width/$line_max_width) + ($paragraph_width % $line_max_width > 0)) * $line_max_width;
            $text_width = $text_width - $paragraph_width + $paragraph_max_width;

            if ($need_exposed === false && $text_width >= $max_width && $key != $max_paragraph_key) {
                $parsed_text .= '<span class="jsSeeMore exposed_text_hide">… <a href="javascript:void(0);">' . $link_text . '</a></span><span class="exposed_text_show" style="display: none;"></span>';
                $need_exposed = true;
            }

            if ($is_exposed_paragraph) {
                $exposed_text .= '<p class="exposed_text_show" style="display: none;">' . $parsed_text . '</p>';
            } else {
                $exposed_text .= '<p>' . $parsed_text . '</p>';
            }
        }

        return $exposed_text;
    }

    /**
     * @param $paragraph
     * @param $max_width
     * @param $text_width
     * @param $need_exposed
     * @param $parsed_text
     */
    public function calcTextWidth($paragraph, $max_width, &$text_width, &$need_exposed, &$parsed_text) {
        $character_array = Util::mbStrSplit($paragraph);

        foreach ($character_array as $char) {
            $text_width += Util::getCharacterWidth($char);

            if (!$need_exposed && $text_width > $max_width) {
                $need_exposed = true;
                $parsed_text .= '<br>'; // Insert unique tag
            }

            $parsed_text .= $char;
        }
    }

    /**
     * @param $paragraph
     * @return string
     */
    public function detachMentionName($paragraph) {
        $pattern = '#(.*)<span ?.*>(.*)<\/span>(.*)#';
        preg_match($pattern, $paragraph, $matches);

        if (empty($matches)) {
            return $paragraph;
        }

        return $matches;
    }

    /**
     * @param $text
     * @return array
     */
    public function explodeParagraph($text) {
        $text = str_replace('</p>', '', $text);
        $paragraphs = explode('<p>', $text);

        return $paragraphs;
    }

    /**
     * @param $text
     * @return bool
     */
    public function ctypeIdeographicSpace($text) {
        // Replace multiple ideographic space
        $text = str_replace('　', '', $text);

        if (ctype_space($text)) {
            return true;
        }

        if (Util::isNullOrEmpty($text)) {
            return true;
        }

        return false;
    }

    /**
     * @param $comment_text
     * @return string
     */
    public function encodeComment($comment_text) {
        $comment = array('text' => $comment_text);
        return json_encode($comment);
    }

    /**
     * @param $comment
     * @return mixed
     */
    public function decodeComment($comment) {
        $extra_data = json_decode($comment);
        return $extra_data->text;
    }
}