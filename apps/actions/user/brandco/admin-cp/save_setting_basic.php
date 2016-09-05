<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.entities.Cp');

class save_setting_basic extends BrandcoPOSTActionBase {
    
    protected $ContainerName = 'save_setting_basic';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => 'edit_setting_basic/{cp_id}?mid=action-not-filled',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    private $file_info = array();
    private $file_rectangle_info = array();
    /** @var  CpFlowService $cp_service */
    private $cp_service;
    /** @var  CpInfoService $cp_info_service */
    private $cp_info_service;
    private $validator_service;

    public function doThisFirst() {
        // サービスの呼び出し
        $this->cp_service = $this->createService('CpFlowService');
        $this->cp_info_service = $this->createService('CpInfoService');
    }

    public function validate() {
        // CP主催者の検証
        $this->Data['brand'] = $this->getBrand();
        $this->validator_service = new CpValidator($this->Data['brand']->id);
        if (!$this->validator_service->isOwner($this->POST['cp_id'])) {
            return false;
        }

        // SettingBasicValidatorの作成
        /** @var SettingBasicValidator $setting_basic_validator */
        $setting_basic_validator = new SettingBasicValidator($this->POST, $this->FILES, $this->Data, $this->isLoginManager());
        $setting_basic_validator->validate();

        // 変数に値を格納
        $this->cp       = $setting_basic_validator->getCp();
        $this->cp_info  = $setting_basic_validator->getCpInfo();
        $this->file_info = $setting_basic_validator->getFileInfo();
        $this->file_rectangle_info = $setting_basic_validator->getFileRectangleInfo();

        $this->POST     = $setting_basic_validator->getPostData();
        $this->Data     = array_merge($this->Data, $setting_basic_validator->getData());

        if (!$setting_basic_validator->isValid()) {
            $this->Validator = new aafwValidator();
            foreach ($setting_basic_validator->getErrors() as $key => $val) {
                $this->Validator->setError($key, $val);
            }

            return false;
        }

        return true;
    }

    function doAction() {

        if($this->FILES['image_file']){
            // メインバナー画像 保存
            $this->cp->image_url = StorageClient::getInstance()->putObject(
                StorageClient::toHash('brand/'.$this->Data['brand']->id . '/cp_setting_basic/' . StorageClient::getUniqueId()), $this->file_info);
        } else if ($this->POST['image_url']) {
            $this->cp->image_url = $this->POST['image_url'];
        }

        if ($this->FILES['image_rectangle_file']) {
            // モニプラメディア画像 保存
            $this->cp->image_rectangle_url = StorageClient::getInstance()->putObject(
                StorageClient::toHash('brand/'.$this->Data['brand']->id . '/cp_setting_basic/' . StorageClient::getUniqueId()), $this->file_rectangle_info);
        } elseif (!$this->POST['rectangle_flg']) {
            $this->cp->image_rectangle_url = "";
        }

        // インセンティブありしか更新できない
        if (!$this->cp->isNonIncentiveCp()) {
            // winner_countを更新するときに使うクーポンを更新する。
            if ($this->isNumeric($this->POST['winner_count'])) {
                $cp_actions = $this->cp_service->getCpActionsByCpId($this->cp->id);
                $coupon_manager = new CpCouponActionManager();
                /** @var CouponService $coupon_service */
                $coupon_service = $this->createService('CouponService');
                foreach ($cp_actions as $cp_action) {
                    if ($cp_action->type != CpAction::TYPE_COUPON) {
                        continue;
                    }
                    $coupon_action = $coupon_manager->getConcreteAction($cp_action);
                    if (!$coupon_action->coupon_id) {
                        continue;
                    }
                    $coupon = $coupon_service->getCouponById($coupon_action->coupon_id);
                    $coupon->reserved_num += ($this->POST['winner_count'] - $this->cp->winner_count);
                    $coupon_service->coupons->save($coupon);
                }
            }

            if ($this->Data['announceTime']) {
                $this->cp->announce_date = $this->Data['announceTime'];
            }
        }

        // 常設キャンペーン以外更新を実行する
        if (!$this->isPermanent($this->POST['permanent_flg'])) {
            if ($this->Data['endTime']) {
                $this->cp->end_date = $this->Data['endTime'];
            }
        }

        $this->cp->use_cp_page_close_flg = $this->Data['use_cp_page_close_flg'];

        if ($this->Data['closeTime']) {
            $this->cp->cp_page_close_date = $this->Data['closeTime'];
        }

        //ドラフト保存、あるいはマネージャー権限しか更新できない
        if ($this->cp->status == Cp::STATUS_DEMO || $this->cp->fix_basic_flg != Cp::SETTING_FIX || ($this->cp->fix_basic_flg == Cp::SETTING_FIX && $this->isLoginManager())) {

            if (!$this->cp->isNonIncentiveCp()) {
                if ($this->POST['show_winner_label'] == Cp::FLAG_SHOW_VALUE) {
                    $this->cp->show_winner_label = Cp::FLAG_SHOW_VALUE;
                } else {
                    $this->cp->show_winner_label = Cp::FLAG_HIDE_VALUE;
                }

                if ($this->POST['winner_label']) {
                    $this->cp->winner_label = $this->POST['winner_label'];
                }

                if (!$this->isEmpty($this->POST['shipping_method'])) {
                    $this->cp->shipping_method = $this->POST['shipping_method'];
                }
            }

            if ($this->POST['show_recruitment_note'] == Cp::FLAG_SHOW_VALUE) {
                $this->cp->show_recruitment_note = Cp::FLAG_SHOW_VALUE;
            } else {
                $this->cp->show_recruitment_note = Cp::FLAG_HIDE_VALUE;
            }

            if ($this->POST['recruitment_note']) {
                $this->cp->recruitment_note = $this->POST['recruitment_note'];
            } else {
                $this->cp->recruitment_note = '';
            }

            if ($this->POST['join_limit_sns_flg'] == Cp::JOIN_LIMIT_SNS_ON) {
                $this->cp->join_limit_sns_flg = Cp::JOIN_LIMIT_SNS_ON;
            } else {
                $this->cp->join_limit_sns_flg = Cp::JOIN_LIMIT_SNS_OFF;
            }

            $this->POST['join_limit_sns'] = $this->POST['join_limit_sns'] ? $this->POST['join_limit_sns'] : array();

            $snsArray = array();

            foreach ($this->POST['join_limit_sns'] as $postKey => $postValue) {
                $snsArray[] = $postValue;
            }
            $this->cp->refreshJoinLimitSns($snsArray);

            if ($this->POST['restricted_age_flg'] == Cp::CP_RESTRICTED_AGE_FLG_ON) {
                $this->cp->restricted_age_flg   = Cp::CP_RESTRICTED_AGE_FLG_ON;
                $this->cp->restricted_age       = $this->POST['restricted_age'];
            } else {
                $this->cp->restricted_age_flg   = Cp::CP_RESTRICTED_AGE_FLG_OFF;
            }

            if ($this->POST['restricted_gender_flg'] == Cp::CP_RESTRICTED_GENDER_FLG_ON) {
                $this->cp->restricted_gender_flg = Cp::CP_RESTRICTED_GENDER_FLG_ON;
                $this->cp->restricted_gender     = $this->POST['restricted_gender'];
            } else {
                $this->cp->restricted_gender_flg = Cp::CP_RESTRICTED_GENDER_FLG_OFF;
            }

            $this->cp->restricted_address_flg = $this->POST['restricted_address_flg'] == Cp::CP_RESTRICTED_ADDRESS_FLG_ON ? Cp::CP_RESTRICTED_ADDRESS_FLG_ON : Cp::CP_RESTRICTED_ADDRESS_FLG_OFF;
            $this->cp->updateCpRestrictedAddress($this->POST['restricted_addresses']);

            if (!$this->isEmpty($this->POST['salesforce_id'])) {
                $this->cp_info->salesforce_id = $this->POST['salesforce_id'];
            }

            $this->cp->announce_display_label_use_flg = $this->POST['announce_display_label_use_flg'];
            if ($this->cp->announce_display_label_use_flg == 1) {
                $this->cp->announce_display_label = $this->POST['announce_display_label'];
            } elseif ($this->cp->announce_display_label_use_flg == 0) {
                $this->cp->announce_display_label = '';
            }
        }

        if ($this->POST['use_extend_tag'] == Cp::FLAG_SHOW_VALUE) {
            $this->cp->use_extend_tag = Cp::FLAG_SHOW_VALUE;
        } else {
            $this->cp->use_extend_tag = Cp::FLAG_HIDE_VALUE;
        }

        if ($this->POST['extend_tag']) {
            $this->cp->extend_tag = $this->POST['extend_tag'];
        } else {
            $this->cp->extend_tag = "";
        }

        if (!$this->isEmpty($this->POST['reference_url'])) {
            $this->cp->reference_url = $this->POST['reference_url'];
        } elseif (!$this->cp->reference_url) {
            $this->cp->reference_url = $this->cp->getUrlPath($this->getBrand());
        }

        try {

            $this->cp_service->getCpModel()->begin();
            
            if ($this->cp->fix_basic_flg != Cp::SETTING_FIX || $this->cp->status == Cp::STATUS_DEMO) {
                if ($this->cp->isNonIncentiveCp()) {
                    $this->cp->permanent_flg = $this->POST['permanent_flg'];
                } else {
                    // スピードくじアクションがあれば、winner_countの整合性をつける
                    $cp_action = $this->cp_service->getInstantWinActionByCpId($this->cp->id);
                    if ($cp_action) {
                        /** @var CpInstantWinActionManager $cp_instant_win_action_manager */
                        $cp_instant_win_action_manager = new CpInstantWinActionManager();
                        $cp_instant_win_action = $cp_instant_win_action_manager->getCpConcreteActionByCpActionId($cp_action['id']);

                        /** @var InstantWinPrizeService $instant_win_prize_service */
                        $instant_win_prize_service = $this->getService('InstantWinPrizeService');
                        $instant_win_prize = $instant_win_prize_service->getInstantWinPrizeByPrizeStatus($cp_instant_win_action->id, InstantWinPrizes::PRIZE_STATUS_PASS);
                        $instant_win_prize->max_winner_count = $this->POST['winner_count'];
                        $instant_win_prize_service->updateInstantWinPrize($instant_win_prize);
                    }

                    $this->cp->winner_count = $this->POST['winner_count'];
                }

                //一番初めのアクションのタイトルを更新    
                $first_action = $this->cp_service->getFirstActionOfCp($this->cp->id);
                $actionData = $first_action->getCpActionData();
                $actionData->title = $this->POST['title'];
                $first_action->getActionManagerClass()->updateConcreteAction($first_action, $actionData->getValues());

                $this->cp->public_date = $this->Data['publicTime'];
                $this->cp->start_date = $this->Data['startTime'];
                $this->cp->fix_basic_flg = $this->POST['save_type'];
            }

            $this->cp_service->updateCp($this->cp);
            $this->cp_info_service->saveCpInfo($this->cp_info);

            $this->cp_service->getCpModel()->commit();

        } catch (Exception $e) {
            $this->cp_service->getCpModel()->rollback();
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error($e);
            return 'redirect: ' . Util::rewriteUrl('admin-cp', 'edit_setting_basic', array($this->cp->id), array('mid' => 'failed'));
        }

        $this->Data['saved'] = 1;

        if ($this->POST['save_type'] == Cp::SETTING_FIX) {
            $query['mid'] = 'action-saved';
        } else {
            $query['mid'] = 'action-draft';
        }

        $return = 'redirect: ' . Util::rewriteUrl('admin-cp', 'edit_setting_basic', array($this->cp->id), $query);

        return $return;
    }

    /**
     * @param $status
     * @return bool
     */
    public function isPermanent($status) {
        return $status == Cp::PERMANENT_FLG_ON;
    }
}
