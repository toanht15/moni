<?php if($data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
<?php else: ?>
    <?php $disable = ($data['action']->status == CpAction::STATUS_FIX) ? 'disabled': '' ?>
<?php endif; ?>

<?php if ($_GET['connect'] == 'fb'):  ?>
    <script>
        jQuery(function($) {
            Brandco.unit.openModal('#connectFBPanelKind');
        });
    </script>
<?php endif; ?>

<section class="moduleEdit1">

    <section class="moduleCont1" id="moduleCont1">
        <h1 class="editFangate1 jsModuleContTile">アカウント設定</h1>
        <div class="moduleSettingWrap jsModuleContTarget">
            <ul class="moduleSetting">
                <?php if ($this->ActionError && !$this->ActionError->isValid('brand_social_account_id')): ?>
                    <p class="iconError1"><?php assign ($this->ActionError->getMessage('brand_social_account_id')) ?></p>
                <?php endif; ?>
                <?php if(count($data['brand_social_accounts'])): ?>
                    <?php foreach ($data['brand_social_accounts'] as $social_account): ?>
                        <li>
                            <label>
                                <input type="radio"
                                       name="brand_social_account_id"
                                       id="<?php assign($social_account->id) ?>"
                                       value="<?php assign($social_account->id) ?>"
                                    <?php assign($social_account->id == $data['fb_like_account'] ? "checked=checked" : ""); ?>
                                    <?php assign(in_array($social_account->id, $data['connected_brand_social_account_ids']) ? "disabled=disabled" : ""); ?>
                                    <?php assign($disable == 'disabled' ? 'disabled="disabled"' : ''); ?>>
                                <img src="<?php assign($this->setVersion('/img/sns/iconSns'.SocialApps::getSocialMediaProviderShortName($social_account->social_app_id).'2.png')) ?>" alt="accout name" width="20">
                                <img src="<?php assign($social_account->picture_url) ?>" alt="<?php assign($social_account->name); ?>" width="20"><?php assign($social_account->name); ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    連携しているFacebookページはありません。<br />設定をお願い致します。
                <?php endif ?>
                <?php if (Util::isDefaultBRANDCoDomain()): ?>
                    <li><a href="<?php assign(Util::rewriteUrl('facebook', 'connect', array(), array('callback_url' => $data['callback_url']))); ?>" class="linkAdd jsOpenModal">Facebookページを連携</a></li>
                <?php endif; ?>
                <!-- /.moduleSetting --></ul>
            <!-- /.moduleSettingWrap --></div>
        <!-- /.moduleCont1 --></section>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('CpActionModuleDeadLine')->render([
            'ActionForm'       => $data['ActionForm'],
            'ActionError'      => $data['ActionError'],
            'cp_action'        => $data['action'],
            'is_login_manager' => $data['pageStatus']['isLoginManager'],
            'disable'          => $disable,
        ])); ?>
    <!-- /.moduleEdit1 --></section>

<section class="modulePreview1">
    <header class="modulePreviewHeader">
        <p>スマートフォン<a href="#" class="toggle_switch left jsModulePreviewSwitch">toggle_switch</a>PC</p>
    <!-- /.modulePreviewHeader --></header>
    <div class="displaySP jsModulePreviewArea">
        <section class="messageWrap" id="socialButtons">

            <section class="message" id="socialButton_0">
                <div class="messageFooter">
                    <ul class="btnSet">
                        <p class="skip"><a href="javascript:void(0);"><small>いいね！せず次へ</small></a></p>
                        <!-- /.btnSet --></ul>
                </div>
            <!-- /.message --></section>

            <?php foreach ($data['brand_social_accounts'] as $social_account): ?>
                <section class="message" style="display: none;" id="socialButton_<?php assign($social_account->id) ?>">
                    <div class="messageEngagement">
                        <h1><?php assign($data['titles'][$social_account->id]); ?></h1>
                        <div class="engagementInner">
                            <figure><img src="<?php assign($social_account->picture_url); ?>" alt="<?php assign($social_account->name); ?>" width="65" height="65"></figure>
                            <div class="engagementFb_pc">
                                <div class="fb-like" data-href="<?php assign("https://facebook.com/pages/" . $social_account->name . "/" . $social_account->social_media_account_id); ?>" data-layout="standard" data-action="like" data-show-faces="true" data-share="false"></div>
                            <!-- /.engagementFb_pc --></div>
                                <div class="engagementFb_sp">
                                    <div class="fb-like" data-href="<?php assign("https://facebook.com/pages/" . $social_account->name . "/" . $social_account->social_media_account_id); ?>" data-layout="box_count" data-action="like" data-show-faces="false" data-share="false"></div>
                            <!-- /.engagementFb_sp --></div>
                        <!-- /.engagementInner --></div>
                    <!-- /.messageEngagement --></div>
                    <div class="messageFooter">
                        <p class="skip"><a href="javascript:void(0);"><small>いいね！せず次へ</small></a></p>
                    </div>
                <!-- /.message --></section>
            <?php endforeach; ?>
            <!-- /.messageWrap --></section>
    <!-- /.displaySP --></div>
<!-- /.modulePreview1 --></section>

<div class="modal1 jsModal" id="connectFBPanelKind">
    <section class="modalCont-large jsModalCont">
        <iframe
            data-src="<?php assign(Util::rewriteUrl('facebook', 'connect', array(), array('code' => $_GET['code'], 'state' => $_GET['state'], 'error_reason' => $_GET['error_reason'], 'callback_url' => Util::getCurrentUrl()))) ?>"
            frameborder="0"></iframe>
    </section>
</div>
