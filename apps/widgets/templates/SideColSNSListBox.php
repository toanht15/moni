<?php if($data['brand_social_accounts']):?>
    <section class="contBoxSide-snsList jsEditAreaWrap">
        <?php if($data['brand']->id == '219'): ?>
            <h1>店舗情報一覧</h1>
        <?php else: ?>
            <h1>公式SNSアカウント</h1>
        <?php endif; ?>
        <ul class="socialPanelKinds" <?php if($data['brand']->id == '219'): ?> style="height:250px; overflow-y:scroll;" <?php endif; ?> >
            <?php foreach($data['brand_social_accounts'] as $brandSocialAccount): ?>
                <?php if (!$brandSocialAccount->social_app_id): ?>
                    <!--Rss stream-->
                    <li data-rssid="<?php assign($brandSocialAccount->id); ?>">
                        <a href="<?php assign($brandSocialAccount->link); ?>" target="_blank">
                            <span class="snsIcon"><img src="<?php assign($this->setVersion('/img/sns/iconSnsRss1.png')); ?>" alt="" width="22" ></span>
                            <span class="snsTitle"><?php assign($brandSocialAccount->title); ?></span>
                        </a>
                    </li>
                <?php elseif($brandSocialAccount->social_app_id == SocialApps::PROVIDER_FACEBOOK): ?>
                    <!-- Facebook stream -->
                    <li data-brand-social-account-id="<?php assign($brandSocialAccount->id); ?>">
                        <a href="<?php assign($brandSocialAccount->page_link); ?>" <?php if($brandSocialAccount->target_blank): ?>target="_blank"<?php endif; ?>>
                            <span class="snsIcon"><img src="<?php assign($this->setVersion('/img/sns/iconSnsFB1.png')); ?>" alt="" width="22" ></span>
                            <span class="snsTitle"><?php assign(json_decode($brandSocialAccount->store)->name); ?></span>
                        </a>
                    </li>
                <?php elseif($brandSocialAccount->social_app_id == SocialApps::PROVIDER_INSTAGRAM):?>
                    <li data-brand-social-account-id="<?php assign($brandSocialAccount->id)?>">
                        <a href="<?php assign($brandSocialAccount->page_link); ?>" <?php if ($brandSocialAccount->target_blank): ?>target="_blank"<?php endif; ?>>
                            <span class="snsIcon"><img src="<?php assign($this->setVersion('/img/sns/iconSnsIG1.png'))?>" alt="" width="22" ></span>
                            <span class="snsTitle"><?php assign($brandSocialAccount->screen_name)?></span>
                        </a>
                    </li>
                <?php elseif($brandSocialAccount->social_app_id == SocialApps::PROVIDER_TWITTER):?>
                    <!-- Twitter stream -->
                    <li data-brand-social-account-id="<?php assign($brandSocialAccount->id); ?>">
                        <a href="<?php assign($brandSocialAccount->page_link); ?>" <?php if($brandSocialAccount->target_blank): ?>target="_blank"<?php endif; ?>>
                            <span class="snsIcon"><img src="<?php assign($this->setVersion('/img/sns/iconSnsTW1.png')); ?>" alt="" width="22" ></span>
                            <span class="snsTitle">@<?php assign(json_decode($brandSocialAccount->store)->screen_name); ?></span>
                        </a>
                    </li>
                <?php elseif($brandSocialAccount->social_app_id == SocialApps::PROVIDER_GOOGLE): ?>
                    <!-- Youtube stream -->
                    <li data-brand-social-account-id="<?php assign($brandSocialAccount->id); ?>">
                        <a href="<?php assign($brandSocialAccount->page_link); ?>" <?php if($brandSocialAccount->target_blank): ?>target="_blank"<?php endif; ?>>
                            <span class="snsIcon"><img src="<?php assign($this->setVersion('/img/sns/iconSnsYT1.png')); ?>" alt="" width="22" ></span>
                            <span class="snsTitle"><?php assign(json_decode($brandSocialAccount->store)->name); ?></span>
                        </a>
                    </li>
                <?php endif;?>
            <?php endforeach;?>
        </ul>
        <!-- /.contBoxSide--></section>
<?php endif;?>