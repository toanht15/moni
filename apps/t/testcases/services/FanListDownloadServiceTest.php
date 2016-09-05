<?php
AAFW::import ('jp.aainc.classes.services.FanListDownloadService');
AAFW::import('jp.aainc.classes.services.CpCreateSqlService');
AAFW::import('jp.aainc.t.helpers.adapters.UserProfileTestHelper');

class FanListDownloadServiceTest extends BaseTest {
    /** @var FanListDownloadServiceTest $target */
    private $target;
    /** @var CpFlowService $cp_flow_service */
    private $cp_flow_service;
    /** @var UserProfileTestHelper $profile_helper */
    private $profile_helper;

    private $cp_condition = array(
        array(
            CpAction::TYPE_ENTRY,
            CpAction::TYPE_QUESTIONNAIRE
        ),
    );

    public function setUp() {
        $this->target = aafwServiceFactory::create("FanListDownloadService");
        $this->cp_flow_service = aafwServiceFactory::create("CpFlowService");
        $this->profile_helper = new UserProfileTestHelper();

        list($this->brand, $this->cp, $this->cp_action_groups, $this->cp_actions, $this->cp_concrete_actions)
        = $this->newCampaign($this->cp_condition);

        $this->brand_global_setting = $this->newBrandGlobalSetting(
            $this->brand,
            'original_sns_accounts',
            SocialAccountService::SOCIAL_MEDIA_GDO
        );

        $condition = array(
            'brand' => $this->brand,
            'public_flg' => BrandPageSetting::STATUS_PUBLIC,
            'tag_text' => '',
            'agreement' => '',
            'privacy_required_name' => '1',
            'privacy_required_sex' => '1',
            'privacy_required_birthday' => '1',
            'privacy_required_address' => '1',
            'privacy_required_tel' => '1',
            'privacy_required_restricted' => '1',
            'top_page_og_url' => '',
            'meta_title' => '',
            'meta_description' => '',
            'meta_keyword' => '',
            'og_image_url' => '',
        );
        $this->brand_page_setting = $this->newBrandPageSetting($condition);

        $condition = array(
            'brand' => $this->brand,
            'social_media_account_id' => 1111,
            'social_app_id' => SocialApps::PROVIDER_FACEBOOK
        );
        $brand_social_account = $this->newBrandSocialAccounts($condition);
        $test_user1 = $this->newBrandUserByBrand($this->brand);
        $test_user2 = $this->newCampaignUserByCp($this->cp);

        $pq_condition1 = array(
            'question_type' => QuestionTypeService::FREE_ANSWER_TYPE,
            'number' => 1,
            'public' => 1
        );
        $quesitonnaires1 = $this->profile_helper->newProfileQuestionnaireByBrand($this->brand, $pq_condition1);

        $this->questionnaire_relation1 = $quesitonnaires1[0];
        $this->choices1 = $quesitonnaires1[3];

        $pq_condition2 = array(
            'question_type' => QuestionTypeService::CHOICE_ANSWER_TYPE,
            'number' => 2,
            'public' => 1,
            'use_other_choice_flg' => CpQuestionnaireService::NOT_USE_OTHER_CHOICE,
            'random_order_flg' => CpQuestionnaireService::NOT_RANDOM_ORDER,
            'multi_answer_flg' => CpQuestionnaireService::SINGLE_ANSWER
        );
        $quesitonnaires2 = $this->profile_helper->newProfileQuestionnaireByBrand($this->brand, $pq_condition2);
        $this->questionnaire_relation2 = $quesitonnaires2[0];
        $this->choices2 = $quesitonnaires2[3];

        $pq_condition3 = array(
            'question_type' => QuestionTypeService::CHOICE_ANSWER_TYPE,
            'number' => 3,
            'public' => 1,
            'use_other_choice_flg' => CpQuestionnaireService::NOT_USE_OTHER_CHOICE,
            'random_order_flg' => CpQuestionnaireService::NOT_RANDOM_ORDER,
            'multi_answer_flg' => CpQuestionnaireService::MULTI_ANSWER
        );

        $quesitonnaires3 = $this->profile_helper->newProfileQuestionnaireByBrand($this->brand, $pq_condition3);
        $this->questionnaire_relation3 = $quesitonnaires3[0];
        $this->choices3 = $quesitonnaires3[3];

        $cv_condition = array(
            array(
                'name' => 'cv_test1'
            ),
            array(
                'name' => 'cv_test2'
            )
        );
//        $conversions = $this->profile_helper->newConversionsByBrand($this->brand, $cv_condition);

        $test_user1_condition = array(
            'brands_users_relations' => array(
                'rate' => 100,
                'optin_flg' => 2,
                'last_login_date' => date('Y-m-d H:i:s'),
                'login_count' => 3,
            ),
            'user_search_info' => array(
                'sex' => 'm',
                'birthday' => date('Y-m-d H:i:s')
            ),
            'brands_users_search_info' => array(
                'cp_entry_count' => 3,
                'cp_announce_count' => 2,
                'message_delivered_count' => 2,
                'message_read_count' => 1
            ),
            'social_accounts' => array(
                SocialAccount::SOCIAL_MEDIA_FACEBOOK => array(
                    'friend_count' => 5,
                    'profile_page_url' => 'https://facebook.com/1111'
                ),
                SocialAccount::SOCIAL_MEDIA_TWITTER => array(
                    'friend_count' => 10,
                    'profile_page_url' => 'https://twitter.com/1111'
                ),
                SocialAccount::SOCIAL_MEDIA_GOOGLE => array(
                    'profile_page_url' => 'https://plus.google.com/1111'
                ),
                SocialAccount::SOCIAL_MEDIA_GDO => array(
                ),
            ),
            'shipping_addresses' => array(
                'pref_id' => 1
            ),
            'profile_questionnaire' => array(
            ),
//            'brands_users_conversions' => array(
//                'conversions' => $conversions,
//                'count' => array(2, 4)
//            ),
            'social_likes' => array(
                'like_id' => $brand_social_account->social_media_account_id,
                'social_media_id' => SocialAccountService::SOCIAL_MEDIA_FACEBOOK
            )
        );
        $test_user2_condition = array(
            'brands_users_relations' => array(
                'rate' => 200,
                'optin_flg' => 1,
                'last_login_date' => date('Y-m-d H:i:s'),
                'login_count' => 4,
            ),
            'user_search_info' => array(
                'sex' => 'f',
                'birthday' => date('Y-m-d H:i:s')
            ),
            'brands_users_search_info' => array(
                'cp_entry_count' => 4,
                'cp_announce_count' => 3,
                'message_delivered_count' => 3,
                'message_read_count' => 2
            ),
            'social_accounts' => array(
                SocialAccount::SOCIAL_MEDIA_FACEBOOK => array(
                    'friend_count' => 10,
                    'profile_page_url' => 'https://facebook.com/2222'
                ),
                SocialAccount::SOCIAL_MEDIA_TWITTER => array(
                    'friend_count' => 15,
                    'profile_page_url' => 'https://twitter.com/2222'
                ),
                SocialAccount::SOCIAL_MEDIA_GOOGLE => array(
                    'profile_page_url' => 'https://plus.google.com/2222'
                ),
                SocialAccount::SOCIAL_MEDIA_GDO => array(
                ),
            ),
            'shipping_addresses' => array(
                'pref_id' => 2
            ),
            'profile_questionnaire' => array(
            ),
//            'brands_users_conversions' => array(
//                'conversions' => $conversions,
//                'count' => array(3, 1)
//            ),
        );
        $this->test_user1_profile = $this->profile_helper->newProfileByBrandUser($test_user1['relation'], $test_user1_condition);
        $this->test_user2_profile = $this->profile_helper->newProfileByBrandUser($test_user2['relation'], $test_user2_condition);

        $join_users = array();
        $join_users['user_id'][1] = $test_user1['users']->id;
        $join_users['relation_id'][1] = $test_user1['relation']->id;
        $join_users['cp_user_id'][1] = 0;
        $join_users['user_id'][2] = $test_user2['users']->id;
        $join_users['relation_id'][2] = $test_user2['relation']->id;
        $join_users['cp_user_id'][2] = $test_user2['cp_user']->id;
        $this->join_users = $join_users;

        $this->page_info = array(
            'cp_id'     => $this->cp->id,
            'action_id' => $this->cp_flow_service->getEntryActionByCpId($this->cp->id)->id,
            'brand_id'  => $this->brand->id,
            'tab_no'    => CpCreateSqlService::TAB_PAGE_PROFILE
        );
    }

    public function testGetActionData01_originalSnsAccount() {
        $page_info = $this->page_info;
        $page_info['action_id'] += 1;
        $this->assertNotEmpty($this->target->getActionData(FanListDownloadService::TYPE_PROFILE, $page_info)['original_sns_account']);
    }

    public function testGetActionData02_targetAction() {
        $page_info = $this->page_info;
        $page_info['action_id'] += 1;
        $this->assertNotEmpty($this->target->getActionData(FanListDownloadService::TYPE_PROFILE, $page_info)['target_action']);
    }

    public function testGetActionData03_pageSettings() {
        $page_info = $this->page_info;
        $page_info['action_id'] += 1;
        $this->assertNotEmpty($this->target->getActionData(FanListDownloadService::TYPE_PROFILE, $page_info)['page_settings']);
    }

    public function testGetActionData04_getSocialLikes() {
        $page_info = $this->page_info;
        $page_info['action_id'] += 1;
        $this->assertNotEmpty($this->target->getActionData(FanListDownloadService::TYPE_PROFILE, $page_info)['getSocialLikes']);
    }

    public function testGetActionData05_profile_questions() {
        $page_info = $this->page_info;
        $page_info['action_id'] += 1;
        $this->assertNotEmpty($this->target->getActionData(FanListDownloadService::TYPE_PROFILE, $page_info)['getSocialLikes']);
    }

//    public function testGetActionData06_conversions() {
//        $page_info = $this->page_info;
//        $page_info['action_id'] += 1;
//        $this->assertNotEmpty($this->target->getActionData(FanListDownloadService::TYPE_PROFILE, $page_info)['conversions']);
//    }

    public function testGetActionData07_cpActions() {
        $page_info = $this->page_info;
        $page_info['action_id'] += 1;
        $this->assertNotEmpty($this->target->getActionData(FanListDownloadService::TYPE_PROFILE, $page_info)['cp_actions']);
    }

    public function testGetActionData08_lastCpAction() {
        $page_info = $this->page_info;
        $page_info['action_id'] += 1;
        $this->assertNotEmpty($this->target->getActionData(FanListDownloadService::TYPE_PROFILE, $page_info)['last_cp_action']);
    }
}
