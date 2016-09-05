<div class="itemTableWrap">
        <table class="itemTable">
            <thead>
                <tr>
                    <th rowspan="2" class="jsAreaToggleWrap">
                        評価<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_RATE] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchMemberRate.php', array(
                            'search_rate' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_RATE]
                        ))) ?>
                    </th>
                    <th rowspan="2" class="jsAreaToggleWrap">
                        会員No<a href="javascript:void(0)" flg="fan_no" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_MEMBER_NO] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchMemberNo.php', array(
                            'search_profile_member_no' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_MEMBER_NO],
                            'flg' => 'fan_no'
                        ))) ?>
                    </th>
                    <?php if($data['duplicateAddressShowType'] == CpCreateSqlService::SHIPPING_ADDRESS_DUPLICATE || ($data['duplicateAddressShowType'] == CpCreateSqlService::SHIPPING_ADDRESS_USER_DUPLICATE && $data['isShowDuplicateAddressCpUserList'])): ?>
                        <th rowspan="2" class="jsAreaToggleWrap">
                            住所<br>重複<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_DUPLICATE_ADDRESS] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                            <?php write_html($this->parseTemplate('SearchDuplicateAddress.php', array(
                                'search_duplicate_address' => $data['search_condition'][CpCreateSqlService::SEARCH_DUPLICATE_ADDRESS],
                                'cp_id' => $data['cp_id'] ? $data['cp_id']->id : null,
                                'duplicate_address_show_type' => $data['duplicateAddressShowType']
                            ))) ?>
                        </th>
                    <?php endif; ?>
                    <th rowspan="2" class="jsAreaToggleWrap">
                        登録期間<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchRegisterPeriod.php', array(
                            'search_profile_register_period' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD]
                        ))) ?>
                    </th>
                    <th colspan="<?php write_html(count($data['original_sns_account_array']) + 7)?>">
                        SNS連携 / 友達・フォロワー数
                    </th>
                    <?php if(!$data['isSocialLikesEmpty'] && $data['facebook_accounts']): ?>
                        <th colspan="<?php write_html($data['facebook_accounts']->total()*CpCreateSqlService::DISPLAY_3_ITEMS)?>">
                            <span class="iconFB2">Facebook</span>Facebookアクション
                        </th>
                    <?php endif; ?>
                    <?php if(!$data['isTwitterFollowsEmpty'] && $data['twitter_accounts']): ?>
                        <th colspan="<?php write_html($data['twitter_accounts']->total()*CpCreateSqlService::DISPLAY_3_ITEMS)?>">
                            <span class="iconTW2">Twitter</span>Twitterアクション
                        </th>
                    <?php endif; ?>
                    <th rowspan="2" class="jsAreaToggleWrap">
                        最終ログイン<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_LAST_LOGIN] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchLastLogin.php', array(
                            'search_profile_last_login' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_LAST_LOGIN]
                        ))) ?>
                    </th>
                    <th rowspan="2" class="jsAreaToggleWrap">
                        ログイン<br>回数<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_LOGIN_COUNT] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchCountInfo.php', array(
                            'search_type' => CpCreateSqlService::SEARCH_PROFILE_LOGIN_COUNT,
                            'search_type_name' => CpCreateSqlService::$search_count_item[CpCreateSqlService::SEARCH_PROFILE_LOGIN_COUNT],
                            'search_condition' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_LOGIN_COUNT]
                        ))) ?>
                    </th>
                    <?php $privacy_required_count = 0; ?>
                    <?php if($data['page_settings']->privacy_required_sex): ?>
                        <?php $privacy_required_count += 1;?>
                        <th rowspan="2" class="jsAreaToggleWrap">
                            性別<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SEX] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                            <?php write_html($this->parseTemplate('SearchSex.php', array(
                                'search_profile_sex' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SEX]
                            ))) ?>
                        </th>
                    <?php endif; ?>
                    <?php if($data['page_settings']->privacy_required_address): ?>
                        <?php $privacy_required_count += 1;?>
                        <th rowspan="2" class="jsAreaToggleWrap">
                            都道府県<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_ADDRESS] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                            <?php write_html($this->parseTemplate('SearchAddress.php', array(
                                'search_profile_address' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_ADDRESS]
                            ))) ?>
                        </th>
                    <?php endif; ?>
                    <?php if($data['page_settings']->privacy_required_birthday): ?>
                        <?php $privacy_required_count += 1;?>
                        <th rowspan="2" class="jsAreaToggleWrap">
                            年齢<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_AGE] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                            <?php write_html($this->parseTemplate('SearchAge.php', array(
                                'search_profile_age' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_AGE]
                            ))) ?>
                        </th>
                    <?php endif; ?>
                    <th colspan="2">
                        キャンペーン
                    </th>
                    <th colspan="3">
                        メッセージ
                    </th>
                    <?php if ($data['use_profile_questions']): ?>
                        <th rowspan="2" class="jsAreaToggleWrap">
                            アンケート<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE_STATUS] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                            <?php write_html($this->parseTemplate('SearchQuestionnaireStatus.php', array(
                                'search_questionnaire_status' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE_STATUS]
                            ))) ?>
                        </th>
                    <?php endif; ?>
                    <?php foreach ($data['use_profile_questions'] as $profile_question_relation):?>
                        <?php $profile_question = $data['profile_questions'][$profile_question_relation->id]?>
                        <th rowspan="2" class="jsAreaToggleWrap" title="<?php assign('Q'.$profile_question_relation->number.'.'.$profile_question->question)?>">
                            <?php assign(Util::cutTextByWidth('Q'.$profile_question_relation->number.'.'.$profile_question->question, 190))?>
                            <?php $template_name = $profile_question->type_id != QuestionTypeService::FREE_ANSWER_TYPE ? 'SearchChoiceQuestionnaire.php' : 'SearchFreeAnswerQuestionnaire.php'; ?>
                            <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE.'/'.$profile_question_relation->id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                            <?php write_html($this->parseTemplate($template_name, array(
                                'search_questionnaire'      => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE.'/'.$profile_question_relation->id],
                                'question_id'               => $profile_question->id,
                                'relation_id'               => $profile_question_relation->id,
                                'search_questionnaire_type' => CpQuestionnaireService::TYPE_PROFILE_QUESTION
                            ))) ?>
                        </th>
                    <?php endforeach;?>
                    <?php if ($data['extend_columns']): //TODO ハードコーディング: カンコーブランドの追加カラム ?>
                        <?php foreach ($data['extend_columns'] as $question_relation_id => $column_name): ?>
                            <th rowspan="2" class="jsAreaToggleWrap" title="<?php assign($column_name) ?>">
                                <?php assign(Util::cutTextByWidth($column_name, 190))?>
                                <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_CHILD_BIRTH_PERIOD.'/'.$question_relation_id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                                <?php write_html($this->parseTemplate("SearchChildBirthPeriod.php", array(
                                    'relation_id' => $question_relation_id,
                                    'search_child_birth_period' => $data['search_condition'][CpCreateSqlService::SEARCH_CHILD_BIRTH_PERIOD.'/'.$question_relation_id]
                                )))?>
                            </th>
                        <?php endforeach; ?>
                    <?php endif;?>
                    <?php foreach ($data['conversions'] as $conversion): ?>
                        <th rowspan="2" class="jsAreaToggleWrap" title="<?php assign($conversion->name); ?>">
                            <?php assign(Util::cutTextByWidth($conversion->name, 190)); ?>
                            <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_CONVERSION.'/'.$conversion->id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                            <?php write_html($this->parseTemplate('SearchConversions.php', array(
                                'search_profile_conversion' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_CONVERSION.'/'.$conversion->id],
                                'conversion'         => $conversion
                            ))) ?>
                        </th>
                    <?php endforeach; ?>

                    <?php foreach ($data['definitions'] as $def):?>
                        <?php if ($def->attribute_type == BrandUserAttributeDefinitions::ATTRIBUTE_TYPE_SET):?>
                            <th rowspan="2" class="jsAreaToggleWrap" title="<?php assign($def->attribute_name); ?>">
                                <?php assign(Util::cutTextByWidth($def->attribute_name, 190)); ?>
                                <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_IMPORT_VALUE.'/'.$def->id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                                <?php write_html($this->parseTemplate('SearchImportValue.php', array(
                                    'search_import_value' => $data['search_condition'][CpCreateSqlService::SEARCH_IMPORT_VALUE.'/'.$def->id],
                                    'definition'          => $def
                                ))) ?>
                            </th>
                        <?php else: ?>
                            <th rowspan="2"><?php assign($def->attribute_name);?></th>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <th class="snsAccount jsAreaToggleWrap">
                        <span class="iconFB2">Facebook</span>
                        <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.SocialAccountService::SOCIAL_MEDIA_FACEBOOK] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchSocialAccount.php', array(
                            'search_social_account' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.SocialAccountService::SOCIAL_MEDIA_FACEBOOK],
                            'social_media_type' => SocialAccountService::SOCIAL_MEDIA_FACEBOOK
                        ))) ?>
                    </th>
                    <th class="snsAccount jsAreaToggleWrap">
                        <span class="iconTW2">Twitter</span>
                        <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.SocialAccountService::SOCIAL_MEDIA_TWITTER] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchSocialAccount.php', array(
                            'search_social_account' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.SocialAccountService::SOCIAL_MEDIA_TWITTER],
                            'social_media_type' => SocialAccountService::SOCIAL_MEDIA_TWITTER
                        ))) ?>
                    </th>
                    <th class="snsAccount jsAreaToggleWrap">
                        <span class="iconLN2">Line</span>
                        <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.SocialAccountService::SOCIAL_MEDIA_LINE] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchSocialAccount.php', array(
                            'search_social_account' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.SocialAccountService::SOCIAL_MEDIA_LINE],
                            'social_media_type' => SocialAccountService::SOCIAL_MEDIA_LINE
                        ))) ?>
                    </th>
                    <th class="snsAccount jsAreaToggleWrap">
                        <span class="iconIG2">Instagram</span>
                        <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.SocialAccountService::SOCIAL_MEDIA_INSTAGRAM] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchSocialAccount.php', array(
                            'search_social_account' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.SocialAccountService::SOCIAL_MEDIA_INSTAGRAM],
                            'social_media_type' => SocialAccountService::SOCIAL_MEDIA_INSTAGRAM
                        ))) ?>
                    </th>
                    <th class="snsAccount jsAreaToggleWrap">
                        <span class="iconYH2">Yahoo! JAPAN</span>
                        <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.SocialAccountService::SOCIAL_MEDIA_YAHOO] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchSocialAccount.php', array(
                            'search_social_account' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.SocialAccountService::SOCIAL_MEDIA_YAHOO],
                            'social_media_type' => SocialAccountService::SOCIAL_MEDIA_YAHOO
                        ))) ?>
                    </th>
                    <th class="snsAccount jsAreaToggleWrap">
                        <span class="iconGP2">Google+</span>
                        <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.SocialAccountService::SOCIAL_MEDIA_GOOGLE] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchSocialAccount.php', array(
                            'search_social_account' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.SocialAccountService::SOCIAL_MEDIA_GOOGLE],
                            'social_media_type' => SocialAccountService::SOCIAL_MEDIA_GOOGLE
                        ))) ?>
                    </th>
                    <?php if (in_array(SocialAccountService::SOCIAL_MEDIA_GDO, $data['original_sns_account_array'])): ?>
                        <th class="snsAccount jsAreaToggleWrap">
                            <span class="iconGdo2">GDO</span>
                            <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.SocialAccountService::SOCIAL_MEDIA_GDO] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                            <?php write_html($this->parseTemplate('SearchSocialAccount.php', array(
                                'search_social_account' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.SocialAccountService::SOCIAL_MEDIA_GDO],
                                'social_media_type' => SocialAccountService::SOCIAL_MEDIA_GDO
                            ))) ?>
                        </th>
                    <?php endif; ?>
                    <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_LINKEDIN, $data['original_sns_account_array'])): ?>
                        <th class="snsAccount jsAreaToggleWrap">
                            <span class="iconIN2">LinkedIn</span>
                            <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.SocialAccountService::SOCIAL_MEDIA_LINKEDIN] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                            <?php write_html($this->parseTemplate('SearchSocialAccount.php', array(
                                'search_social_account' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.SocialAccountService::SOCIAL_MEDIA_LINKEDIN],
                                'social_media_type' => SocialAccountService::SOCIAL_MEDIA_LINKEDIN
                            ))) ?>
                        </th>
                    <?php endif; ?>
                    <th class="snsAccount jsAreaToggleWrap">
                        合計<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_SUM] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchSocialAccountSum.php', array(
                            'search_social_sum_count' => $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_SUM],
                        ))) ?>
                    </th>
                    <?php if (!$data['isSocialLikesEmpty']): ?>
                        <?php foreach ($data['facebook_accounts'] as $fb_account): ?>
                            <?php
                                $has_liked_page =  $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id]['search_social_account_interactive/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id.'/'.CpCreateSqlService::LIKED] || $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id]['search_social_account_interactive/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id.'/'.CpCreateSqlService::NOT_LIKE];
                                $has_liked_count = $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id]['search_social_account_is_liked_count/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id.'/'.CpCreateSqlService::LIKED] ||
                                                   $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id]['search_social_account_is_liked_count/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id.'/'.CpCreateSqlService::NOT_LIKE] ||
                                                   $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id]['search_social_account_is_liked_count/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id.'/from'] ||
                                                   $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id]['search_social_account_is_liked_count/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id.'/to'];
                                $has_commented_count =  $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id]['search_social_account_is_commented_count/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id.'/'.CpCreateSqlService::LIKED] ||
                                                        $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id]['search_social_account_is_commented_count/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id.'/'.CpCreateSqlService::NOT_LIKE] ||
                                                        $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id]['search_social_account_is_commented_count/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id.'/from'] ||
                                                        $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id]['search_social_account_is_commented_count/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id.'/to'];

                            ?>
                            <th class="userActionInfo jsAreaToggleWrap">
                                <img src="<?php assign($fb_account->picture_url)?>" width="20" height="20" alt="<?php assign($fb_account->name)?>" title="<?php assign($fb_account->name)?>">
                                <a href="javascript:void(0)" class="<?php  assign($has_liked_page ? 'iconBtnSort' : 'btnArrowB1')  ?> jsAreaToggle">絞り込む</a>
                                <?php write_html($this->parseTemplate('SearchSocialAccountInteractive.php', array(
                                    'search_social_account_interactive' => $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id],
                                    'social_account_name' => $fb_account->name,
                                    'social_app_id' => SocialApps::PROVIDER_FACEBOOK,
                                    'social_media_id' => $fb_account->social_media_account_id,
                                ))) ?>
                                <span class="textBalloon1">
                                    <span><?php assign($fb_account->name);?>にいいね！</span>
                                <!-- /.textBalloon1 --></span>
                            </th>
                            <th class="userActionInfo jsAreaToggleWrap">
                                <span class="iconLike">
                                    <a href="javascript:void(0)" class="<?php assign($has_liked_count ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                                </span>
                                <?php write_html($this->parseTemplate('SearchSocialAccountLikeCount.php', array(
                                    'social_account_name' => $fb_account->name,
                                    'search_social_account_interactive' => $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id],
                                    'social_app_id' => SocialApps::PROVIDER_FACEBOOK,
                                    'social_media_id' => $fb_account->social_media_account_id,
                                    'search_fb_posts_like_count_type_name' => CpCreateSqlService::$search_sns_action_count[CpCreateSqlService::SEARCH_FB_POSTS_LIKE_COUNT],
                                ))) ?>
                                <span class="textBalloon1">
                                <span><?php assign($fb_account->name);?>の投稿にいいね！</span>
                                <!-- /.textBalloon1 --></span>
                            </th>
                            <th class="userActionInfo jsAreaToggleWrap">
                                <span class="iconComment">
                                    <a href="javascript:void(0)" class="<?php assign($has_commented_count ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                                </span>
                                <?php write_html($this->parseTemplate('SearchSocialAccountCommentCount.php', array(
                                    'social_account_name' => $fb_account->name,
                                    'search_social_account_interactive' => $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id],
                                    'social_app_id' => SocialApps::PROVIDER_FACEBOOK,
                                    'social_media_id' => $fb_account->social_media_account_id,
                                    'search_fb_posts_comment_count_type_name' => CpCreateSqlService::$search_sns_action_count[CpCreateSqlService::SEARCH_FB_POSTS_COMMENT_COUNT],
                                ))) ?>
                                <span class="textBalloon1">
                                <span><?php assign($fb_account->name);?>の投稿にコメント</span>
                                <!-- /.textBalloon1 --></span>
                            </th>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if (!$data['isTwitterFollowsEmpty']): ?>
                        <?php foreach($data['twitter_accounts'] as $tw_account): ?>
                            <?php
                                $has_followed =  $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id]['search_social_account_interactive/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id.'/'.CpCreateSqlService::LIKED] || 
                                                      $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id]['search_social_account_interactive/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id.'/'.CpCreateSqlService::NOT_LIKE];
                                $has_replied_count = $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id]['search_social_account_is_replied_count/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id.'/'.CpCreateSqlService::LIKED] ||
                                                   $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id]['search_social_account_is_replied_count/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id.'/'.CpCreateSqlService::NOT_LIKE] ||
                                                   $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id]['search_social_account_is_replied_count/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id.'/from'] ||
                                                   $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id]['search_social_account_is_replied_count/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id.'/to'];
                                $has_retweeted_count =  $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id]['search_social_account_is_retweeted_count/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id.'/'.CpCreateSqlService::LIKED] ||
                                                        $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id]['search_social_account_is_retweeted_count/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id.'/'.CpCreateSqlService::NOT_LIKE] ||
                                                        $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id]['search_social_account_is_retweeted_count/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id.'/from'] ||
                                                        $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id]['search_social_account_is_retweeted_count/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id.'/to'];

                            ?>
                            <th class="userActionInfo jsAreaToggleWrap">
                                <img src="<?php assign($tw_account->picture_url)?>" width="20" height="20" alt="<?php assign($tw_account->name)?>" title="<?php assign($tw_account->name)?>">
                                <a href="javascript:void(0)" class="<?php assign($has_followed ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                                <?php write_html($this->parseTemplate('SearchSocialAccountInteractive.php', array(
                                    'search_social_account_interactive' => $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id],
                                    'social_account_name' => $tw_account->name,
                                    'social_app_id' => SocialApps::PROVIDER_TWITTER,
                                    'social_media_id' => $tw_account->social_media_account_id,
                                ))) ?>
                                <span class="textBalloon1">
                                    <span><?php assign($tw_account->name);?>をフォロー</span>
                                <!-- /.textBalloon1 --></span>
                            </th>
                            <th class="userActionInfo jsAreaToggleWrap">
                                <span class="iconReply"></span>
                                <a href="javascript:void(0)" class="<?php assign($has_replied_count ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                                <?php write_html($this->parseTemplate('SearchSocialAccountReplyCount.php', array(
                                    'social_account_name' => $tw_account->name,
                                    'search_social_account_interactive' => $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id],
                                    'social_app_id' => SocialApps::PROVIDER_TWITTER,
                                    'social_media_id' => $tw_account->social_media_account_id,
                                    'search_tw_tweet_reply_count_type_name' => CpCreateSqlService::$search_sns_action_count[CpCreateSqlService::SEARCH_TW_TWEET_REPLY_COUNT],
                                ))) ?>
                                <span class="textBalloon1">
                                <span><?php assign($tw_account->name);?>のツイートをリプライ</span>
                                <!-- /.textBalloon1 --></span>
                            </th>
                            <th class="userActionInfo jsAreaToggleWrap">
                                <span class="iconRetweet"></span>
                                <a href="javascript:void(0)" class="<?php assign($has_retweeted_count ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                                <?php write_html($this->parseTemplate('SearchSocialAccountRetweetCount.php', array(
                                    'social_account_name' => $tw_account->name,
                                    'search_social_account_interactive' => $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id],
                                    'social_app_id' => SocialApps::PROVIDER_TWITTER,
                                    'social_media_id' => $tw_account->social_media_account_id,
                                    'search_tw_tweet_retweet_count_type_name' => CpCreateSqlService::$search_sns_action_count[CpCreateSqlService::SEARCH_TW_TWEET_RETWEET_COUNT],
                                ))) ?>
                                <span class="textBalloon1">
                                <span><?php assign($tw_account->name);?>のツイートをリツイート</span>
                                <!-- /.textBalloon1 --></span>
                            </th>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <th class="snsAccount jsAreaToggleWrap">
                        参加<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_CP_ENTRY_COUNT] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchCountInfo.php', array(
                            'search_type' => CpCreateSqlService::SEARCH_CP_ENTRY_COUNT,
                            'search_type_name' => CpCreateSqlService::$search_count_item[CpCreateSqlService::SEARCH_CP_ENTRY_COUNT],
                            'search_condition' => $data['search_condition'][CpCreateSqlService::SEARCH_CP_ENTRY_COUNT]
                        ))) ?>
                    </th>
                    <th class="snsAccount jsAreaToggleWrap">
                        当選<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_CP_ANNOUNCE_COUNT] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchCountInfo.php', array(
                            'search_type' => CpCreateSqlService::SEARCH_CP_ANNOUNCE_COUNT,
                            'search_type_name' => CpCreateSqlService::$search_count_item[CpCreateSqlService::SEARCH_CP_ANNOUNCE_COUNT],
                            'search_condition' => $data['search_condition'][CpCreateSqlService::SEARCH_CP_ANNOUNCE_COUNT]
                        ))) ?>
                    </th>
                    <th class="snsAccount jsAreaToggleWrap">
                        受信<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_MESSAGE_DELIVERED_COUNT] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchCountInfo.php', array(
                            'search_type' => CpCreateSqlService::SEARCH_MESSAGE_DELIVERED_COUNT,
                            'search_type_name' => CpCreateSqlService::$search_count_item[CpCreateSqlService::SEARCH_MESSAGE_DELIVERED_COUNT],
                            'search_condition' => $data['search_condition'][CpCreateSqlService::SEARCH_MESSAGE_DELIVERED_COUNT]
                        ))) ?>
                    </th>
                    <th class="snsAccount jsAreaToggleWrap">
                        閲覧<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_MESSAGE_READ_COUNT] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchCountInfo.php', array(
                            'search_type' => CpCreateSqlService::SEARCH_MESSAGE_READ_COUNT,
                            'search_type_name' => CpCreateSqlService::$search_count_item[CpCreateSqlService::SEARCH_MESSAGE_READ_COUNT],
                            'search_condition' => $data['search_condition'][CpCreateSqlService::SEARCH_MESSAGE_READ_COUNT]
                        ))) ?>
                    </th>
                    <th class="snsAccount jsAreaToggleWrap">
                        閲覧率<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_MESSAGE_READ_RATIO] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchMessageReadRatio.php', array(
                            'search_condition' => $data['search_condition'][CpCreateSqlService::SEARCH_MESSAGE_READ_RATIO]
                        ))) ?>
                    </th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($data['fan_list_users'] as $fan_list_user): ?>
                <?php $user_profile = $data['user_profile'][$fan_list_user->user_id]; ?>
                <?php if ($data['action']->id): ?>
                    <?php $page_user_message = $data['page_user_message'][$fan_list_user->user_id]; ?>
                    <?php if ($page_user_message[0]): ?>
                        <tr class="sendUser">
                    <?php elseif ($page_user_message[1]): ?>
                        <tr class="checkedUser">
                    <?php else: ?>
                        <tr>
                    <?php endif; ?>
                <?php else: ?>
                    <tr>
                <?php endif; ?>
                        <td class="userRating">
                            <?php  $rate_info = $data['brand_user_relation_service']->getMemberRate($user_profile['rate']);  ?>
                            <img src="<?php assign($this->setVersion($rate_info['image_url'])) ?>" width="<?php assign($user_profile['rate'] == BrandsUsersRelationService::BLOCK ? 18 : 24 )?>" height="<?php assign($user_profile['rate'] == BrandsUsersRelationService::BLOCK ? 18 : 24 )?>" alt="<?php assign($user_profile['rate'] == BrandsUsersRelationService::BLOCK ? 'block user' : 'rating '.$user_profile['rate'] )?>"><?php assign($rate_info['rate']) ?>
                            <p class="ratingBox"><a class="ratingBlock" id="<?php assign($user_profile['brand_user_relation_id']) ?>">ブロック</a><span class="starRating" id="<?php assign($user_profile['brand_user_relation_id']) ?>" data-score="<?php assign($user_profile['rate'])?>"></span></p>
                        </td>
                        <td><?php assign($user_profile['no'] > 0 ? $user_profile['no'] : '-') ?></td>

                        <?php if($data['duplicateAddressShowType'] == CpCreateSqlService::SHIPPING_ADDRESS_DUPLICATE): ?>
                            <td><?php assign($user_profile['shipping_address_duplicate_count'] ? ($user_profile['shipping_address_duplicate_count'] == BrandsUsersRelationService::NOT_DUPLICATE_ADDRESS ? '重複なし' : $user_profile['shipping_address_duplicate_count'] ) : '未取得' )?></td>
                        <?php elseif($data['duplicateAddressShowType'] == CpCreateSqlService::SHIPPING_ADDRESS_USER_DUPLICATE && $data['isShowDuplicateAddressCpUserList']): ?>
                            <td><?php assign($user_profile['shipping_address_user_duplicate_count'] ? ($user_profile['shipping_address_user_duplicate_count'] == CpUser::NOT_DUPLICATE_ADDRESS ? '重複なし'  : $user_profile['shipping_address_user_duplicate_count']): '未取得' ) ?></td>
                        <?php endif; ?>

                        <?php if ($user_profile['history'] == 'NEW'): ?>
                            <td><span class="attention1" title="<?php assign('登録日：'.date("Y/m/d", strtotime($user_profile['history_by_datetime'])))?>"><?php assign($user_profile['history']) ?></span></td>
                        <?php else: ?>
                            <td><span title="<?php assign('登録日：'.date("Y/m/d", strtotime($user_profile['history_by_datetime'])))?>"><?php assign($user_profile['history']) ?><span></td>
                        <?php endif; ?>
                        <td>
                            <?php
                                if (!empty($user_profile['sa1_id'])) {
                                    if (empty($data['is_hide_personal_info']) && !empty($user_profile['sa1_profile_page_url'])) {
                                        write_html('<a href="' . assign_str($user_profile['sa1_profile_page_url']) . '" target="_blank" class="iconFB2">Facebook</a>' . assign_str($user_profile['sa1_friend_count']));
                                    }
                                    elseif (empty($data['is_hide_personal_info']) && !empty($user_profile['sa1_uid'])) {
                                        write_html('<a href="https://www.facebook.com/' . assign_str($user_profile['sa1_uid']).'" target="_blank" class="iconFB2">Facebook</a>' . assign_str($user_profile['sa1_friend_count']));
                                    } else {
                                        write_html('<span class="iconFB2">Facebook</span>'.assign_str($user_profile['sa1_friend_count']));
                                    }
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                                if (!empty($user_profile['sa3_id'])) {
                                    if (empty($data['is_hide_personal_info']) && !empty($user_profile['sa3_profile_page_url'])) {
                                        write_html('<a href="' . assign_str($user_profile['sa3_profile_page_url']) . '" target="_blank" class="iconTW2">Twitter</a>' . assign_str($user_profile['sa3_friend_count']));
                                    } elseif (empty($data['is_hide_personal_info']) && !empty($user_profile['sa3_name'])) {
                                        write_html('<a href="https://twitter.com/' . assign_str($user_profile['sa3_name']) . '" target="_blank" class="iconTW2">Twitter</a>' . assign_str($user_profile['sa3_friend_count']));
                                    } else {
                                        write_html('<span class="iconTW2">Twitter</span>'.assign_str($user_profile['sa3_friend_count']));
                                    }
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                                if (!empty($user_profile['sa8_id'])) {
                                    if (empty($data['is_hide_personal_info']) && !empty($user_profile['sa8_profile_page_url'])) {
                                        write_html('<a href="' . assign_str($user_profile['sa8_profile_page_url']) . '" target="_blank" class="iconLN2">LINE</a>');
                                    } else {
                                        write_html('<span class="iconLN2">LINE</span>');
                                    }
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                                if (!empty($user_profile['sa7_id'])) {
                                    if (empty($data['is_hide_personal_info']) && !empty($user_profile['sa7_profile_page_url'])) {
                                        write_html('<a href="'.assign_str($user_profile['sa7_profile_page_url']).'" target="_blank" class="iconIG2">Instagram</a>'.assign_str($user_profile['sa7_friend_count']));
                                    } elseif (empty($data['is_hide_personal_info']) && !empty($user_profile['sa7_name'])) {
                                        write_html('<a href="https://www.instagram.com/'.assign_str($user_profile['sa7_name']).'" target="_blank" class="iconIG2">Instagram</a>'.assign_str($user_profile['sa7_friend_count']));
                                    } else {
                                        write_html('<span class="iconIG2">Instagram</span>'.assign_str($user_profile['sa7_friend_count']));
                                    }
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                                if (!empty($user_profile['sa5_id'])) {
                                    if (empty($data['is_hide_personal_info']) && !empty($user_profile['sa5_profile_page_url'])) {
                                        write_html('<a href="'.assign_str($user_profile['sa5_profile_page_url']).'" target="_blank" class="iconYH2">Yahoo!</a>');
                                    } else {
                                        write_html('<span class="iconYH2">Yahoo!</span>');
                                    }
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                                if (!empty($user_profile['sa4_id'])) {
                                    if (empty($data['is_hide_personal_info']) && !empty($user_profile['sa4_profile_page_url'])) {
                                        write_html('<a href="'.assign_str($user_profile['sa4_profile_page_url']).'" target="_blank" class="iconGP2">Google</a>');
                                    } else {
                                        write_html('<span class="iconGP2">Google</span>');
                                    }
                                }
                            ?>
                        </td>
                        <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_GDO, $data['original_sns_account_array'])): ?>
                            <td>
                                <?php
                                    if (!empty($user_profile['sa6_id'])) {
                                        if (!empty($user_profile['sa6_profile_page_url'])) {
                                            write_html('<a href="' . assign_str($user_profile['sa6_profile_page_url']) . '" target="_blank" class="iconGdo2">GDO</a>');
                                        } else {
                                            write_html('<span class="iconGdo2">GDO</span>');
                                        }
                                    }
                                ?>
                            </td>
                        <?php endif; ?>
                        <?php if(in_array(SocialAccountService::SOCIAL_MEDIA_LINKEDIN, $data['original_sns_account_array'])): ?>
                            <td>
                                <?php
                                if (!empty($user_profile['sa9_id'])) {
                                    if (empty($data['is_hide_personal_info']) && !empty($user_profile['sa9_profile_page_url'])) {
                                        write_html('<a href="' . assign_str($user_profile['sa9_profile_page_url']) . '" target="_blank" class="iconIN2">LinkedIn</a>');
                                    } else {
                                        write_html('<span class="iconIN2">LinkedIn</span>');
                                    }
                                }
                                ?>
                            </td>
                        <?php endif; ?>
                        <td><?php assign($user_profile['sa1_friend_count'] + $user_profile['sa3_friend_count'] + $user_profile['sa7_friend_count']);?></td>
                        <?php if (!$data['isSocialLikesEmpty']): ?>
                        <?php foreach($data['facebook_accounts'] as $fb_account): ?>
                            <?php if($user_profile['like_id'][$fb_account->social_media_account_id]):?>
                                <td><img src="<?php assign($fb_account->picture_url)?>" width="20" height="20" alt="<?php assign($fb_account->name)?>" title="<?php assign($fb_account->name)?>"></td>
                            <?php else: ?>
                                <td></td>
                            <?php endif; ?>
                            <?php if($user_profile['likes_count'][$fb_account->social_media_account_id]): ?>
                                <td><span class="iconLike"><?php assign($user_profile['likes_count'][$fb_account->social_media_account_id]?:''); ?></span></td>
                            <?php else: ?>
                                <td></td>
                            <?php endif; ?>
                            <?php if($user_profile['comments_count'][$fb_account->social_media_account_id]): ?>
                                <td><span class="iconComment"><?php assign($user_profile['comments_count'][$fb_account->social_media_account_id]?:''); ?></span></td>
                            <?php else: ?>
                                <td></td>
                            <?php endif; ?>
                            
                        <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if (!$data['isTwitterFollowsEmpty']): ?>
                            <?php foreach($data['twitter_accounts'] as $tw_account): ?>
                                <?php if($user_profile['tw_uid'][$tw_account->social_media_account_id]):?>
                                    <td><img src="<?php assign($tw_account->picture_url)?>" width="20" height="20" alt="<?php assign($tw_account->name)?>" title="<?php assign($tw_account->name)?>"></td>
                                <?php else: ?>
                                    <td></td>
                                <?php endif; ?>
                                    <?php if($user_profile['replies_count'][$tw_account->social_media_account_id]):?>
                                    <td><span class="iconReply"><?php assign($user_profile['replies_count'][$tw_account->social_media_account_id]?:''); ?></span></td>
                                <?php else: ?>
                                    <td></td>
                                <?php endif; ?>
                                <?php if($user_profile['retweets_count'][$tw_account->social_media_account_id]):?>
                                    <td><span class="iconRetweet"><?php assign($user_profile['retweets_count'][$tw_account->social_media_account_id]?:''); ?></span></td>
                                <?php else: ?>
                                    <td></td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <td><?php assign($user_profile['last_login_date']) ?></td>
                        <td><?php assign($user_profile['login_count']) ?></td>
                        <?php if($data['page_settings']->privacy_required_sex): ?>
                            <td>
                                <?php if ($user_profile['sex'] == 'm'):?>
                                    <span class = 'iconSexM'>
                                <?php elseif ($user_profile['sex'] == 'f'): ?>
                                    <span class = 'iconSexF'>
                                <?php else: ?>
                                    <span class = 'iconSexN'>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                        <?php if ($data['page_settings']->privacy_required_address): ?>
                            <td><?php assign($user_profile['pref_name']) ?></td>
                        <?php endif; ?>
                        <?php if ($data['page_settings']->privacy_required_birthday): ?>
                            <td><?php assign($user_profile['age']) ?></td>
                        <?php endif; ?>
                        <td><?php assign(!empty($user_profile['cp_entry_count']) ? $user_profile['cp_entry_count'] : 0) ?></td>
                        <td><?php assign(!empty($user_profile['cp_announce_count']) ? $user_profile['cp_announce_count'] : 0) ?></td>
                        <td><?php assign(!empty($user_profile['message_delivered_count']) ? $user_profile['message_delivered_count'] : 0) ?></td>
                        <td><?php assign(!empty($user_profile['message_read_count']) ? $user_profile['message_read_count'] : 0) ?></td>
                        <td><?php assign(!empty($user_profile['message_read_ratio']) ? $user_profile['message_read_ratio'] : '0%') ?></td>
                        <?php if($data['use_profile_questions']): ?>
                            <td><?php assign($user_profile['profile_questionnaire_status']) ?></td>
                        <?php endif; ?>
                        <?php foreach ($data['profile_questions'] as $profile_question):?>
                            <?php if (isset($user_profile['question_'.$profile_question->id])): ?>
                                <?php $answer = $user_profile['question_'.$profile_question->id]?>
                                <td title='<?php assign($answer) ?>'><?php assign(Util::cutTextByWidth($answer, 190)) ?></td>
                            <?php else: ?>
                                <td></td>
                            <?php endif; ?>
                        <?php endforeach;?>

                        <?php foreach ($data['extend_columns'] as $question_relation_id => $column_name):  //TODO ハードコーディング カンコーブランドの追加カラム?>
                             <td><?php assign($user_profile['children_ages'][$question_relation_id]) ?></td>
                        <?php endforeach;?>

                        <?php foreach($data['conversions'] as $conversion): ?>
                            <td><?php assign($user_profile['conversion'.$conversion->id]);?></td>
                        <?php endforeach; ?>

                        <?php foreach ($data['definitions'] as $def): ?>
                            <td><?php
                                $value = $data['user_attributes'][$def->id][$fan_list_user->user_id];
                                assign($value);
                            ?></td>
                        <?php endforeach; ?>
                    </tr>
            <?php endforeach; ?>
            <?php
                $td_count = (count($data['original_sns_account_array']) + 17) + $privacy_required_count + ($data['use_profile_questions'] ? count($data['use_profile_questions']) + 1 : 0 ) + ($data['conversions'] ? $data['conversions']->total() : 0) + ($data['definitions'] ? $data['definitions']->total() : 0) + ($data['duplicateAddressShowType'] == CpCreateSqlService::SHIPPING_ADDRESS_DUPLICATE || ($data['duplicateAddressShowType'] == CpCreateSqlService::SHIPPING_ADDRESS_USER_DUPLICATE && $data['isShowDuplicateAddressCpUserList']) ? 1 : 0) + (count($data['extend_columns']) ? count($data['extend_columns']) : 0);
                if(!$data['isSocialLikesEmpty']) {
                    $td_count += ($data['facebook_accounts'] ? $data['facebook_accounts']->total()*CpCreateSqlService::DISPLAY_3_ITEMS : 0);
                }
                if(!$data['isTwitterFollowsEmpty']) {
                    $td_count += ($data['twitter_accounts'] ? $data['twitter_accounts']->total()*CpCreateSqlService::DISPLAY_3_ITEMS : 0);
                }
            ?>
            <?php for($i = 1; $i <= CpCreateSqlService::DISPLAY_20_ITEMS-count($data['fan_list_users']); $i++): ?>
                <tr>
                    <?php for($td = 1; $td <= $td_count; $td++): ?>
                        <td></td>
                    <?php endfor; ?>
                </tr>
            <?php endfor; ?>
            </tbody>
        <!-- /.itemTable --></table>
    <!-- /.itemTableWrap --></div>
