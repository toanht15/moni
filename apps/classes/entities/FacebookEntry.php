<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');
AAFW::import('jp.aainc.classes.entities.base.IPanelEntry');

class FacebookEntry extends aafwEntityBase implements IPanelEntry {

    const ENTRY_TYPE_PHOTO = 'photo';
    const ENTRY_TYPE_LINK = 'link';
    const ENTRY_TYPE_STATUS = 'status';
    const ENTRY_TYPE_QUESTION = 'question';
    const ENTRY_TYPE_VIDEO = 'video';

    const STATUS_TYPE_NOTE = "created_note";
    const STATUS_TYPE_ADDED_VIDEO = 'added_video';
    const STATUS_TYPE_SHARED_STORY = 'shared_story';

    const FACEBOOK_HOST = 'www.facebook.com';

    const FACEBOOK_STAGING = 'fbstaging';

    public function getEntryPrefix() {
        return self::ENTRY_PREFIX_FACEBOOK;
    }

    public function getType() {
        return StreamService::STREAM_TYPE_FACEBOOK;
    }

    private static $entry_type_name_array = array(
        self::ENTRY_TYPE_PHOTO => "写真",
        self::ENTRY_TYPE_LINK => "リンク",
        self::ENTRY_TYPE_STATUS => "投稿",
        self::ENTRY_TYPE_QUESTION => "質問",
        self::ENTRY_TYPE_VIDEO => "動画",
    );

    protected $_Relations = array(

        'FacebookStreams' => array(
            'stream_id' => 'id',
        )

    );

    public function getEntryTypeName() {
        return self::$entry_type_name_array[$this->type];
    }

    public function getStoreName() {
        return "FacebookEntries";
    }

    public function getServicePrefix() {
        return 'FacebookStream';
    }

    public function isSocialEntry() {
        return true;
    }

    public function getBrandSocialAccount() {
        $service_factory = new aafwServiceFactory ();

        $streamService = $service_factory->create('FacebookStreamService');
        $stream = $streamService->getStreamById($this->stream_id);
        $brandSocialAccountService = $service_factory->create('BrandSocialAccountService');

        return $brandSocialAccountService->getBrandSocialAccountById($stream->brand_social_account_id);
    }

    public function asArray() {
        return [
            "id" => $this->id,
            "object_id" => $this->object_id,
            "link" => $this->link,
            "image_url" => $this->image_url,
            "display_type" => $this->display_type,
            "panel_text" => $this->panel_text,
            'type' => $this->type,
            'status_type' => $this->getStatusType(),
            'video_url' => $this->getVideoSource(),
            "is_social_entry" => $this->isSocialEntry(),
            "service_prefix" => $this->getServicePrefix()
        ];
    }

    /**
     * フルテキスト取得
     * @return mixed
     */
    public function getFullText() {
        $extra_data = json_decode($this->extra_data);

        if ($extra_data->message) {
            return $extra_data->message;
        } elseif ($extra_data->story) {
            return $extra_data->story;
        }

        return $extra_data->description;
    }

    /**
     * @return string|true
     */
    public function getStatusType() {
        // Shared Facebook Video Post has the same status type with Facebook Video Post
        if ($this->type == self::ENTRY_TYPE_VIDEO
            && $this->status_type == self::STATUS_TYPE_SHARED_STORY
            && parse_url($this->link, PHP_URL_HOST) == self::FACEBOOK_HOST) {

            return self::STATUS_TYPE_ADDED_VIDEO;
        }

        return $this->status_type;
    }

    /**
     * @return string|true|void
     */
    public function getVideoSource() {
        if ($this->type != self::ENTRY_TYPE_VIDEO) return;

        if ($this->status_type != self::STATUS_TYPE_ADDED_VIDEO && parse_url($this->link, PHP_URL_HOST) != self::FACEBOOK_HOST) {
            $video_url = json_decode($this->extra_data)->source;
            $parsed_video_url = parse_url($video_url);

            return $parsed_video_url['scheme'] . '://' . $parsed_video_url['host'] . (isset($parsed_video_url['path']) ? $parsed_video_url['path'] : '');
        }

        return $this->link;
    }
}
