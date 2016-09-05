
<?php
$is_users_num_visible = $data['brand_info']['is_users_num_visible'];
$is_login = $data['side_col_info']['is_login'];
$is_show_sns_box = $data['side_col_info']['is_show_sns_box'];

$service_factory = new aafwServiceFactory();
/** BrandGlobalSettingService $brand_global_settings_service */
$brand_global_settings_service = $service_factory->create('BrandGlobalSettingService');
$lp_mode = $brand_global_settings_service->getBrandGlobalSetting($data['brand']->id, BrandGlobalSettingService::LP_MODE)->content
?>

<?php if ($is_show_sns_box || $is_users_num_visible || !$is_login ): ?>
    <?php if (!$lp_mode): ?>
        <section class="contBoxSide">
            <?php if ( $is_users_num_visible || !$is_login ): ?>
                <div class="fanCounter">
                    <?php if ($is_users_num_visible): ?>
                        <h1><strong><?php assign(number_format($data['brand_info']['users_num'])); ?></strong>人</h1>
                    <?php endif; ?>

                    <?php if (!$is_login): ?>
                        <p>登録すると最新情報やキャンペーン情報などをいち早く手に入れられます</p>
                        <p class="btnSet"><span class="btn3"><a href="<?php assign(Util::rewriteUrl('my','signup')) ?>">新規登録（無料）</a></span></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($is_show_sns_box): ?>
                <ul class="snsBtns-btn">
                    <li><div id="fb-root" data-appid = "<?php assign(config("@facebook.Admin.AppId")); ?>"></div>
                        <div class="fb-like" data-href="https://<?php assign(Util::constructBaseURL($data['brand']->id, $data['brand']->directory_name)); ?>" data-layout="button" data-action="like" data-show-faces="true" data-share="false"></div>
                    </li><li><a href="https://twitter.com/share" class="twitter-share-button" data-lang="ja" data-count="none">ツイート</a>
                    </li><li><div class="g-plusone" data-size="tall" data-annotation="none"></div></li>
                    <!-- /.snsBtns --></ul>
            <?php endif; ?>

        <!-- /.contBoxSide--></section>
    <?php else: ?>
        <?php if ($is_show_sns_box): ?>
            <section class="contBoxSide">
                <ul class="snsBtns-btn">
                    <li><div id="fb-root" data-appid = "<?php assign(config("@facebook.Admin.AppId")); ?>"></div>
                        <div class="fb-like" data-href="https://<?php assign(Util::constructBaseURL($data['brand']->id, $data['brand']->directory_name)); ?>" data-layout="button" data-action="like" data-show-faces="true" data-share="false"></div>
                    </li><li><a href="https://twitter.com/share" class="twitter-share-button" data-lang="ja" data-count="none">ツイート</a>
                    </li><li><div class="g-plusone" data-size="tall" data-annotation="none"></div></li>
                    <!-- /.snsBtns --></ul>
            <!-- /.contBoxSide--></section>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
