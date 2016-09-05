<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');
AAFW::import('jp.aainc.classes.entities.base.IPanelEntry');

class PhotoEntry extends aafwEntityBase implements IPanelEntry {

    const TOP_STATUS_AVAILABLE      = 0;
    const TOP_STATUS_HIDDEN         = 1;

    protected $_Relations = array(
        'PhotoUsers' => array(
            'photo_user_id' => 'id'
        )
    );

    public function getEntryPrefix() {
        return self::ENTRY_PREFIX_PHOTO;
    }

    public function getStoreName() {
        return "PhotoEntries";
    }

    public function isSocialEntry() {
        return false;
    }

    public function getServicePrefix() {
        return 'PhotoStream';
    }

    public function asArray() {
        $photo_user = $this->getPhotoUser();
        $cp_user = $photo_user->getCpUser();
        $user = $cp_user->getUser();
        $cp = $cp_user->getCp();

        return [
            "id" => $this->id,
            "user_name" => $user->name,
            "user_profile_image_url" => $user->profile_image_url,
            "title" => $photo_user->photo_title,
            "body" => $photo_user->photo_comment,
            "image_url" => $photo_user->photo_url,
            "display_type" => $this->display_type,
            "is_social_entry" => $this->isSocialEntry(),
            "service_prefix" => $this->getServicePrefix(),
            "pub_date" => $this->pub_date,
            "cp_title" => $cp->getTitle()
        ];
    }
}
