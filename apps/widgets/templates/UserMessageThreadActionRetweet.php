<?php
$join_status = $data['message_info']['action_status']->status;
$retweet_failure = ($data['retweet_message']->retweeted == CpRetweetAction::POST_RETWEET && $data['message_info']['action_status']->status == CpUserActionStatus::NOT_JOIN);
if ($join_status == CpUserActionStatus::JOIN){
    $disabled = 'disabled';
} else {
    $disabled = '';
}
?>
    <section class="message jsMessage" id="message_<?php assign($data['message_info']['message']->id); ?>">
        <a id="<?php assign('ca_' . $data["message_info"]["cp_action"]->id) ?>"></a>

        <form class="executeRetweetActionForm" action="<?php assign(Util::rewriteUrl('messages', "api_pre_execute_retweet_action.json")); ?>"
              data-execute-url="<?php assign(Util::rewriteUrl('messages', "api_execute_retweet_action.json")); ?>"
              data-redirect-url="<?php assign(Util::rewriteUrl( 'auth', 'campaign_login', '', array('platform' => 'tw', 'redirect_url' => urlencode(Util::getCurrentUrl()), 'cp_id'=>$data['cp_user']->cp_id))) ?>"
              data-retweet-failure="<?php assign($retweet_failure ? 1 : 0)?>"
              method="POST" enctype="multipart/form-data">
            <?php write_html($this->csrf_tag()); ?>
            <?php write_html($this->formHidden('cp_gift_action_id', $data['message_info']["concrete_action"]->id)); ?>
            <?php write_html($this->formHidden('cp_action_id', $data['message_info']["cp_action"]->id)); ?>
            <?php write_html($this->formHidden('cp_user_id', $data['cp_user']->id)); ?>
            <?php write_html($this->formHidden('brand_id', $data['pageStatus']['brand']->id));?>

            <?php if($data["message_info"]["concrete_action"]->image_url): ?>
                <p class="messageImg"><img src="<?php assign($data['message_info']['concrete_action']->image_url); ?>" alt="campaign img"></p>
            <?php endif; ?>
            <?php $message_text = $data['message_info']['concrete_action']->html_content ? $data['message_info']['concrete_action']->html_content : $this->toHalfContentDeeply($data['message_info']['concrete_action']->text); ?>
            <section class="messageText"><?php write_html($message_text); ?></section>

            <div class="messageRetweet">
                <p class="postAccount"><a href="//twitter.com/<?php assign($data['message_info']['concrete_action']->twitter_screen_name)?>" target="_blank"><img src="<?php assign($data['message_info']['concrete_action']->twitter_profile_image_url)?>" width="50" height="50" alt=""><span><?php assign($data['message_info']['concrete_action']->twitter_name . '@' . $data['message_info']['concrete_action']->twitter_screen_name)?></span></a>
                    <small class="timeLogo"><span class="iconTW2_2">Twitter</span></small>
                </p>

                <div class="postBody">
                    <p class="text"><?php write_html($this->toHalfContentDeeply($data['message_info']['concrete_action']->tweet_text))?></p>
                    <ul class="postImg">
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
                    <p class="date"><small class="supplement1"><?php assign($data['message_info']['concrete_action']->tweet_date)?></small></p>
                    <ul class="twActions">
                        <li><a href="//twitter.com/intent/follow?screen_name=<?php assign($data['message_info']['concrete_action']->twitter_screen_name)?>" class="twFollow">フォローする</a></li>
                        <li><a href="//twitter.com/intent/tweet?in_reply_to=<?php assign($data['message_info']['concrete_action']->tweet_id)?>" class="twReply">リプライ</a></li>
                        <li><a href="//twitter.com/intent/favorite?tweet_id=<?php assign($data['message_info']['concrete_action']->tweet_id)?>" class="twFavo">お気に入り</a></li>
                        <!-- /.twActions --></ul>
                    <!-- /.postOption --></div>
                <!-- /.messageRetweet --></div>
            <ul class="btnSet">
                <li class="btnTwRetweet jsRetweetBtnElement">
                    <?php if(!$join_status): ?>
                        <a class="cmd_execute_retweet_action" href="javascript:void(0)">リツイート</a>
                    <?php else: ?>
                        <span class="middle1 cmd_execute_retweet_action">リツイート</span>
                    <?php endif; ?>
                </li>
                <!-- /.btnSet --></ul>
            <?php if ($data['message_info']['concrete_action']->skip_flg && !$join_status):?>
                <p class="messageSkip"><a href="javascript:void(0)" style="pointer-events: <?php assign($join_status?'none':'auto')?>"><small>リツイートせず次へ</small></a></p>
            <?php endif;?>

        </form>
        <!-- /.message --></section>
<?php write_html($this->scriptTag("user/UserActionRetweetService")); ?>