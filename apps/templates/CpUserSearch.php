    <div class="userListSearch jsSortItemTarget" <?php assign($data['hasSearchCondition'] ? 'style=display:block' : '')?>>
        <ul class="userListSearchCont">
            <?php
                $service_factory = new aafwServiceFactory();
                /** @var BrandPageSettingService $brand_page_setting_service */
                $brand_page_setting_service = $service_factory->create('BrandPageSettingService');
                $page_settings = $brand_page_setting_service->getPageSettingsByBrandId($data['brand_id']);
                /** @var BrandGlobalSettingService $brand_global_setting_service */
                $brand_global_setting_service = $service_factory->create('BrandGlobalSettingService');
                $original_sns_account = $brand_global_setting_service->getBrandGlobalSetting($data['brand_id'], BrandGlobalSettingService::ORIGINAL_SNS_ACCOUNTS);

                /** @var $brand_service BrandService */
                $brand_service = $service_factory->create('BrandService');
                $definitions = null;

                /** @var BrandSocialAccountService $brand_social_account_service */
                $brand_social_account_service = $service_factory->create('BrandSocialAccountService');
                $facebook_accounts = $brand_social_account_service->getSocialAccountsByBrandId($data['brand_id'],SocialApps::PROVIDER_FACEBOOK);
                $twitter_accounts = $brand_social_account_service->getSocialAccountsByBrandId($data['brand_id'], SocialApps::PROVIDER_TWITTER);

            ?>
            <li class="jsAreaToggleWrap" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_RATE] ? "display:none" : "display:list-item")?>>
                評価<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_RATE] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                <?php write_html($this->parseTemplate('SearchMemberRate.php', array(
                    'search_rate' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_RATE]
                ))) ?>
            </li>
            <li class="jsAreaToggleWrap" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_MEMBER_NO] ? "display:none" : "display:list-item")?>>
                会員No<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_MEMBER_NO] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                <?php write_html($this->parseTemplate('SearchMemberNo.php', array(
                    'search_profile_member_no' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_MEMBER_NO]
                ))) ?>
            </li>
            <?php if($data['duplicateAddressShowType'] == CpCreateSqlService::SHIPPING_ADDRESS_USER_DUPLICATE && $data['isShowDuplicateAddressCpUserList']): ?>
                <li class="jsAreaToggleWrap" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_DUPLICATE_ADDRESS] ? "display:none" : "display:list-item")?>>
                    住所重複<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_DUPLICATE_ADDRESS] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                    <?php write_html($this->parseTemplate('SearchDuplicateAddress.php', array(
                        'search_duplicate_address' => $data['search_condition'][CpCreateSqlService::SEARCH_DUPLICATE_ADDRESS],
                        'cp_id' => $data['cp_id'],
                        'duplicate_address_show_type' => $data['duplicateAddressShowType']
                    ))) ?>
                </li>
            <?php endif; ?>
            <li class="jsAreaToggleWrap" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD] ? "display:none" : "display:list-item")?>>
                登録期間<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                <?php write_html($this->parseTemplate('SearchRegisterPeriod.php', array(
                    'search_profile_register_period' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD]
                ))) ?>
            </li>

            <?php foreach(SocialAccountService::$availableSocialAccount as $social_media_id): ?>
                <?php if ($social_media_id == SocialAccountService::SOCIAL_MEDIA_GDO && !($social_media_id == $original_sns_account && $original_sns_account->content == SocialAccountService::SOCIAL_MEDIA_GDO)) continue ?>
                <li class="jsAreaToggleWrap" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.$social_media_id] ? "display:none" : "display:list-item")?>>
                    <span class="<?php assign(SocialAccountService::$socialSmallIcon[$social_media_id]) ?>"><?php assign(SocialAccountService::$socialAccountLabel[$social_media_id]) ?></span>
                    <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.$social_media_id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                    <?php write_html($this->parseTemplate('SearchSocialAccount.php', array(
                        'search_social_account' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.$social_media_id],
                        'social_media_type' => $social_media_id
                    ))) ?>
                </li>
            <?php endforeach; ?>

            <li class="jsAreaToggleWrap" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_SUM] ? "display:none" : "display:list-item")?>>
                合計<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_SUM] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                <?php write_html($this->parseTemplate('SearchSocialAccountSum.php', array(
                    'search_social_sum_count' => $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_SUM],
                ))) ?>
            </li>
            <?php if(!$data['isSocialLikesEmpty']): ?>
                <?php foreach($facebook_accounts as $fb_account): ?>
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
                    <li class="userActionInfo jsAreaToggleWrap" style="<?php assign($data['hasSearchCondition'] & !$has_liked_page ? "display:none" : "display:list-item")?>">
                        <img src="<?php assign($fb_account->picture_url)?>" width="20" height="20" alt="<?php assign($fb_account->name)?>" title="<?php assign($fb_account->name)?>">
                        <a href="javascript:void(0)" class="<?php  assign($has_liked_page ? 'iconBtnSort' : 'btnArrowB1')  ?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchSocialAccountInteractive.php', array(
                            'search_social_account_interactive' => $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id],
                            'social_account_name' => $fb_account->name,
                            'social_app_id' => SocialApps::PROVIDER_FACEBOOK,
                            'social_media_id' => $fb_account->social_media_account_id,
                        ))) ?>
                        <span class="textBalloon1">
                                <span><?php assign($fb_account->name)?>にいいね！</span>
                            <!-- /.textBalloon1 --></span>
                    </li>
                    <li class="userActionInfo jsAreaToggleWrap" style="<?php assign($data['hasSearchCondition'] & !$has_liked_count ? "display:none" : "display:list-item")?>">
                        <span class="iconLike">
                        <a href="javascript:void(0)" class="<?php assign($has_liked_count ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchSocialAccountLikeCount.php', array(
                            'social_account_name' => $fb_account->name,
                            'search_social_account_interactive' => $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id],
                            'social_app_id' => SocialApps::PROVIDER_FACEBOOK,
                            'social_media_id' => $fb_account->social_media_account_id,
                            'search_fb_posts_like_count_type_name' => CpCreateSqlService::$search_sns_action_count[CpCreateSqlService::SEARCH_FB_POSTS_LIKE_COUNT],
                        ))) ?>
                        <span class="textBalloon1">
                                <span><?php assign($fb_account->name)?>の投稿にいいね！</span>
                            <!-- /.textBalloon1 --></span>
                    </li>
                    <li class="userActionInfo jsAreaToggleWrap" style="<?php assign($data['hasSearchCondition'] & !$has_commented_count ? "display:none" : "display:list-item")?>">
                        <span class="iconComment">
                        <a href="javascript:void(0)" class="<?php assign($has_commented_count ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchSocialAccountCommentCount.php', array(
                            'social_account_name' => $fb_account->name,
                            'search_social_account_interactive' => $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_FACEBOOK.'/'.$fb_account->social_media_account_id],
                            'social_app_id' => SocialApps::PROVIDER_FACEBOOK,
                            'social_media_id' => $fb_account->social_media_account_id,
                            'search_fb_posts_comment_count_type_name' => CpCreateSqlService::$search_sns_action_count[CpCreateSqlService::SEARCH_FB_POSTS_COMMENT_COUNT],
                        ))) ?>
                        <span class="textBalloon1">
                                <span><?php assign($fb_account->name)?>の投稿にコメント </span>
                            <!-- /.textBalloon1 --></span>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if(!$data['isTwitterFollowsEmpty']): ?>
                <?php foreach($twitter_accounts as $tw_account): ?>
                    <?php
                        $has_followed_page =  $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id]['search_social_account_interactive/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id.'/'.CpCreateSqlService::LIKED] || 
                                            $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id]['search_social_account_interactive/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id.'/'.CpCreateSqlService::NOT_LIKE];
                        $has_retweeted_count = $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id]['search_social_account_is_retweeted_count/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id.'/'.CpCreateSqlService::LIKED] ||
                                           $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id]['search_social_account_is_retweeted_count/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id.'/'.CpCreateSqlService::NOT_LIKE] ||
                                           $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id]['search_social_account_is_retweeted_count/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id.'/from'] ||
                                           $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id]['search_social_account_is_retweeted_count/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id.'/to'];
                        $has_replied_count =  $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id]['search_social_account_is_replied_count/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id.'/'.CpCreateSqlService::LIKED] ||
                                                $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id]['search_social_account_is_replied_count/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id.'/'.CpCreateSqlService::NOT_LIKE] ||
                                                $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id]['search_social_account_is_replied_count/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id.'/from'] ||
                                                $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id]['search_social_account_is_replied_count/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id.'/to'];

                    ?>
                    <li class="userActionInfo jsAreaToggleWrap" style=<?php assign($data['hasSearchCondition'] && !$has_followed_page ? "display:none" : "display:list-item")?>>
                        <img src="<?php assign($tw_account->picture_url)?>" width="20" height="20" alt="<?php assign($tw_account->name)?>">
                        <a href="javascript:void(0)" class="<?php assign($has_followed_page ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchSocialAccountInteractive.php', array(
                            'search_social_account_interactive' => $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id],
                            'social_account_name' => $tw_account->name,
                            'social_app_id' => SocialApps::PROVIDER_TWITTER,
                            'social_media_id' => $tw_account->social_media_account_id,
                        ))) ?>
                        <span class="textBalloon1">
                                <span><?php assign($tw_account->name)?>のツイートをリプライ</span>
                            <!-- /.textBalloon1 --></span>
                    </li>
                    <li class="userActionInfo jsAreaToggleWrap" style="<?php assign($data['hasSearchCondition'] && !$has_retweeted_count ? "display:none" : "display:list-item")?>">
                        <span class="iconRetweet">
                        <a href="javascript:void(0)" class="<?php assign($has_retweeted_count ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchSocialAccountRetweetCount.php', array(
                            'social_account_name' => $tw_account->name,
                            'search_social_account_interactive' => $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id],
                            'social_app_id' => SocialApps::PROVIDER_TWITTER,
                            'social_media_id' => $tw_account->social_media_account_id,
                            'search_tw_tweet_retweet_count_type_name' => CpCreateSqlService::$search_sns_action_count[CpCreateSqlService::SEARCH_TW_TWEET_RETWEET_COUNT],
                        ))) ?>
                        <span class="textBalloon1">
                                <span><?php assign($tw_account->name)?>のツイートをリツイート</span>
                            <!-- /.textBalloon1 --></span>
                    </li>
                    <li class="userActionInfo jsAreaToggleWrap" style="<?php assign($data['hasSearchCondition'] && !$has_replied_count ? "display:none" : "display:list-item")?>">
                        <span class="iconReply">
                        <a href="javascript:void(0)" class="<?php assign($has_replied_count ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchSocialAccountReplyCount.php', array(
                            'social_account_name' => $tw_account->name,
                            'search_social_account_interactive' => $data['search_condition'][CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'.$tw_account->social_media_account_id],
                            'social_app_id' => SocialApps::PROVIDER_TWITTER,
                            'social_media_id' => $tw_account->social_media_account_id,
                            'search_tw_tweet_reply_count_type_name' => CpCreateSqlService::$search_sns_action_count[CpCreateSqlService::SEARCH_TW_TWEET_REPLY_COUNT],
                        ))) ?>
                        <span class="textBalloon1">
                                <span><?php assign($tw_account->name)?>のツイートをリプライ </span>
                            <!-- /.textBalloon1 --></span>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
            <li class="jsAreaToggleWrap" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_LAST_LOGIN] ? "display:none" : "display:list-item")?>>
                最終ログイン<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_LAST_LOGIN] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                <?php write_html($this->parseTemplate('SearchLastLogin.php', array(
                    'search_profile_last_login' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_LAST_LOGIN]
                ))) ?>
            </li>
            <li class="jsAreaToggleWrap" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_LOGIN_COUNT] ? "display:none" : "display:list-item")?>>
                ログイン回数<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_LOGIN_COUNT] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                <?php write_html($this->parseTemplate('SearchCountInfo.php', array(
                    'search_profile_login_count' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_LOGIN_COUNT]
                ))) ?>
            </li>
            <?php if($page_settings->privacy_required_sex): ?>
                <li class="jsAreaToggleWrap" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SEX] ? "display:none" : "display:list-item")?>>
                    性別<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SEX] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                    <?php write_html($this->parseTemplate('SearchSex.php', array(
                        'search_profile_sex' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_SEX]
                    ))) ?>
                </li>
            <?php endif; ?>
            <?php if($page_settings->privacy_required_address): ?>
                <li class="jsAreaToggleWrap" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_ADDRESS] ? "display:none" : "display:list-item")?>>
                    都道府県<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_ADDRESS] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                    <?php write_html($this->parseTemplate('SearchAddress.php', array(
                        'search_profile_address' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_ADDRESS]
                    ))) ?>
                </li>
            <?php endif; ?>
            <?php if($page_settings->privacy_required_birthday): ?>
                <li class="jsAreaToggleWrap" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_AGE] ? "display:none" : "display:list-item")?>>
                    年齢<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_AGE] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                    <?php write_html($this->parseTemplate('SearchAge.php', array(
                        'search_profile_age' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_AGE]
                    ))) ?>
                </li>
            <?php endif; ?>
            <li class="jsAreaToggleWrap" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_CP_ENTRY_COUNT] ? "display:none" : "display:list-item")?>>
                キャンペーン参加回数<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_CP_ENTRY_COUNT] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                <?php write_html($this->parseTemplate('SearchCountInfo.php', array(
                    'search_type' => CpCreateSqlService::SEARCH_CP_ENTRY_COUNT,
                    'search_type_name' => 'search_profile_entry_count',
                    'search_condition' => $data['search_condition'][CpCreateSqlService::SEARCH_CP_ENTRY_COUNT]
                ))) ?>
            </li>
            <li class="jsAreaToggleWrap" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_CP_ANNOUNCE_COUNT] ? "display:none" : "display:list-item")?>>
                キャンペーン当選回数<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_CP_ANNOUNCE_COUNT] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                <?php write_html($this->parseTemplate('SearchCountInfo.php', array(
                    'search_type' => CpCreateSqlService::SEARCH_CP_ANNOUNCE_COUNT,
                    'search_type_name' => 'search_cp_announce_count',
                    'search_condition' => $data['search_condition'][CpCreateSqlService::SEARCH_CP_ANNOUNCE_COUNT]
                ))) ?>
            </li>
            <li class="jsAreaToggleWrap" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_MESSAGE_DELIVERED_COUNT] ? "display:none" : "display:list-item")?>>
                メッセージ受信数<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_MESSAGE_DELIVERED_COUNT] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                <?php write_html($this->parseTemplate('SearchCountInfo.php', array(
                    'search_type' => CpCreateSqlService::SEARCH_MESSAGE_DELIVERED_COUNT,
                    'search_type_name' => 'search_message_delivered_count',
                    'search_condition' => $data['search_condition'][CpCreateSqlService::SEARCH_MESSAGE_DELIVERED_COUNT]
                ))) ?>
            </li>
            <li class="jsAreaToggleWrap" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_MESSAGE_READ_COUNT] ? "display:none" : "display:list-item")?>>
                メッセージ開封数<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_MESSAGE_READ_COUNT] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                <?php write_html($this->parseTemplate('SearchCountInfo.php', array(
                    'search_type' => CpCreateSqlService::SEARCH_MESSAGE_READ_COUNT,
                    'search_type_name' => 'search_message_read_count',
                    'search_condition' => $data['search_condition'][CpCreateSqlService::SEARCH_MESSAGE_READ_COUNT]
                ))) ?>
            </li>
            <li class="jsAreaToggleWrap" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_MESSAGE_READ_RATIO] ? "display:none" : "display:list-item")?>>
                メッセージ閲覧率<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_MESSAGE_READ_RATIO] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                <?php write_html($this->parseTemplate('SearchMessageReadRatio.php', array(
                    'search_condition' => $data['search_condition'][CpCreateSqlService::SEARCH_MESSAGE_READ_RATIO]
                ))) ?>
            </li>

            <? // 参加時アンケートに関する絞り込み ?>
            <?php
                /** @var CpQuestionnaireService $profile_questionnaire_service */
                $profile_questionnaire_service = $service_factory->create('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);
                $profile_question_relations = $profile_questionnaire_service->getPublicProfileQuestionRelationByBrandId($data['brand_id']);
                $use_profile_questions = $profile_questionnaire_service->useProfileQuestion($profile_question_relations);
            ?>
            <?php if($use_profile_questions): ?>
                <li class="jsAreaToggleWrap" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE_STATUS] ? "display:none" : "display:list-item")?>>
                    アンケート<a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE_STATUS] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                    <?php write_html($this->parseTemplate('SearchQuestionnaireStatus.php', array(
                        'search_questionnaire_status' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE_STATUS]
                    ))) ?>
                </li>
            <?php endif; ?>
            <?php foreach ($use_profile_questions as $profile_relation):?>
                <?php $profile_question = $profile_questionnaire_service->getQuestionById($profile_relation->question_id); ?>
                <li class="jsAreaToggleWrap" title="<?php assign('Q'.$profile_relation->number.'.'.$profile_question->question) ?>" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE.'/'.$profile_relation->id] ? "display:none" : "display:list-item")?>>
                    <?php assign(Util::cutTextByWidth('Q'.$profile_relation->number.'.'.$profile_question->question, 190))?>
                    <?php if($profile_question->type_id != QuestionTypeService::FREE_ANSWER_TYPE) {
                        $template_name = 'SearchChoiceQuestionnaire.php';
                    } else {
                        $template_name = 'SearchFreeAnswerQuestionnaire.php';
                    }?>
                    <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE.'/'.$profile_relation->id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                    <?php write_html($this->parseTemplate($template_name, array(
                        'search_questionnaire'      => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE.'/'.$profile_relation->id],
                        'question_id'               => $profile_question->id,
                        'relation_id'               => $profile_relation->id,
                        'search_questionnaire_type' => CpQuestionnaireService::TYPE_PROFILE_QUESTION
                    ))) ?>
                </li>
            <?php endforeach;?>

            <? // コンバージョンの絞り込み ?>
            <?php
                $conversion_service = $service_factory->create('ConversionService');
                $conversions = $conversion_service->getConversionsByBrandId($data['brand_id']);
            ?>
            <?php foreach ($conversions as $conversion): ?>
                <li class="jsAreaToggleWrap" title="<?php assign($conversion->name) ; ?>" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_CONVERSION.'/'.$conversion->id] ? "display:none" : "display:list-item")?>>
                    <?php assign(Util::cutTextByWidth($conversion->name, 190)); ?>
                    <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_CONVERSION.'/'.$conversion->id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                    <?php write_html($this->parseTemplate('SearchConversions.php', array(
                        'search_profile_conversion' => $data['search_condition'][CpCreateSqlService::SEARCH_PROFILE_CONVERSION.'/'.$conversion->id],
                        'conversion'         => $conversion
                    ))) ?>
                </li>
            <?php endforeach; ?>

            <?php
                $definitions = $brand_service->getCustomAttributeDefinitions($data['brand_id']);
                foreach ($definitions as $def):
            ?>
                <?php if($def->attribute_type == BrandUserAttributeDefinitions::ATTRIBUTE_TYPE_SET):?>
                    <li class="jsAreaToggleWrap" title="<?php assign($def->attribute_name); ?>" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_IMPORT_VALUE.'/'.$def->id] ? "display:none" : "display:list-item")?>>
                        <?php assign(Util::cutTextByWidth($def->attribute_name, 190)); ?>
                        <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_IMPORT_VALUE.'/'.$def->id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchImportValue.php', array(
                            'search_import_value' => $data['search_condition'][CpCreateSqlService::SEARCH_IMPORT_VALUE.'/'.$def->id],
                            'definition'          => $def
                        ))) ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>

            <? // 参加状況に関する絞り込み ?>
            <?php $item_no = 1; ?>
            <?php
                /** @var CpFlowService $cp_flow_service */
                $cp_flow_service = $service_factory->create('CpFlowService');
                /** @var CpMessageDeliveryService $delivery_service */
                $delivery_service = $service_factory->create('CpMessageDeliveryService');
                $cp_action_groups = $cp_flow_service->getCpActionGroupsByCpId($data['cp_id']);
            ?>
            <?php foreach($cp_action_groups as $group): ?>
                <?php $actions = $cp_flow_service->getCpActionsByCpActionGroupId($group->id); ?>

                <?php if ($actions) $actions = $actions->toArray(); ?>

                <?php if ($data['show_sent_time'] && $actions[0]->type != CpAction::TYPE_ANNOUNCE_DELIVERY): ?>
                    <?php $target_count = $delivery_service->getTargetsCountByActionId($actions[0]->id); ?>
                    <?php if ($target_count): ?>
                        <li class="jsAreaToggleWrap" title="<?php assign($group->getStepName()) ?>送信日時" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_DELIVERY_TIME.'/'.$actions[0]->id] ? "display:none" : "display:list-item")?>>
                            <?php assign($group->getStepName()) ?>送信日時
                            <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_DELIVERY_TIME.'/'.$actions[0]->id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                            <?php write_html($this->parseTemplate('SearchDeliveryTime.php', array(
                                'search_delivery_time' => $data['search_condition'][CpCreateSqlService::SEARCH_DELIVERY_TIME.'/'.$actions[0]->id],
                                'action'                       => $actions[0]
                            ))) ?>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php foreach($actions as $action): ?>
                    <?php $cp_action_data = $action->getCpActionData(); ?>
                    <li class="jsAreaToggleWrap" title="<?php assign($item_no . '.' . $cp_action_data->title) ?>" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION.'/'.$action->id] ? "display:none" : "display:list-item")?>>
                        <?php assign(Util::cutTextByWidth($item_no . '.' . $cp_action_data->title, 190)) ?>
                        <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION.'/'.$action->id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchParticipateCondition.php', array(
                            'search_participate_condition' => $data['search_condition'][CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION.'/'.$action->id],
                            'action' => $action
                        ))) ?>
                    </li>
                    <?php $item_no += 1; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>

            <? // アンケートに関する絞り込み ?>
            <?php $action_no = 0; ?>
            <?php foreach($data['cp_actions'] as $action): ?>
                <?php $action_no += 1; ?>
                <?php if($action->type === CpAction::TYPE_QUESTIONNAIRE): ?>
                    <?php
                        if (!$cp_questionnaire_service) $cp_questionnaire_service = $service_factory->create('CpQuestionnaireService');
                        $questionnaire_action = $cp_questionnaire_service->getCpQuestionnaireAction($action->id);
                        $relations = $cp_questionnaire_service->getRelationsByQuestionnaireActionId($questionnaire_action->id);
                    ?>
                    <?php foreach($relations as $relation): ?>
                        <?php $question = $cp_questionnaire_service->getQuestionById($relation->question_id);?>
                        <li class="jsAreaToggleWrap" title="<?php assign($action_no.'-Q'.$relation->number.'.'.$question->question); ?>" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_QUESTIONNAIRE.'/'.$relation->id] ? "display:none" : "display:list-item")?>>
                            <?php assign(Util::cutTextByWidth('Q'.$relation->number.'.'.$question->question, 190)); ?>

                            <?php if(QuestionTypeService::isChoiceQuestion($question->type_id)){
                                $q_temp_name = 'SearchChoiceQuestionnaire.php';
                            } else {
                                $q_temp_name = 'SearchFreeAnswerQuestionnaire.php';
                            }?>

                            <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_QUESTIONNAIRE.'/'.$relation->id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                            <?php write_html($this->parseTemplate($q_temp_name, array(
                                'search_questionnaire'      => $data['search_condition'][CpCreateSqlService::SEARCH_QUESTIONNAIRE.'/'.$relation->id],
                                'question_id'               => $question->id,
                                'relation_id'               => $relation->id,
                                'search_questionnaire_type' => CpQuestionnaireService::TYPE_CP_QUESTION
                            ))) ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if ($action->type === CpAction::TYPE_PHOTO): ?>
                    <?php
                        if (!$cp_photo_action_service) $cp_photo_action_service = $service_factory->create('CpPhotoActionService');
                        $cp_photo_action = $cp_photo_action_service->getCpPhotoAction($action->id);
                    ?>
                    <?php if ($cp_photo_action->fb_share_required || $cp_photo_action->tw_share_required): ?>
                        <li class="jsAreaToggleWrap" title="<?php assign($action_no.'-シェアSNS'); ?>" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_PHOTO_SHARE_SNS . '/' . $action->id] ? "display:none" : "display:list-item")?>>
                            <?php assign($action_no.'-シェアSNS'); ?>
                            <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PHOTO_SHARE_SNS . '/' . $action->id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                            <?php write_html($this->parseTemplate('SearchPhotoShareSns.php', array(
                                'search_photo_share_sns' => $data['search_condition'][CpCreateSqlService::SEARCH_PHOTO_SHARE_SNS . '/' . $action->id],
                                'action_id'              => $action->id
                            ))) ?>
                        </li>
                        <li class="jsAreaToggleWrap" title="<?php assign($action_no.'-シェアテキスト'); ?>" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_PHOTO_SHARE_TEXT . '/' . $action->id] ? "display:none" : "display:list-item")?>>
                            <?php assign($action_no.'-シェアテキスト'); ?>
                            <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PHOTO_SHARE_TEXT . '/' . $action->id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                            <?php write_html($this->parseTemplate('SearchPhotoShareText.php', array(
                                'search_photo_share_text' => $data['search_condition'][CpCreateSqlService::SEARCH_PHOTO_SHARE_TEXT . '/' . $action->id],
                                'action_id'               => $action->id
                            ))) ?>
                        </li>
                    <?php endif; ?>
                    <?php if ($cp_photo_action->panel_hidden_flg): ?>
                        <li class="jsAreaToggleWrap" title="<?php assign($action_no.'-検閲'); ?>" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_PHOTO_APPROVAL_STATUS . '/' . $action->id] ? "display:none" : "display:list-item")?>>
                            <?php assign($action_no.'-検閲'); ?>
                            <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_PHOTO_APPROVAL_STATUS . '/' . $action->id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                            <?php write_html($this->parseTemplate('SearchPhotoApprovalStatus.php', array(
                                'search_photo_approval_status' => $data['search_condition'][CpCreateSqlService::SEARCH_PHOTO_APPROVAL_STATUS . '/' . $action->id],
                                'action_id'                    => $action->id
                            ))) ?>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($action->type === CpAction::TYPE_INSTAGRAM_HASHTAG): ?>
                    <?php
                    if (!$cp_instagram_hashtag_action_service) $cp_instagram_hashtag_action_service = $service_factory->create('CpInstagramHashtagActionService');
                    $cp_instagram_hashtag_action = $cp_instagram_hashtag_action_service->getCpInstagramHashtagActionByCpActionId($action->id);
                    ?>
                    <li class="jsAreaToggleWrap" title="<?php assign($action_no.'-ユーザネーム重複'); ?>" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION . '/' . $action->id] ? "display:none" : "display:list-item")?>>
                        <?php assign($action_no.'-ユーザネーム重複'); ?>
                        <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION . '/' . $action->id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchInstagramHashtagDuplicate.php', array(
                            'search_instagram_hashtag_duplicate' => $data['search_condition'][CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_DUPLICATION . '/' . $action->id],
                            'action_id'                            => $action->id
                        ))) ?>
                    </li>
                    <li class="jsAreaToggleWrap" title="<?php assign($action_no.'-登録投稿順序'); ?>" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME . '/' . $action->id] ? "display:none" : "display:list-item")?>>
                        <?php assign($action_no.'-登録投稿順序'); ?>
                        <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME . '/' . $action->id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchInstagramHashtagReverse.php', array(
                            'search_instagram_hashtag_reverse' => $data['search_condition'][CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_REVERSE_POST_TIME . '/' . $action->id],
                            'action_id'                                  => $action->id
                        ))) ?>
                    </li>
                    <?php if ($cp_instagram_hashtag_action->approval_flg): ?>
                        <li class="jsAreaToggleWrap" title="<?php assign($action_no.'-検閲'); ?>" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS . '/' . $action->id] ? "display:none" : "display:list-item")?>>
                            <?php assign($action_no.'-検閲'); ?>
                            <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS . '/' . $action->id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                            <?php write_html($this->parseTemplate('SearchInstagramHashtagApprovalStatus.php', array(
                                'search_instagram_hashtag_approval_status' => $data['search_condition'][CpCreateSqlService::SEARCH_INSTAGRAM_HASHTAG_APPROVAL_STATUS . '/' . $action->id],
                                'action_id'                                => $action->id
                            ))) ?>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if($action->type == CpAction::TYPE_SHARE): ?>
                    <li class="jsAreaToggleWrap" title="シェア状況" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_SHARE_TYPE] ? "display:none" : "display:list-item")?>>
                        シェア状況
                        <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_SHARE_TYPE] ? 'iconBtnSort' : 'btnArrowB1'); ?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchShareType.php', array(
                            'search_share' => $data['search_condition'][CpCreateSqlService::SEARCH_SHARE_TYPE]
                        ))) ?>
                    </li>
                    <li class="jsAreaToggleWrap" title="シェアコメント" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_SHARE_TEXT] ? "display:none" : "display:list-item")?>>
                        シェアコメント
                        <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_SHARE_TEXT] ? 'iconBtnSort' : 'btnArrowB1'); ?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchShareText.php', array(
                            'search_share' => $data['search_condition'][CpCreateSqlService::SEARCH_SHARE_TEXT]
                        ))) ?>
                    </li>
                <?php endif; ?>
                <?php if ($action->type == CpAction::TYPE_FACEBOOK_LIKE): ?>
                    <li class="jsAreaToggleWrap" title="<?php assign($action_no) ?>-Facebookいいね！状況" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_FB_LIKE_TYPE . '/' . $action->id] ? "display:none" : "display:list-item")?>>
                        <?php assign($action_no) ?>-Facebookいいね！状況
                        <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_FB_LIKE_TYPE . '/' . $action->id] ? 'iconBtnSort' : 'btnArrowB1'); ?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchFbLikeType.php', array(
                            'search_fb_like' => $data['search_condition'][CpCreateSqlService::SEARCH_FB_LIKE_TYPE . '/' . $action->id],
                            'action_id' => $action->id))) ?>
                    </li>
                <?php endif ?>
                <?php if ($action->type == CpAction::TYPE_TWITTER_FOLLOW): ?>
                    <li class="jsAreaToggleWrap" title="<?php assign($action_no) ?>-Twitterフォロー状況" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_TW_FOLLOW_TYPE . '/' . $action->id] ? "display:none" : "display:list-item")?>>
                        <?php assign($action_no) ?>-Twitterフォロー状況
                        <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_TW_FOLLOW_TYPE . '/' . $action->id] ? 'iconBtnSort' : 'btnArrowB1'); ?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchTwFollowType.php', array(
                            'search_tw_follow' => $data['search_condition'][CpCreateSqlService::SEARCH_TW_FOLLOW_TYPE . '/' . $action->id],
                            'action_id' => $action->id))) ?>
                    </li>
                <?php endif ?>
                <?php if ($action->type == CpAction::TYPE_TWEET): ?>
                    <li class="jsAreaToggleWrap" title="<?php assign($action_no) ?>-ツイート状況" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_TWEET_TYPE . '/' . $action->id] ? "display:none" : "display:list-item")?>>
                        <?php assign($action_no) ?>-ツイート状況
                        <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_TWEET_TYPE . '/' . $action->id] ? 'iconBtnSort' : 'btnArrowB1'); ?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchTweetType.php', array(
                            'search_tweet' => $data['search_condition'][CpCreateSqlService::SEARCH_TWEET_TYPE . '/' . $action->id],
                            'action_id' => $action->id,
                            'tweet_types' => TweetMessage::$tweet_statuses
                        ))) ?>
                    </li>
                <?php endif; ?>
                <?php if($action->type == CpAction::TYPE_YOUTUBE_CHANNEL): ?>
                    <li class="jsAreaToggleWrap" title="登録状況" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION . '/' . $action->id] ? "display:none" : "display:list-item")?>>
                        <?php assign($action_no.'-登録状況'); ?>
                        <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION . '/' . $action->id] ? 'iconBtnSort' : 'btnArrowB1'); ?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchYoutubeChannelSubscription.php', array(
                            'search_ytch_subscription' => $data['search_condition'][CpCreateSqlService::SEARCH_YOUTUBE_CHANNEL_SUBSCRIPTION . '/' . $action->id],
                            'action_id' => $action->id
                        ))); ?>
                    </li>
                <?php endif; ?>
                <?php if ($action->type === CpAction::TYPE_POPULAR_VOTE): ?>
                    <?php
                    if (!$cp_popular_vote_action_service) $cp_popular_vote_action_service = $service_factory->create('CpPopularVoteActionService');
                    $cp_popular_vote_action = $cp_popular_vote_action_service->getCpPopularVoteActionByCpActionId($action->id);
                    ?>
                    <li class="jsAreaToggleWrap" title="<?php assign($action_no.'-投票'); ?>" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_POPULAR_VOTE_CANDIDATE . '/' . $action->id] ? "display:none" : "display:list-item")?>>
                        <?php assign($action_no.'-投票'); ?>
                        <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_POPULAR_VOTE_CANDIDATE . '/' . $action->id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                        <?php write_html($this->parseTemplate('SearchPopularVoteCandidate.php', array(
                            'cp_popular_vote_candidates' => $cp_popular_vote_action->getCpPopularVoteCandidates(array('del_flg' => 0)),
                            'search_popular_vote_candidate' => $data['search_condition'][CpCreateSqlService::SEARCH_POPULAR_VOTE_CANDIDATE . '/' . $action->id],
                            'action_id'               => $action->id
                        ))) ?>
                    </li>
                    <?php if ($cp_popular_vote_action->fb_share_required || $cp_popular_vote_action->tw_share_required): ?>
                        <li class="jsAreaToggleWrap" title="<?php assign($action_no.'-シェアSNS'); ?>" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_POPULAR_VOTE_SHARE_SNS . '/' . $action->id] ? "display:none" : "display:list-item")?>>
                            <?php assign($action_no.'-シェアSNS'); ?>
                            <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_POPULAR_VOTE_SHARE_SNS . '/' . $action->id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                            <?php write_html($this->parseTemplate('SearchPopularVoteShareSns.php', array(
                                'search_popular_vote_share_sns' => $data['search_condition'][CpCreateSqlService::SEARCH_POPULAR_VOTE_SHARE_SNS . '/' . $action->id],
                                'action_id'              => $action->id
                            ))) ?>
                        </li>
                        <li class="jsAreaToggleWrap" title="<?php assign($action_no.'-シェアされた投票理由'); ?>" style=<?php assign($data['hasSearchCondition'] && !$data['search_condition'][CpCreateSqlService::SEARCH_POPULAR_VOTE_SHARE_TEXT . '/' . $action->id] ? "display:none" : "display:list-item")?>>
                            <?php assign($action_no.'-シェアされた投票理由'); ?>
                            <a href="javascript:void(0)" class="<?php assign($data['search_condition'][CpCreateSqlService::SEARCH_POPULAR_VOTE_SHARE_TEXT . '/' . $action->id] ? 'iconBtnSort' : 'btnArrowB1')?> jsAreaToggle">絞り込む</a>
                            <?php write_html($this->parseTemplate('SearchPopularVoteShareText.php', array(
                                'search_popular_vote_share_text' => $data['search_condition'][CpCreateSqlService::SEARCH_POPULAR_VOTE_SHARE_TEXT . '/' . $action->id],
                                'action_id'               => $action->id
                            ))) ?>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>

        </ul>
        <!-- /userListSearch --></div>
