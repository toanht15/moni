<?php if( $data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
<?php else: ?>
    <?php $disable = ( $data['action']->status == CpAction::STATUS_FIX) ? 'disabled' : '' ?>
<?php endif; ?>

<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array('brand' => $data['pageStatus']['brand']))) ?>

<section class="moduleEdit1">
    <section class="moduleCont1 jsModuleContWrap">
        <h1 class="editYoutubeFollow1 jsModuleContTile">YouTubeチャンネル登録設定</h1>
        <div class="moduleSettingWrap jsModuleContTarget">
            <dl class="moduleSettingList">

                <dt class="moduleSettingTitle jsModuleContTile">アカウント設定</dt>
                <dd class="moduleSettingDetail jsModuleContTarget">
                    <?php if ($this->ActionError && !$this->ActionError->isValid('brand_social_account_id')): ?>
                        <span class="iconError1">選択してください</span>
                    <?php endif; ?>
                    <ul class="moduleSetting" id="ytAccountSetting">
                        <?php if(count($data['brand_social_accounts'])): ?>
                            <?php foreach ($data['brand_social_accounts'] as $social_account): ?>
                                <li id="ytAccountList_<?php assign($social_account->id) ?>" value="<?php assign($data['action']->id);?>">
                                    <label>
                                        <input type="radio"
                                               name="brand_social_account_id"
                                               id="<?php assign($social_account->id) ?>"
                                               value="<?php assign($social_account->id) ?>"
                                               data-name="<?php assign($social_account->name); ?>"
                                               data-screen_name="<?php assign($social_account->screen_name); ?>"
                                               data-picture_url="<?php assign($social_account->picture_url) ?>"
                                            <?php assign($social_account->id == $data['target_account']->id ? "checked=checked" : ""); ?>
                                            <?php assign(in_array($social_account->id, $data['connected_brand_social_account_ids']) ? "disabled=disabled" : ""); ?>
                                            <?php assign($disable == 'disabled' ? 'disabled="disabled"' : ''); ?>>
                                        <img src="<?php assign($this->setVersion('/img/sns/iconSnsYT2.png')) ?>" alt="YouTube" width="20">
                                        <img src="<?php assign($social_account->picture_url) ?>" alt="<?php assign($social_account->name); ?>" height="20" width="20"><?php assign($social_account->name); ?>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            連携しているYouTubeアカウントはありません。<br />
                        <?php endif ?>
                        <?php if (Util::isDefaultBRANDCoDomain()): ?>
                            <li><a href="<?php assign($disable ? 'javascript:void(0)' : Util::rewriteUrl('google', 'connect', array(), array('callback_url' => $data['callback_url']))); ?>" class="linkAdd jsOpenModal">追加する</a></li>
                        <?php endif; ?>
                    <!-- /.moduleSetting --></ul>
                <!-- /.moduleSettingDetail --></dd>

                <dt class="moduleSettingTitle jsModuleContTile">紹介する動画の設定</dt>
                <dd class="moduleSettingDetail jsModuleContTarget">
                    <?php if ($this->ActionError && !$this->ActionError->isValid('entry')): ?>
                        <span class="iconError1">選択してください</span>
                    <?php endif; ?>
                    <label><?php write_html($this->formCheckBox('intro_flg', array($this->getActionFormValue('intro_flg')), array($disable => $disable), array('1' => '設定する'))); ?></label>
                    <?php foreach ($data['streams'] as $brand_social_account_id => $entries): ?>
                        <?php write_html($this->formSelect(
                            'entry_'.$brand_social_account_id,
                            $data['target_entry']->id. ',' .$data['target_entry']->object_id,
                            array(
                                $disable => $disable,
                                'class' => 'jsSelectYtEntry',
                                'id' => 'stream_select_'.$brand_social_account_id,
                                'style' => 'display:none'),
                            $entries
                        )); ?>
                    <?php endforeach; ?>
                <!-- /.moduleSettingDetail --></dd>

            <!-- /.moduleSettingList --></dl>
        <!-- /.moduleSettingWrap --></div>
    <!-- /.moduleCont1 --></section>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('CpActionModuleDeadLine')->render([
        'ActionForm'  => $data['ActionForm'],
        'ActionError' => $data['ActionError'],
        'cp_action'   => $data['action'],
        'is_login_manager' => $data['pageStatus']['isLoginManager'],
        'disable'     => $disable,
    ])); ?>
<!-- /.moduleEdit1 --></section>

<section class="modulePreview1">

    <header class="modulePreviewHeader">
        <p>スマートフォン<a href="#" class="toggle_switch left jsModulePreviewSwitch">toggle_switch</a>PC</p>
    <!-- /.modulePreviewHeader --></header>

    <div class="displaySP jsModulePreviewArea">
        <section class="messageWrap">

            <section class="message_engagement">

                <h1 class="messageHd1" id="ytChPreviewTitle">「<?php assign($data['target_account']->name ? : 'アカウント名'); ?>」のYouTubeチャンネルを登録しよう！</h1>
                <div class="engagementInner">
                    <figure><img src="<?php assign($data['target_account']->picture_url); ?>" alt="<?php assign($data['target_account']->name); ?>" width="65" height="65" id="ytChPreviewImg"></figure>
                    <p class="engagementYt">
                        <span class="btnYtFollow"><a href="javascript:void(0);">チャンネル登録</a></span><br>
                        <strong id="ytChPreviewName"><?php assign($data['target_account']->name ? : 'アカウント名'); ?></strong><br>
                        <small>※Googleアカウントへのログインが求められます</small>
                    <!-- /.engagementTw --></p>
                <!-- /.engagementInner --></div>
                <div class="demoMovie" id="ytChPreviewMovie" style="display: <?php assign($data['ActionForm']['intro_flg'] ? 'block' : 'none'); ?>">
                    <p><iframe src="https://www.youtube.com/embed/<?php assign($data['target_entry']->object_id);?>?rel=0" id="ytChPreviewMovieIframe" frameborder="0" allowfullscreen></iframe></p>
                <!-- /.demoMovie --></div>
                <div class="messageFooter">
                    <p class="skip" id="ytChPreviewSkipFlg"><a href="javascript:void(0)"><small>チャンネル登録せず次へ</small></a></p>
                <!-- /.messageFooter --></div>
            <!-- /.message --></section>

        <!-- /.messageWrap --></section>
    </div>

<!-- /.modulePreview --></section>
