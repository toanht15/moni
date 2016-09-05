<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserCampaignJoinValidator');
AAFW::import('jp.aainc.classes.services.monipla.SendCpInfoForMonipla');
AAFW::import('jp.aainc.classes.brandco.auth.trait.BrandcoAuthTrait');
AAFW::import('jp.aainc.classes.CpInfoContainer');
AAFW::import('jp.aainc.classes.services.monipla.MoniplaLotteryService');
AAFW::import('jp.aainc.classes.core.UserManager');

class join extends BrandcoPOSTActionBase {

    use BrandcoAuthTrait;

    public $NeedOption = array(BrandOptions::OPTION_CRM, BrandOptions::OPTION_CP);
    protected $ContainerName = 'join';

    public $NeedUserLogin = true;
    public $CsrfProtect = true;

    /** @var  $cp_flow_service CpFlowService */
    private $cp_flow_service;

    private $cp;
    private $user_demography_flg;

    public function validate() {

        /** @var  $cp_flow_service CpFlowService */
        $this->cp_flow_service = $this->createService('CpFlowService');
        if (!Util::isNullOrEmpty($this->cp_id)) {
            $this->cp = CpInfoContainer::getInstance()->getCpById($this->cp_id);
        }
        $validator = new UserCampaignJoinValidator($this->cp);
        $validator->validate();
        if (!$validator->isValid()) {
            if ($validator->getErrors()['cp_id'][0] == UserCampaignJoinValidator::CP_CANT_JOIN) {
                $this->Form['package'] = '';
                $this->Form['action'] = 'campaigns/'.$this->cp_id.'?mid=cp_over_time';
                return false;
            } else if ($validator->getErrors()['cp_id'][0] == config("@message.userMessage.cp_join_limit.msg")) {
                $this->Form['package'] = '';
                $this->Form['action'] = 'campaigns/'.$this->cp_id.'?mid=cp_join_limit';
                return false;
            } else if ($validator->getErrors()['cp_id'][0] == config("@message.userMessage.cp_winner_limit.msg")) {
                $this->Form['package'] = '';
                $this->Form['action'] = 'campaigns/'.$this->cp_id.'?mid=cp_winner_limit';
                return false;
            } else {
                                return '404';
            }
        }

        // 限定CP用
        if ($this->cp->hasJoinLimitSnsWithoutPlatform()) {
            $available_social_media = null;
            $join_limited_sns = $this->cp->getJoinLimitSns();
            $social_account_service = $this->getService('SocialAccountService');

            $userInfo = $this->getSession('pl_monipla_userInfo');
            foreach ($userInfo['socialAccounts'] as $social_account) {
                $cur_social_media = $social_account_service->getSocialMedia(SocialAccountService::SOCIAL_MEDIA_KEY_TYPE, $social_account->socialMediaType);

                if ($cur_social_media[SocialAccountService::SOCIAL_MEDIA_KEY_CLIENT_ID] == $_SESSION['clientId']) return true;

                if (!$available_social_media && in_array($cur_social_media[SocialAccountService::SOCIAL_MEDIA_KEY_ID], $join_limited_sns)) {
                    $available_social_media = $cur_social_media;
                }
            }

            if ($available_social_media) {
                $_SESSION['clientId'] = $available_social_media[SocialAccountService::SOCIAL_MEDIA_KEY_CLIENT_ID];
                return true;
            }

            $this->Form['package'] = '';
            $this->Form['action'] = 'campaigns/' . $this->cp_id;
            return false;
        }

        return true;
    }

    function doAction() {

        /** @var CpUser $cp_users */
        $cp_users = aafwEntityStoreFactory::create('CpUsers');

        /** @var  $cp_user_service CPUserService */
        $cp_user_service = $this->createService('CpUserService');

        /** @var $user_service UserService */
        $user_service = $this->createService('UserService');

        /** @var  $cp_flow_service CpFlowService */
        $cp_flow_service = $this->createService('CpFlowService');

        // ユーザー情報を取得
        $user = $user_service->getUserByMoniplaUserId($this->Data['pageStatus']['userInfo']->id);

        //セッションに入れたサードパーティの値をDBに保存する
        $this->updateThirdPartyUserRelation($user->id);

        // エントリーアクションを取得
        list($cp_action, $concrete_action) = $cp_flow_service->getEntryActionInfoByCpId($this->cp_id);

        // cp_userを取得
        $cp_user = $cp_user_service->getCpUserByCpIdAndUserId($this->cp_id, $user->id);

        // brand_user_relationを取得
        $brand_user_relation = $this->getBrandsUsersRelation();

        // cp_userが存在しない場合
        if (!$cp_user_service->isJoinedCp($this->cp_id, $user->id, $cp_user, $this->cp)) {

            try {

                $this->setCpRefererSession($this->cp_id);

                $this->setCpFromIdSession($this->cp_id);

                $cp_users->begin();

                if ($this->cp->selection_method == CpCreator::ANNOUNCE_FIRST) {
                    //  トランザクション開始
                    $entry_action = $cp_flow_service->getFirstActionOfCp($this->cp_id);
                    /** @var CpTransactionService $transaction_service */
                    $transaction_service = $this->createService('CpTransactionService');
                    $transaction_service->getCpTransactionByIdForUpdate($entry_action->id);
                    if ($this->cp->isOverLimitWinner()) {
                        $cp_users->rollback();
                        return 'redirect: ' . Util::rewriteUrl('', 'campaigns', array($this->cp_id), array('mid' => 'cp_join_limit'));
                    }
                }

                $sns_kind = $cp_flow_service->getJoinSnsKindByClientId($_SESSION['clientId']);
                if (!$cp_user) {
                    // 新規ユーザーチェック
                    $cp_user = $cp_user_service->createCpUser($this->cp_id, $user->id, false, $this->beginner_flg, $sns_kind, $this->getUserDemographyFlg(), $this->getSession('cp_fid_'.$this->cp_id), $this->getSession('cp_ref_'.$this->cp_id));

                    $user_agent = array(
                        'user_agent' => $this->SERVER['HTTP_USER_AGENT'],
                        'device_type' => Util::isSmartPhone()
                    );

                    $join_status = $this->getUserDemographyFlg() == CpUser::DEMOGRAPHY_STATUS_NOT_MATCH ? CpUserActionStatus::CAN_NOT_JOIN : CpUserActionStatus::JOIN;
                    $cp_user_service->sendJoinActionMessage($cp_user->id, $cp_action, $concrete_action, true, $user_agent, $join_status);
                } else {
                    $cp_user->from_id = $this->getSession('cp_fid_'.$this->cp_id);
                    $cp_user->join_sns = $sns_kind;
                    $cp_user->demography_flg = $this->getDemographyFlg();
                    $cp_user_service->updateCpUser($cp_user);

                    $join_status = $this->getUserDemographyFlg() == CpUser::DEMOGRAPHY_STATUS_NOT_MATCH ? CpUserActionStatus::CAN_NOT_JOIN : CpUserActionStatus::JOIN;
                    $cp_user_service->joinAction($cp_user->id, $cp_action->id, $this->SERVER['HTTP_USER_AGENT'], Util::isSmartPhone(), $join_status);
                }

                if ($cp_action->type == CpAction::TYPE_QUESTIONNAIRE) {
                    $qa_sess = $this->getBrandSession('qa');
                    $qa_answers = $qa_sess[$this->cp_id];
                    $cp_questionnaire_service = $this->getService('CpQuestionnaireService');

                    if ($qa_answers) {
                        unset($qa_sess[$this->cp_id]);
                        $this->setBrandSession('qa', $qa_sess);

                        foreach ($qa_answers as $key => $value) {
                            list($question_type, $question_id, $choice_id) = explode('/', $key);
                            $other_choice = $cp_questionnaire_service->getOtherChoice($question_id);
                            $questionnaires_questions_relation = $cp_questionnaire_service->getRelationByQuestionnaireActionIdAndQuestionId($concrete_action->id, $question_id);

                            if ($value) {
                                if ($question_type == 'single_answer') {
                                    if ($other_choice && $value == $other_choice->id && $qa_answers['single_answer_othertext/' . $question_id]) {
                                        $other_text_answer = $qa_answers['single_answer_other_text/' . $question_id];
                                    } else {
                                        $other_text_answer = null;
                                    }

                                    $cp_questionnaire_service->setQuestionChoiceAnswer($questionnaires_questions_relation->id, $brand_user_relation->id, $question_id, $value, $other_text_answer);
                                } elseif ($question_type == 'multi_answer') {
                                    if ($other_choice && $value == $other_choice->id && $qa_answers['multi_answer_othertext/' . $question_id . '/' . $choice_id]) {
                                        $other_text_answer = $qa_answers['multi_answer_othertext/' . $question_id . '/' . $choice_id];
                                    } else {
                                        $other_text_answer = null;
                                    }

                                    $cp_questionnaire_service->setQuestionChoiceAnswer($questionnaires_questions_relation->id, $brand_user_relation->id, $question_id, $choice_id, $other_text_answer);
                                } elseif ($question_type == 'free_answer') {
                                    $cp_questionnaire_service->setQuestionFreeAnswer($questionnaires_questions_relation->id, $brand_user_relation->id, $question_id, $value);
                                }
                            }
                        }
                    }
                }

                $cp_users->commit();
                $this->setSession('cp_ref_'.$this->cp_id, null);
                $this->setSession('cp_fid_'.$this->cp_id, null);

            } catch (Exception $e) {
                $cp_users->rollback();
                $logger = aafwLog4phpLogger::getDefaultLogger();
                $logger->warn("User join campaign create cp_user failed cp_id = " . $this->cp_id . "user_id = " . $user->id);
                $logger->warn($e);
                return "404";
            }
        }

        if ($this->cp->status != Cp::STATUS_DEMO && !$this->cp->isNonIncentiveCp() && $this->getBrand()->test_page != Brand::BRAND_TEST_PAGE) {
            /** @var SendCpInfoForMonipla $send_cp_info_for_monipla */
            $send_cp_info_for_monipla = $this->createService('SendCpInfoForMonipla');
            $send_cp_info_for_monipla->sendCpUserStatus($cp_user->id, $cp_action->id, $this->brand->app_id, $cp_action->type);
        }

        try {
            $cp_users->begin();

            // プロフィールアンケート無し・利用規約無し・SNS登録の場合、personal_flgを1にする (聞く必要なないため)
            if (!$this->hasProfileQuestionOrAgreement() && !$user->provisional_flg) {
                if ($this->Data['pageStatus']['userInfo']->id) {
                    /** @var UserManager $user_manager */
                    $user_manager = new UserManager($this->Data['pageStatus']['userInfo']);
                    $platform_user = $user_manager->getUserByQuery($this->Data['pageStatus']['userInfo']->id);
                } else {
                    $platform_user = $this->Data['pageStatus']['userInfo'];
                }

                // メールアドレスが取れているかチェック
                if ($platform_user->mailAddress) {
                    /** @var BrandsUsersRelationService $brands_users_relation_service */
                    $brands_users_relation_service = $this->getService('BrandsUsersRelationService');
                    $brand_user_relation->personal_info_flg = BrandsUsersRelation::SIGNUP_WITH_INFO;
                    $brands_users_relation_service->updateBrandsUsersRelation($brand_user_relation);
                }
            }

            $cp_users->commit();
        } catch (Exception $e) {
            $cp_users->rollback();
            $brand_user_relation->personal_info_flg  = BrandsUsersRelation::SIGNUP_WITHOUT_INFO;

            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error("updateBrandsUsersRelation failed! cp_id = " . $this->cp_id . "user_id = " . $brand_user_relation->id);
            $logger->error($e);
        }

        if (!$this->shouldShowUpForm($user, $brand_user_relation, $concrete_action->cp_action_id) && $this->getUserDemographyFlg() == CpUser::DEMOGRAPHY_STATUS_COMPLETE) {
            try {

                $cp_users->begin(aafwEntityStoreBase::TIL_READ_COMMITTED);

                // 次のアクションが存在するかチェック
                $next_action = $cp_flow_service->getCpNextActionByCpActionId($cp_action->id);

                // 次のアクションがある場合
                if ($next_action->id) {

                    // 次のアクションを取得
                    $cp_action = CpInfoContainer::getInstance()->getCpActionById($next_action->cp_next_action_id);

                    // 具体的なアクションを取得
                    /** @var CpActionManager $action_manager */
                    $manager = $cp_action->getActionManagerClass();
                    $concrete_action = $manager->getConcreteAction($cp_action);

                    // メッセージ送信
                    $cp_user_service->sendActionMessage($cp_user->id, $cp_action, $concrete_action, true);

                    // 宝くじ
                    $this->getLotteryCode($user);

                    // メール送信
                    $this->sendEntryMail($user->id, $cp_user->cp_id);
                }

                $cp_users->commit();

            } catch (Exception $e) {
                $cp_users->rollback();
                $logger = aafwLog4phpLogger::getDefaultLogger();
                $logger->warn("User join campaign failed cp_id = " . $this->cp_id . "user_id = " . $user->id);
                $logger->warn($e);
                return "404";
            }
        }
        $queryParam = array();
        if ($this->beginner_flg == CpUser::BEGINNER_USER) {
            $queryParam = array('tid' => 'signup_complete');
        }
        return 'redirect: ' . Util::rewriteUrl('messages', 'thread', array("cp_id" => $this->cp_id), $queryParam);
    }

    private function shouldShowUpForm($user, $brand_user_relation, $cp_action_id) {
        /** @var CpEntryProfileQuestionnaireService $service */
        $service = $this->createService('CpEntryProfileQuestionnaireService');
        return $user->provisional_flg ||
            $brand_user_relation->personal_info_flg == BrandsUsersRelation::FORCE_WITH_INFO ||
            $brand_user_relation->personal_info_flg == BrandsUsersRelation::SIGNUP_WITHOUT_INFO ||
        ($service->countQuestionnairesByCpActionId($cp_action_id) > 0 && $brand_user_relation->personal_info_flg == BrandsUsersRelation::SIGNUP_WITH_INFO);
    }

    public function getUserDemographyFlg() {
        if (!$this->user_demography_flg) {
            $this->user_demography_flg = $this->getDemographyFlg();
        }

        return $this->user_demography_flg;
    }

    public function getDemographyFlg() {

        if ($this->cp->isRestrictedCampaign()) {
            AAFW::import('jp.aainc.classes.core.UserAttributeManager');
            $userAttributeManager = new UserAttributeManager($this->Data['pageStatus']['userInfo'], $this->getMoniplaCore());

            if ($this->cp->restricted_age_flg) {
                $birthday = $userAttributeManager->getBirthDay();

                if (!$birthday) {
                    return CpUser::DEMOGRAPHY_STATUS_DEFAULT;
                } elseif ($this->cp->restricted_age > $this->getUserAge($birthday)) {
                    return CpUser::DEMOGRAPHY_STATUS_NOT_MATCH;
                }
            }

            if ($this->cp->restricted_gender_flg) {
                $gender = $userAttributeManager->getSex();

                if (!$gender) {
                    return CpUser::DEMOGRAPHY_STATUS_DEFAULT;
                } elseif (Cp::$cp_restricted_brief_gender[$this->cp->restricted_gender] != $gender) {
                    return CpUser::DEMOGRAPHY_STATUS_NOT_MATCH;
                }
            }

            if ($this->cp->restricted_address_flg) {
                AAFW::import('jp.aainc.classes.core.ShippingAddressManager');
                $shippingAddressManager = new ShippingAddressManager($this->Data['pageStatus']['userInfo'], $this->getMoniplaCore());

                $address = $shippingAddressManager->getShippingAddress();
                $cp_restricted_address_service = $this->getService('CpRestrictedAddressService');

                if (!$address || $address->prefId == 0) {
                    return CpUser::DEMOGRAPHY_STATUS_DEFAULT;
                } elseif (!in_array($address->prefId, $cp_restricted_address_service->getCpRestrictedAddressIds($this->cp->id))) {
                    return CpUser::DEMOGRAPHY_STATUS_NOT_MATCH;
                }
            }
        }

        return CpUser::DEMOGRAPHY_STATUS_COMPLETE;
    }

    /**
     * @return bool
     */
    protected function hasProfileQuestionOrAgreement() {
        $brand_page_setting = $this->brand->getBrandPageSetting();

        return $brand_page_setting->isProfileQuestionRequired() || $brand_page_setting->agreement;
    }

    /**
     * @param $user
     */
    protected function getLotteryCode($user) {
        if (!$this->cp->isDemo()) {
            /** @var MoniplaLotteryService $monipla_lottery_service */
            $monipla_lottery_service = $this->getService('MoniplaLotteryService');
            $monipla_lottery_service->getCode($user);
        }
    }

    /**
     * @param $user_id
     * @param $cp_id
     */
    protected function sendEntryMail($user_id, $cp_id) {
        /** @var UserMailService $user_mail_service */
        $user_mail_service = $this->getService('UserMailService');

        $user_mail_service->sendEntryMail($user_id, $cp_id);
    }
}