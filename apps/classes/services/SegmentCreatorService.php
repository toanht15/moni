<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.lib.db.aafwDataBuilder');

class SegmentCreatorService extends aafwServiceBase {

    const CONDITION_LABEL_MAX_WIDTH     = 400;
    const SUB_CONDITION_LABEL_MAX_WIDTH = 140;

    const SEGMENT_PROVISION_CATEGORY_MODE       = 1;
    const SEGMENT_PROVISION_CONDITION_MODE      = 2;
    const SEGMENT_PROVISION_SUB_CONDITION_MODE  = 3;

    const PROVISION_CATEGORY_USER_DATA              = 1;
    const PROVISION_CATEGORY_CUSTOM_PROFILE         = 2;
    const PROVISION_CATEGORY_CONVERSION_LOGS        = 3;
    const PROVISION_CATEGORY_SNS_CONNECTION         = 4;
    const PROVISION_CATEGORY_SNS_REACTION           = 5;
    const PROVISION_CATEGORY_CAMPAIGN               = 6;
    const PROVISION_CATEGORY_MESSAGE                = 7;
    const PROVISION_CATEGORY_EXTERNAL_CONNECTION    = 8;
    const PROVISION_CATEGORY_IMPORT_VALUE           = 9;

    const PROVISION_SUB_CATEGORY_CAMPAIGN       = 1;
    const PROVISION_SUB_CATEGORY_MESSAGE        = 2;
    const PROVISION_SUB_CATEGORY_SNS_REACTION   = 3;

    private static $segment_targeted_class = array(
        self::SEGMENT_PROVISION_CATEGORY_MODE       => 'jsProvisionCondition',
        self::SEGMENT_PROVISION_CONDITION_MODE      => 'jsProvisionSubCondition',
        self::SEGMENT_PROVISION_SUB_CONDITION_MODE  => 'jsProvisionConditionDetail'
    );

    private static $provision_category_detail = array(
        self::PROVISION_CATEGORY_USER_DATA => array(
            SegmentCreateSqlService::SEARCH_PROFILE_RATE,
            SegmentCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD,
            SegmentCreateSqlService::SEARCH_PROFILE_LAST_LOGIN,
            SegmentCreateSqlService::SEARCH_PROFILE_LOGIN_COUNT,
            SegmentCreateSqlService::SEARCH_PROFILE_SEX,
            SegmentCreateSqlService::SEARCH_PROFILE_AGE,
            SegmentCreateSqlService::SEARCH_PROFILE_ADDRESS
        ),
        self::PROVISION_CATEGORY_CUSTOM_PROFILE => array(
            SegmentCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE,
            SegmentCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE_STATUS
        ),
        self::PROVISION_CATEGORY_CONVERSION_LOGS => array(
            SegmentCreateSqlService::SEARCH_PROFILE_CONVERSION
        ),
        self::PROVISION_CATEGORY_SNS_CONNECTION => array(
            SegmentCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT
        ),
        self::PROVISION_CATEGORY_SNS_REACTION => array(
            SegmentCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE
        ),
        self::PROVISION_CATEGORY_CAMPAIGN => array(
            SegmentCreateSqlService::SEARCH_PARTICIPATE_CONDITION
        ),
        self::PROVISION_CATEGORY_IMPORT_VALUE => array(
            SegmentCreateSqlService::SEARCH_IMPORT_VALUE
        )
    );

    private static $provision_category_list = array(
        self::PROVISION_CATEGORY_USER_DATA          => array('target_type' => self::PROVISION_CATEGORY_USER_DATA, 'condition_label' => 'ユーザー情報'),
        self::PROVISION_CATEGORY_CUSTOM_PROFILE     => array('target_type' => self::PROVISION_CATEGORY_CUSTOM_PROFILE, 'condition_label' => 'カスタムプロフィール'),
        self::PROVISION_CATEGORY_CONVERSION_LOGS    => array('target_type' => self::PROVISION_CATEGORY_CONVERSION_LOGS, 'condition_label' => 'コンバージョンタグ'),
        self::PROVISION_CATEGORY_SNS_CONNECTION     => array('target_type' => self::PROVISION_CATEGORY_SNS_CONNECTION, 'condition_label' => 'SNS連携'),
        self::PROVISION_CATEGORY_SNS_REACTION       => array('target_type' => self::PROVISION_CATEGORY_SNS_REACTION, 'condition_label' => 'SNS上のリアクション'),
        self::PROVISION_CATEGORY_CAMPAIGN           => array('target_type' => self::PROVISION_CATEGORY_CAMPAIGN, 'condition_label' => 'キャンペーン'),
        self::PROVISION_CATEGORY_MESSAGE            => array('target_type' => self::PROVISION_CATEGORY_MESSAGE, 'condition_label' => 'メッセージ'),
//        self::PROVISION_CATEGORY_EXTERNAL_CONNECTION => array('target_type' => self::PROVISION_CATEGORY_EXTERNAL_CONNECTION, 'condition_label' => '外部連携DMP連携')
        self::PROVISION_CATEGORY_IMPORT_VALUE       => array('target_type' => self::PROVISION_CATEGORY_IMPORT_VALUE, 'condition_label' => 'DMP連携')
    );

    private static $segment_condition_list = array(
        self::PROVISION_CATEGORY_USER_DATA => array(
            SegmentCreateSqlService::SEARCH_PROFILE_RATE               => array('target_type' => SegmentCreateSqlService::SEARCH_PROFILE_RATE, 'condition_label' => '評価'),
            SegmentCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD    => array('target_type' => SegmentCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD, 'condition_label' => '登録期間'),
            SegmentCreateSqlService::SEARCH_PROFILE_LAST_LOGIN         => array('target_type' => SegmentCreateSqlService::SEARCH_PROFILE_LAST_LOGIN, 'condition_label' => '最終ログイン'),
            SegmentCreateSqlService::SEARCH_PROFILE_LOGIN_COUNT        => array('target_type' => SegmentCreateSqlService::SEARCH_PROFILE_LOGIN_COUNT, 'condition_label' => 'ログイン回数'),
            SegmentCreateSqlService::SEARCH_PROFILE_SEX                => array('target_type' => SegmentCreateSqlService::SEARCH_PROFILE_SEX, 'condition_label' => '性別'),
            SegmentCreateSqlService::SEARCH_PROFILE_AGE                => array('target_type' => SegmentCreateSqlService::SEARCH_PROFILE_AGE, 'condition_label' => '年齢'),
            SegmentCreateSqlService::SEARCH_PROFILE_ADDRESS            => array('target_type' => SegmentCreateSqlService::SEARCH_PROFILE_ADDRESS, 'condition_label' => '都道府県')
        ),
        self::PROVISION_CATEGORY_SNS_CONNECTION => array(
            SocialAccount::SOCIAL_MEDIA_FACEBOOK    => array('target_type' => SegmentCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT, 'condition_label' => 'Facebook', 'target_id' => SocialAccount::SOCIAL_MEDIA_FACEBOOK),
            SocialAccount::SOCIAL_MEDIA_TWITTER     => array('target_type' => SegmentCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT, 'condition_label' => 'Twitter', 'target_id' => SocialAccount::SOCIAL_MEDIA_TWITTER),
            SocialAccount::SOCIAL_MEDIA_LINE        => array('target_type' => SegmentCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT, 'condition_label' => 'LINE', 'target_id' => SocialAccount::SOCIAL_MEDIA_LINE),
            SocialAccount::SOCIAL_MEDIA_INSTAGRAM   => array('target_type' => SegmentCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT, 'condition_label' => 'Instagram', 'target_id' => SocialAccount::SOCIAL_MEDIA_INSTAGRAM),
            SocialAccount::SOCIAL_MEDIA_YAHOO       => array('target_type' => SegmentCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT, 'condition_label' => 'Yahoo! JAPAN', 'target_id' => SocialAccount::SOCIAL_MEDIA_YAHOO),
            SocialAccount::SOCIAL_MEDIA_GOOGLE      => array('target_type' => SegmentCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT, 'condition_label' => 'Google', 'target_id' => SocialAccount::SOCIAL_MEDIA_GOOGLE),
        ),
        self::PROVISION_CATEGORY_SNS_REACTION => array(
            SocialAccount::SOCIAL_MEDIA_FACEBOOK => array('target_type' => self::PROVISION_SUB_CATEGORY_SNS_REACTION, 'condition_label' => 'Facebook', 'target_id' => SocialApps::PROVIDER_FACEBOOK),
            SocialAccount::SOCIAL_MEDIA_TWITTER => array('target_type' => self::PROVISION_SUB_CATEGORY_SNS_REACTION, 'condition_label' => 'Twitter', 'target_id' => SocialApps::PROVIDER_TWITTER)
        )
    );

    /**
     * @var array
     */
    private static $segment_condition_cp_type = array(
        self::PROVISION_CATEGORY_CAMPAIGN => Cp::TYPE_CAMPAIGN,
        self::PROVISION_CATEGORY_MESSAGE => Cp::TYPE_MESSAGE
    );


    private $data_builder;
    private $brand_id;

    public function __construct($brand_id) {
        $this->data_builder = aafwDataBuilder::newBuilder();
        $this->brand_id = $brand_id;
    }

    public function getConditionView($category_mode, $target_id, $target_type) {
        $parser = new PHPParser();
        $condition_view = "";

        switch ($category_mode) {
            case self::SEGMENT_PROVISION_CATEGORY_MODE:
                $conditions = $this->getCategoryConditionListByType($target_type);
                $condition_view = $parser->parseTemplate('segment/SegmentConditionList.php', array(
                    'conditions' => $conditions,
                    'category_mode' => self::SEGMENT_PROVISION_CONDITION_MODE));
                break;
            case self::SEGMENT_PROVISION_CONDITION_MODE:
                $conditions = $this->getSubCategoryConditionListByType($target_type, $target_id);
                $condition_view = $parser->parseTemplate('segment/SegmentConditionList.php', array(
                    'conditions' => $conditions,
                    'category_mode' => self::SEGMENT_PROVISION_SUB_CONDITION_MODE));
                break;
            case self::SEGMENT_PROVISION_SUB_CONDITION_MODE:
                $condition_view = $this->getConditionViewByType($target_type, $target_id);
                break;
        }

        return $condition_view;
    }

    /**
     * @param $target_type
     * @param null $preset_condition
     * @return array|mixed
     */
    public function getCategoryConditionListByType($target_type, $preset_condition = null) {
        $condition_list = array();

        switch ($target_type) {
            case self::PROVISION_CATEGORY_USER_DATA:
                $condition_list = $this->getSegmentConditionUserDataList($preset_condition);
                break;
            case self::PROVISION_CATEGORY_SNS_CONNECTION:
            case self::PROVISION_CATEGORY_SNS_REACTION:
                $condition_list = self::getSegmentConditionList($target_type, $preset_condition);
                break;
            case self::PROVISION_CATEGORY_CAMPAIGN:
            case self::PROVISION_CATEGORY_MESSAGE:
                $condition_list = $this->getSegmentCpConditionListByType(self::getCpTypeByConditionType($target_type), $preset_condition);
                break;
            case self::PROVISION_CATEGORY_CUSTOM_PROFILE:
                $condition_list = $this->getSegmentProfileQuestionConditionList($preset_condition);
                break;
            case self::PROVISION_CATEGORY_CONVERSION_LOGS:
                $condition_list = $this->getSegmentConversionList($preset_condition);
                break;
            case self::PROVISION_CATEGORY_IMPORT_VALUE:
                $condition_list = $this->getSegmentImportValueConditionList($preset_condition);
                break;
        }

        return $condition_list;
    }

    /**
     * @param $preset_condition
     * @return array
     */
    public function getSegmentConversionList($preset_condition) {
        $condition_list = array();
        $conversion_service = $this->getService('ConversionService');

        if ($preset_condition) {
            $conversion = $conversion_service->getConversionById($preset_condition);
            $condition_list[$conversion->id] = array(
                'target_id' => $conversion->id,
                'target_type' => SegmentCreateSqlService::SEARCH_PROFILE_CONVERSION,
                'condition_label' => $conversion->name
            );

            return $condition_list;
        }

        $conversions = $conversion_service->getConversionsByBrandId($this->brand_id);
        foreach ($conversions as $conversion) {
            $condition_list[$conversion->id] = array(
                'target_id' => $conversion->id,
                'target_type' => SegmentCreateSqlService::SEARCH_PROFILE_CONVERSION,
                'condition_label' => $conversion->name
            );
        }

        return $condition_list;
    }

    /**
     * @param $preset_condition
     * @return array
     */
    public function getSegmentImportValueConditionList($preset_condition) {
        $condition_list = array();
        $brand_service = $this->getService('BrandService');

        if ($preset_condition) {
            $definition = $brand_service->getBrandUserAttributeDefinitionById($preset_condition);
            $condition_list[$definition->id] = array(
                'target_id' => $definition->id,
                'target_type' => SegmentCreateSqlService::SEARCH_IMPORT_VALUE,
                'condition_label' => $definition->attribute_name
            );

            return $condition_list;
        }

        $definitions = $brand_service->getCustomAttributeDefinitions($this->brand_id);
        foreach ($definitions as $definition) {
            $condition_list[$definition->id] = array(
                'target_id' => $definition->id,
                'target_type' => SegmentCreateSqlService::SEARCH_IMPORT_VALUE,
                'condition_label' => $definition->attribute_name
            );
        }

        return $condition_list;
    }

    /**
     * @param $preset_condition
     * @return array
     */
    public function getSegmentProfileQuestionConditionList($preset_condition) {
        $condition_list = array();
        $cp_questionnaire_service = $this->getService('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);

        if ($preset_condition === 0) {
            $condition_list[] = array(
                'target_type' => SegmentCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE_STATUS,
                'condition_label' => "アンケート回答"
            );

            return $condition_list;
        }

        if ($preset_condition) {
            $relation = $cp_questionnaire_service->getProfileQuestionRelationsById($preset_condition);
            $question = $cp_questionnaire_service->getQuestionById($relation->question_id);

            $condition_list[$relation->id] = array(
                'target_id' => $relation->id,
                'target_type' => SegmentCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE,
                'condition_label' => 'Q' . $relation->number . '.' . $question->question
            );

            return $condition_list;
        }

        /** @var CpQuestionnaireService $profile_questionnaire_service */
        $profile_questionnaire_service = $this->getService('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);
        $profile_question_relations = $profile_questionnaire_service->getPublicProfileQuestionRelationByBrandId($this->brand_id);
        $use_profile_questions = $profile_questionnaire_service->useProfileQuestion($profile_question_relations);

        if ($use_profile_questions) {
            $condition_list[] = array(
                'target_type' => SegmentCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE_STATUS,
                'condition_label' => "アンケート回答"
            );

            foreach ($use_profile_questions as $profile_question) {
                $cp_questionnaire_service = $this->getService('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);

                $relation = $cp_questionnaire_service->getProfileQuestionRelationsById($profile_question->id);
                $question = $cp_questionnaire_service->getQuestionById($relation->question_id);

                $condition_list[$relation->id] = array(
                    'target_id' => $relation->id,
                    'target_type' => SegmentCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE,
                    'condition_label' => 'Q' . $relation->number . '.' . $question->question
                );
            }
        }

        return $condition_list;
    }

    /**
     * @param $target_type
     * @param $target_id
     * @param $preset_condition
     * @return array
     */
    public function getSubCategoryConditionListByType($target_type, $target_id, $preset_condition = null) {
        $condition_list = array();

        if ($target_type == self::PROVISION_SUB_CATEGORY_CAMPAIGN || $target_type == self::PROVISION_SUB_CATEGORY_MESSAGE) {
            $cp_flow_service = $this->getService('CpFlowService');
            $cp_groups = $cp_flow_service->getCpActionGroupsByCpId($target_id);

            $cur_step = 1;
            foreach ($cp_groups as $cp_group) {
                $actions = $cp_flow_service->getCpActionsByCpActionGroupId($cp_group->id);

                foreach ($actions as $action) {
                    $condition_list[$action->id] = array(
                        'target_type' => SegmentCreateSqlService::SEARCH_PARTICIPATE_CONDITION,
                        'target_id' => $action->id,
                        'condition_label' => $cur_step . '　' . $action->getCpActionData()->title
                    );

                    if ($action->type == CpAction::TYPE_QUESTIONNAIRE) {
                        /** @var CpQuestionnaireService $cp_questionnaire_service */
                        $cp_questionnaire_service = $this->getService('CpQuestionnaireService');
                        $questionnaire_action = $cp_questionnaire_service->getCpQuestionnaireAction($action->id);
                        $relations = $cp_questionnaire_service->getRelationsByQuestionnaireActionId($questionnaire_action->id);

                        foreach ($relations as $relation) {
                            $question = $cp_questionnaire_service->getQuestionById($relation->question_id);

                            $condition_list[$action->id . '/' . SegmentCreateSqlService::SEARCH_QUESTIONNAIRE . '/' . $relation->id] = array(
                                'target_id' => $action->id . '/' . $relation->id,
                                'target_type' => SegmentCreateSqlService::SEARCH_QUESTIONNAIRE,
                                'condition_label' => $cur_step . '　Q' . $relation->number . '.' . $question->question
                            );
                        }
                    } elseif ($action->type == CpAction::TYPE_PHOTO) {
                        /** @var CpPhotoActionService $cp_photo_action_service */
                        $cp_photo_action_service = $this->getService('CpPhotoActionService');
                        $cp_photo_action = $cp_photo_action_service->getCpPhotoAction($action->id);

                        if ($cp_photo_action->fb_share_required || $cp_photo_action->tw_share_required) {
                            $condition_list[$action->id . '/' . SegmentCreateSqlService::SEARCH_PHOTO_SHARE_SNS] = array(
                                'target_id' => $action->id,
                                'target_type' => SegmentCreateSqlService::SEARCH_PHOTO_SHARE_SNS,
                                'condition_label' => $cur_step . "　シェアSNS"
                            );

                            $condition_list[$action->id . '/' . SegmentCreateSqlService::SEARCH_PHOTO_SHARE_TEXT] = array(
                                'target_id' => $action->id,
                                'target_type' => SegmentCreateSqlService::SEARCH_PHOTO_SHARE_TEXT,
                                'condition_label' => $cur_step . "　シェアテキスト"
                            );
                        }

                        if ($cp_photo_action->panel_hidden_flg) {
                            $condition_list[$action->id . '/' . SegmentCreateSqlService::SEARCH_PHOTO_APPROVAL_STATUS] = array(
                                'target_id' => $action->id,
                                'target_type' => SegmentCreateSqlService::SEARCH_PHOTO_APPROVAL_STATUS,
                                'condition_label' => $cur_step . "　検閲"
                            );
                        }
                    } elseif ($action->type == CpAction::TYPE_INSTAGRAM_HASHTAG) {
                        /** @var CpInstagramHashtagActionService $cp_instagram_hashtag_action_service */
                        $cp_instagram_hashtag_action_service = $this->getService('CpInstagramHashtagActionService');
                        $cp_instagram_hashtag_action = $cp_instagram_hashtag_action_service->getCpInstagramHashtagActionByCpActionId($action->id);

                        $condition_list[$action->id . '/' . SegmentCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION] = array(
                            'target_id' => $action->id,
                            'target_type' => SegmentCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION,
                            'condition_label' => $cur_step . "　ユーザネーム重複"
                        );

                        $condition_list[$action->id . '/' . SegmentCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME] = array(
                            'target_id' => $action->id,
                            'target_type' => SegmentCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME,
                            'condition_label' => $cur_step . "　登録投稿順序"
                        );

                        if ($cp_instagram_hashtag_action->approval_flg) {
                            $condition_list[$action->id . '/' . SegmentCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS] = array(
                                'target_id' => $action->id,
                                'target_type' => SegmentCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS,
                                'condition_label' => $cur_step . "　検閲"
                            );
                        }
                    } elseif ($action->type == CpAction::TYPE_SHARE) {
                        $condition_list[$action->id . '/' . SegmentCreateSqlService::SEARCH_SHARE_TYPE] = array(
                            'target_id' => $action->id,
                            'target_type' => SegmentCreateSqlService::SEARCH_SHARE_TYPE,
                            'condition_label' => $cur_step . "　シェア状況"
                        );

                        $condition_list[$action->id . '/' . SegmentCreateSqlService::SEARCH_SHARE_TEXT] = array(
                            'target_id' => $action->id,
                            'target_type' => SegmentCreateSqlService::SEARCH_SHARE_TEXT,
                            'condition_label' => $cur_step . "　シェアコメント"
                        );
                    } elseif ($action->type == CpAction::TYPE_FACEBOOK_LIKE) {
                        $condition_list[$action->id . '/' . SegmentCreateSqlService::SEARCH_FB_LIKE_TYPE] = array(
                            'target_id' => $action->id,
                            'target_type' => SegmentCreateSqlService::SEARCH_FB_LIKE_TYPE,
                            'condition_label' => $cur_step . "　Facebookいいね！状況"
                        );
                    } elseif ($action->type == CpAction::TYPE_TWITTER_FOLLOW) {
                        $condition_list[$action->id . '/' . SegmentCreateSqlService::SEARCH_TW_FOLLOW_TYPE] = array(
                            'target_id' => $action->id,
                            'target_type' => SegmentCreateSqlService::SEARCH_TW_FOLLOW_TYPE,
                            'condition_label' => $cur_step . "　Twitterフォロー状況"
                        );
                    } elseif ($action->type == CpAction::TYPE_YOUTUBE_CHANNEL) {
                        $condition_list[$action->id . '/' . SegmentCreateSqlService::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION] = array(
                            'target_id' => $action->id,
                            'target_type' => SegmentCreateSqlService::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION,
                            'condition_label' => $cur_step . "　登録状況"
                        );
                    } elseif ($action->type == CpAction::TYPE_POPULAR_VOTE) {
                        /** @var CpPopularVoteActionService $cp_popular_vote_action_service */
                        $cp_popular_vote_action_service = $this->getService('CpPopularVoteActionService');
                        $cp_popular_vote_action = $cp_popular_vote_action_service->getCpPopularVoteActionByCpActionId($action->id);

                        $condition_list[$action->id . '/' . SegmentCreateSqlService::SEARCH_POPULAR_VOTE_CANDIDATE] = array(
                            'target_id' => $action->id,
                            'target_type' => SegmentCreateSqlService::SEARCH_POPULAR_VOTE_CANDIDATE,
                            'condition_label' => $cur_step . "　投票"
                        );

                        if ($cp_popular_vote_action->fb_share_required || $cp_popular_vote_action->tw_share_required) {
                            $condition_list[$action->id . '/' . SegmentCreateSqlService::SEARCH_POPULAR_VOTE_SHARE_SNS] = array(
                                'target_id' => $action->id,
                                'target_type' => SegmentCreateSqlService::SEARCH_POPULAR_VOTE_SHARE_SNS,
                                'condition_label' => $cur_step . "　シェアSNS"
                            );

                            $condition_list[$action->id . '/' . SegmentCreateSqlService::SEARCH_POPULAR_VOTE_SHARE_TEXT] = array(
                                'target_id' => $action->id,
                                'target_type' => SegmentCreateSqlService::SEARCH_POPULAR_VOTE_SHARE_TEXT,
                                'condition_label' => $cur_step . "　シェアテキスト"
                            );
                        }
                    }

                    $cur_step++;
                }
            }
        } else if ($target_type == self::PROVISION_SUB_CATEGORY_SNS_REACTION) {
            /** @var BrandSocialAccountService $brand_social_account_service */
            $brand_social_account_service = $this->getService('BrandSocialAccountService');
            $social_accounts = $brand_social_account_service->getSocialAccountsByBrandId($this->brand_id, $target_id);

            foreach ($social_accounts as $social_account) {
                $condition_list[$social_account->social_media_account_id] = array(
                    'target_type' => SegmentCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE,
                    'target_id' => $target_id . '/' . $social_account->social_media_account_id,
                    'condition_label' => $social_account->name
                );
            }
        }

        if ($preset_condition) {
            return array($preset_condition => $condition_list[$preset_condition]);
        }

        return $condition_list;
    }

    /**
     * @param $type
     * @param $preset_condition
     * @return array
     */
    public function getSegmentCpConditionListByType($type, $preset_condition) {
        $query = 'SELECT c.id cp_id
            FROM cps c
                LEFT JOIN cp_action_groups cag
                    ON cag.cp_id = c.id AND cag.order_no = 1 AND cag.del_flg = 0
                LEFT JOIN cp_actions ca
                    ON ca.cp_action_group_id = cag.id AND ca.order_no = 1 AND ca.del_flg = 0
            WHERE c.del_flg = 0
                AND c.archive_flg = 0
                AND c.type = ' . $type . '
                AND c.brand_id = ' . $this->brand_id . '';

        if ($preset_condition != null) {
            $query .= ' AND c.id = ' . $preset_condition;
        }

        $query .= ' ORDER BY c.id DESC';

        $cp_list = $this->data_builder->getBySQL($query, array(array('__NOFETCH__' => true)));
        $condition_list = array();

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');

        while ($row = $this->data_builder->fetch($cp_list)) {
            $condition_list[$row['cp_id']] = array(
                'target_type' => $type,
                'target_id' => $row['cp_id'],
                'condition_label' => $row['cp_id'] . "　" . $cp_flow_service->getCpTitleByCpId($row['cp_id'])
            );
        }

        return $condition_list;
    }

    /**
     * @param $target_type
     * @param $target_id
     * @param $condition_data
     * @return string|パースした結果
     */
    public function getConditionViewByType($target_type, $target_id, $condition_data = array()) {
        $parser = new PHPParser();
        $condition_view = "";

        switch ($target_type) {
            case SegmentCreateSqlService::SEARCH_PROFILE_RATE:
                $condition_view = $parser->parseTemplate('segment/SegmentConditionProfileRate.php', array('condition_data' => $condition_data));
                break;
            case SegmentCreateSqlService::SEARCH_PROFILE_SEX:
                $condition_view = $parser->parseTemplate('segment/SegmentConditionProfileSex.php', array('condition_data' => $condition_data));
                break;
            case SegmentCreateSqlService::SEARCH_PROFILE_ADDRESS:
                $condition_view = $parser->parseTemplate('segment/SegmentConditionProfileAddress.php', array('condition_data' => $condition_data));
                break;
            case SegmentCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD:
                $condition_view = $parser->parseTemplate('segment/SegmentConditionRegisterPeriod.php', array('condition_type' => $target_type, 'condition_data' => $condition_data));
                break;
            case SegmentCreateSqlService::SEARCH_PROFILE_LAST_LOGIN:
                $condition_view = $parser->parseTemplate('segment/SegmentConditionLastLogin.php', array('condition_type' => $target_type, 'condition_data' => $condition_data));
                break;
            case SegmentCreateSqlService::SEARCH_PROFILE_LOGIN_COUNT:
                $unit_label = '回';
                $condition_view = $parser->parseTemplate('segment/SegmentConditionNumRange.php', array('condition_type' => $target_type, 'condition_data' => $condition_data, 'unit_label' => $unit_label));
                break;
            case SegmentCreateSqlService::SEARCH_PROFILE_AGE:
                $unit_label = '歳';
                $condition_view = $parser->parseTemplate('segment/SegmentConditionNumRange.php', array('condition_type' => $target_type, 'condition_data' => $condition_data, 'unit_label' => $unit_label));
                break;
            case SegmentCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT:
                $condition_view = $parser->parseTemplate('segment/SegmentConditionProfileSocialAccount.php', array('condition_type' => $target_type, 'target_id' => $target_id, 'condition_data' => $condition_data));
                break;
            case SegmentCreateSqlService::SEARCH_PARTICIPATE_CONDITION:
                $condition_view = $parser->parseTemplate('segment/SegmentConditionCampaignActionStatus.php', array('condition_type' => $target_type, 'target_id' => $target_id, 'condition_data' => $condition_data));
                break;
            case SegmentCreateSqlService::SEARCH_QUESTIONNAIRE:
            case SegmentCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE:
                $condition_view = $parser->parseTemplate('segment/SegmentConditionQuestionnaireQuestion.php', array('condition_type' => $target_type, 'target_id' => $target_id, 'condition_data' => $condition_data));
                break;
            case SegmentCreateSqlService::SEARCH_PROFILE_CONVERSION:
                $condition_view = $parser->parseTemplate('segment/SegmentConditionConversionLogs.php', array('condition_type' => $target_type, 'target_id' => $target_id, 'condition_data' => $condition_data));
                break;
            case SegmentCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE:
                $condition_view = $parser->parseTemplate('segment/SegmentConditionSocialAccountInteractive.php', array(
                    'condition_type' => $target_type,
                    'target_id' => $target_id,
                    'condition_data' => $condition_data
                ));
                break;
            case SegmentCreateSqlService::SEARCH_IMPORT_VALUE:
                $condition_view = $parser->parseTemplate('segment/SegmentConditionImportValue.php', array('condition_data' => $condition_data, 'target_id' => $target_id));
                break;
            case SegmentCreateSqlService::SEARCH_PHOTO_SHARE_SNS:
            case SegmentCreateSqlService::SEARCH_POPULAR_VOTE_SHARE_SNS:
                $target_data = array('target_type' => $target_type, 'target_id' => $target_id);
                $condition_view = $parser->parseTemplate('segment/SearchConditionShareSNSList.php', array('condition_data' => $condition_data, 'target_data' => $target_data));
                break;
            case SegmentCreateSqlService::SEARCH_POPULAR_VOTE_CANDIDATE:
                $condition_view = $parser->parseTemplate('segment/SearchConditionPopularVoteCandidate.php', array('condition_data' => $condition_data, 'target_id' => $target_id));
                break;
            case SegmentCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE_STATUS:
            case SegmentCreateSqlService::SEARCH_PHOTO_SHARE_TEXT:
            case SegmentCreateSqlService::SEARCH_PHOTO_APPROVAL_STATUS:
            case SegmentCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION:
            case SegmentCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME:
            case SegmentCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS:
            case SegmentCreateSqlService::SEARCH_SHARE_TYPE:
            case SegmentCreateSqlService::SEARCH_SHARE_TEXT:
            case SegmentCreateSqlService::SEARCH_FB_LIKE_TYPE:
            case SegmentCreateSqlService::SEARCH_TW_FOLLOW_TYPE:
            case SegmentCreateSqlService::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION:
            case SegmentCreateSqlService::SEARCH_POPULAR_VOTE_SHARE_TEXT:
                $choice_data = SegmentCreateSqlService::getSearchConditionChoiceData($target_type);
                $choice_data['target_id'] = $target_id;
                $condition_view = $parser->parseTemplate('segment/SegmentConditionCheckbox.php', array('choice_data' => $choice_data, 'condition_data' => $condition_data));
                break;
            default:
                break;
        }

        return $condition_view;
    }

    public function getSegmentConditionUserDataList($preset_condition) {

        $brandSettingPage = BrandInfoContainer::getInstance()->getBrandPageSetting();

        $provision_category_user_data_tmp = self::$segment_condition_list[self::PROVISION_CATEGORY_USER_DATA];

        if(!$brandSettingPage->privacy_required_sex) {
            unset($provision_category_user_data_tmp[SegmentCreateSqlService::SEARCH_PROFILE_SEX]);
        }

        if(!$brandSettingPage->privacy_required_address) {
            unset($provision_category_user_data_tmp[SegmentCreateSqlService::SEARCH_PROFILE_ADDRESS]);
        }

        if(!$brandSettingPage->privacy_required_birthday) {
            unset($provision_category_user_data_tmp[SegmentCreateSqlService::SEARCH_PROFILE_AGE]);
        }

        if ($preset_condition) {
            return array($preset_condition => $provision_category_user_data_tmp[$preset_condition]);
        }

        return $provision_category_user_data_tmp;
    }

    /**
     * @param $condition_type
     * @return int|null|string
     */
    public static function getProvisionCategoryType($condition_type) {
        foreach (self::$provision_category_detail as $category_type => $condition_list) {
            if (in_array($condition_type, $condition_list)) {
                return $category_type;
            }
        }

        return null;
    }

    /**
     * @param null $preset_category
     * @return array
     */
    public static function getProvisionCategoryList($preset_category = null) {
        if ($preset_category) {
            return array($preset_category => self::$provision_category_list[$preset_category]);
        }

        return self::$provision_category_list;
    }

    /**
     * @param $category_mode
     * @return mixed
     */
    public static function getSegmentTargetedClass($category_mode) {
        return self::$segment_targeted_class[$category_mode];
    }

    /**
     * @param $condition_type
     * @param $preset_condition
     * @return array
     */
    public static function getSegmentConditionList($condition_type, $preset_condition = null) {
        if ($preset_condition) {
            return array($preset_condition => self::$segment_condition_list[$condition_type][$preset_condition]);
        }

        return self::$segment_condition_list[$condition_type];
    }

    /**
     * @param $condition_type
     * @return mixed
     */
    public static function getCpTypeByConditionType($condition_type) {
        return self::$segment_condition_cp_type[$condition_type];
    }

    /**
     * @param $category_mode
     * @param $condition_label
     * @return string
     */
    public static function getBriefConditionText($category_mode, $condition_label) {
        if ($category_mode == self::SEGMENT_PROVISION_CATEGORY_MODE) {
            return $condition_label;
        }

        $max_width = $category_mode == self::SEGMENT_PROVISION_CONDITION_MODE ? self::CONDITION_LABEL_MAX_WIDTH : self::SUB_CONDITION_LABEL_MAX_WIDTH;
        return Util::cutTextByWidth($condition_label, $max_width);
    }
}