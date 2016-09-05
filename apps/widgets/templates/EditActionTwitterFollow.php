<?php if($data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
<?php else: ?>
    <?php $disable = ($data['action']->status == CpAction::STATUS_FIX) ? 'disabled': '' ?>
<?php endif; ?>
<section class="moduleEdit1">

    <section class="moduleCont1" id="moduleCont1">
        <h1 class="editTwitterFollow1 jsModuleContTile">Twitterフォロー設定</h1>
        <div class="moduleSettingWrap jsModuleContTarget">
            <dl class="moduleSettingList">
                <?php if ($this->ActionError && !$this->ActionError->isValid('auth')): ?>
                    <p class="iconError1"><?php assign($this->ActionError->getMessage('auth')) ?></p>
                <?php endif; ?>
                <dt class="moduleSettingTitle jsModuleContTile">アカウント設定</dt>
                <dd class="moduleSettingDetail jsModuleContTarget">
                    <ul class="moduleSetting" id="twAccountSetting">
                        <?php if ($this->ActionError && !$this->ActionError->isValid('brand_social_account_id')): ?>
                            <p class="iconError1"><?php assign($this->ActionError->getMessage('brand_social_account_id')) ?></p>
                        <?php endif; ?>
                        <?php if ($this->ActionError && !$this->ActionError->isValid('social_account')): ?>
                            <p class="iconError1"><?php assign($this->ActionError->getMessage('social_account')) ?></p>
                        <?php endif; ?>
                        <?php if(count($data['brand_social_accounts'])): ?>
                            <?php foreach ($data['brand_social_accounts'] as $social_account): ?>
                                <li>
                                    <label>
                                        <input type="radio"
                                               name="brand_social_account_id"
                                               id="<?php assign($social_account->id) ?>"
                                               value="<?php assign($social_account->id) ?>"
                                            <?php assign( $social_account->id == $data['tw_follow_social_account'] ? "checked=checked" : ""); ?>
                                            <?php assign(in_array($social_account->id, $data['connected_brand_social_account_ids']) ? "disabled=disabled" : ""); ?>
                                            <?php assign($disable == 'disabled' ? 'disabled="disabled"' : ''); ?>>
                                        <img src="<?php assign($this->setVersion('/img/sns/iconSns'.SocialApps::getSocialMediaProviderShortName($social_account->social_app_id).'2.png')) ?>" alt="accout name" width="20">
                                        <img src="<?php assign($social_account->picture_url) ?>" alt="<?php assign($social_account->name); ?>" width="20"><?php assign($social_account->name); ?>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            連携しているSNSはありません。<br />設定をお願い致します。
                        <?php endif ?>
                        <?php if (Util::isDefaultBRANDCoDomain()): ?>
                            <li><a href="<?php assign(Util::rewriteUrl('twitter', 'connect', array(), array('callback_url' => $data['callback_url']))); ?>" class="linkAdd jsOpenModal">Twitterアカウントを連携</a></li>
                        <?php endif; ?>
                        <!-- /.moduleSetting --></ul>
                    <!-- /.moduleSettingDetail --></dd>

                <dt class="moduleSettingTitle jsModuleContTile">スキップ設定</dt>
                <dd class="moduleSettingDetail jsModuleContTarget">
                    <p><label><?php write_html($this->formCheckBox('skip_flg', array($this->getActionFormValue('skip_flg')), array($disable => $disable), array('1' => 'スキップを許可'))); ?></label>
                        <?php write_html($this->formHidden('skip_flg_load', $this->getActionFormValue('skip_flg'))); ?><br>
                        <small>※ユーザがフォローしなくても次へ進めます。</small>
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
        <section class="messageWrap" id="socialButtons">

            <section class="message_engagement" id="socialButton_0">

                <div class="messageFooter">
                    <p class="skip jsSkipFlgPreview"><a href="javascript:void(0);"><small>フォローせず次へ</small></a></p>
                </div>
                <!-- /.message --></section>

            <?php foreach ($data['brand_social_accounts'] as $social_account): ?>
                <?php $section_msg_class = $social_account->social_app_id == SocialApps::PROVIDER_FACEBOOK ? 'message_share' : 'message_engagement'; ?>

                <section class="<?php assign($section_msg_class); ?>" style="display: none" id="socialButton_<?php assign($social_account->id) ?>">
                    <?php if ($social_account->social_app_id == SocialApps::PROVIDER_TWITTER): ?>
                      <h1 class="messageHd1"><?php assign($data['titles'][$social_account->id]); ?></h1>
                      <div class="engagementInner">
                        <figure><img src="<?php assign($social_account->picture_url); ?>" alt="<?php assign($social_account->name); ?>" width="65" height="65"></figure>
                        <p class="engagementTw">
                            <strong><?php assign($social_account->name); ?></strong><br>
                            <small>@<?php assign($social_account->screen_name); ?></small>
                        <!-- /.engagementTw --></p>
                      <!-- /.engagementInner --></div>
                    <?php endif; ?>

                    <div class="messageFooter">
                        <ul class="btnSet">
                            <li class="btnTwFollow"><a href="javascript:void(0)" class="large1"><?php assign($data['is_last_action'] ? 'フォローする' : 'フォローして次へ'); ?></a></li>
                        </ul>
                        <p class="skip jsSkipFlgPreview"><a href="javascript:void(0);"><small>フォローせず次へ</small></a></p>
                    </div>
                <!-- /.message --></section>
            <?php endforeach; ?>
            <!-- /.messageWrap --></section>
        <!-- /.displayPC --></div>
    <!-- /.modulePreview --></section>
