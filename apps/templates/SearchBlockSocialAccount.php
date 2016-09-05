<div class="customaudienceRefinement jsModuleContWrap">
    <div class="categoryLabel jsModuleContTile close">
        <p>SNS連携</p>
        <!-- /.categoryLabel --></div>
    <div class="refinementWrap jsModuleContTarget close">

        <div class="customaudienceSnsConnect">
            <ul class="itemLabel">
                <li>/</li>
                <li>連携済</li>
                <li>未連携</li>
                <li>友達数</li>
                <!-- /.itemLabel --></ul>
            <ul class="snsAccount jsSearchInputBlock">
                <?php
                $service_factory = new aafwServiceFactory();
                /** @var BrandGlobalSettingService $brand_global_setting_service */
                $brand_global_setting_service = $service_factory->create('BrandGlobalSettingService');
                $original_sns_account = $brand_global_setting_service->getBrandGlobalSetting($data['brand_id'], BrandGlobalSettingService::ORIGINAL_SNS_ACCOUNTS);
                ?>
                <?php foreach(SocialAccountService::$availableSocialAccount as $social_id): ?>
                    <?php if ($social_id == SocialAccountService::SOCIAL_MEDIA_GDO && !($social_id == $original_sns_account && $original_sns_account->content == SocialAccountService::SOCIAL_MEDIA_GDO)) continue ?>
                    <?php $is_link = 'search_social_account/' . $social_id . '/' . CpCreateSqlService::LINK_SNS;?>
                    <?php $not_link = 'search_social_account/' . $social_id . '/' . CpCreateSqlService::NOT_LINK_SNS;?>
                    <li class="account">
                        <dl>
                            <form>
                                <?php $search_type = CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.$social_id ?>
                                <?php write_html($this->formHidden("search_type", $search_type)) ?>
                                <dt><span class="<?php assign(SocialAccountService::$socialSmallIcon[$social_id]) ?>"><?php assign(SocialAccountService::$socialAccountLabel[$social_id]) ?></span></dt>
                                <dd><input type="checkbox" name="<?php assign($is_link . '/' . $data['search_no']) ?>" value="1" <?php $data[$search_type][$is_link] ? assign("checked") : false ?> class="connect_social_class"></dd>
                                <dd><input type="checkbox" name="<?php assign($not_link . '/' . $data['search_no']) ?>" value="1" <?php $data[$search_type][$not_link] ? assign("checked") : false ?>></dd>

                                <?php if (in_array($social_id, SocialAccountService::$socialHasFriendCount)): ?>
                                    <dd>
                                        <?php write_html($this->formText(
                                            'search_friend_count_from/' . $social_id,
                                            $data[$search_type]['search_friend_count_from/'.$social_id],
                                            array('class'=>'inputNum', 'disabled' => 'disabled')
                                        )); ?>
                                        <span class="dash">〜</span>
                                        <?php write_html($this->formText(
                                            'search_friend_count_to/' . $social_id,
                                            $data[$search_type]['search_friend_count_to/'.$social_id],
                                            array('class'=>'inputNum', 'disabled' => 'disabled')
                                        )); ?>
                                    </dd>
                                <?php else: ?>
                                    <dd>-</dd>
                                <?php endif; ?>
                            </form>
                        </dl>
                    </li>
                <?php endforeach; ?>
                <!-- /.snsAccount --></ul>
            <!-- /.customaudienceSnsConnect --></div>
        <div class="otherItem">
            <div class="jsSearchInputBlock">
                <p class="settingLabel">SNS友達・フォロワー数の合計</p>
                <form>
                    <?php write_html($this->formHidden("search_type", CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_SUM)) ?>
                    <?php $data["search_type"] = CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_SUM;
                    $data["unit_label"] = "人";
                    ?>
                    <?php write_html($this->parseTemplate("SearchRangeInputNum.php", $data)) ?>
                </form>
            </div>
            <!-- /.otherItem --></div>
        <!-- /.refinementWrap --></div>
    <!-- /.customaudiencRefinement --></div>