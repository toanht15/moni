<?php if($data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
<?php else: ?>
    <?php $disable = ($data['action']->status == CpAction::STATUS_FIX)?'disabled':'' ?>
<?php endif; ?>
    <section class="moduleEdit1">

        <?php write_html($this->parseTemplate('CpActionModuleTitle.php', array('disable'=>$disable))); ?>

        <section class="moduleCont1">
            <h1 class="editFangate1 jsModuleContTile">エンゲージメント設定</h1>
            <div class="moduleSettingWrap jsModuleContTarget">
                <ul class="moduleSetting">
                    <?php if(!count($data['brand_social_accounts'])): ?>
                        連携しているSNSはありません。
                    <?php endif; ?>
                    <?php foreach ($data['brand_social_accounts'] as $social_account): ?>
                        <?php if ($social_account->social_app_id == SocialApps::PROVIDER_GOOGLE) continue; ?>
                        <li>
                            <label>
                                <input type="radio"
                                       name="brand_social_account_id"
                                       id="<?php assign($social_account->id) ?>"
                                       value="<?php assign($social_account->id) ?>"
                                       <?php assign( $social_account->id == $data['engagement_social_account'] ? "checked=checked" : ""); ?>
                                       <?php assign(in_array($social_account->id, $data['connected_brand_social_account_ids']) ? "disabled=disabled" : ""); ?>
                                       <?php assign($disable == 'disabled' ? 'disabled="disabled"' : ''); ?>>
                                <img src="<?php assign($this->setVersion('/img/sns/iconSns'.SocialApps::getSocialMediaProviderShortName($social_account->social_app_id).'2.png')) ?>" alt="accout name" width="20">
                                <img src="<?php assign($social_account->picture_url) ?>" alt="<?php assign($social_account->name); ?>" width="20"><?php assign($social_account->name); ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                    <?php $callback_url = urlencode(Util::rewriteUrl('admin-cp', 'edit_action_base', array($data['action']->id), array('connect' => 'fb'))) ?>
                    <li><a href="<?php assign(Util::rewriteUrl( 'facebook', 'connect', array(), array('callback_url' =>  $callback_url))); ?>" class="linkAdd jsOpenModal">Facebookページを連携</a></li>
                    <!-- /.moduleSetting --></ul>
                <!-- /.moduleSettingWrap --></div>
            <!-- /.moduleCont1 --></section>
    <!-- /.moduleEdit1 --></section>

<section class="modulePreview1">
    <header class="modulePreviewHeader">
        <p>スマートフォン<a href="#" class="toggle_switch left jsModulePreviewSwitch">toggle_switch</a>PC</p>
        <!-- /.modulePreviewHeader --></header>

    <div class="displaySP jsModulePreviewArea">
        <?php write_html($this->parseTemplate('UserActionEngagement.php', array('brand_social_accounts' => $data['brand_social_accounts']))); ?>
    <!-- /.displayPC --></div>
<!-- /.modulePreview --></section>

<?php if($data['ActionForm']['connect'] == 'fb'): /* SNS連携からの戻りの時はモーダルを開く */?>
    <script>
        jQuery(function($){
            Brandco.unit.openModal('.modal1');
        });
    </script>
<?php endif;?>
<div class="modal1 jsModal">
    <section class="modalCont-large jsModalCont">
        <iframe
            data-src="<?php assign(Util::rewriteUrl('facebook', 'connect', array(), array('callback_url' =>  $callback_url ,'code' => $_GET['code'], 'state' => $_GET['state'], 'error_reason' => $_GET['error_reason']))) ?>"
            frameborder="0"></iframe>
    </section>
</div>
