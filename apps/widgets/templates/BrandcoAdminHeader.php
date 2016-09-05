<header class="editHeadWrap">
    <section class="editHead">
        <div class="wrap">
            <h1><a href="<?php write_html(Util::rewriteUrl(null, null)) ?>"><img src="<?php assign($this->setVersion('/img/base/imgLogo_w.png'))?>" width="108" height="18" alt="モニプラ"></a></h1>
            <nav class="editMenu">
                <ul>
                    <?php if($data['login_info']['brand']->hasOption(BrandOptions::OPTION_CP, BrandInfoContainer::getInstance()->getBrandOptions())):?>
                        <li class="actionCampaign">
                            <a href="<?php write_html(Util::rewriteUrl('admin-cp','public_cps', array(), array('type' => Cp::TYPE_CAMPAIGN))) ?>">キャンペーン</a>
                            <ul class="editMenuInner">
                                <li><a href="<?php assign(Util::rewriteUrl('admin-cp','public_cps', array(), array('type' => Cp::TYPE_CAMPAIGN))) ?>">一覧</a></li>
                                <li><a href="<?php write_html(Util::rewriteUrl('admin-cp', 'edit_setting_skeleton')) ?>">作成</a></li>
                                <?php if(!$data['isAgent']): ?>
                                    <li><a href="<?php write_html(Util::rewriteUrl('admin-coupon', 'coupon_list')) ?>">クーポン設定</a></li>
                                    <li><a href="<?php write_html(Util::rewriteUrl('admin-code-auth', 'code_auth_list')) ?>">認証コード設定</a></li>
                                <?php endif ?>
                            </ul>
                        </li>
                    <?php endif;?>
                    <?php if($data['login_info']['brand']->hasOption(BrandOptions::OPTION_CRM, BrandInfoContainer::getInstance()->getBrandOptions())):?>
                        <li class="actionMessage">
                            <a href="<?php write_html(Util::rewriteUrl('admin-cp','public_cps', array(), array('type' => Cp::TYPE_MESSAGE))) ?>">メッセージ</a>
                            <ul class="editMenuInner">
                                <li><a href="<?php assign(Util::rewriteUrl('admin-cp','public_cps', array(), array('type' => Cp::TYPE_MESSAGE))) ?>">一覧</a></li>
                                <li><a href="<?php write_html(Util::rewriteUrl('admin-cp', 'edit_customize_skeleton', array(), array('type' => Cp::TYPE_MESSAGE  ))) ?>">作成</a></li>
                            </ul>
                        </li>
                    <?php endif;?>
                    <?php if($data['login_info']['brand']->hasOption(BrandOptions::OPTION_CMS, BrandInfoContainer::getInstance()->getBrandOptions())):?>
                        <li class="actionContents">
                            <a href="<?php assign(Util::rewriteUrl('admin-blog','static_html_entries')) ?>">コンテンツ</a>
                            <ul class="editMenuInner">
                                <li><a href="<?php assign(Util::rewriteUrl('admin-blog','static_html_entries')) ?>">ページ</a></li>
                                <?php if($data['login_info']['brand']->hasOption(BrandOptions::OPTION_COMMENT, BrandInfoContainer::getInstance()->getBrandOptions())):?>
                                    <li><a href="<?php assign(Util::rewriteUrl('admin-comment','plugin_list')) ?>">コメント</a></li>
                                <?php endif; ?>
                                <!-- /.editMenuInner --></ul>
                        </li>
                    <?php endif;?>
                    <?php if($data['login_info']['brand']->hasOption(BrandOptions::OPTION_FAN_LIST, BrandInfoContainer::getInstance()->getBrandOptions())):?>
                        <li class="actionFan">
                            <a href="<?php write_html(Util::rewriteUrl('admin-fan', 'show_brand_user_list')) ?>">ファン</a>
                            <?php if($data['increased_fans']): ?>
                                <span class="badge1"><?php assign($data['increased_fans']) ?></span>
                            <?php endif; ?>
                            <?php if($data['can_download_brand_fan_list']
                                || ($data['login_info']['manager']->authority == Manager::SUPER_USER && $data['has_ads_option'])
                                ||  $data['login_info']['brand']->hasOption(BrandOptions::OPTION_SEGMENT, BrandInfoContainer::getInstance()->getBrandOptions())): ?>
                                <ul class="editMenuInner">
                                    <li><a href="<?php assign(Util::rewriteUrl('admin-fan', 'show_brand_user_list')) ?>">一覧</a></li>
                                <?php if($data['login_info']['manager'] !== null && $data['login_info']['manager']->authority != Manager::AGENT && $data['has_ads_option']):?>
                                    <li><a href="<?php assign(Util::rewriteUrl('admin-fan', 'ads_list')) ?>">SNS広告管理</a></li>
                                <?php endif; ?>
                                <?php if($data['can_download_brand_fan_list']): ?>
                                    <li><a href="<?php assign(Util::rewriteUrl('admin-cp', 'fan_list_download', array(), array('r'=> 1))) ?>">ファンデータ<br />ダウンロード</a></li>
                                <?php endif; ?>
                                <?php if($data['login_info']['brand']->hasOption(BrandOptions::OPTION_SEGMENT, BrandInfoContainer::getInstance()->getBrandOptions())):?>
                                    <li><a href="<?php assign(Util::rewriteUrl('admin-segment', 'segment_list')) ?>">セグメント</a></li>
                                <?php endif; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endif;?>
                    <?php if($data['login_info']['brand']->hasOption(BrandOptions::OPTION_DASHBOARD, BrandInfoContainer::getInstance()->getBrandOptions())):?>
                        <li class="actionDashboard">
                            <a href="<?php write_html(Util::rewriteUrl('admin-dashboard', 'dashboard_list')) ?>">ダッシュボード</a>
                        </li>
                    <?php endif;?>
                    <li class="actionInfo">
                        <a href="<?php write_html(Util::rewriteUrl('admin-information', 'notification_list')) ?>">お知らせ</a>
                        <?php if($data['notification_non_read']): ?>
                            <span class="badge1"><?php assign($data['notification_non_read']) ?></span>
                        <?php endif; ?>
                    </li>
                    <li class="actionContact">
                        <a href="<?php write_html(Util::rewriteUrl('admin-inquiry', 'show_inquiry_list')) ?>">お問い合わせ</a>
                        <?php if($data['inquiry_non_read']): ?>
                            <span class="badge1"><?php assign($data['inquiry_non_read']) ?></span>
                        <?php endif; ?>
                    </li>
                    <li class="actionSetting">
                        <a href="<?php assign(Util::rewriteUrl('admin-settings', 'administrator_settings_form')) ?>">設定</a>
                        <ul class="editMenuInner">
                            <?php if(!$data['isAgent']): ?>
                                <li><a href="<?php assign(Util::rewriteUrl('admin-settings', 'administrator_settings_form')) ?>">管理者設定</a></li>
                                <li><a href="<?php assign(Util::rewriteUrl('admin-settings', 'user_settings_form')) ?>">ユーザー設定</a></li>
                                <?php if($data['login_info']['can_set_sign_up_mail']):?>
                                    <li><a href="<?php assign(Util::rewriteUrl('admin-settings', 'signup_mail_settings_form')) ?>">登録メール設定</a></li>
                                <?php endif; ?>
                                <li><a href="<?php assign(Util::rewriteUrl('admin-settings', 'page_settings_form')) ?>">ページ設定</a></li>
                                <?php if (Util::isDefaultBRANDCoDomain() ||
                                    $data['login_info']['brand']->id == Brand::KENKO_KENTEI_ID ||
                                    $data['login_info']['brand']->id == Brand::DM_TEST_AA_DEV ||
                                    $data['login_info']['brand']->id == Brand::OLYMPUS_ID ||
                                    $data['login_info']['brand']->id == Brand::JR_ODEKAKE_NET): ?>
                                <li><a href="<?php assign(Util::rewriteUrl('admin-settings', 'conversion_setting_form')) ?>">CVタグ設定</a></li>
                                <?php endif; ?>
                                <li><a href="<?php assign(Util::rewriteUrl('admin-settings', 'redirector_settings_form')) ?>">リダイレクト設定</a></li>
                                <li><a href="<?php assign(Util::rewriteUrl('admin-settings', 'inquiry_settings_form')) ?>">通知先メール<br>アドレス設定</a></li>
                                <li><a href="javascript:void(0);" data-link="<?php assign(Util::rewriteUrl('admin-blog', 'file_list', array(), array('f_id' => BrandUploadFile::POPUP_FROM_SETTING_MENU))) ?>" class="jsFileUploaderPopup">ファイル管理</a></li>
                            <?php endif ?>
                            <li><a href="javascript:void(0);" data-link="<?php assign(Util::rewriteUrl('admin-settings', 'manual_list', array(), array('f_id' => BrandUploadFile::POPUP_FROM_SETTING_MENU))) ?>" class="jsFileUploaderPopup">マニュアル</a></li>
                        <!-- /.editMenuInner --></ul>
                    </li>
                </ul>
                <!-- /.editMenu --></nav>
            <!-- /.wrap --></div>
        <!-- /.editHead --></section>
    <?php if($data['is_closed_brand']): ?>
        <section class="privateMode">
            <p class="editLock">このページはクローズされています</p>
        </section>
    <?php elseif($data['login_info']['public_flg'] != BrandPageSettingService::STATUS_PUBLIC): ?>
        <section class="privateMode">
            <p class="editLock">ページは現在非公開です<a href="<?php assign(Util::rewriteUrl('admin-settings', 'page_settings_form')) ?>">設定</a></p>
        </section>
    <?php endif; ?>
    <!-- /.editHead --></header>

<div class="wrap">
    <?php foreach ((array)$data['token_expiry_error_pages'] as $page): ?>
        <?php
        $extend_url = Util::rewriteUrl('facebook', 'auth', array(), array('page_id' => $page->social_media_account_id,'mode' => 'extend',)); ?>
        あと2週間以内にFacebookページ<?php assign($page->name) ?>のアクセストークンの有効期限が切れます。<a href="<?php assign($extend_url) ?>">こちら</a>より更新を行ってください。<br>
    <?php endforeach; ?>
    <?php if ($data['token_expiry_error_pages'] && $data['token_expiry_alert_pages']):?>
        <br><br>
    <?php endif; ?>
    <?php foreach ((array)$data['token_expiry_alert_pages'] as $page): ?>
        <?php $extend_url = Util::rewriteUrl('facebook', 'auth', array(), array('page_id' => $page->social_media_account_id,'mode' => 'extend',)); ?>
        Facebookページ<?php assign($page->name) ?>のアクセストークンの有効期限が切れています。<a href="<?php assign($extend_url) ?>">こちら</a>より更新を行ってください。<br><br>
    <?php endforeach; ?>
</div>

<?php if($this->live800):?>
    <!-- Live800アイコンコード名:Test[固定] 開始-->
    <script language="javascript"
            src="https://chat.live800.jp/live800/chatClient/staticButton.js?jid=7332457060&companyID=67968&configID=74679&codeType=steady&info=<?php assign($this->live800);?>"></script><script
        id='write' language="javascript">function writehtml(){var
            temptext=StaticIcon_generate();document.getElementById('live74672').innerHTML=temptext;setTimeout('write.src',9000);}writehtml();</script>
    <!-- Live800アイコンコード名:Test[固定] 終わり-->

    <!-- Live800トラッキングコード: 開始-->
    <script language="javascript"
            src="https://chat.live800.jp/live800/chatClient/monitor.js?jid=7332457060&companyID=67968&configID=74671&codeType=custom&info=<?php assign($this->live800);?>"></script>
    <!-- Live800トラッキングコード: 終わり-->
<?php endif; ?>

<div class="modal1 jsModal" id="photoEntries">
    <section class="modalCont-large jsModalCont">
        <iframe data-src="<?php assign(Util::rewriteUrl('admin-top', 'photo_entries')) ?>"
                frameborder="0"></iframe>
    </section>
</div>
