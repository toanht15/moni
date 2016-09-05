<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );

class PhotoUser extends aafwEntityBase {
    protected $_Relations = array(
        'CpUsers' => array(
            'cp_user_id' => 'id'
        ),
        'PhotoEntries' => array(
            'id' => 'photo_user_id'
        ),
        'PhotoUserShares' => array(
            'id' => 'photo_user_id'
        ),
        'CpActions' => array(
            'cp_action_id' => 'id'
        )
    );

    const APPROVAL_STATUS_DEFAULT   = 0;
    const APPROVAL_STATUS_APPROVE   = 1;
    const APPROVAL_STATUS_REJECT    = 2;

    private $approval_status_classes = array(
        self::APPROVAL_STATUS_DEFAULT => 'label5',
        self::APPROVAL_STATUS_APPROVE => 'label4',
        self::APPROVAL_STATUS_REJECT => 'label2'
    );

    private $approval_statuses = array(
        self::APPROVAL_STATUS_DEFAULT => '未承認',
        self::APPROVAL_STATUS_APPROVE => '承認',
        self::APPROVAL_STATUS_REJECT => '非承認'
    );

    public function getCroppedPhoto() {
        $photo_path = pathinfo($this->photo_url);
        return $photo_path['dirname'] . '/' . $photo_path['filename'] . '_s.' . $photo_path['extension'];
    }

    public function getMiddlePhoto() {
        $photo_path = pathinfo($this->photo_url);
        return $photo_path['dirname'] . '/' . $photo_path['filename'] . '_m.' . $photo_path['extension'];
    }

    public function getPhotoHiddenFlg() {
        return $this->getPhotoEntry()->hidden_flg;
    }

    public function getApprovalStatusClass() {
        return $this->approval_status_classes[$this->approval_status];
    }

    public function getApprovalStatus() {
        return $this->approval_statuses[$this->approval_status];
    }

    public function getOpenGraphDescription() {
        /** @var CpPhotoActionService $cp_photo_action_service */
        $cp_photo_action_service = $this->getService('CpPhotoActionService');
        $cp_photo_action = $cp_photo_action_service->getCpPhotoAction($this->cp_action_id);
        $og_description = '';

        if ((!$this->photo_title && !$this->photo_comment) || (!$this->photo_title && $this->photo_comment)) { // 両方なしまたはコメントだけ
            $og_description = $this->cutLongText($cp_photo_action->title, 30);
        }elseif (($this->photo_title && $this->photo_comment) || ($this->photo_title && !$this->photo_comment)) { // 両方ありまたはタイトルだけ
            $og_description = $this->cutLongText($this->photo_title . ' - ' . $cp_photo_action->title, 30);
        }
        return $og_description;
    }

    public function getPhotoDetailUrl($brand_id, $directory_name) {
        /** @var PhotoStreamService $photo_stream_service */
        $photo_stream_service = $this->getService('PhotoStreamService');
        $photo_entry = $photo_stream_service->getPhotoEntryByPhotoUserId($this->id);
        return Util::constructBaseURL($brand_id, $directory_name) . 'photo/detail/' . $photo_entry->id;
    }
}
