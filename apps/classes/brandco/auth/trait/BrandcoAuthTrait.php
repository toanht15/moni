<?php

trait BrandcoAuthTrait {

    private $personal_info_type;

    /**
     * @return BrandcoAuthService
     */
    public function getBrandcoAuthService() {
        return $this->getService('BrandcoAuthService');
    }

    /**
     * @return UserService
     */
    public function getUserService() {
        return $this->getService('UserService');
    }

    /**
     * @return BrandsUsersRelationService
     */
    public function getBrandsUsersRelationService() {
        return $this->getService('BrandsUsersRelationService');
    }

    /**
     * @return CpUserActionStatusService
     */
    public function getCpUserActionStatusService() {
        return $this->getService('CpUserActionStatusService');
    }

    /**
     * @return OldMoniplaUserOptinService
     */
    public function getOldMoniplaUserOptinService() {
        AAFW::import('jp.aainc.classes.services.monipla.OldMoniplaUserOptinService');

        return $this->getService('OldMoniplaUserOptinService');
    }

    /**
     * @return CpQuestionnaireService
     */
    public function getCpQuestionnaireService() {
        AAFW::import('jp.aainc.classes.services.CpQuestionnaireService');

        return $this->getService('CpQuestionnaireService', array(CpQuestionnaireService::TYPE_PROFILE_QUESTION));
    }

    /**
     * @param $user
     * @param $brands_users_relation
     * @return bool
     */
    public function canLogin($user, $brands_users_relation) {
        if (!$user) {
            return false;
        }

        if ($user->provisional_flg) {
            return false;
        }

        if (!$brands_users_relation || $brands_users_relation->withdraw_flg || !$brands_users_relation->personal_info_flg) {
            return false;
        }

        return true;
    }

    /**
     * @param $userInfo
     * @param $brands_users_relation
     */
    public function login($userInfo, $brands_users_relation) {
        // CSRF対策のためセッションをリセット
        session_regenerate_id(true);
        $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('pl_monipla_userInfo', $this->getBrandcoAuthService()->castSocialAccounts($userInfo));
        $redisPersistentSession = new RedisPersistentSession();

        // ログイン成功でトークンリセット
        $redisPersistentSession->setSessionId(session_id(), true);

        $this->setLogin($brands_users_relation);
    }

    /**
     * @param $monipla_user_id
     * @return null
     */
    public function getUserInfoByMoniplaUserId($monipla_user_id) {
        $ret = $this->getBrandcoAuthService()->getUserInfoByQuery($monipla_user_id);
        if ($ret->result->status !== Thrift_APIStatus::SUCCESS) {
            aafwLog4phpLogger::getDefaultLogger()->error($ret);
            return null;
        }

        return $ret;
    }

    /**
     * @param $access_token
     * @return null
     */
    public function getUserInfoByAccessToken($access_token) {
        $ret = $this->getBrandcoAuthService()->getUserInfo($access_token);
        if ($ret->result->status !== Thrift_APIStatus::SUCCESS) {
            if ($ret->result->errors[0]->message === 'AccessToken: expired') {
                aafwLog4phpLogger::getDefaultLogger()->info('AccessToken: expired');
            } else {
                aafwLog4phpLogger::getDefaultLogger()->error($ret);
            }

            return null;
        }

        return $ret;
    }

    /**
     * @param $mail_address
     * @return null
     */
    public function getUserByMailAddress($mail_address) {
        $ret = $this->getBrandcoAuthService()->getUsersByMailAddress($mail_address);
        if ($ret->result->status !== Thrift_APIStatus::SUCCESS) {
            aafwLog4phpLogger::getDefaultLogger()->error($ret);
            return null;
        }
        return $ret->user[0];
    }

    /**
     * @param $monipla_user_id
     * @param $client_id
     * @return null
     */
    public function createAuthorizationCodeByMoniplaUserIdAndClientId($monipla_user_id, $client_id) {
        $ret = $this->getBrandcoAuthService()->createAuthorizationCode($monipla_user_id, $client_id, 'FULL_CONTROL');
        if ($ret->result->status !== Thrift_APIStatus::SUCCESS) {
            aafwLog4phpLogger::getDefaultLogger()->error($ret);
            return null;
        }


        return $ret->code;
    }

    /**
     * @param $code
     * @param $client_id
     * @return array|null
     */
    public function createAccessTokenByCodeAndClientId($code, $client_id) {
        $ret = $this->getBrandcoAuthService()->createAccessToken($client_id, $code);

        if ($ret->result->status !== Thrift_APIStatus::SUCCESS) {
            if ($ret->result->errors[0]->message === 'AccessToken: invalid') {
                aafwLog4phpLogger::getDefaultLogger()->info('AccessToken: invalid');
            } else {
                aafwLog4phpLogger::getDefaultLogger()->error($ret);
            }

            return null;
        }


        return array('accessToken' => $ret->accessToken, 'refreshToken' => $ret->refreshToken);
    }

    /**
     * @param $mail_address
     * @param $password
     * @return bool
     */
    public function getMoniplaUserIdByMailAddressAndPassword($mail_address, $password) {
        $ret = $this->getBrandcoAuthService()->checkAccount($mail_address, $password);
        if ($ret->result->status !== Thrift_APIStatus::SUCCESS) {
            if ($ret->result->errors[0]->message === 'レコードが見付かりませんでした') {
                aafwLog4phpLogger::getDefaultLogger()->info('レコードが見付かりませんでした');
            } else {
                aafwLog4phpLogger::getDefaultLogger()->error($ret);
            }

            return false;
        }

        return $ret->userId;

    }

    /**
     * @param $mail_address
     * @param $password
     * @return bool
     */
    public function createAAIDByMailAddressAndPassword($mail_address, $password) {
        // AAIDユーザー追加
        $ret = $this->getBrandcoAuthService()->entryUser($mail_address, $password);
        if ($ret->status !== Thrift_APIStatus::SUCCESS) {
            aafwLog4phpLogger::getDefaultLogger()->error($ret);
            return false;
        }

        return true;
    }

    /**
     * @param $userInfo
     * @param $provisional_flg
     * @param $app_id
     * @param $access_token
     * @param $refresh_token
     * @param $client_id
     * @return mixed
     */
    public function getOrCreateMoniplaUser($userInfo, $provisional_flg, $app_id, $access_token, $refresh_token, $client_id) {
        // Userの作成
        $user = $this->getOrCreateUser($userInfo, $provisional_flg);

        // UserApplication・SocialAccountsの更新
        $this->createOrUpdateUserApplicationAndSocialAccounts($userInfo, $user->id, $app_id, $access_token, $refresh_token, $client_id);

        return $user;
    }

    /**
     * @param $userInfo
     * @param $user_id
     * @param $app_id
     * @param $access_token
     * @param $refresh_token
     * @param $client_id
     */
    public function createOrUpdateUserApplicationAndSocialAccounts($userInfo, $user_id, $app_id, $access_token, $refresh_token, $client_id) {
        /** @var  SocialAccountService $social_account_service */
        $social_account_service     = $this->getService('SocialAccountService');
        /** @var  UserApplicationService $user_application_service */
        $user_application_service   = $this->getService('UserApplicationService');

        // UserApplicationの作成
        $user_application_service->createOrUpdateUserApplication($user_id, $app_id, $access_token, $refresh_token, $client_id);

        // Socialログインの場合、socialAccountを登録する
        if (count($userInfo->socialAccounts[0])) {
            $social_account_service->setSocialAccountsList($userInfo->socialAccounts, $user_id);
        }
    }

    /**
     * @param $userInfo
     * @param int $provisional_flg
     * @return mixed
     */
    public function getOrCreateUser($userInfo, $provisional_flg = User::PROVISIONAL_FLG_ON) {
        $user = $this->getUserService()->getUserByMoniplaUserId($userInfo->id);
        if (!$user) {
            $user = $this->getUserService()->createEmptyUser();
            $user->monipla_user_id  = $userInfo->id;
            $user->provisional_flg = $provisional_flg;
        }

        $user->name                 = $userInfo->name;
        $user->mail_address         = $userInfo->mailAddress;
        $user->profile_image_url    = $userInfo->socialAccounts[0]->profileImageUrl;

        // 仮会員処理が必要なければ
        if ($provisional_flg == User::PROVISIONAL_FLG_OFF) {
            $user->provisional_flg = $provisional_flg;
        }

        if ($this->isLoginManager()) {
            $user->aa_flg = 1;
        }

        $user = $this->getUserService()->updateUser($user);

        return $user;
    }

    /**
     * @param $brand_id
     * @param $user_id
     * @param int $personal_info_flg
     * @return entity|void
     */
    public function getOrCreateBrandUserRelation($brand_id, $user_id, $personal_info_flg = BrandsUsersRelation::SIGNUP_WITH_INFO) {
        // BRANDCoファン登録
        $brands_users_relation = $this->getBrandsUsersRelationService()->getBrandsUsersRelation($brand_id, $user_id);
        if (!$brands_users_relation || $brands_users_relation->withdraw_flg) {
            if (!$brands_users_relation) {
                $brands_users_relation = $this->getBrandsUsersRelationService()->createEmptyBrandsUsersRelation();
                $brands_users_relation->brand_id = $brand_id;
                $brands_users_relation->user_id  = $user_id;
                $brands_users_relation->no       = 0;
            } else if ($brands_users_relation->withdraw_flg) {
                $this->getCpUserActionStatusService()->recoveryCpUserActionMessageAndStatus($user_id, $brand_id);
            }

            $brands_users_relation->withdraw_flg = 0;
            $brands_users_relation->del_info_flg = 0;
            $brands_users_relation->personal_info_flg = $personal_info_flg;
            $brands_users_relation->referrer     = $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('referrer_' . $brand_id);
            $brands_users_relation->from_id      = $this->getSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('fid_' . $brand_id);
            $brands_users_relation->from_kind    = BrandsUsersRelationService::FROM_KIND_BRANDCO;

            $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('referrer_' . $this->brand->id, null);
            $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('fid_' . $this->brand->id, null);

            $brands_users_relation = $this->getBrandsUsersRelationService()->updateBrandsUsersRelation($brands_users_relation);

            //ブランド新規登録とき、カスタマイズメールを送信
            if($this->isSendBrandCustomWelcomeMail($this->brand)){
                /** @var UserMailService $user_mail_service */
                $user_mail_service = $this->createService('UserMailService');
                $user_mail_service->sendSignUpCustomMail($user_id, $brand_id);
            }

        } else {
            // PersonalInfoFlgの更新
            if ($brands_users_relation->personal_info_flg != BrandsUsersRelation::SIGNUP_WITH_INFO) {
                $brands_users_relation->personal_info_flg = $personal_info_flg;
                $brands_users_relation = $this->getBrandsUsersRelationService()->updateBrandsUsersRelation($brands_users_relation);
            }
        }

        return $brands_users_relation;
    }

    /**
     * @param $userId
     * @param int $optin
     * @param null $mpfb_from_id
     * @param null $mpfb_free_item
     * @return bool
     */
    public function updateOptin($userId, $optin = 0, $mpfb_from_id = null, $mpfb_free_item = null) {
        // MPFB側の更新
        $this->updateOptinMpFb($userId, $optin, $mpfb_from_id, $mpfb_free_item);

        // AAID側の更新
        if(!$this->updateOptinAAID($userId, $optin)){
            return false;
        }
        return true;
    }

    /**
     * @param $userId
     * @param int $optin
     * @param null $from_id
     * @param null $free_item
     */
    public function updateOptinMpFb($userId, $optin = 0, $from_id = null, $free_item = null){
        $this->getOldMoniplaUserOptinService()->get_or_create($userId, $optin);
        $this->getOldMoniplaUserOptinService()->update($userId, $optin, $from_id, $free_item);
    }

    /**
     * @param $userId
     * @param int $optin
     * @return bool
     */
    public function updateOptinAAID($userId, $optin = 0){
        $ret = $this->getBrandcoAuthService()->setOptin($userId, $optin);
        if ($ret->status !== Thrift_APIStatus::SUCCESS) {
            aafwLog4phpLogger::getDefaultLogger()->error($ret);
            return false;
        }

        return true;
    }

    public function updatePersonalInfo($config, $object) {
        if($config->privacy_required_nickname) {
            $object->userManager->changeName($object->name);
        }

        if($config->privacy_required_password) {
            $object->userManager->resetPassword($object->password);
        }

        if($config->privacy_required_sex) {
            $object->userAttributeManager->setSex($object->sex);
        }

        if($config->privacy_required_birthday) {
            $object->userAttributeManager->setBirthday($object->birthDay_y, $object->birthDay_m, $object->birthDay_d);
        }

        if($config->privacy_required_name || $config->privacy_required_address || $config->privacy_required_tel) {
            $object->shippingAddressManager->setAddress($object);
        }
    }

    public function setValidatorDefinition() {
        $this->ValidatorDefinition = array(
            'mailAddress'       => array('type' => 'str', 'validator' => array('MailAddress')),
            'password'          => array('type' => 'str', 'length' => 45, 'validator' => array('AlnumSymbol')),
            'passwordRetype'    => array('type' => 'str', 'equals' => '@_password_@'),
            'name'              => array('type' => 'str', 'length' => 200),
            'lastName'          => array('type' => 'str', 'length' => 45),
            'firstName'         => array('type' => 'str', 'length' => 45),
            'lastNameKana'      => array('type' => 'str', 'length' => 45, 'validator' => array('ZenHira')),
            'firstNameKana'     => array('type' => 'str', 'length' => 45, 'validator' => array('ZenHira')),
            'zipCode'           => array('type' => 'str', 'validator' => array('ZipCode')),
            'prefId'            => array('type' => 'num'),
            'address1'          => array('type' => 'str', 'length' => 255),
            'address2'          => array('type' => 'str', 'length' => 255),
            'address3'          => array('type' => 'str', 'length' => 255),
            'telNo'             => array('type' => 'num', 'regex' => '/^0\d{9,11}$/'),
            'sex'               => array('type' => 'str', 'regex' => '#^f|m$#'),
            'birthDay'          => array('type' => 'date'));

        /** @var CpQuestionnaireService $question_service */
        $question_service = $this->createService('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);

        // 呼び出し元の導線。キャンペーンのjoinとsignupでバリデーション項目を動的に変更する。
        $this->Data['profile_questions_relations'] = null;
        if (!$this->cp_user||!$this->cp_action_id) {
            $this->Data['profile_questions_relations'] = $question_service->getSignupProfileQuestionRelationByBrandId($this->getBrand()->id);
        } else {
            /** @var CpFlowService $cp_flow_service */
            $cp_flow_service = $this->createService('CpFlowService');
            $this->personal_info_type = $personal_info_type = $cp_flow_service->isEntryActionWithProfileQuestionnairesByQuery($this->getBrand()->id, $this->cp_action_id, $this->cp_user);
            if ($personal_info_type === CpFlowService::ENTRY_WITHOUT_INFO) {
                $this->Data['profile_questions_relations'] = $question_service->getEntryActionProfileQuestionRelationsByBrandIdAndCpActionId($this->getBrand()->id, $this->cp_action_id);
            } else if($personal_info_type === CpFlowService::ENTRY_WITH_INFO) {
                $cp = CpInfoContainer::getInstance()->getCpById($this->cp_user->cp_id);
                if ($cp->requireRestriction($this->cp_user)) {
                    // 参加条件が必要な場合は、一律全項目をチェックする。
                    $this->Data['profile_questions_relations'] = $question_service->getEntryActionProfileQuestionRelationsByBrandIdAndCpActionId($this->getBrand()->id, $this->cp_action_id);
                } else {
                    $this->Data['profile_questions_relations'] = $question_service->getResendEntryActionProfileQuestionRelationsByBrandIdAndCpActionId($this->getBrand()->id,
                        $this->cp_action_id);
                }
            }
        }

        // 性能改善のためにまとめる
        $pq_question_ids = array();
        foreach ($this->Data['profile_questions_relations'] as $profile_questions_relations) {
            $pq_question_ids[] = $profile_questions_relations->question_id;
        }
        if (count($pq_question_ids) > 0) {
            $profile_question_map = $question_service->getQuestionMapByIds($pq_question_ids);
        }

        foreach ($this->Data['profile_questions_relations'] as $profile_questions_relations) {
            if ($profile_questions_relations->requirement_flg) {
                $this->ValidatorDefinition['answer_' . $profile_questions_relations->question_id]['required'] = true;
            }
            $profile_questions = $profile_question_map[$profile_questions_relations->question_id];
            if ($profile_questions->type_id == QuestionTypeService::FREE_ANSWER_TYPE) {
                $this->ValidatorDefinition['answer_' . $profile_questions_relations->question_id]['type'] = 'str';
                $this->ValidatorDefinition['answer_' . $profile_questions_relations->question_id]['length'] = 255;
            }
        }

        if ($this->shouldShowUpPrivacyInfo($personal_info_type, $cp)) {
            if (!$this->entryMailAddress && $this->userManager->isMailAddressRequired()) {
                $this->ValidatorDefinition['mailAddress']['required'] = true;
            }

            if ($this->Data['pageSettings']->privacy_required_nickname) {
                $this->ValidatorDefinition['name']['required'] = true;
            }

            if ($this->Data['pageSettings']->privacy_required_password) {
                $this->ValidatorDefinition['password']['required'] = true;
                $this->ValidatorDefinition['passwordRetype']['required'] = true;
            }

            if ($this->Data['pageSettings']->privacy_required_name) {
                $this->ValidatorDefinition['lastName']['required'] = true;
                $this->ValidatorDefinition['firstName']['required'] = true;
                $this->ValidatorDefinition['lastNameKana']['required'] = true;
                $this->ValidatorDefinition['firstNameKana']['required'] = true;
            }

            if ($this->Data['pageSettings']->privacy_required_address == BrandPageSetting::GET_ALL_ADDRESS) {
                $this->ValidatorDefinition['zipCode']['required'] = true;
                $this->zipCode = $this->zipCode1 . '-' . $this->zipCode2;
                $this->ValidatorDefinition['address1']['required'] = true;
                $this->ValidatorDefinition['address2']['required'] = true;
            }

            if ($this->Data['pageSettings']->privacy_required_tel) {
                $this->ValidatorDefinition['telNo']['required'] = true;
                $this->telNo = $this->telNo1 . $this->telNo2 . $this->telNo3;
            }

            if ($this->Data['pageSettings']->privacy_required_sex) {
                $this->ValidatorDefinition['sex']['required'] = true;
            }

            if($this->Data['pageSettings']->agreement && $this->Data['pageSettings']->show_agreement_checkbox) {
                $this->ValidatorDefinition['agree_agreement']['required'] = true;
            }

            if ($this->Data['pageSettings']->privacy_required_birthday) {
                $this->ValidatorDefinition['birthDay']['required'] = true;
                $this->birthDay = $this->birthDay_y . '-' . $this->birthDay_m . '-' . $this->birthDay_d;

                // aafwValidatorにおけるDateの大小比較でのエラー文言が不適切なため、regexでエラーを発生させる
                if ($this->birthDay_y > 2100 || strtotime($this->birthDay) < strtotime("-115 year") ||
                    strtotime($this->birthDay) > strtotime("now")
                ) {
                    $this->ValidatorDefinition['birthDay']['regex'] = '^dummy$/';
                }
            }
        }
    }

    public function validateData() {
        if($this->Data['pageSettings']->privacy_required_restricted && $this->personal_info_type !== CpFlowService::ENTRY_WITH_INFO) {
            // 年齢制限
            $age = $this->getUserAge($this->birthDay);

            if($age < $this->Data['pageSettings']->restricted_age) {
                $this->Validator->setError('restrictedAge', 'auth.signup.restrictedAge');
            }
        }

        /** @var CpQuestionnaireService $question_service */
        $question_service = $this->createService('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);

        // 性能改善のためにまとめてとる
        $question_ids = array();
        foreach ($this->Data['profile_questions_relations'] as $question_relation) {
            $question_ids[] = $question_relation->question_id;
        }
        if (count($question_ids) > 0) {
            $question_map = $question_service->getQuestionMapByIds($question_ids);
            $req_map = $question_service->getRequirementMapByQuestionIds($question_ids);
            $other_choice_map = $question_service->getOtherChoiceMapByQuestionIds($question_ids);
        }

        foreach($this->Data['profile_questions_relations'] as $profile_question_relation) {

            //「その他」を答えるとその他の文書が必要
            $question = $question_map[$profile_question_relation->question_id];
            $other_choice = $other_choice_map[$question->id];
            if ($other_choice && $question->type_id == QuestionTypeService::CHOICE_ANSWER_TYPE &&
                (in_array($other_choice->id, $this->POST['answer_'.$profile_question_relation->question_id]) || $this->POST['answer_'.$profile_question_relation->question_id] == $other_choice->id)) {
                if(!$this->POST['other_answer_'.$profile_question_relation->question_id]) {
                    $this->Validator->setError('other_answer_'.$profile_question_relation->question_id, 'INVALID_OTHER_CHOICE');
                } elseif(mb_strlen($this->POST['other_answer_'.$profile_question_relation->question_id], 'UTF-8') > 255) {
                    $this->Validator->setError('other_answer_'.$profile_question_relation->question_id, 'INPUT_WITHIN_255');
                }

            }

            if ($question->type_id == QuestionTypeService::CHOICE_ANSWER_TYPE) {
                $question_requirement = $req_map[$profile_question_relation->question_id];
                if (!$question_requirement->multi_answer_flg) {
                    continue;
                }
                if ($this->POST['answer_' . $question->id] && !is_array($this->POST['answer_' . $question->id])) {
                    $this->Validator->setError('answer_' . $question->id, 'NOT_MATCH_TYPE');
                }
            }
        }

        if(!$this->entryMailAddress && $this->userManager->isMailAddressRequired() && $this->userManager->checkExistMailAddress($this->mailAddress)) {
            $this->Validator->setError('mailAddress', 'EXISTED_MAIL_ADDRESS');
        }

        return $this->Validator->isValid();
    }

    public function getUserAge($birthday) {
        $birthday_time = new DateTime($birthday);
        $birthday = $birthday_time->format('Ymd');

        return floor( ( date('Ymd') - $birthday ) / 10000 );
    }

    public function getUserGender($user_gender) {
        $gender_array = array(
            'f' => Cp::CP_RESTRICTED_GENDER_FEMALE,
            'm' => Cp::CP_RESTRICTED_GENDER_MALE
        );

        return $gender_array[$user_gender];
    }

    public function createProfileQuestionAnswers($brands_users_relation) {
        AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
        $data_builder = aafwDataBuilder::newBuilder();

        //フリーアンケートの回答を保存
        /** @var CpQuestionnaireService $question_service */
        $question_service = $this->createService('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);

        // 性能改善のためにまとめてとる
        $question_ids = array();
        foreach ($this->Data['profile_questions_relations'] as $question_relation) {
            $question_ids[] = $question_relation->question_id;
        }
        if (count($question_ids) > 0) {
            $question_map = $question_service->getQuestionMapByIds($question_ids);
            $req_map = $question_service->getRequirementMapByQuestionIds($question_ids);
            $choice_map = $question_service->getChoiceIdToChoiceMapByQuestionIds($question_ids);
            $other_choice_map = $question_service->getOtherChoiceMapByQuestionIds($question_ids);
        }

        foreach ($this->Data['profile_questions_relations'] as $question_relation) {
            $question = $question_map[$question_relation->question_id];

            if ($question->type_id == QuestionTypeService::CHOICE_ANSWER_TYPE || $question->type_id == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE) {
                $question_requirement = $req_map[$question->id];
                $other_choice = $other_choice_map[$question->id];

                $update_answer_sql = "/* BrandcoAuthTrait->createProfileQuestionAnswers */ UPDATE profile_question_choice_answers SET del_flg = 1,updated_at = now()
                WHERE brands_users_relation_id = {$brands_users_relation->id} AND questionnaires_questions_relation_id = {$question_relation->id} AND del_flg = 0";
                $result = $data_builder->executeUpdate($update_answer_sql);
                if (!$result) {
                    throw new aafwException("UPDATE FAILED!");
                }

                if ($question_requirement->multi_answer_flg) {
                    //古いデータベースに保存するため
                    $answer_arr = array();

                    //新しいデータベースに保存
                    foreach ($this->POST['answer_' . $question->id] as $choice_id) {
                        $other_answer = ($other_choice->id == $choice_id) ? $this->POST['other_answer_' . $question->id] : '';

                        if (!is_null($choice_id)) {
                            $question_service->setQuestionChoiceAnswer($question_relation->id, $brands_users_relation->id, $question->id, $choice_id, $other_answer);
                            $answer_arr[] = $choice_map[$choice_id]->choice;
                        }
                    }

                    $this->POST['answer_' . $question->id] = implode(",", $answer_arr);

                } else {
                    if (($question->type_id == QuestionTypeService::CHOICE_ANSWER_TYPE && !is_null($this->POST['answer_' . $question->id])) ||
                        ($question->type_id == QuestionTypeService::CHOICE_PULLDOWN_ANSWER_TYPE && $this->POST['answer_' . $question->id] > 0)) {
                        $other_answer = $this->POST['answer_' . $question->id] == $other_choice->id ? $this->POST['other_answer_' . $question->id] : '';
                        $question_service->setQuestionChoiceAnswer($question_relation->id, $brands_users_relation->id, $question->id, $this->POST['answer_' . $question->id], $other_answer);
                        $this->POST['answer_' . $question->id] = $choice_map[$this->POST['answer_' . $question->id]]->choice;
                    }
                }
                //古いデータベースに保存するため
                if ($this->POST['other_answer_' . $question->id] && $this->POST['answer_' . $question->id] == 'その他') {
                    $this->POST['answer_' . $question->id] = $this->POST['answer_' . $question->id] . '（' . $this->POST['other_answer_' . $question->id] . '）';
                }
            } else {
                $update_answer_sql = "/* BrandcoAuthTrait->createProfileQuestionAnswers */ UPDATE profile_question_free_answers SET del_flg = 1,updated_at = now()
                WHERE brands_users_relation_id = {$brands_users_relation->id} AND questionnaires_questions_relation_id = {$question_relation->id} AND del_flg = 0";
                $result = $data_builder->executeUpdate($update_answer_sql);
                if (!$result) {
                    throw new aafwException("UPDATE FAILED!");
                }
                $question_service->setQuestionFreeAnswer($question_relation->id, $brands_users_relation->id, $question->id, $this->POST['answer_' . $question->id]);
            }

            //古いデータベースに保存
            /** @var ProfileQuestionnaireService $profile_questionnaire_service */
            $profile_questionnaire_service = $this->createService('ProfileQuestionnaireService');
            $old_new_question = $profile_questionnaire_service->getOldNewProfileQuestionByNewQuestionId($question->id);
            if ($old_new_question) {
                $old_question_id = $old_new_question->old_question_id;
            } else {
                $old_question_id = $question->id;
            }
            $profile_questionnaire_service->createProfileQuestionAnswer($brands_users_relation->id, $old_question_id, $this->POST['answer_' . $question->id]);
        }
    }

    public function shouldShowUpPrivacyInfo($personal_info_type, $cp) {
        if (!class_exists('CpFlowService')) {
            aafwServiceFactory::create('CpFlowService');
        }
        if ($cp !== null && $cp->requireRestriction($this->cp_user)) {
            return true;
        }
        return $personal_info_type !== CpFlowService::ENTRY_WITH_INFO;
    }

    /**
     * @param $cp
     * @param $cp_user
     * @return array
     */
    public function getDemographyStatus($cp, $cp_user) {

        if (!$cp->isRestrictedCampaign()) return array(false, null);

        if ($cp_user && !$cp_user->isNotMatchDemography()) return array(false, null);
        try {
            $demography_errors = null;

            if(!$this->Data['pageStatus']['userInfo']->id){
                if ($cp->restricted_age_flg) {
                    $demography_errors[] = "[" . $cp->restricted_age . "歳以上]";
                }
                if ($cp->restricted_gender_flg) {
                    $demography_errors[] = "[" . Cp::$cp_restricted_gender[$cp->restricted_gender] . "]";
                }
                if ($cp->restricted_address_flg) {
                    $cp_restricted_address_service = $this->getService('CpRestrictedAddressService');
                    $demography_errors[] = "[都道府県、" . $cp_restricted_address_service->getCpRestrictedAddressesString($cp->id) . "在住]";
                }
                if ($demography_errors != null) {
                    return array(false, 'このキャンペーンは'.implode('', $demography_errors).'の方のみ参加することができます');
                }
            }

            AAFW::import('jp.aainc.classes.core.UserAttributeManager');
            $userAttributeManager = new UserAttributeManager($this->Data['pageStatus']['userInfo'], $this->getMoniplaCore());

            if ($cp->restricted_age_flg) {
                $birthday = $userAttributeManager->getBirthDay();

                if ($birthday && $cp->restricted_age > $this->getUserAge($birthday)) {
                    $demography_errors[] = "[" . $cp->restricted_age . "歳以上]";
                }
            }

            if ($cp->restricted_gender_flg) {
                $gender = $userAttributeManager->getSex();

                if ($gender && Cp::$cp_restricted_brief_gender[$cp->restricted_gender] != $gender) {
                    $demography_errors[] =  "[" . Cp::$cp_restricted_gender[$cp->restricted_gender] . "]";
                }
            }

            AAFW::import('jp.aainc.classes.core.ShippingAddressManager');
            $shippingAddressManager = new ShippingAddressManager($this->Data['pageStatus']['userInfo'], $this->getMoniplaCore());

            if ($cp->restricted_address_flg) {
                $address = $shippingAddressManager->getShippingAddress();
                $cp_restricted_address_service = $this->getService('CpRestrictedAddressService');

                if ($address && $address->prefId != 0 && !in_array($address->prefId, $cp_restricted_address_service->getCpRestrictedAddressIds($cp->id))) {
                    $demography_errors[] = "[都道府県、" . $cp_restricted_address_service->getCpRestrictedAddressesString($cp->id) . "在住]";
                }
            }

            if ($demography_errors != null) {
                return array(true,'このキャンペーンは'.implode('', $demography_errors).'の方のみ参加することができます');
            }
        } catch (Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error('Exception BrandcoAuthTrait@getDemographyStatus error ' . $e->getMessage());
        }

        return array(false, null);
    }

    /**
     * @param $user
     * @param $brand
     * @param $entry_cp_id
     */
    public function sendWelcomeMail($user, $brand, $entry_cp_id = null) {

        if($this->canSendWelcomeMail($brand, $user)) {

            /** @var UserMailService $user_mail_service */
            $user_mail_service = $this->createService('UserMailService');

            $user_mail_service->sendWelcomeMail($user, $brand->id, $entry_cp_id);

        }

    }

    /**
     * @param $parsed_url
     * @return string
     */
    public function unparse_url($parsed_url) {
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';

        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    public function isSendBrandCustomWelcomeMail($brand){

        /** @var AdminInviteTokenService $admin_invite_service */
        $admin_invite_service = $this->createService('AdminInviteTokenService');

        if($admin_invite_service->getValidInvitedToken($brand->id)){
            return false;
        }

        /** @var BrandGlobalSettingService $brandGlobalSettingService */
        $brandGlobalSettingService = $this->getService('BrandGlobalSettingService');

        $canSetSignupMailSetting = $brandGlobalSettingService->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::CAN_SET_SIGN_UP_MAIL);

        if (Util::isNullOrEmpty($canSetSignupMailSetting)) {
            return false;
        }

        $brandPageSetting = BrandInfoContainer::getInstance()->getBrandPageSetting();

        if($brandPageSetting->send_signup_mail_flg){
            return true;
        }

        return false;
    }

    public function canSendWelcomeMail($brand, $user){

        /** @var AdminInviteTokenService $admin_invite_service */
        $admin_invite_service = $this->createService('AdminInviteTokenService');

        if($admin_invite_service->getValidInvitedToken($brand->id)){
            return false;
        }

        // TODO 特別対応 Welcomeメールを送らない
        if ($brand->id == Brand::KENKO_KENTEI_ID ||
            $brand->id == Brand::CLUB_LAVIE ||
            $brand->id == Brand::CLUB_LENOVO ||
            $brand->id == Brand::LAVIE_SPECIALFAN ||
            $brand->id == Brand::LENOVO_SPECIALFAN)
        {
                return false;
        }

        if (!$user->created_at || strtotime('+1 day', strtotime($user->created_at)) > time()) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isPersonalFormRequired() {
        AAFW::import('jp.aainc.classes.BrandInfoContainer');
        $brand_page_setting = BrandInfoContainer::getInstance()->getBrandPageSetting();
        $required_personal_form = $brand_page_setting->isPersonalFormRequired();
        if ($required_personal_form) {
            return true;
        }

        if ($this->getCpQuestionnaireService()->getPublicProfileQuestionRelationByBrandId($brand_page_setting->brand_id)) {
            return true;
        }

        return false;
    }
}