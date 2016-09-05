<?php if($data['is_fan_list_page']): ?>
    <?php $disable = '' ?>
<?php else : ?>
    <?php $disable = ($data['action']->status == CpAction::STATUS_FIX)?'disabled':'' ?>
<?php endif; ?>

<section class="moduleEdit1">

    <?php write_html($this->parseTemplate('CpActionModuleTitle.php', array('disable' =>$disable))); ?>
    <?php write_html($this->parseTemplate('CpActionModuleImage.php', array('disable'=>$disable))); ?>
    <?php write_html($this->parseTemplate('CpActionModuleText.php', array('disable'=>$disable))); ?>


    <?php write_html($this->formHidden('twitter_name', $data['concrete_action']->twitter_name)); ?>
    <?php write_html($this->formHidden('twitter_screen_name', $data['concrete_action']->twitter_screen_name)); ?>
    <?php write_html($this->formHidden('twitter_profile_image_url', $data['concrete_action']->twitter_profile_image_url)); ?>
    <?php write_html($this->formHidden('tweet_id', $data['concrete_action']->tweet_id)); ?>
    <?php write_html($this->formHidden('tweet_text', $data['concrete_action']->tweet_text)); ?>
    <?php write_html($this->formHidden('tweet_has_photo', $data['concrete_action']->tweet_has_photo)); ?>
    <?php write_html($this->formHidden('tweet_date', $data['concrete_action']->tweet_date)); ?>
    <?php write_html($this->formHidden('tweet_photos', $data['tweet_photos'] ? implode(',', $data['tweet_photos']) : ''))?>

    <section class="moduleCont1">
        <h1 class="editRetweet1 jsModuleContTile">リツイート設定</h1>
        <div class="moduleSettingWrap jsModuleContTarget">
            <dl class="moduleSettingList">
                <dt class="moduleSettingTitle close jsModuleContTile">リツート対象のツイートのURL</dt>
                <dd class="moduleSettingDetail jsModuleContTarget">
                    <p class="iconError1 jsRetweetErrorUrl" style="display: <?php assign(( $this->ActionError && !$this->ActionError->isValid('tweet_url') ? 'block' : 'none'))?>;">ツイートのURL形式で入力してください。</p>
                    <p><?php write_html( $this->formText( 'tweet_url', $this->getActionFormValue('tweet_url'), array('class' => 'jsSetupTweetUrl', 'maxlength'=>'512', $disable=>$disable))); ?></p>
                    <p><a href="javascript:void(0)" style="pointer-events: <?php assign($disable != '' ? 'none' : 'auto')?>" class="jsApplyTweetUrl" data-api_apply_tweet_url="<?php assign(Util::rewriteUrl('admin-cp', 'api_apply_tweet_url.json')); ?>">プレビューに反映</a></p>
                    <!-- /.moduleSettingDetail --></dd>
                <dt class="moduleSettingTitle close jsModuleContTile">スキップ設定</dt>
                <dd class="moduleSettingDetail jsModuleContTarget">
                    <p><label><?php write_html( $this->formCheckBox( 'skip_flg', array($this->getActionFormValue('skip_flg')), array($disable=>$disable, 'class'=>'jsSetupSkipFlg'), array('1' => 'スキップを許可'))); ?></label><br><small>※ユーザーがリツイートしなくても次に進めます</small></p>
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
                <div class="messageRetweet jsPreviewMessageRetweet" style="display: none;">
                    <p class="postAccount"><a href="javascript:void(0)"><img class="jsPreviewTwitterProfileImageUrl" src="<?php assign($data['concrete_action']->twitter_profile_image_url)?>" width="50" height="50" alt=""><span class="jsPreviewTwitterName"><?php assign($data['concrete_action']->twitter_name . '@' . $data['concrete_action']->twitter_screen_name)?></span></a>
                        <small class="timeLogo"><span class="iconTW2_2">Twitter</span></small>
                    </p>

                    <div class="postBody jsPreviewTweetBody">
                        <p class="text jsPreviewTweetText"><?php write_html($this->toHalfContentDeeply($data['concrete_action']->tweet_text))?></p>
                        <ul class="postImg jsPreviewPostImg">
                        <?php if($data['tweet_photos']) :?>
                                <?php if(count($data['tweet_photos']) == 1):?>
                                    <li class="sizeFull"><img src="<?php assign($data['tweet_photos'][0])?>" style="width: 100%"></li>
                                <?php elseif (count($data['tweet_photos']) == 2):?>
                                    <li class="sizeHalf"><img src="<?php assign($data['tweet_photos'][0])?>" style="height: 100%"></li>
                                    <li class="sizeHalf"><img src="<?php assign($data['tweet_photos'][1])?>" style="height: 100%"></li>
                                <?php elseif (count($data['tweet_photos']) == 3):?>
                                    <li class="sizeHalf"><img src="<?php assign($data['tweet_photos'][0])?>" style="height: 100%"></li>
                                    <li class="sizeQuarter"><img src="<?php assign($data['tweet_photos'][1])?>" style="width: 100%"></li>
                                    <li class="sizeQuarter"><img src="<?php assign($data['tweet_photos'][2])?>" style="width: 100%"></li>
                                <?php else:?>
                                    <li class="sizeQuarter"><img src="<?php assign($data['tweet_photos'][0])?>" style="width: 100%"></li>
                                    <li class="sizeQuarter"><img src="<?php assign($data['tweet_photos'][1])?>" style="width: 100%"></li>
                                    <li class="sizeQuarter"><img src="<?php assign($data['tweet_photos'][2])?>" style="width: 100%"></li>
                                    <li class="sizeQuarter"><img src="<?php assign($data['tweet_photos'][3])?>" style="width: 100%"></li>
                                <?php endif;?>
                        <?php endif; ?>
                            <!-- /.postImg --></ul>
                        <!-- /.postBody --></div>

                    <div class="postOption">
                        <p class="date"><small class="supplement1 jsPreviewTweetDate"><?php assign($data['concrete_action']->tweet_date)?></small></p>
                        <ul class="twActions">
                            <li><a href="javascript:void(0)" class="twFollow">フォローする</a></li>
                            <li><a href="javascript:void(0)" class="twReply">リプライ</a></li>
                            <li><a href="javascript:void(0)" class="twFavo">お気に入り</a></li>
                            <!-- /.twActions --></ul>
                        <!-- /.postOption --></div>
                    <!-- /.messageRetweet --></div>
                <ul class="btnSet">
                    <li class="btnTwRetweet"><a href="javascript:void(0)">リツイート</a></li>
                    <!-- /.btnSet --></ul>
                <p class="messageSkip" style="display: none;"><a href="javascript:void(0)"><small>リツイートせず次へ</small></a></p>
                <!-- /.message --></section>
        </section>
    </div>
    <!-- /.modulePreview --></section>