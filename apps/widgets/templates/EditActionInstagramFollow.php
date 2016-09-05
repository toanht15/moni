<?php if( $data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
<?php else: ?>
    <?php $disable = ( $data['action']->status == CpAction::STATUS_FIX) ? 'disabled' : '' ?>
<?php endif; ?>

<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array('brand' => $data['pageStatus']['brand']))) ?>

<section class="moduleEdit1">
    <section class="moduleCont1 jsModuleContWrap">
        <h1 class="editInstagramFollow1 jsModuleContTile">Instagramフォロー設定</h1>
        <div class="moduleSettingWrap jsModuleContTarget">
            <dl class="moduleSettingList">
                <dt class="moduleSettingTitle closse jsModuleContTile">アカウント設定</dt>
                <dd class="moduleSettingDetail jsModuleContTarget">
                    <?php if ($this->ActionError && !$this->ActionError->isValid('brand_social_account_id')): ?>
                        <span class="iconError1">選択してください</span>
                    <?php endif; ?>
                    <ul class="moduleSetting" id="igAccountSetting">
                        <?php if(count($data['brand_social_accounts'])): ?>
                            <?php foreach ($data['brand_social_accounts'] as $social_account): ?>
                                <li id="igAccountList_<?php assign($social_account->id) ?>" value="<?php assign($data['action']->id);?>">
                                    <label>
                                        <input type="radio"
                                               name="brand_social_account_id"
                                               id="<?php assign($social_account->id) ?>"
                                               value="<?php assign($social_account->id) ?>"
                                            <?php assign($social_account->id == $data['tgt_account']->id ? "checked=checked" : ""); ?>
                                            <?php assign(in_array($social_account->id, $data['connected_brand_social_account_ids']) ? "disabled=disabled" : ""); ?>
                                            <?php assign($disable == 'disabled' ? 'disabled="disabled"' : ''); ?>>
                                        <img src="<?php assign($this->setVersion('/img/sns/iconSns'.SocialApps::getSocialMediaProviderShortName($social_account->social_app_id).'2.png')) ?>" alt="Instagram" width="20">
                                        <img src="<?php assign($social_account->picture_url) ?>" alt="<?php assign($social_account->name); ?>" height="20" width="20"><?php assign($social_account->name); ?>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            連携しているInstagramアカウントはありません。<br />
                        <?php endif ?>
                        <?php if (Util::isDefaultBRANDCoDomain()): ?>
                            <li><a href="<?php assign($disable ? 'javascript:void(0)' : Util::rewriteUrl('instagram', 'connect', array(), array('callback_url' => $data['callback_url']))); ?>" class="linkAdd jsOpenModal">追加する</a></li>
                        <?php endif; ?>
                    <!-- /.moduleSetting --></ul>
                <!-- /.moduleSettingDetail --></dd>
                <dt class="moduleSettingTitle <?php if (!$this->ActionError || $this->ActionError->isValid('current_entry_id')) assign("close"); ?> jsModuleContTile" id="igBindSetting">埋め込み投稿設定</dt>
                <dd class="moduleSettingDetail jsModuleContTarget">
                    <?php if ($this->ActionError && !$this->ActionError->isValid('current_entry_id')): ?>
                        <span class="iconError1">選択してください</span>
                    <?php endif; ?>
                    <p>
                        <input type="hidden" name="current_entry_id" value="<?php assign($data['tgt_entry']->id)?>" id="currentEntryId">
                        <span class="selectedImg" id="currentEntryImg" style="<?php assign( $data['tgt_entry']->image_url ? '' : "display: none") ?>">選択中<img src="<?php assign($data['tgt_entry']->image_url); ?>" width="100" height="100" alt="post text"></span>
                        <a href="<?php assign($disable ? 'javascript:void(0)' : '#selectInstagramEntryPanel')?>" id="selectInstagramEntry" class="linkAdd jsOpenModal small1" data-option="<?php assign('?tgt_act_id='. $data['tgt_account']->id) ?>&action_id=<?php assign($data['action']->id);?>">投稿一覧から選択</a><br>
                        <small>※ユーザーがInstagramアカウントとアライドIDを連携している場合、ユーザがフォローしてくれたか追跡できます。</small>
                    </p>
                <!-- /.moduleSettingDetail --></dd>
            <!-- /.moduleSettingList --></dl>
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
        <section class="messageWrap">
            <section class="message_engagement">
                <?php if($data['response_html']): ?>
                    <h1 class="messageHd1" id="igAccNamePreview">「<?php assign($data['tgt_account']->screen_name); ?>」のInstagramアカウントをフォローしよう！</h1>
                    <div class="engagementInner followIg jsIgSelectEntry" id="igAccEntryPreview">
                        <div class="engagementIg">
                            <?php write_html($data['response_html']); ?>
                        <!-- /.engagementIg --></div>
                    <!-- /.engagementInner --></div>
                <?php else: ?>
                    <h1 class="messageHd1" id="igAccNamePreview">「アカウント名」のInstagramアカウントをフォローしよう！</h1>
                    <div class="engagementInner followIg jsIgSelectEntry" id="igAccEntryPreview">
                        <div class="engagementIg">
                            <p class="postDummy_ig">post dummy</p>
                        <!-- /.engagementIg --></div>
                    <!-- /.engagementInner --></div>
                <?php endif; ?>

                <div class="messageFooter">
                    <?php if(!$data['is_last_action']): ?>
                        <ul class="btnSet">
                                <li class="btn3"><a class="middle1" href="javascript:void()">次へ</a></li>
                            <!-- /.btnSet --></ul>
                    <?php endif; ?>
                </div>
                <!-- /.message --></section>

            <!-- /.messageWrap --></section>
    </div>

    <!-- /.modulePreview --></section>

<div class="modal1 jsModal" id="selectInstagramEntryPanel">
    <section class="modalCont-large jsModalCont">
        <iframe data-src="<?php assign(Util::rewriteUrl('admin-cp', 'select_instagram_entry')) ?>" frameborder="0"></iframe>
    </section>
</div>
