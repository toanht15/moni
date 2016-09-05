<!-- site base setting -->
<style>
    /* site background */
    body {
        background-color: <?php assign($data['brand']->getColorBackground())?>;
    <?php if($data['brand']->background_img_url):?>
        background-image: url(<?php assign($data['brand']->background_img_url)?>);
    <?php endif;?>
    <?php if($data['brand']->getBackgroundImageRepeatType() == Brand::BACKGROUND_IMAGE_REPEAT_TYPE_NO):?>
        background-repeat: no-repeat;
    <?php elseif($data['brand']->getBackgroundImageRepeatType() == Brand::BACKGROUND_IMAGE_REPEAT_TYPE_X):?>
        background-repeat: repeat-x;
    <?php elseif($data['brand']->getBackgroundImageRepeatType() == Brand::BACKGROUND_IMAGE_REPEAT_TYPE_Y):?>
        background-repeat: repeat-y;
    <?php elseif($data['brand']->getBackgroundImageRepeatType() == Brand::BACKGROUND_IMAGE_REPEAT_TYPE_REPEAT):?>
        background-repeat: repeat;
    <?php endif;?>
    }

    /* panel title color */
    .contBoxMain-cms .postType:before,
    .contBoxMain .postType:before {
        color: <?php assign($data['brand']->getColorMain())?>;
    }

    /* message box for smartphone*/
    <?php if(Util::isSmartPhone()):?>

    .hd2 {
        color: <?php assign($data['brand']->getColorText())?>;
    }
    .pageTop a{
        color: <?php assign($data['brand']->getColorText())?>;
    }
    <?php endif; ?>

    /* site title color */
    .companyName h1 {
        color: <?php assign($data['brand']->getColorText())?>;
    }

    /* nav link color */
    .gnavi li, .gnavi li a {
        color: <?php assign($data['brand']->getColorText())?>;
    }
    body>footer, body>footer a {
        color: <?php assign($data['brand']->getColorText())?>;
    }

    <?php if ($data['brand']->isLimitedBrandPage(BrandInfoContainer::getInstance()->getBrandGlobalSettings())): ?>
    .newLabel {position:absolute; top:24px ; left:3px; z-index:500;}
    .newLabel span {padding:0 6px; font-size:11px; color:#FFF; line-height:11px; vertical-align:middle; text-align:center; border-radius:3px; border:1px solid #FFF; background:rgba(0,0,0,0.25);}
    <?php endif ?>
    
    <?php if($data['can_set_header_tag_setting']):?>
        <?php write_html($data['header_tag_text']);?>
    <?php endif; ?>

</style>
<?php if ($data['is_olympus_header_footer']): ?>
    <?php write_html($this->parseTemplate('OlympusHeader.php', $data)) ?>
<?php elseif ($data['is_whitebelg_header_footer']):?>
    <?php write_html($this->parseTemplate('WhitebelgHeader.php', $data)) ?>
<?php elseif ($data['is_kenken_header_footer']):?>
    <?php write_html($this->parseTemplate('KenkenHeader.php', $data)) ?>
<?php elseif ($data['is_uq_header_footer']):?>
    <?php write_html($this->parseTemplate('UQHeader.php', $data)) ?>
<?php elseif ($data['layout_type'] != StaticHtmlEntries::LAYOUT_FULL): ?>
    <?php if(!$data['isLogin'] && $data['public_flg'] != BrandPageSettingService::STATUS_PUBLIC): ?>
        <section class="privateMode">
            <p class="editLock">ページは現在非公開です<a href="<?php assign(Util::rewriteUrl('admin-settings', 'page_settings_form')) ?>">設定</a></p>
        </section>
    <?php endif; ?>
    <?php if ($data['has_header_option'] || $data['can_show_syn_menu']):?>
        <?php if (!$data['is_promotion'] || Util::isSmartPhone()): ?>
            <header class="<?php assign($data['hide_header_login_button'] ? 'noAccount' : '' ) ?>">
                <?php if (!$data['is_promotion'] && !$data['hide_header_login_button']): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeaderAccountSection')->render($data)) ?>
                <?php endif; ?>

                <section class="company">
                    <div class="companyName">
                        <?php if(!$data['hide_brand_logo']): ?>

                            <p class="logo jsEditAreaWrap">
                                <?php if($data['has_top_option']):?>
                                    <a href="<?php assign(Util::getBaseUrl())?>"><img src="<?php assign($data['brand']->getProfileImage())?>" width="130" height="130" alt=""></a>
                                <?php else:?>
                                    <img src="<?php assign($data['brand']->getProfileImage())?>" width="130" height="130" alt="" style="position: absolute;left: 0;top: 0;">
                                <?php endif;?>

                                <?php if($data['isLoginAdmin']):?>
                                    <span class="editArea">
                                      <a href="#editProfile" class="jsOpenModal"><span>編集する</span></a>
                                    </span>
                                <?php endif;?>
                            </p>
                            <h1 class="jsEditAreaWrap">
                                <?php assign($data['brand']->name) ?>
                                <?php if($data['isLoginAdmin']):?>
                                    <span class="editArea">
                                        <a href="#editProfile" class="jsOpenModal"><span>編集する</span></a>
                                    </span>
                                <?php endif;?>
                            </h1>

                        <?php endif; ?>
                        <?php if (Util::isSmartPhone() && ($data['globalMenus'] || $data['can_show_syn_menu'])):?>
                            <p class="spGnavBtn"><a href="#side-menu" class="syn-notice jsMenuTrigger">MENU</a></p>
                        <?php endif;?>
                        <!-- /.companyName --></div>
                    <?php
                    $service_factory = new aafwServiceFactory();
                    /** BrandGlobalSettingService $brand_global_settings_service */
                    $brand_global_settings_service = $service_factory->create('BrandGlobalSettingService');
                    ?>
                    <?php if($data['brand_info']['is_users_num_visible'] && Util::isBaseUrl() && !$brand_global_settings_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::LP_MODE)->content): ?>
                        <p class="spFanNumber"><?php assign($data['brand_info']['users_num']); ?>人</p>
                    <?php endif; ?>

                    <?php if (!Util::isSmartPhone()): ?>
                        <nav class="gnavi jsEditAreaWrap">
                            <?php if($data['globalMenus']):?>
                                <ul class="jsGnavi">
                                    <?php foreach($data['globalMenus'] as $menu):?>
                                        <li><a href="<?php assign($menu->link)?>"<?php if($menu->is_blank_flg):?> target="_blank"<?php endif;?>><?php assign($menu->name)?></a></li>
                                    <?php endforeach;?>
                                </ul>
                                <a href="#" class="openLink">0</a>
                            <?php elseif($data['display']): ?>
                                <ul><li>サイト内リンクを設定できます</li></ul>
                            <?php endif;?>
                            <?php if($data['isLoginAdmin']):?>
                                <section class="editArea">
                                    <a href="#globalMenus" class="jsOpenModal"><span>編集する</span></a>
                                </section>
                            <?php endif;?>
                            <!-- /.gnavi --></nav>
                    <?php endif ?>
                </section>
            </header>
        <?php endif;?>
        <?php if (Util::isSmartPhone() && ($data['globalMenus'] || $data['can_show_syn_menu'])): ?>
            <?php write_html($this->parseTemplate('SynMenu.php', $data)); ?>
        <?php endif;?>
    <?php endif;?>
<?php endif; ?>
