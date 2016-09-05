<?php if ($data['brand']->id == '452'): // TODO NECレノボグループ対応?>
    <section class="lenovoFooter">
        <style>
            @media screen and (max-width:959px) {
                .lenovoFooter { width:100%; margin:20px 0 0 0;}
                .lenovoFooter img { width:100%;}
                .lenovoSupplement1 { font-size:11px; color:#333; margin:30px 10px 0 10px; line-height:1.25;}
                .lenovoSupplement1 .pcBlock{ display:none;}
                .lenovoFooter .pcBanner { display:none;}
                .spBanner { margin: 0 10px;}
            }
            @media screen and (min-width:960px)  {
                .lenovoFooter { width:960px; margin:30px auto;}
                .lenovoSupplement1 { font-size:11px; color:#333; text-align:center; margin:40px 0 0 0;}
                .lenovoFooter .spBanner { display:none;}
                .spBanner { margin: 0 10px;}
            }
        </style>
        <p class="pcBanner"><a href="http://yoga.lenovo-active.com/?ipromoID=jp_pub_cs_pro" target="_blank"><img src="https://s3-ap-northeast-1.amazonaws.com/parts.brandco.jp/image/brand/c4ca4238a0b923820dcc509a6f75849b/upload_file/b45b7316ece57cba7a2e62dd2f70479814fb6be9/pc-bnr_footlenovo.png" alt="Lenovo 新しいYOGAシリーズを体験しよう" width="960" height="134"></a></p>
        <p class="spBanner"><a href="http://yoga.lenovo-active.com/?ipromoID=jp_pub_cs_pro" target="_blank"><img src="https://s3-ap-northeast-1.amazonaws.com/parts.brandco.jp/image/brand/c4ca4238a0b923820dcc509a6f75849b/upload_file/75788a957f30385cf70002905baab50a540462ee/sp-bnr_footlenovo.png" alt="Lenovo 新しいYOGAシリーズを体験しよう"></a></p>
        <p class="lenovoSupplement1">Lenovo、レノボ、レノボロゴ、Yogaは、Lenovo Corporationの商標。<br>Intel、インテル、Intel ロゴ、Intel Inside、Intel Inside ロゴ、Intel Atom、Intel Atom Inside、Intel Core、Core Inside、Ultrabook は、<br class="pcBlock">
            アメリカ合衆国および/またはその他の国における Intel Corporation の商標です。</p>
    </section>
<?php elseif ($data['brand']->id == '453'): ?>
    <section class="lavieFooter">
        <style>
            @media screen and (max-width:959px) {
                .lavieFooter { width:100%; margin:20px 0 0 0;}
                .lavieFooter img { width:100%;}
                .lenovoSupplement1 { font-size:11px; color:#333; margin:30px 10px 0 10px; line-height:1.25;}
                .lavieFooter .pcBanner { display:none;}
                .spBanner { margin: 0 10px;}
            }
            @media screen and (min-width:960px)  {
                .lavieFooter { width:960px; margin:30px auto;}
                .lenovoSupplement1 { font-size:11px; color:#333; text-align:center; margin:40px 0 0 0;}
                .lavieFooter .spBanner { display:none;}
                .spBanner { margin: 0 10px;}
            }
        </style>
        <p class="pcBanner"><a href="https://121ware.com/navigate/products/pc/special/lavie/zero/" target="_blank"><img src="https://s3-ap-northeast-1.amazonaws.com/parts.brandco.jp/image/brand/c4ca4238a0b923820dcc509a6f75849b/upload_file/b6d4487794704b289e657329a057d0a185015ea4/pc-bnr_footlavie.png" alt="LAVIE 世界最軽量モバイル登場" width="960" height="134"></a></p>
        <p class="spBanner"><a href="https://121ware.com/navigate/products/pc/special/lavie/zero/" target="_blank"><img src="https://s3-ap-northeast-1.amazonaws.com/parts.brandco.jp/image/brand/c4ca4238a0b923820dcc509a6f75849b/upload_file/b92cd6fd3d7f232e63fd76a76f77e97157c5a56e/sp-bnr_footlavie.png" alt="LAVIE 世界最軽量モバイル登場"></a></p>
        <p class="lenovoSupplement1">Intel、インテル、Intel ロゴ、Intel Inside、Intel Inside ロゴ、Intel Atom、Intel Atom Inside、Intel Core、Core Inside、Ultrabook は、<br class="pcBlock">アメリカ合衆国および/またはその他の国における Intel Corporation の商標です。</p>
    </section>
<?php endif; ?>

<?php
// isLoginManagerの判定がおかしいので
// 一時的にコメントアウトする
//if ($this->getAction()->isLoginManager()) {
if (true) {
    $service_factory = new aafwServiceFactory();
    /** @var UserSearchService $user_search_service */
    $user_search_service = $service_factory->create('UserSearchService');
    if ($_GET[UserSearchService::TOKEN_KEY]) {
        $brandId = $data['brand']->id;
        $userId = UserSearchService::verifyOnetimeToken($_GET[UserSearchService::TOKEN_KEY]);
        if ($userId) {
            $brand_url = $user_search_service->getAuthBrandUrl($brandId, $userId);
            $refresh = $_SERVER['REQUEST_URI'];
            header("Refresh: 0.03; URL=$refresh");
        }
    }
}
?>
<?php if ($data['is_olympus_header_footer']): ?>
    <?php write_html($this->parseTemplate('OlympusFooter.php', $data)) ?>
<?php elseif($data['is_whitebelg_header_footer']): ?>
    <?php write_html($this->parseTemplate('WhitebelgFooter.php', $data)) ?>
<?php elseif($data['is_kenken_header_footer']): ?>
    <?php write_html($this->parseTemplate('KenkenFooter.php', $data)) ?>
<?php elseif($data['is_uq_header_footer']): ?>
    <?php write_html($this->parseTemplate('UQFooter.php', $data)) ?>
<?php else: ?>
<footer>
    <?php endif ?>
    <div class="copyright">
        <ul class="links">
            <?php if ($data['is_olympus_header_footer']): ?>
                <li><a href="<?php assign(Util::rewriteUrl( 'inquiry', 'index', array(), array('cp_id' => ($this->cp_id) ? : 0) )); ?>" target="_blank">モニプラお問い合わせ</a></li>
            <?php elseif(!$data['hide_inquiry_link']): ?>
                <li><a href="<?php assign(Util::rewriteUrl( 'inquiry', 'index', array(), array('cp_id' => ($this->cp_id) ? : 0) )); ?>">お問い合わせ</a></li>
            <?php endif ?>

            <?php if (BrandInfoContainer::getInstance()->getBrandPageSetting()->agreement): ?>
                <li><a href="<?php assign(Util::rewriteUrl('', 'agreement')); ?>"><?php assign($data['brand']->name); ?>公式ファンサイト利用規約</a></li>
            <?php endif; ?>
        </ul>

        <?php if ($data['is_olympus_header_footer']): ?>
            © <script type="text/javascript">
                myD = new Date();
                myYear = myD.getYear();
                myYears = (myYear < 2000) ? myYear + 1900 : myYear;
                document.write(myYears);
            </script> Olympus Corporation.
        <?php elseif($data['is_whitebelg_header_footer']): ?>
            <small>Copyright (c)
                <script type="text/javascript">
                    myD = new Date();
                    myYear = myD.getYear();
                    myYears = (myYear < 2000) ? myYear + 1900 : myYear;
                    document.write(myYears);
                </script> サッポロビール株式会社. ALL RIGHTS RESERVED
            </small>
        <?php elseif(!$data['brand'] || $data['brand']->isPlan(BrandContract::PLAN_PROMOTION_MONIPLA, BrandInfoContainer::getInstance()->getBrandContract())):?>
            <small>Copyright © <?php assign(date('Y'));?> Allied Architects. ALL RIGHTS RESERVED</small>
        <?php else:?>
            <small>Copyright © <?php assign(date('Y'));?> <?php assign($data['brand']->enterprise_name ? $data['brand']->enterprise_name : $data['brand']->name); ?>. ALL RIGHTS RESERVED</small>
        <?php endif;?>
    </div>

    <?php if(!$data['hide_footer_menu']): ?>
        <ul class="links">
            <?php if(Util::isSmartPhone() && $data['isLogin'] && $data['brand']->hasOption(BrandOptions::OPTION_HEADER, BrandInfoContainer::getInstance()->getBrandOptions()))://ログインのある場合だけログアウトを表示?>
                <li><a href="<?php assign(Util::rewriteUrl( 'my', 'logout' )); ?>">ログアウト</a></li>
            <?php endif;?>
            <?php if($data['isLoginAdmin']):?>
                <li><a href="<?php assign($this->setVersion('/pdf/client_agreement.pdf'))?>" target="_blank">モニプラ出展規約</a></li>
            <?php endif;?>
            <li><a href="<?php assign('//'.config('Domain.aaid'))?>/agreement" target="_blank">アライドID利用規約</a></li>
            <?php if ($data['brand']->id == 479): ?>
                <li><a href="http://www.aainc.co.jp/privacy/" target="_blank">アライドアーキテクツ株式会社 個人情報保護方針</a></li>
            <?php else: ?>
                <li><a href="http://www.aainc.co.jp/privacy/" target="_blank">個人情報保護方針</a></li>
            <?php endif; ?>
            <li><a href="http://allied-id.com/maintenance" target="_blank">メンテナンス情報</a></li>
        </ul>
        <p class="poweredBy"><small>Powered by monipla</small></p>
    <?php endif;?>
</footer>

<?php if($this->getAction()->isLoginManager()):?>
    <?php if($this->getAction()->getAAFunction() !== ''):?>
    <script>
        $(function() {
            $('#jsAaFunctionButton').click(function(){
                $('#jsAaFunction').toggle(300);
                $('body').animate({
                    scrollTop: $(document).height()
                }, 300);
            });
        });

    </script>
    <button id="jsAaFunctionButton" style="border: 1px; background: rgba(160, 160, 160, 0.1); width:100%; height: 38px;font-style: italic;">AA Function</button>
    <article id="jsAaFunction" style="display: none; padding: 30px 0;">
        <?php write_html($this->getAction()->getAAFunction())?>
    </article>
    <?php endif;?>
<?php endif;?>

<img id="ajaxReloadBox" src="<?php assign($this->setVersion('/img/base/ajax-loader.gif'))?>" width="30" height="30" style="display:none" />
<script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>

<?php if(DEBUG): ?>
    <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/Brandco.js'))?>"></script>
    <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/Brandco.net.js'))?>"></script>
    <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/Brandco.api.js'))?>"></script>
    <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/Brandco.message.js'))?>"></script>
    <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/Brandco.helper.js'))?>"></script>
    <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/Brandco.paging.js'))?>"></script>

<?php else: ?>
    <script type="text/javascript" src="<?php assign($this->setVersion('/js/brandco/dest/lib-all.js'))?>"></script>
<?php endif; ?>

<?php if($data['twitter_counter_flg']): ?>
    <script type="text/javascript" src="<?php assign($this->setVersion('/js/min/twitter.text.counter.min.js'))?>"></script>
<?php endif; ?>

<?php if($data['isLoginAdmin']):?>
    <?php write_html($this->scriptTag('admin_unit', false))?>
<?php endif;?>

<?php write_html($this->scriptTag('html5shiv-printshiv', false))?>

<?php if(Util::isSmartPhone()):?>
    <?php write_html($this->scriptTag('unit_sp', false))?>
<?php else:?>
    <script src="<?php assign($this->setVersion('/js/masonry.pkgd.min.js'))?>"></script>
    <?php if (Util::isSnsCategoryUrl()): ?>
        <?php write_html($this->scriptTag('BrandcoMasonryCategoryService'))?>
    <?php else: ?>
        <?php write_html($this->scriptTag('BrandcoMasonryTopService'))?>
    <?php endif; ?>
    <?php write_html($this->scriptTag('unit', false))?>
<?php endif;?>

<?php write_html($this->scriptTag('jquery.blockUI', false))?>

<?php if($_GET['mid']): ?>
    <?php if($data['isLoginAdmin']):?>
        <section class="noticeBar1 jsNoticeBarArea1" id="mid-message">
            <p class="<?php assign(config('@message.adminMessage.'.$_GET['mid'].'.class')) ?> jsNoticeBarClose" id="jsMessage1"><?php assign(config('@message.adminMessage.'.$_GET['mid'].'.msg')) ?></p>
        </section>
        <script type="text/javascript">
            $('article').each(function(){
                $(this).prepend($('#mid-message'));
                return false;
            });
            Brandco.unit.showNoticeBar($('#jsMessage1'));
        </script>
    <?php else:?>
        <div class="modal1 jsModal" id="modalSignup1" style="display: block;">
            <section class="modalCont-small jsModalCont" style="top: 40px; opacity: 1; display: block;">
                <p><?php assign(config('@message.userMessage.'.$this->params['mid'].'.msg')) ?></p>
                <a href="#closeModal" class="modalCloseBtn">閉じる</a>
            </section>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if($data['script']): ?>
    <?php foreach($data['script'] as $script): ?>
        <?php write_html($this->scriptTag($script)); ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoTopMainTag')->render(array('brand' => $data['brand'], 'userInfo' => $data['userInfo'], 'extend_tag' => $data['extend_tag']))) ?>

<?php if (config('AdEbis.Status') && $_GET['tid'] == 'signup_complete'): ?>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('AdEbis')->render(array('brand_id' => $data['brand']->id, 'platform_user_id' => $data['userInfo']->id, 'page_type' => Cps::ADEBIS_NEW_USER))); ?>
<?php endif; ?>

<?php write_html($this->parseTemplate('GoogleAnalytics.php', $data)); ?>

<?php write_html(aafwWidgets::getInstance()->loadWidget('CriteoTracker')->render(array('platform_user_id'=>$data['userInfo']->id))) ?>

<?php write_html($this->parseTemplate('InitiateMergerTag.php', array('platform_user_id'=>$data['userInfo']->id,'brand_id'=>$data['brand']->id,'cp_id'=>$data['cp']->id))); ?>

<?php write_html($this->parseTemplate('Rtoaster.php'));?>

<?php if (extension_loaded ('newrelic')) {
    if(config('NewRelic.use')) {
        write_html(newrelic_get_browser_timing_footer());
    }
} ?>

<?php
//TODO Olympus's Hard coding. Update category tag
if ($data['olympus_tag']): ?>
    <script type="text/javascript" language="javascript"> /* <![CDATA[ */
        var yahoo_retargeting_id = 'P8OKD8GB3G';
        var yahoo_retargeting_label = '';
        /* ]]> */
    </script>
    <script type="text/javascript" language="javascript" src="//b92.yahoo.co.jp/js/s_retargeting.js"></script>
    <script type="text/javascript">
        /* <![CDATA[ */
        var google_conversion_id = 933552831;
        var google_custom_params = window.google_tag_params;
        var google_remarketing_only = true;
        /* ]]> */
    </script>
    <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"> </script>
    <noscript>
        <div style="display:inline;">
            <img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/933552831/? value=0&amp;guid=ON&amp;script=0"/>
        </div>
    </noscript>
<?php endif ?>
</body>
</html>
