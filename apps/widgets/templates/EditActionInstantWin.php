<?php if( $data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
    <?php $disable_public = ( $data['cp']->status == Cp::STATUS_FIX) ? 'disabled' : '' ?>
<?php else: ?>
    <?php $disable = ( $data['action']->status == CpAction::STATUS_FIX) ? 'disabled' : '' ?>
    <?php $disable_public = ( $data['action']->status == CpAction::STATUS_FIX) ? 'disabled' : '' ?>
<?php endif; ?>

<section class="moduleEdit1">
    <?php write_html($this->parseTemplate('CpActionModuleTitle.php', array( 'disable' => $disable))); ?>

    <section class="moduleCont1">
        <h1 class="editInstantWin1 jsModuleContTile">スピードくじ設定</h1>
        <div class="moduleSettingWrap jsModuleContTarget">
            <dl class="moduleSettingList">

                <dt class="moduleSettingTitle jsModuleContTile close jsAnimationSetting">抽選前</dt>
                <dd class="moduleSettingDetail jsModuleContTarget jsAnimationSetting">
                    <dl>
                        <dt>本文</dt>
                        <dd>
                            <?php write_html($this->formTextArea('text', PHPParser::ACTION_FORM, array('maxlength' => CpValidator::MAX_TEXT_LENGTH, 'cols' => 30, 'rows' => 10, 'id' => 'text_area', $disable => $disable))); ?>
                            <a href="javascript:;"
                               class="openNewWindow1"
                               id="markdown_rule_popup"
                               data-link="<?php assign(Util::rewriteUrl('admin-cp', 'markdown_rule')); ?>" >
                                文字や画像の装飾について</a>
                            <br>
                            <!-- Campaign Status 1: STATUS_FIX, 2: DEFAULT -->
                            <a href="javascript:void(0);"
                               data-link="<?php assign(Util::rewriteUrl('admin-blog', 'file_list', array(), array('f_id' => BrandUploadFile::POPUP_FROM_TEXT_MODULE, 'stt' => ($data['disable'] ? 1 : 2)))) ?>"
                               class="openNewWindow1 jsFileUploaderPopup">ファイル管理から画像選択</a>
                        </dd>
                    </dl>
                    <!-- /.moduleSettingDetail --></dd>

            <?php $i = 0; ?>
            <?php foreach($data['instant_win_prizes'] as $result): ?>
                <?php $f_id = $i == 0 ? BrandUploadFile::POPUP_FROM_INSTANT_LOSE_SETTING : BrandUploadFile::POPUP_FROM_INSTANT_WIN_SETTING ?>
                <?php $setting_class = $i == 0 ? 'jsLoseSetting' : 'jsWinSetting' ?>
                <dt class="moduleSettingTitle close jsModuleContTile <?php assign($setting_class) ?>"><?php $i == 0 ? assign('落選時') : assign('当選時') ;?></dt>
                <dd class="moduleSettingDetail jsModuleContTarget <?php assign($setting_class) ?>">
                    <dl>
                        <dt>画像</dt>
                        <dd>
                            <ul class="moduleSetting">
                                <li><label><?php write_html( $this->formRadio('image_type_' . $i, $result->image_type, array('class' => 'labelTitleLose', $disable => $disable), array(InstantWinPrizes::IMAGE_DEFAULT => 'デフォルト画像'), array(), " ")); ?></label></li>
                                <li><label><?php write_html( $this->formRadio('image_type_' . $i, $result->image_type, array('class' => 'labelTitleLose', $disable => $disable), array(InstantWinPrizes::IMAGE_UPLOAD => 'アップロード'), array(), " ")); ?></label><input type="file" name="<?php assign('image_file_' . $i); ?>" id="<?php assign('image_file_' . $i); ?>" class="<?php assign('actionImageLose' . $i); ?>" disabled="disabled"></li>
                            </ul>
                        </dd>
                        <dt>本文</dt>
                        <dd>
                            <?php write_html( $this->formTextArea( 'text_' . $i, $result->text, array('maxlength' => CpValidator::MAX_TEXT_LENGTH, 'cols' => 30, 'rows' => 10, 'id' => 'text_area_' . $i, $disable => $disable))); ?>
                            <a href="javascript:;"
                               class="openNewWindow1"
                               id="markdown_rule_popup"
                               data-link="<?php assign(Util::rewriteUrl('admin-cp', 'markdown_rule')); ?>" >
                                文字や画像の装飾について</a>
                            <br>
                            <!-- Campaign Status 1: STATUS_FIX, 2: DEFAULT -->
                            <a href="javascript:void(0);"
                               data-link="<?php assign(Util::rewriteUrl('admin-blog', 'file_list', array(), array('f_id' => $f_id, 'stt' => ($data['disable'] ? 1 : 2)))) ?>"
                               class="openNewWindow1 jsFileUploaderPopup">ファイル管理から画像選択</a>
                        </dd>
                    </dl>
                    <!-- /.moduleSettingDetail --></dd>
                <?php $i++; ?>
            <?php endforeach; ?>

                <dt class="moduleSettingTitle jsModuleContTile">詳細設定</dt>
                <dd class="moduleSettingDetail jsModuleContTarget">
                    <dl>
                        <dt>当選確率</dt>
                        <dd class="moduleLotEstablish">
                            <ul class="moduleLotJoin">
                                <li>
                                    <label><?php write_html($this->formRadio('logic_type', PHPParser::ACTION_FORM, array($disable_public=>$disable_public), array(CpInstantWinActions::LOGIC_TYPE_TIME => '自動'))); ?></label>
                                </li>
                                <li>
                                    <small>※期間内で当選者を自動で振り分けます。</br>※当選者が10人未満の場合は手動をおすすめします。</small>
                                </li>
                                <li>
                                    <?php if ($this->ActionError && !$this->ActionError->isValid('winning_rate_1')): ?>
                                        <dd class="iconError1"><?php assign($this->ActionError->getMessage('winning_rate_1'))?></dd>
                                    <?php endif; ?>
                                    <label><?php write_html($this->formRadio('logic_type', PHPParser::ACTION_FORM, array($disable_public=>$disable_public), array(CpInstantWinActions::LOGIC_TYPE_RATE => '手動'))); ?></label>
                                    <?php write_html($this->formText('winning_rate_1', $result->winning_rate, array('id'=>'winning_rate', $disable=>$disable))); ?>%
                                    <small>（0.001～99.999%）</small><small id='expectedChallenge' data-count="<?php assign($result->max_winner_count); ?>">※約<?php assign(number_format($result->max_winner_count / $result->winning_rate * 100)); ?>回のチャレンジで当選者数に達する見込みです。</small>
                                </li>
                            </ul>
                        </dd>

                        <dt>チャレンジ回数</dt>
                        <dd class="moduleLotEstablish">
                            <ul class="moduleLotJoin">
                                <?php if ($this->ActionError && !$this->ActionError->isValid('time_value')): ?>
                                    <dd class="iconError1"><?php assign($this->ActionError->getMessage('time_value'))?></dd>
                                <?php endif; ?>
                                <li>
                                    <label><?php write_html($this->formRadio('once_flg', PHPParser::ACTION_FORM, array($disable_public=>$disable_public), array(InstantWinPrizes::ONCE_FLG_OFF => ' '), array(), " ")); ?></label>
                                    <?php write_html($this->formText('time_value', PHPParser::ACTION_FORM, array('id'=>'challengeTimeValue', $disable=>$disable))); ?>
                                    <?php write_html($this->formSelect('time_measurement', PHPParser::ACTION_FORM, array('id'=>'challengeTimeMeasurement', $disable=>$disable), CpInstantWinActions::$time_measurement_array)); ?>に1回参加
                                </li>
                                <li>
                                    <?php write_html($this->formRadio( 'once_flg', PHPParser::ACTION_FORM, array('class'=>'labelTitle', $disable_public=>$disable_public), array(InstantWinPrizes::ONCE_FLG_ON => '1回だけ参加可能'), array(), " ")); ?>
                                </li>
                            </ul>
                        </dd>
                    </dl>
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
        <p class="btn2"><a href="javascript:void(0)" class="small1" id="throughPreview">再生</a></p>
        <!-- /.modulePreviewHeader --></header>

    <ul class="tablink1" id="instantTab">
        <li class="current" id="animationTab"><span>抽選前</span></a></li>
        <li id="loseTab"><span>落選時</span></li>
        <li id="winTab"><span>当選時</span></li>
        <!-- /.tablink1 --></ul>

    <div class="displaySP jsModulePreviewArea">

        <section class="messageWrap" id="animationData"
                 data-ani="<?php assign($this->setVersion('/img/module/instantWin/animeLucky_draw1.gif'))?>"
                 data-img="<?php assign($this->setVersion('/img/module/instantWin/animeLucky_start1.gif'))?>"
                 data-lose="<?php assign($data['instant_win_prizes'][0]->image_url)?>"
                 data-win="<?php assign($data['instant_win_prizes'][1]->image_url)?>">

            <section class="message" id="animationPreview">
                <p class="messageImg" id="instantWinImage"><img src="<?php assign($this->setVersion('/img/module/instantWin/animeLucky_start1.gif'))?>"></p>
                <p class="messageText" id="textPreview"><?php write_html($this->toHalfContentDeeply($data['ActionForm']->text))?></p>
                <div class="messageFooter">
                    <ul class="btnSet">
                        <li class="btn3"><a href="javascript:void(0)" class="large1" id="btnPreview">チャレンジする</a></li>
                    </ul>
                </div>
                <!-- /.message --></section>

            <section class="message" style="display:none" id="losePreview">
                <p class="messageImg"><img src="<?php assign($data['instant_win_prizes'][0]->image_url)?>" width="600" height="300" id="loseImagePreview"></p>
                <p class="messageText" id="loseTextPreview"><?php write_html($this->toHalfContentDeeply($data['instant_win_prizes'][0]->text))?></p>
                <p class="messageLotNext">次回参加まであと<br><strong id="challengeTimes"></strong></p>
                <!-- /.message --></section>

            <section class="message" style="display:none" id="winPreview">
                <p class="messageImg"><img src="<?php assign($data['instant_win_prizes'][1]->image_url)?>" width="600" height="300" id="winImagePreview"></p>
                <p class="messageText" id="winTextPreview"><?php write_html($this->toHalfContentDeeply($data['instant_win_prizes'][1]->text))?></p>
                <!-- /.message --></section>

        </section>
    </div>

    <!-- /.modulePreview --></section>
