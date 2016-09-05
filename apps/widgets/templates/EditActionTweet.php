<?php if($data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
<?php else: ?>
    <?php $disable = ($data['action']->status == CpAction::STATUS_FIX) ? 'disabled' : '' ?>
<?php endif; ?>

<section class="moduleEdit1">
    <?php write_html($this->parseTemplate('CpActionModuleTitle.php', array('disable' =>$disable))); ?>
    <?php write_html($this->parseTemplate('CpActionModuleImage.php', array('disable'=>$disable))); ?>
    <?php write_html($this->parseTemplate('CpActionModuleText.php', array('disable'=>$disable))); ?>
    <section class="moduleCont1">
        <h1 class="editTweet1 jsModuleContTile">ツイート設定</h1>
        <div class="moduleSettingWrap jsModuleContTarget">
            <dl class="moduleSettingList">
                <dt class="moduleSettingTitle close jsModuleContTile">ツイート</dt>
                <dd class="moduleSettingDetail jsModuleContTarget">
                    <dl>
                        <dt>プリセット</dt>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('tweet_default_text')): ?>
                            <p class="iconError1"><?php assign ( $this->ActionError->getMessage('tweet_default_text') )?></p>
                        <?php endif; ?>
                        <dd>
                            <?php write_html( $this->formTextArea( 'tweet_default_text', $this->getActionFormValue('tweet_default_text'), array('maxlength'=>CpTweetAction::MAX_TEXT_LENGTH, 'cols'=>25, 'rows'=>10, 'id'=>'tweetDefaultText',$disable=>$disable))); ?>
                        </dd>
                        <dt>ツイートに付与するテキスト</dt>
                        <?php if ( $this->ActionError && !$this->ActionError->isValid('tweet_fixed_text')): ?>
                            <p class="iconError1"><?php assign ( $this->ActionError->getMessage('tweet_fixed_text') )?></p>
                        <?php endif; ?>
                        <dd>
                            <?php write_html( $this->formTextArea( 'tweet_fixed_text', $this->getActionFormValue('tweet_fixed_text'), array('maxlength'=>CpTweetAction::MAX_TEXT_LENGTH, 'cols'=>25, 'rows'=>10, 'placeholder'=>' #ハッシュタグ', 'class'=>'hashtag', 'id'=>'tweetFixedText', $disable=>$disable))); ?>
                        </dd>
                    </dl>
                    <span class="iconError1 jsTweetLengthError" style="display: none">入力した文字数は140を超えました。</span>
                    <p class="counter"><span class="attention1">140</span>/140</p>
                    <!-- /.moduleSettingDetail --></dd>
                <dt class="moduleSettingTitle close jsModuleContTile">画像投稿</dt>
                <dd class="moduleSettingDetail jsModuleContTarget">
                    <p><label><?php write_html( $this->formRadio( 'photo_flg', $this->getActionFormValue('photo_flg'), array($disable=>$disable, 'class'=>'photoFlg'), array('1' => '画像投稿を必須にする'), array(), "")); ?></label></p>
                    <p><label><?php write_html( $this->formRadio( 'photo_flg', $this->getActionFormValue('photo_flg'), array($disable=>$disable, 'class'=>'photoFlg'), array('0' => '画像投稿を任意にする'), array(), "")); ?></label></p>
                    <p><label><?php write_html( $this->formRadio( 'photo_flg', $this->getActionFormValue('photo_flg'), array($disable=>$disable, 'class'=>'photoFlg'), array('2' => '画像投稿フォームを非表示にする'), array(), "")); ?></label></p>
                    <!-- /.moduleSettingDetail --></dd>
                <dt class="moduleSettingTitle close jsModuleContTile">スキップ設定</dt>
                <dd class="moduleSettingDetail jsModuleContTarget">
                    <p><label><?php write_html( $this->formCheckBox( 'skip_flg', array($this->getActionFormValue('skip_flg')), array($disable=>$disable, 'class'=>'skipFlg'), array('1' => 'スキップを許可'))); ?></label><br><small>※ユーザーがツイートしなくても次に進めます</small></p>
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
            <section class="message">
                <p class="messageImg"><img src="" width="600" height="300" id="imagePreview"></p>
                <section class="messageText" id="textPreview"></section>

                <div class="messageTweet">
                    <p><small class="counter"><span class="attention1">140</span>/140</small></p>
                    <p class="tweetText">
                        <span class="iconError1 jsTweetLengthError" style="display: none">入力した文字数は140を超えました。</span>
                        <textarea placeholder="ツイート内容を入力してください。" style="pointer-events: none;"></textarea>
                        <span class="hashtag"></span>
                        <small class="supplement1">※ツイートに自動で追加されます</small>
                        <!-- /.tweetText --></p>
                    <!-- /.messageTweet --></div>
                <p class="module jsImageUploadForm" style="display: <?php assign($this->getActionFormValue('photo_flg') == CpTweetAction::PHOTO_OPTION_HIDE ? 'none' : 'block')?>">
                  <span class="fileUpload_img <?php assign($this->getActionFormValue('photo_flg') == CpTweetAction::PHOTO_REQUIRE ? 'require1' : '')?>">
                    <span class="thumb"></span><input type="file">
                    <small class="supplement1">※画像は最大4枚まで投稿できます。</small>
                  <!-- /.fileUpload_img --></span>
                            <!-- /.module --></p>

                <div class="messageFooter">
                    <ul class="btnSet">
                        <li class="btnTwTweet"><a href="javascript:void(0)"><?php assign($data['is_last_action'] ? 'ツイートする' : 'ツイートして次へ'); ?></a></li>
                        <!-- /.btnSet --></ul>
                    <p class="skip jsSkipLink" style="display: none;"><a href="javascript:void(0)"><small>ツイートせず次へ</small></a></p>
                </div>
            </section>
        </section>
    </div>
    <!-- /.modulePreview --></section>
