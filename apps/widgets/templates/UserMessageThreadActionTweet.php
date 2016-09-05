<?php
$join_status = $data['message_info']['action_status']->status;
$posted_tweet = ($data['tweet_message']->tweet_text != '' && $data['tweet_message']->tweet_content_url != '' && $data['message_info']['action_status']->status == CpUserActionStatus::NOT_JOIN);
$join_status = $join_status || $posted_tweet;
if ($join_status == CpUserActionStatus::JOIN){
    $disabled = 'disabled';
} else {
    $disabled = '';
}
?>
<section class="<?php if (!$data['is_skip_action']): ?>message <?php endif ?>inview jsMessage" id="message_<?php assign($data['message_info']['message']->id); ?>">
    <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

    <form class="executeTweetActionForm" action="<?php assign(Util::rewriteUrl('messages', "api_pre_execute_tweet_action.json")); ?>"
          data-execute-url="<?php assign(Util::rewriteUrl('messages', "api_execute_tweet_action.json")); ?>"
          data-redirect-url="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'tw', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id'=>$data['cp_user']->cp_id))) ?>"
          data-posted-tweet="<?php assign($posted_tweet ? $data['tweet_message']->tweet_content_url : '')?>"
          method="POST" enctype="multipart/form-data">
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('cp_gift_action_id', $data['message_info']["concrete_action"]->id)); ?>
        <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
        <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>
        <?php write_html($this->formHidden('brand_id', $data['pageStatus']['brand']->id));?>

        <?php if(!$data['is_skip_action']): ?>
            <?php if($data["message_info"]["concrete_action"]->image_url): ?>
                <p class="messageImg"><img src="<?php assign($data['message_info']['concrete_action']->image_url); ?>" alt="campaign img"></p>
            <?php endif; ?>
            <?php $message_text = $data['message_info']['concrete_action']->html_content ? $data['message_info']['concrete_action']->html_content : $this->toHalfContentDeeply($data['message_info']['concrete_action']->text); ?>
            <section class="messageText"><?php write_html($message_text); ?></section>

            <div class="messageTweet">
                <p style="display: <?php assign((!$join_status || $data['tweet_message']->skipped) ? 'block' : 'none');?>"><small class="counter"><span class="attention1 jsRemainingCharacters">140</span>/140</small></p>
                <p class="tweetText">
                    <?php if ($join_status && !$data['tweet_message']->skipped): ?>
                        <span class="postText"><?php write_html($this->toHalfContentDeeply($data['tweet_message']->tweet_text))?><br><?php write_html($this->toHalfContentDeeply($data['message_info']['concrete_action']->tweet_fixed_text))?></span>
                        <?php foreach($data['tweet_photos'] as $tweet_photo): ?>
                            <img src="<?php assign($tweet_photo->image_url)?>" width="41" height="41" alt="">
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="iconError1 jsRemainingCharacterInvalid" style="display: none">入力した文字数は140を超えました。</span>
                        <?php write_html( $this->formTextArea( 'tweet_default_text', $data['message_info']['concrete_action']->tweet_default_text, array('class'=>'jsTweetDefaultText','placeholder'=>'ツイート内容を入力してください。', $disabled=>$disabled))); ?>
                        <?php if($data['message_info']['concrete_action']->tweet_fixed_text):?>
                            <span class="hashtag jsTweetFixedText"><?php write_html($this->toHalfContentDeeply($data['message_info']['concrete_action']->tweet_fixed_text));?></span>
                            <small class="supplement1">※ツイートに自動で追加されます</small>
                        <?php endif;?>
                    <?php endif; ?>
                    <!-- /.tweetText --></p>
                <!-- /.messageTweet --></div>
            <?php if(!$join_status || $data['tweet_message']->skipped): ?>
                <?php if($data['cp_user']->cp_id != '3343'): ?>
                    <p class="module" style="display: <?php assign($data['message_info']['concrete_action']->photo_flg == CpTweetAction::PHOTO_OPTION_HIDE ? 'none' : 'block')?>">
                        <?php if ($data['message_info']['concrete_action']->photo_flg == CpTweetAction::PHOTO_REQUIRE):?>
                            <span class="iconError1 jsRequirePhoto" style="display: none">画像を必ずアップロードしてください。</span>
                        <?php endif;?>
                        <span class="fileUpload_img <?php assign($data['message_info']['concrete_action']->photo_flg == CpTweetAction::PHOTO_REQUIRE ? 'require1' : '')?>">
                        <span id="fileUploadList"><span><span class="thumb" id="thumb_1"></span><input type="file" name="tweet_photo_upload_1" class="photo_upload" id="1" <?php if ($join_status):?>disabled="disabled"<?php endif;?>></span></span>
                        <small class="supplement1">※画像 (上限3MB) を最大4枚まで投稿できます。</small>
                    <!-- /.fileUpload_img --></span>
                        <!-- /.module --></p>
                <?php endif; ?>
            <?php endif; ?>

            <div class="messageFooter">
                <ul class="btnSet">
                    <li class="btnTwTweet jsTweetBtnElement">
                        <?php if (!$join_status):?>
                            <a class="cmd_execute_tweet_action" href="javascript:void(0)"><?php assign($data['is_last_action'] ? 'ツイートする' : 'ツイートして次へ'); ?></a>
                        <?php else:?>
                            <span class="middle1 cmd_execute_tweet_action"><?php assign($data['is_last_action'] ? 'ツイートする' : 'ツイートして次へ'); ?></span>
                        <?php endif;?>
                    </li>
                    <!-- /.btnSet --></ul>
                <div class="uploadCont" style="display: <?php assign($join_status && !$data['tweet_message']->skipped ? 'block' : 'none' )?>">
                    <p>あなたのツイート<br><a href="<?php assign($data['tweet_message']->tweet_content_url)?>" class="openNewWindow1 jsTweetContentUrl" target="_blank"><?php assign($data['tweet_message']->tweet_content_url)?></a></p>
                    <!-- /.uploadCont --></div>
                <?php if ($data['message_info']['concrete_action']->skip_flg && !$join_status):?>
                    <p class="skip" id="twSkipBtn"><a href="javascript:void(0)" style="pointer-events: <?php assign($join_status?'none':'auto')?>"><small>ツイートせず次へ</small></a></p>
                <?php endif;?>
            </div>
        <?php endif; ?>

        <?php if($data['auto_skip_action']): ?>
            <div class="cmd_auto_execute_skip_tweet_action" data-messageid="<?php assign($data['message_info']["message"]->id); ?>"></div>
        <?php endif; ?>
    </form>
<!-- /.message --></section>
<?php write_html($this->scriptTag("user/UserActionTweetService")); ?>