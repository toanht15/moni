<?php
AAFW::import('jp.aainc.classes.CpInfoContainer');
AAFW::import('jp.aainc.classes.RequestUserInfoContainer');

class SettingBasicValidator extends aafwObject {

    // Service
    /** @var CpFlowService cp_flow_service */
    private $cp_flow_service;
    /** @var CpInfoService cp_info_service */
    private $cp_info_service;
    private $aafw_validator;

    // Variable
    private $post;
    private $files;
    private $data;
    private $is_manager;
    private $errors;
    private $cp;
    private $cp_info;
    private $cp_status;
    private $file_info;
    private $file_rectangle_info;

    // Validator Definition
    private $validator_definition = array(
        'title'                     => array('type' => 'str',   'length' => 80),
        'image_file'                => array('type' => 'file',  'size' => '5MB'),
        'image_rectangle_file'      => array('type' => 'file',  'size' => '15MB'),
        // 公開日時
        'public_date'               => array('type' => 'str',   'length' => 10),
        'publicTimeHH'              => array('type' => 'num',   'range' => array('>=' => 0, '<' => 24)),
        'publicTimeMM'              => array('type' => 'num',   'range' => array('>=' => 0, '<' => 60)),
        // 応募開始日時
        'start_date'                => array('type' => 'str',   'length' => 10),
        'openTimeHH'                => array('type' => 'num',   'range' => array('>=' => 0, '<' => 24)),
        'openTimeMM'                => array('type' => 'num',   'range' => array('>=' => 0, '<' => 60)),
        // 応募終了日時
        'end_date'                  => array('type' => 'str',   'length' => 10),
        'closeTimeHH'               => array('type' => 'num',   'range' => array('>=' => 0, '<' => 24)),
        'closeTimeMM'               => array('type' => 'num',   'range' => array('>=' => 0, '<' => 60)),
        // 当選発表日時
        'announce_date'             => array('type' => 'str',   'length' => 10),
        'announceTimeHH'            => array('type' => 'num',   'range' => array('>=' => 0, '<' => 24)),
        'announceTimeMM'            => array('type' => 'num',   'range' => array('>=' => 0, '<' => 60)),
        // キャンペーン終了日時
        'cp_page_close_date'        => array('type' => 'str',   'length' => 10),
        'cpPageCloseTimeHH'         => array('type' => 'num',   'range' => array('>=' => 0, '<' => 24)),
        'cpPageCloseTimeMM'         => array('type' => 'num',   'range' => array('>=' => 0, '<' => 60)),
        // 発表方法
        'shipping_method'           => array('type' => 'num',   'range' => array('>=' => 0, '<=' => 1)),
        // 当選者数
        'winner_count'              => array('type' => 'num',   'range' => array('>=' => 1)),
        // 当選者ラベル (ex.○○名様)
        'winner_label'              => array('type' => 'str',   'length' => 100),
        // 発表表示フラグ
        'announce_display_label_use_flg'    => array('type' => 'num',   'range' => array('>=' => 0,'<=' => 1)),
        // 発表表示
        'announce_display_label'    => array('type' => 'str',   'length' => 40),
        // セールスフォースID
        'salesforce_id'             => array('type' => 'str',   'length' => 20),
        // ページURL (シェアURLの/page以下)
        'page_url'                  => array('type' => 'str',   'length' => 20)
    );

    public function __construct($post, $files, $data, $is_manager = false) {
        parent::__construct();

        $this->post = $post;
        $this->files = $files;
        $this->data = $data;
        $this->is_manager = $is_manager;
        $this->errors = array();

        $this->setServices();
        $this->setVariables();
    }

    /***********************************************************
     * is~, can~: return boolean
     ***********************************************************/
    /**
     * @return bool
     */
    public function isManager() {
        return $this->is_manager === true;
    }

    /**
     * @return bool
     */
    public function isNotPermanent() {
        return $this->post['permanent_flg'] != Cp::PERMANENT_FLG_ON;
    }

    /**
     * @return bool
     */
    public function isNotShippingMethodPresent() {
        return $this->post['shipping_method'] != Cp::SHIPPING_METHOD_PRESENT;
    }

    public function isValid() {
        return count($this->errors) === 0;
    }

    /**
     * @return bool
     */
    public function canFix() {
        return $this->cp->fix_basic_flg != Cp::SETTING_FIX || $this->cp->status == Cp::STATUS_DEMO;
    }

    /**
     * @return bool
     */
    public function canSave() {
        return $this->cp->fix_basic_flg == Cp::SETTING_DRAFT && $this->post['save_type'] == Cp::SETTING_FIX && !count($this->errors);
    }

    /***********************************************************
     * validate
     ***********************************************************/
    public function validate() {
        // 保存処理が下書き保存 || 内容確定
        if (!in_array($this->post['save_type'], array(Cp::SETTING_DRAFT, Cp::SETTING_FIX))) {
            return false;
        }

        // aafwValidatorの作成・検証
        $this->checkAndSetRequired();
        $this->aafw_validator = new aafwValidator($this->getValidatorDefinition());
        $this->aafw_validator->validate($this->post + array('image_file' => $this->files['image_file']['name']));

        // エラー内容の取得
        $this->errors = $this->aafw_validator->getErrors();

        // 正方形画像バリデート
        $this->file_info = $this->validateImageFile('image_file', 360, 360);
        if (is_null($this->file_info)) {
            //ファイル送信されなくて、既存もなかった場合はエラー
            if( $this->post['save_type'] == Cp::SETTING_FIX && !$this->cp->image_url ){
                $this->setError('image_file', 'NOT_SET');
            }
        }

        // 長方形画像バリデート
        $this->file_rectangle_info = $this->validateImageFile('image_rectangle_file', 1000, 524);

        // 日付系のバリデート
        $this->insertDateTimeToData();
        $this->validateDateTime();

        // その他
        $this->validateReferenceUrl();
        $this->validateRestrictedAge();
    }

    /**
     * @param $name
     * @param $width
     * @param $height
     * @return array|null
     */
    public function validateImageFile($name, $width, $height) {
        $file_info = null;
        if ($this->files[$name]) {
            $fileValidator = new FileValidator($this->files[$name], FileValidator::FILE_TYPE_IMAGE);
            if (!$fileValidator->isValidFile()) {
                $this->setError($name, 'NOT_MATCHES');
            } else {
                $file_info = $fileValidator->getFileInfo();
            }
            $imageValidator = new ImageValidator($this->files[$name]['name']);
            if (!$imageValidator->isEqualSize($width, $height)) {
                $this->setError($name, 'NOT_MATCHES');
            }
        }

        return $file_info;
    }

    public function validateDateTime() {
        // CpValidatorの呼び出し
        /** @var CpValidator $cp_validator */
        $cp_validator = new CpValidator($this->data['brand']->id);

        // 編集可能な場合
        if (!$this->canFix() && !$this->isManager() && !$this->canSave()) {
            return;
        }

        // 公開日時、応募開始日時
        if ($this->canFix()) {
            if (!$cp_validator->isCorrectDate($this->data['publicTime'])) {
                $this->setError('public_date1', 'INVALID_TIME1');
            }

            if (!$cp_validator->isCorrectDate($this->data['startTime'])) {
                $this->setError('start_date1', 'INVALID_TIME1');
            }

            if ($this->post['use_cp_page_close_flg'] && !$cp_validator->isCorrectDate($this->data['closeTime'])) {
                $this->setError('cp_page_close_date1', 'INVALID_TIME1');
            }

            if ($this->post['use_public_date_flg'] && $cp_validator->getMax(array($this->data['publicTime'], $this->data['startTime'])) == $this->data['publicTime']) {
                $this->setError('public_date2', 'INVALID_TIME2');
            }
        }

        // 応募終了日時
        if ($this->isNotPermanent()) {
            if (!in_array($this->getCpStatus(), array(Cp::CAMPAIGN_STATUS_WAIT_ANNOUNCE, Cp::CAMPAIGN_STATUS_CLOSE))) {
                if (!$cp_validator->isCorrectDate($this->data['endTime'])) {
                    $this->setError('end_date1', 'INVALID_TIME1');
                }
            }

            if ($cp_validator->getMax(array($this->data['startTime'], $this->data['endTime'])) != $this->data['endTime']) {
                $this->setError('start_date2', 'INVALID_TIME2');
            }
        }

        // キャンペーン終了日時
        if ($this->post['shipping_method'] != Cp::SHIPPING_METHOD_PRESENT && $this->post['use_cp_page_close_flg']) {
            if ($this->cp->isIncentiveCp()) {
                if ($cp_validator->getMax(array($this->data['announceTime'], $this->data['closeTime'])) != $this->data['closeTime']) {
                    $this->setError('cp_page_close_date2', 'INVALID_TIME2');
                }
            } elseif ($this->isNotPermanent()) {
                if ($cp_validator->getMax(array($this->data['endTime'], $this->data['closeTime'])) != $this->data['closeTime']) {
                    $this->setError('cp_page_close_date4', 'INVALID_TIME2');
                }
            } else {
                if ($cp_validator->getMax(array($this->data['startTime'], $this->data['closeTime'])) != $this->data['closeTime']) {
                    $this->setError('cp_page_close_date3', 'INVALID_TIME2');
                }
            }
        }

        // 発表日時
        if ($this->cp->isIncentiveCp()) {
            if ($this->cp->shipping_method != Cp::SHIPPING_METHOD_PRESENT) {
                if (!in_array($this->getCpStatus(), array(Cp::CAMPAIGN_STATUS_WAIT_ANNOUNCE, Cp::CAMPAIGN_STATUS_CLOSE))
                    && !$cp_validator->isCorrectDate($this->data['announceTime'])) {
                    $this->setError('announce_date1', 'INVALID_TIME1');
                }

                if ($cp_validator->getMax(array($this->data['endTime'], $this->data['announceTime'])) != $this->data['announceTime']) {
                    $this->setError('announce_date2', 'INVALID_TIME2');
                }
            }

            // winner_countを更新するときにチェックします。
            if ($this->post['winner_count'] && ($this->post['winner_count'] > $this->cp->winner_count)) {
                /** @var CouponManager $coupon_manager */
                $coupon_manager = $this->getService('CpCouponActionManager');
                /** @var CouponService $coupon_service */
                $coupon_service = $this->getService('CouponService');

                $first_action_group = $this->cp_flow_service->getCpActionGroupsByCpId($this->cp->id)->current();
                $cp_actions = $this->cp_flow_service->getCpActionsByCpActionGroupId($first_action_group->id);
                $coupon_reserved_num = array();

                foreach ($cp_actions as $cp_action) {
                    if ($cp_action->type != CpAction::TYPE_COUPON) {
                        continue;
                    }
                    $coupon_action = $coupon_manager->getConcreteAction($cp_action);
                    if (!$coupon_action->coupon_id) {
                        continue;
                    }
                    $coupon = $coupon_service->getCouponById($coupon_action->coupon_id);
                    list($used, $total) = $coupon_service->getCouponStatisticByCouponId($coupon->id);
                    if (!$coupon_reserved_num[$coupon->id]) {
                        $coupon_reserved_num[$coupon->id] = $coupon->countReservedNum();
                    }
                    if (($total - $coupon_reserved_num[$coupon->id]) < ($this->post['winner_count'] - $this->cp->winner_count)) {
                        $this->setError('winner_count', 'NOT_ENOUGH_COUPON');
                        break;
                    }
                    $coupon_reserved_num[$coupon->id] += ($this->post['winner_count'] - $this->cp->winner_count);
                }
            }
        }

        if ($this->post['announce_display_label_use_flg'] == 1 && $this->isEmpty($this->post['announce_display_label'])) {
            $this->setError('announce_display_label', 'NOT_REQUIRED');
        }
    }

    public function validateReferenceUrl() {
        if (isset($this->post['reference_url_type'])) {
            if ($this->post['reference_url_type'] == Cp::REFERENCE_URL_TYPE_CP) {
                $this->post['reference_url'] = $this->cp->getUrlPath();
            } else {
                $this->post['reference_url'] = '/' . $this->data['brand']->directory_name . '/page/' . $this->post['page_url'];
            }

            // ホワイトベルグ対応
            if (!in_array($this->data['brand']->id, array(218, 441))) {
                $ref_cps = $this->cp_flow_service->getCpByBrandIdAndReferenceUrl($this->data['brand']->id, $this->post['reference_url']);
                foreach ($ref_cps as $ref_cp) {
                    if ($ref_cp->id != $this->cp->id) {
                        $this->setError('page_url', 'DUPLICATED_REFERENCE_URL');

                        break;
                    }
                }
            }
        }
    }

    public function validateRestrictedAge() {
        if ($this->post['restricted_age_flg'] == Cp::CP_RESTRICTED_AGE_FLG_ON) {
            /** @var BrandPageSettingService $page_setting_service */
            $brand_page_setting_service = $this->getService('BrandPageSettingService');
            $brand_page_setting = $brand_page_setting_service->getPageSettingsByBrandId($this->data['brand']->id);

            if ($brand_page_setting->privacy_required_restricted && $this->post['restricted_age'] < $brand_page_setting->restricted_age) {
                $this->setError('restricted_age', 'CP_RESTRICTED_AGE_NOT_MATCH');
            }
        }
    }


    /***********************************************************
     * getter
     ***********************************************************/
    /**
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * @return mixed
     */
    public function getCp() {
        return $this->cp;
    }

    /**
     * @return mixed
     */
    public function getCpInfo() {
        return $this->cp_info;
    }

    /**
     * @return mixed
     */
    public function getFileInfo() {
        return $this->file_info;
    }

    /**
     * @return mixed
     */
    public function getFileRectangleInfo() {
        return $this->file_rectangle_info;
    }

    /**
     * @return array
     */
    public function getValidatorDefinition() {
        return $this->validator_definition;
    }

    /**
     * @return mixed
     */
    public function getPostData() {
        return $this->post;
    }

    /**
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getCpStatus() {
        if ($this->cp_status) {
            return $this->cp_status;
        }

        $this->cp_status = RequestUserInfoContainer::getInstance()->getStatusByCp($this->cp);

        return $this->cp_status;
    }

    /***********************************************************
     * setter
     ***********************************************************/
    /**
     * @param $key
     * @param $val
     */
    public function setError($key, $val) {
        $this->errors[$key] = $val;
    }

    public function setServices() {
        $this->cp_flow_service = $this->getService('CpFlowService');
        $this->cp_info_service = $this->getService('CpInfoService');
    }

    public function setVariables() {
        $this->cp       = CpInfoContainer::getInstance()->getCpById($this->post['cp_id']);
        $this->cp_info  = $this->cp_info_service->getCpInfoByCpId($this->post['cp_id']);
        $this->cp_info->cp_id = $this->cp_info->cp_id ?: $this->post['cp_id'];

        if ($this->canFix()) {
            $this->post['permanent_flg'] = $this->isNotPermanent() ? Cp::PERMANENT_FLG_OFF : Cp::PERMANENT_FLG_ON;
        } else {
            $this->post['permanent_flg'] = $this->cp->permanent_flg;
        }
    }

    /**
     * @param $key
     * @param bool|true $required
     */
    public function setRequired($key, $required = true) {
        $this->validator_definition[$key]['required'] = $required;
    }

    public function checkAndSetRequired() {
        // 下書き保存の場合
        if ($this->post['save_type'] == Cp::SETTING_DRAFT) {
            $this->setRequired('winner_count', $this->cp->isIncentiveCp());

            return;
        }

        // 編集フラグ (チェックボックス) がONの時に必須チェック
        $this->setRequired('recruitment_note', $this->post['show_recruitment_note'] == Cp::FLAG_SHOW_VALUE);
        $this->setRequired('extend_tag', $this->post['use_extend_tag'] == Cp::FLAG_SHOW_VALUE);
        $this->setRequired('join_limit_sns', $this->post['join_limit_sns_flg'] == Cp::JOIN_LIMIT_SNS_ON);
        $this->setRequired('restricted_age', $this->post['restricted_age_flg'] == Cp::CP_RESTRICTED_AGE_FLG_ON);
        $this->setRequired('restricted_gender', $this->post['restricted_gender_flg'] == Cp::CP_RESTRICTED_GENDER_FLG_ON);
        $this->setRequired('restricted_addresses', $this->post['restricted_address_flg'] == Cp::CP_RESTRICTED_ADDRESS_FLG_ON);
        // 編集フラグ (チェックボックス) がONの時に必須チェック + α
        $this->setRequired('page_url', $this->post['reference_url_type'] == Cp::REFERENCE_URL_TYPE_LP && $this->isManager());
        $this->setRequired('winner_label', $this->post['show_winner_label'] == Cp::FLAG_SHOW_VALUE && $this->cp->isIncentiveCp());

        // クローズ日時設定フラグがONの時に必須チェック
        if ($this->post['use_cp_page_close_flg']) {
            $this->setRequired('cp_page_close_date');
            $this->setRequired('cpPageCloseTimeHH');
            $this->setRequired('cpPageCloseTimeMM');
        }

        // Managerログイン時に必須チェック
        if ($this->isManager()) {
            $this->setRequired('salesforce_id');
        }

        if ($this->canFix()) {
            // 下書き状態 || デモキャンペーン

            $this->setRequired('title');
            $this->setRequired('start_date');
            $this->setRequired('openTimeHH');
            $this->setRequired('openTimeMM');

            // 公開日時フラグがONの場合
            if ($this->post['set_public_date_flg'] == Cp::PUBLIC_DATE_ON) {
                $this->setRequired('public_date');
                $this->setRequired('publicTimeHH');
                $this->setRequired('publicTimeMM');
            }

            // 常設キャンペーンでない場合
            if ($this->cp->isIncentiveCp()) {
                $this->setRequired('shipping_method');
                $this->setRequired('winner_count');
                $this->setRequired('announce_display_label_use_flg');
            }
        } else if ($this->isManager()) {
            // 確定状態 (デモキャンペーンを除く) + マネージャー権限

            // 常設キャンペーンでない場合 + 発表方法がプレゼントではない場合
            if ($this->cp->isIncentiveCp() && $this->isNotShippingMethodPresent()) {
                $this->setRequired('announce_date');
                $this->setRequired('announceTimeHH');
                $this->setRequired('announceTimeMM');
            }

            // キャンペーンが永続でない場合
            if ($this->isNotPermanent()) {
                $this->setRequired('end_date');
                $this->setRequired('closeTimeHH');
                $this->setRequired('closeTimeMM');
            }
        }
    }

    /**
     * @param $date
     * @param string $hour
     * @param string $minute
     * @param string $second
     * @return string
     */
    public function createDateTime($date, $hour = "00", $minute = "00", $second = "00") {
        return $date . " " . $hour . ":" . $minute . ":" . $second;
    }

    /**
     * @return array
     */
    public function insertDateTimeToData() {
        // 応募開始時刻・公開時刻
        if ($this->canFix()) {
            // フォーム内容
            $this->data['startTime']  = $this->createDateTime($this->post['start_date'], $this->post['openTimeHH'], $this->post['openTimeMM']);
            $this->data['publicTime'] = $this->createDateTime($this->post['public_date'], $this->post['publicTimeHH'], $this->post['publicTimeMM']);
        } else {
            // 既存のもの
            $this->data['startTime']  = DateTime::createFromFormat('Y-m-d H:i:s', $this->cp->start_date)->format('Y/m/d H:i:s');
            $this->data['publicTime'] = DateTime::createFromFormat('Y-m-d H:i:s', $this->cp->public_date)->format('Y/m/d H:i:s');
        }

        // 編集フラグにより値を出し分ける
        $this->data['publicTime'] = $this->post['use_public_date_flg'] ? $this->data['publicTime'] : $this->data['startTime'];
        $this->data['closeTime']  = $this->post['use_cp_page_close_flg'] ? $this->createDateTime($this->post['cp_page_close_date'], $this->post['cpPageCloseTimeHH'], $this->post['cpPageCloseTimeMM']) : '0000-00-00 00:00:00';
        $this->data['use_cp_page_close_flg'] = $this->post['use_cp_page_close_flg'] ?: 0;

        // キャンペーン終了時刻
        if ($this->isNotPermanent() && ($this->canFix() || (!$this->canFix() && $this->isManager()))) {
            if ($this->post['closeTimeDate']) {
                $this->data['endTime'] = $this->createDateTime($this->post['end_date'], 23, 59, 59);
            } else {
                $this->data['endTime'] = $this->createDateTime($this->post['end_date'], $this->post['closeTimeHH'], $this->post['closeTimeMM'], 59);
            }
        }

        // 当選発表時刻
        if ($this->cp->isIncentiveCp() && $this->post['announce_date']) {
            $this->data['announceTime'] = $this->createDateTime($this->post['announce_date'], $this->post['announceTimeHH'], $this->post['announceTimeMM']);
        }
    }

}

