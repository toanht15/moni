<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserMessagesThreadValidator');
AAFW::import('jp.aainc.classes.RequestUserInfoContainer');
AAFW::import('jp.aainc.classes.CpInfoContainer');
AAFW::import('jp.aainc.classes.brandco.auth.trait.BrandcoAuthTrait');
AAFW::import('jp.aainc.classes.core.UserManager');

class thread extends BrandcoGETActionBase {

    use BrandcoAuthTrait;

    public $NeedOption = array(BrandOptions::OPTION_CRM, BrandOptions::OPTION_CP);
    public $NeedUserLogin = true;
    public $NeedRedirect = true;
    public $checkCpClosed = true;

    private $cp_user;
    private $cp;

    private $entry_questionnaire_data;

    public function doThisFirst() {
        $this->Data['cp_id']    = $this->cp_id ?: $this->GET['exts'][0];
        $this->Data['user_id']  = $this->getLoginInfo()['userInfo']->id;
    }

    public function validate() {

        // ログイン情報を取得
        $this->Data['loginInfo'] = $this->getLoginInfo();
        $this->Data['userInfo'] = RequestUserInfoContainer::getInstance()->getByMoniplaUserId($this->Data['loginInfo']['userInfo']->id);
        $this->Data['user_id'] = $this->Data['userInfo']->id;
        if ($this->Data['cp_id'] === null) {
            $msg = "cp_id is null! : GET params=" . json_encode($this->GET);
            aafwLog4phpLogger::getHipChatLogger()->warn($msg);
            aafwLog4phpLogger::getDefaultLogger()->warn($msg);
            return "404";
        }

        $this->cp = CpInfoContainer::getInstance()->getCpById($this->Data['cp_id']);
        if (!$this->cp) {
            return "404";
        }

        if ($this->Data['loginInfo']['userInfo']->id) {
            /** @var CpUserService $cp_user_service */
            $cp_user_service = $this->createService("CpUserService");
            $this->cp_user = $cp_user_service->getCpUserByCpIdAndUserId($this->cp->id, $this->Data['userInfo']->id);
        }

        if ($this->cp->status == Cp::STATUS_DEMO && $this->getSession("demo_token_".$this->cp->id) != hash("sha256", $this->cp->created_at) && !$this->cp_user) {
            return "404";
        }

        $validator = new UserMessagesThreadValidator($this->Data['cp_id'], $this->Data['userInfo']->id, $this->getBrand()->id, $this->cp, $this->cp_user);
        $validator->validate();
        if (!$validator->isValid()) {
            if ($validator->getErrors()['cp_user_id'] && $this->cp->type == Cp::TYPE_CAMPAIGN) {
                if ($this->cp_user->demography_flg != CpUser::DEMOGRAPHY_STATUS_NOT_MATCH) {
                    return 'redirect: ' . Util::rewriteUrl('', 'campaigns', array($this->Data['cp_id']));
                }
            } else {
                return '404';
            }
        }
        return true;
    }

    function doAction() {
        // ogの設定
        $this->Data['pageStatus']['og'] = array(
            'title' =>'メッセージ - '.$this->getBrand()->name,
        );

        // コンテキストにセット
        $this->Data['pageStatus']['brand'] = $this->Data['brand'];
        $this->Data['pageStatus']['cp'] = $this->cp;

        $cp_users = aafwEntityStoreFactory::create('CpUsers');

        try {
            $cp_users->begin();

            /** @var  $cp_user_service CPUserService */
            $cp_user_service = $this->createService('CpUserService');
            /** @var CpFlowService $cp_flow_service */
            $cp_flow_service = $this->createService('CpFlowService');
            /** @var BrandcoAuthService $brandco_auth_service */
            $brandco_auth_service = $this->getBrandcoAuthService();

            // キャンペーンユーザーを取得
            $cp_user = $this->cp_user;
            if ($cp_user === null) {
                $cp_user = $cp_user_service->getCpUserByCpIdAndUserId($this->Data['cp_id'], $this->Data['userInfo']->id);
            }
            $messages = $cp_user_service->getAllCpUserActionMessagesByCpUserIdOrderByActionOrder($cp_user->id)->toArray();

            if ($this->canLoadNextCpAction(count($messages), $messages[0]->cp_action_id)) {
                $next_action = $cp_flow_service->getCpNextActionByCpActionId($messages[0]->cp_action_id);

                if ($next_action->id) {
                    $cp_action = CpInfoContainer::getInstance()->getCpActionById($next_action->cp_next_action_id);
                    $manager = $cp_action->getActionManagerClass();
                    $concrete_action = $manager->getConcreteAction($cp_action);

                    list($next_message, $next_action_status) = $cp_user_service->sendActionMessage($this->cp_user->id, $cp_action, $concrete_action, true);
                    $messages[] = $next_message;
                }
            }

            // メッセージを既読にする
            $cp_user_service->readCpUserActionMessages($messages, $this->cp, $cp_user);

            $cp_action_ids = array();
            $cp_action_map = array();
            foreach ($messages as $message) {
                $cp_action_ids[] = $message->cp_action_id;
                $cp_action_map[$message->cp_action_id] = CpInfoContainer::getInstance()->getCpActionById($message->cp_action_id);
            }
            CpActions::loadActionSpecificCatalogs($cp_action_map);

            /** @var CpUserActionStatusService $cp_user_action_status_service */
            $cp_user_action_status_service = $this->createService('CpUserActionStatusService');
            $cp_user_action_status_map = $cp_user_action_status_service->getCpActionStatusMapByIds($cp_user->id, $cp_action_ids);

            // 各メッセージのアクション情報を取得する。
            $message_info_list = array();
            foreach ($messages as $message) {
                $cp_action = $cp_action_map[$message->cp_action_id];
                if (Util::isNullOrEmpty($cp_action)) {
                    aafwLog4phpLogger::getDefaultLogger()->warn('cp_action is null! : cp_action_id =' . $message->cp_action_id . ' - cp_user_id = ' . $cp_user->id);
                    continue;
                }

                $manager = $cp_action->getActionManagerClass();
                $concrete_action = $manager->getConcreteAction($cp_action);
                $action_status = $cp_user_action_status_map[$message->cp_action_id];

                $message_info = array(
                    "message" => $message,
                    "cp_action" => $cp_action,
                    "concrete_action" => $concrete_action,
                    "action_status" => $action_status
                );

                $message_info_list[] = $message_info;
            }

            $cp = $this->cp;

            $cp_action_type_set = $cp_flow_service->checkCpActionTypesInCp($cp->id, array(CpAction::TYPE_QUESTIONNAIRE, CpAction::TYPE_GIFT, CpAction::TYPE_INSTAGRAM_HASHTAG, CpAction::TYPE_POPULAR_VOTE));
            $this->Data['hasQuestionnaire'] = $cp_action_type_set[CpAction::TYPE_QUESTIONNAIRE] === true;
            $this->Data['hasGift']          = $cp_action_type_set[CpAction::TYPE_GIFT] === true;
            $this->Data['hasInstagramHashtag'] = $cp_action_type_set[CpAction::TYPE_INSTAGRAM_HASHTAG] === true;
            $this->Data['hasPopularVote'] = $cp_action_type_set[CpAction::TYPE_POPULAR_VOTE] === true;

            $first_concrete_action = $message_info_list[0]['concrete_action'];
            $cp_status = RequestuserInfoContainer::getInstance()->getStatusByCp($cp);
            $cp_info = $cp_flow_service->getCampaignInfo($cp, $this->brand, $first_concrete_action, $cp_status);

            // コンテキストに保存
            $this->Data["cp_user"] = $cp_user;
            $this->Data["message_info_list"] = $message_info_list;
            $this->Data["cp_info"] = $cp_info;
            if ($this->canPrefill($message_info_list)) {
                $this->Data["brands_users_relation_id"] = $this->getBrandsUsersRelation()->id;
            }

            $cp_users->commit();

            // すでに参加済みかどうか
            $this->Data['already_joined'] = count($messages) > 1;

            // User関連情報の取得
            $user = $this->Data['userInfo'];
            $relation = $this->getBrandsUsersRelation();
            $userInfo = $brandco_auth_service->getUserInfoByQuery($user->monipla_user_id);
            
            // UserManagerの取得
            $monipla_core = $this->getMoniplaCore();
            $userManager = new UserManager($userInfo, $monipla_core);

            // 再取得するプロフィールアンケート項目の取得
            $this->Data['entry_questionnaire_data'] = $this->getEntryQuestionnaireData($first_concrete_action->cp_action_id);

            // UserProfileFormを表示する必要があるかどうか
            $this->Data['pageStatus']['needDisplayUserProfileForm'] = !$this->Data['already_joined'] && ($user->provisional_flg || !$userInfo->mailAddress);
            $this->Data['pageStatus']['needDisplayPersonalForm'] = false;
            $this->Data['pageStatus']['isFirstEntryRead'] = $cp_flow_service->isEntryActionWithProfileQuestionnaires($relation, count($message_info_list), $message_info_list[0]['cp_action']);
            if ($relation->personal_info_flg == BrandsUsersRelation::SIGNUP_WITHOUT_INFO || $cp->requireRestriction($this->cp_user)) {
                AAFW::import('jp.aainc.classes.core.UserAttributeManager');
                AAFW::import('jp.aainc.classes.core.ShippingAddressManager');

                $this->Data['pageStatus']['needDisplayPersonalForm'] = true;

                $userAttributeManager = new UserAttributeManager($userInfo, $monipla_core);
                $shippingAddressManager = new ShippingAddressManager($userInfo, $monipla_core);

                $preData = array();

                $birthday = $userAttributeManager->getBirthDay();
                if ($birthday) {
                    $preData['birthDay_y'] = date('Y', strtotime($birthday));
                    $preData['birthDay_m'] = date('n', strtotime($birthday));
                    $preData['birthDay_d'] = date('j', strtotime($birthday));
                }

                $preData['sex'] = $userAttributeManager->getSex();

                $shippingAddress = $shippingAddressManager->getShippingAddress();
                foreach ($shippingAddress as $key => $value) {
                    $preData[$key] = $value;
                }

                $this->assign('ActionForm', $preData);
            } else if ($relation->personal_info_flg == BrandsUsersRelation::FORCE_WITH_INFO && $this->IsNotFinished($message_info_list)) {
                // 補填対応
                $this->Data['pageStatus']['needDisplayPersonalForm'] = true;
                $this->Data['ignore_prefill'] = true;
            } else if (!$this->Data['cp_user']->isNotMatchDemography() && $this->Data['pageStatus']['isFirstEntryRead'] && $this->Data['entry_questionnaire_data']['has_entry_questionnaire']) {
                $this->Data['pageStatus']['needDisplayPersonalForm'] = true;
            }

            if ($cp->status == Cp::STATUS_DEMO) {
                $this->Data["pageStatus"]["demo_info"]["is_demo_cp"] = true;
                $this->Data["pageStatus"]["demo_info"]["demo_cp_url"] = $cp->getDemoUrl();
                $this->Data["pageStatus"]["demo_info"]["cp_id"] = $cp->id;
            }

            if ($this->Data['pageStatus']['needDisplayUserProfileForm']) {
                $this->Data['ActionForm']['name'] = $userInfo->name;
                $this->Data['ActionForm']['mail_address'] = $userInfo->mailAddress ?: $userManager->getMailAddressCandidate();
            }

            list($demography_stt, $demography_err) = $this->getDemographyStatus($this->cp, $this->cp_user);
            $this->Data['pageStatus']['isNotMatchDemography'] = $demography_stt;
            $this->Data['pageStatus']['demographyErrors'] = $demography_err;

            $this->Data['pageStatus']['is_whitelist'] = $this->getBrand()->isDisallowedBrand();

            $this->Data['pageStatus']['from_id'] = $this->cp_user->from_id;

            $first_cp_action_group_id = $message_info_list[0]['cp_action']->cp_action_group_id;
            $this->Data['last_cp_action_in_first_group'] = $cp_flow_service->getLastActionInGroupByGroupId($first_cp_action_group_id);

            // インジケーターの設定
            $this->Data['shown_indicator'] = $this->cp->isCpTypeCampaign() && !$this->Data['pageStatus']['isNotMatchDemography'];
            $this->Data['shown_monipla_media_link'] = $this->cp->isCpTypeMessage() || $this->Data['pageStatus']['is_whitelist'] || ($this->cp->getSynCp() && Util::isSmartPhone()) ? 0 : 1;
            $this->Data['can_display_syn_next'] = Util::isSmartPhone() && $this->cp->isSynCpAndFromSynMenu($this->cp_user->from_id);
            $this->Data['map_increment_gauge'] = $this->Data['shown_indicator'] ? $this->createMapIncrementGauge() : array();
            return 'user/brandco/messages/thread.php';
        } catch (Exception $e) {
            $cp_users->rollback();
            throw $e;
        }
    }

    private function IsNotFinished($message_info_list) {
        return count($message_info_list) == 1 && $message_info_list[0]['cp_action']->isLegalOpeningCpAction();
    }

    private function canPrefill($message_info_list) {
        return count($message_info_list) == 1 && $message_info_list[0]['cp_action']->isLegalOpeningCpAction() && $message_info_list[0]['cp_action']->prefill_flg == 1;
    }

    private function getEntryQuestionnaireData($first_cp_action_id) {
        if (!$this->entry_questionnaire_data) {
            /** @var CpEntryProfileQuestionnaireService $cp_entry_profile_questionnaire_service */
            $cp_entry_profile_questionnaire_service = $this->getService('CpEntryProfileQuestionnaireService');
            $questionnaires = $cp_entry_profile_questionnaire_service->getQuestionnairesByCpActionId($first_cp_action_id);

            $this->entry_questionnaire_data['entry_questionnaires'] = $cp_entry_profile_questionnaire_service->convertQuestionnairesToMap($questionnaires);
            $this->entry_questionnaire_data['has_entry_questionnaire'] = $cp_entry_profile_questionnaire_service->hasEntryQuestionnaire($this->entry_questionnaire_data['entry_questionnaires']);
        }

        return $this->entry_questionnaire_data;
    }

    /**
     * @param $msg_count
     * @param $first_cp_action_id
     * @return bool
     */
    private function canLoadNextCpAction($msg_count, $first_cp_action_id) {
        if ($this->cp->isCpTypeMessage()) return false;

        if ($msg_count != 1) return false;

        if ($this->Data['userInfo']->provisional_flg) return false;

        if ($this->getBrandsUsersRelation()->personal_info_flg != BrandsUsersRelation::SIGNUP_WITH_INFO) return false;

        if ($this->cp_user->isNotMatchDemography() || $this->cp->requireRestriction($this->cp_user)) return false;

        $eqd = $this->getEntryQuestionnaireData($first_cp_action_id);
        if ($eqd['has_entry_questionnaire']) return false;

        return true;
    }

    /*******************************************************************************
     * Indicator関連の関数
     *******************************************************************************/
    /**
     * メッセージ読み込み時にインジケータを進めるかどうかのマップを作成する
     * @return array
     */
    public function createMapIncrementGauge() {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        $cp_actions = $cp_flow_service->getCpActionsInFirstGroupByCpId($this->Data['cp_id']);

        // CpActionの配列からCpAction->typeを抽出
        $cp_action_types = $this->extractCpActionTypeFromCpActions($cp_actions);
        // メッセージが表示されるかどうかのマップ (参加完了まで)
        if ($this->isDuplicateCpAction($cp_action_types, array(CpAction::TYPE_QUESTIONNAIRE, CpAction::TYPE_PHOTO, CpAction::TYPE_FREE_ANSWER), 3)) {
            return array();
        }

        $required_user_profile_form = $this->Data['pageStatus']['needDisplayUserProfileForm'];
        $required_personal_form = $this->Data['pageStatus']['needDisplayPersonalForm'];

        return $this->createMapDisplayedMessage($cp_action_types, $required_user_profile_form, $required_personal_form);
    }

    /**
     * CpActionの配列をCpAction->typeの配列にする
     * @param $cp_actions
     * @return array
     */
    private function extractCpActionTypeFromCpActions($cp_actions) {
        $cp_action_types = array();
        foreach ($cp_actions as $cp_action) {
            $cp_action_types[] = $cp_action->type;
        }

        return $cp_action_types;
    }

    /**
     * @param $cp_action_types
     * @param $checked_cp_action_types
     * @param int $limit_duplicated
     * @return bool
     */
    private function isDuplicateCpAction($cp_action_types, $checked_cp_action_types, $limit_duplicated = 2) {
        $array_n_cp_action_type = array_count_values($cp_action_types);

        // CpActionsがそれぞれ3つ以上存在していたらインジケーターを表示しない。
        foreach ($checked_cp_action_types as $checked_cp_action_type) {
            if ($array_n_cp_action_type[$checked_cp_action_type] >= $limit_duplicated) {
                return true;
            }
        }

        return false;
    }

    /**
     * メッセージが表示されるかどうかのマップを作る (参加完了まで)
     * 0: 表示されない
     * 1: 表示される
     * 例: [1, 0, 1, 1, 0]
     * @param array $cp_action_types
     * @param bool|false $required_user_profile_form
     * @param bool|false $required_personal_form
     * @return array
     */
    private function createMapDisplayedMessage($cp_action_types = array(), $required_user_profile_form = false, $required_personal_form = false) {
        $message_map = array();

        // アカウント情報・基本情報Formを表示する場合は、message_mapに追加する
        if ($required_user_profile_form) {
            $message_map[] = 1;
        }
        if ($required_personal_form) {
            $message_map[] = 1;
        }

        // 各Actionをmessage_mapに追加する
        foreach ($cp_action_types as $cp_action_type) {
            switch ($cp_action_type) {
                case CpAction::TYPE_ANNOUNCE_DELIVERY:
                case CpAction::TYPE_CONVERSION_TAG:
                    $message_map[] = 0;
                    break 1;
                case CpAction::TYPE_INSTANT_WIN:
                    $message_map[] = 1;
                case CpAction::TYPE_JOIN_FINISH:
                case CpAction::TYPE_ANNOUNCE:
                    $message_map[] = 1;
                    break 2;
                default:
                    $message_map[] = 1;
            }
        }

        return $message_map;
    }
}
