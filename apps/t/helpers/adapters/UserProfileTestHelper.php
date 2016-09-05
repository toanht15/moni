<?php

class UserProfileTestHelper extends BaseTest {

    /**
     * $conditionの注意点として、必ず$condition['number']を入れる。
     * また、$condition['public']の設定がない限りは公開状態にならない
     * プロフィールアンケート設定
     * @param Brands $brand
     * @param $condition
     * @return mixed
     */
    public function newProfileQuestionnaireByBrand(Brand $brand, $condition) {
        AAFW::import ('jp.aainc.aafw.classes.services.CpQuestionnaireService');

        if (!$brand || !$condition) return;
        if (!$condition['number']) return;
        if($condition['question_type'] == QuestionTypeService::FREE_ANSWER_TYPE) {
            $question = $this->entity(
                'ProfileQuestionnaireQuestions',
                array(
                    'type_id' => QuestionTypeService::FREE_ANSWER_TYPE,
                    'question' => '自由回答テスト'
                )
            );
        } elseif(QuestionTypeService::isChoiceQuestion($condition['question_type'])) {
            $question = $this->entity(
                'ProfileQuestionnaireQuestions',
                array(
                    'type_id' => $condition['question_type'],
                    'question' => '選択回答テスト'
                )
            );

            $choice_requirement = $this->entity(
                'ProfileQuestionChoiceRequirements',
                array(
                    'question_id' => $question->id,
                    'use_other_choice_flg' => $condition['use_other_choice_flg'] ?: CpQuestionnaireService::NOT_USE_OTHER_CHOICE,
                    'random_order_flg' => $condition['random_order_flg'] ?: CpQuestionnaireService::NOT_RANDOM_ORDER,
                    'multi_answer_flg' => $condition['multi_answer_flg'] ?: CpQuestionnaireService::SINGLE_ANSWER
                )
            );

            // 選択肢はデフォルトで2つ作成
            $choice1 = $this->entity(
                'ProfileQuestionChoices',
                array(
                    'question_id' => $question->id,
                    'choice_num' => 1,
                    'choice' => 'テスト選択肢1',
                    'other_choice_flg' => CpQuestionnaireService::NOT_USE_OTHER_CHOICE
                )
            );
            $choice2 = $this->entity(
                'ProfileQuestionChoices',
                array(
                    'question_id' => $question->id,
                    'choice_num' => 2,
                    'choice' => 'テスト選択肢2',
                    'other_choice_flg' => CpQuestionnaireService::NOT_USE_OTHER_CHOICE
                )
            );
        }
        $questionnaire_relation = $this->entity(
            'ProfileQuestionnairesQuestionsRelations',
            array(
                'brand_id' => $brand->id,
                'question_id' => $question->id,
                'requirement_flg' => $condition['requirement_flg'] ?: CpQuestionnaireService::QUESTION_NOT_REQUIRED,
                'number' => $condition['number'],
                'public' => $condition['public'] ?: 0,
            )
        );
        return array($questionnaire_relation, $question, $choice_requirement, array($choice1, $choice2));
    }

    /**
     * コンバージョン設定
     * @param Brands $brand
     * @param $condition
     * @return array
     */
    public function newConversionsByBrand(Brand $brand, $condition) {
        if (!$brand || !$condition) return;

        $conversions = array();
        foreach ($condition as $conversion) {
            $conversions[] = $this->entity(
                'Conversions',
                array(
                    'brand_id' => $brand->id,
                    'name' => $conversion['name'] ?: '',
                    'description' => $conversion['description'] ?: '',
                )
            );
        }
        return $conversions;
    }

    /**
     * プロフィールデータ作成
     * TODO: 更新が必要なカラムは随時追加して下さい
     * @param $relation
     * @param $condition
     * @return array
     */
    public function newProfileByBrandUser(BrandsUsersRelation $relation, $condition) {
        if (!$relation) return;

        $user_profile = array();
        if ($condition['brands_users_relations']) {
            $user_profile['brands_users_relations'] = $this->updateBrandsUsersRelation($relation, $condition['brands_users_relations']);
        }

        if ($condition['user_search_info']) {
            $user_profile['users_search_info'] = $this->createUserSearchInfo($relation, $condition['user_search_info']);
        }

        if ($condition['brand_user_search_info']) {
            $user_profile['brands_users_search_info'] = $this->createBrandsUsersSearchInfo($relation, $condition['user_search_info']);
        }

        if ($condition['social_accounts']) {
            $user_profile['social_accounts'] = $this->createSocialAccounts($relation, $condition['social_accounts']);
        }

        if ($condition['shipping_addresses']) {
            $user_profile['shipping_addresses'] = $this->createShippingAddress($relation, $condition['shipping_addresses']);
        }

        if ($cond_cv = $condition['brands_users_conversions']) {
            $user_profile['brands_users_conversions'] = $this->createBrandsUsersConversions($relation, $condition['brands_users_conversions']);
        }

        if ($condition['profile_questionnaires']) {
            $user_profile['profile_questionnaires'] = $this->createQuestionnairesAnswers($relation, $condition['profile_questionnaires']);
        }

        if ($condition['social_likes']) {
            $user_profile['social_likes'] = $this->createSocialLikes($relation, $condition['social_likes']);
        }
        return $user_profile;
    }

    public function updateBrandsUsersRelation(BrandsUsersRelation $relation, $condition) {
        if ($condition['no']) {
            $existing_relation = $this->find('BrandsUsersRelations', $condition['no']);
            if ($existing_relation != null) {
                $condition['no'] = null;
            }
        }
        $updated_relation = $this->updateEntities(
            'BrandsUsersRelations',
            array('id' => $relation->id),
            array(
                'brand_id' => $relation->brand_id,
                'no' => $condition['no'] ?: $this->max('BrandsUsersRelations', 'no') + 1,
                'rate' => $condition['rate'] ?: 0,
                'optin_flg' => $condition['optin_flg'] ?: 1,
                'last_login_date' => $condition['last_login_date'] ?: '0000-00-00 00:00:00',
                'login_count' => $condition['login_count'] ?: 0,
                'personal_info_flg' => $condition['personal_info_flg'] ?: 1
            )
        );
        return $updated_relation;
    }

    public function createUserSearchInfo(BrandsUsersRelation $relation, $condition) {
        $user_search_info = $this->entity(
            'UserSearchInfos',
            array(
                'user_id' => $relation->user_id,
                'sex' => $condition['sex'] ?: '',
                'birthday' => $condition['birthday'] ?: date('Y-m-d H:i:s')
            )
        );
        return $user_search_info;
    }

    public function createBrandsUsersSearchInfo(BrandsUsersRelation $relation, $condition) {
        $brands_users_search_info = $this->entity(
            'BrandsUsersSearchInfo',
            array(
                'brands_users_relation_id' => $relation->id,
                'cp_entry_count' => $condition['cp_entry_count'] ?: 0,
                'cp_announce_count' => $condition['cp_announce_count'] ?: 0,
                'message_delivered_count' => $condition['message_delivered_count'] ?: 0,
                'message_read_count' => $condition['message_read_count'] ?: 0
            )
        );
        return $brands_users_search_info;
    }

    public function createSocialAccounts(BrandsUsersRelation $relation, $condition) {
        $social_accounts = array();
        foreach ($condition['social_accounts'] as $social_media_id => $account_condition) {
            $social_accounts[$social_media_id] = $this->createSocialAccount($relation, $social_media_id, $account_condition);
        }
        return $social_accounts;
    }

    public function createSocialAccount(BrandsUsersRelation $relation, $social_media_id, $condition) {
        $social_account = $this->entity(
            'SocialAccounts',
            array(
                'user_id' => $relation->user_id,
                'social_media_id' => $social_media_id,
                'profile_page_url' => $condition['profile_page_url'] ?: '',
                'friend_count' => $condition['friend_count'] ?: 0,
            )
        );
        return $social_account;
    }

    public function createShippingAddress(BrandsUsersRelation $relation, $condition) {
        $shipping_addresses = $this->entity(
            'ShippingAddresses',
            array(
                'user_id' => $relation->user_id,
                'first_name' => $condition['first_name'] ?: '',
                'last_name' => $condition['last_name'] ?: '',
                'first_name_kana' => $condition['first_name_kana'] ?: '',
                'last_name_kana' => $condition['last_name_kana'] ?: '',
                'zip_code1' => $condition['zip_code1'] ?: '',
                'zip_code2' => $condition['zip_code2'] ?: '',
                'pref_id' => $condition['pref_id'] ?: '',
                'address1' => $condition['address1'] ?: '',
                'address2' => $condition['address2'] ?: '',
                'address3' => $condition['address3'] ?: '',
                'tel_no1' => $condition['tel_no1'] ?: '',
                'tel_no2' => $condition['tel_no2'] ?: '',
                'tel_no3' => $condition['tel_no3'] ?: ''
            )
        );
        return $shipping_addresses;
    }

    public function createBrandsUsersConversions($relation, $condition) {
        $brands_users_conversions = array();
        $cv_count_key = 0;
        foreach ($condition['conversions'] as $conversion) {
            for ($i = 0; $i < $condition['count'][$cv_count_key]; $i++) {
                $brands_users_conversions[] = $this->createBrandsUsersConversion($relation, $conversion->id);
            }
            $cv_count_key++;
        }
        return $brands_users_conversions;
    }

    public function createBrandsUsersConversion(BrandsUsersRelation $relation, $condition_id) {
        $brands_users_conversion = $this->entity(
            'BrandsUsersConversions',
            array(
                'user_id' => $relation->user_id,
                'brand_id' => $relation->brand_id,
                'conversion_id' => $condition_id
            )
        );
        return $brands_users_conversion;
    }

    public function createQuestionnairesAnswers(BrandsUsersRelation $relation, $profile_questionnaires) {
        AAFW::import ('jp.aainc.aafw.classes.services.CpQuestionnaireService');

        $cp_questionnaire_service = new CpQuestionnaireService(CpQuestionnaireService::TYPE_PROFILE_QUESTION);
        foreach($profile_questionnaires as $questionnaire) {
            $question = $cp_questionnaire_service->getQuestionById($questionnaire->question_id);
            if($question->type_id == QuestionTypeService::FREE_ANSWER_TYPE) {
                $answers[] = $this->entity(
                    'QuestionFreeAnswers',
                    array(
                        'questionnaires_questions_relation_id' => $questionnaire->id,
                        'brands_users_relation_id' => $relation->id,
                        'answer_text' => '自由回答テスト'
                    )
                );
            } else {
                // 選択肢の場合、複数選択の場合は両方回答、単一選択の場合は2つ目の選択肢に回答
                $requirement = $cp_questionnaire_service->getRequirementByQuestionId($question->id);
                $choices = $cp_questionnaire_service->getChoicesByQuestionId($question->id);
                $choice_array = $choices->toArray();
                $answers[] = $this->entity(
                    'QuestionChoiceAnswers',
                    array(
                        'choice_id' => $choice_array[1]->id,
                        'questionnaires_questions_relation_id' => $questionnaire->id,
                        'brands_users_relation_id' => $relation->id,
                    )
                );
                if($requirement->multi_answer_flg) {
                    $answers[] = $this->entity(
                        'QuestionChoiceAnswers',
                        array(
                            'choice_id' => $choice_array[0]->id,
                            'questionnaires_questions_relation_id' => $questionnaire->id,
                            'brands_users_relation_id' => $relation->id,
                        )
                    );

                }
            }
        }
        return $answers;
    }

    public function createSocialLikes(BrandsUsersRelation $relation, $social_likes) {
        $social_like = $this->entity(
            'SocialLikes',
            array(
                'user_id' => $relation->user_id,
                'like_id' => $social_likes['like_id'],
                'social_media_id' => $social_likes['social_media_id']
            )
        );
        return $social_like;
    }

}