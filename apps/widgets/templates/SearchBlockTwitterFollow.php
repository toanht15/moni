<div class="customaudienceRefinement jsModuleContWrap">
    <div class="categoryLabel jsModuleContTile close">
        <p><span class="iconTW2">Twitter</span>Twitterアクション</p>
        <!-- /.categoryLabel --></div>
    <div class="refinementWrap jsModuleContTarget close">
        <div class="setting">
            <?php foreach($data['twitter_accounts'] as $twitter_account): ?>
                <?php  
                    $disabled = $data['search_social_account_interactive']['search_social_account_is_liked_count/'.$data['social_app_id'].'/'. $twitter_account->social_media_account_id.'/'.CpCreateSqlService::LIKED] ? '' : 'disabled';
                ?>
                <div class="refinementItem">
                    <form>
                        <?php write_html($this->formHidden("search_type", CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_TWITTER.'/'. $twitter_account->social_media_account_id)) ?>
                        <p class="settingLabel"><?php assign(Util::cutTextByWidth($twitter_account->name,300)); ?></p>
                        <p class="settingDetail">
                        <ul>
                            <li><label><input type="checkbox" name='search_social_account_interactive/<?php assign(SocialApps::PROVIDER_TWITTER .'/'. $twitter_account->social_media_account_id .'/'.CpCreateSqlService::LIKED)?>' >Twitterフォロー有</label></li>
                            <li><label><input type="checkbox" name='search_social_account_interactive/<?php assign(SocialApps::PROVIDER_TWITTER .'/'. $twitter_account->social_media_account_id .'/'.CpCreateSqlService::NOT_LIKE)?>' >Twitterフォロー無</label></li>
                         </ul>
                        <ul>
                            <li><label><input type="checkbox" class = "jscheckIsRetweetCount" data-element_id = "<?php assign($data['social_app_id'] .'/'. $twitter_account->social_media_account_id)?>" name='search_social_account_is_retweeted_count/<?php assign($data['social_app_id'] .'/'. $twitter_account->social_media_account_id .'/'.CpCreateSqlService::LIKED)?>' 
                                            <?php assign($data['search_social_account_interactive']['search_social_account_is_retweeted_count/'.$data['social_app_id'].'/'. $twitter_account->social_media_account_id.'/'.CpCreateSqlService::LIKED] ? 'checked' : '')?>>リツイート有</label></li>
                            <li><label><input type="checkbox" name='search_social_account_is_retweeted_count/<?php assign($data['social_app_id'] .'/'. $twitter_account->social_media_account_id.'/'.CpCreateSqlService::NOT_LIKE)?>' 
                                            <?php assign($data['search_social_account_interactive']['search_social_account_is_retweeted_count/'.$data['social_app_id'].'/'. $twitter_account->social_media_account_id.'/'.CpCreateSqlService::NOT_LIKE] ? 'checked' : '')?>>リツイート無</label></li>
                        </ul>
                        <div class="range">
                            <p><span class="iconRetweet">リツイート</span></p>
                            <p>
                                <?php write_html($this->formText(
                                    $data['search_tw_tweet_retweet_count_type_name'].'/'.$data['social_app_id'].'/'.$twitter_account->social_media_account_id.'/from',
                                    $this->POST ? PHPParser::ACTION_FORM : $data['search_social_account_interactive'][$data['search_tw_tweet_retweet_count_type_name'].'/'.$data['social_app_id'].'/'.$twitter_account->social_media_account_id.'/from'],
                                    array('maxlength'=>'20', 'class' => 'inputNum', $disabled=>$disabled)
                                )); ?>回
                                <span class="dash">～</span>
                                <?php write_html($this->formText(
                                    $data['search_tw_tweet_retweet_count_type_name'].'/'.$data['social_app_id'].'/'.$twitter_account->social_media_account_id.'/to',
                                    $this->POST ? PHPParser::ACTION_FORM : $data['search_social_account_interactive'][$data['search_tw_tweet_retweet_count_type_name'].'/'.$data['social_app_id'].'/'.$twitter_account->social_media_account_id.'/to'],
                                    array('maxlength'=>'20', 'class' => 'inputNum', $disabled=>$disabled)
                                )); ?>回
                            </p>
                            <!-- /.range --></div>
                        <ul>
                            <li><label><input type="checkbox" class = "jscheckIsReplyCount" data-element_id = "<?php assign($data['social_app_id'] .'/'. $twitter_account->social_media_account_id)?>" name='search_social_account_is_replied_count/<?php assign($data['social_app_id'] .'/'. $twitter_account->social_media_account_id .'/'.CpCreateSqlService::LIKED)?>' 
                                        <?php assign($data['search_social_account_interactive']['search_social_account_is_replied_count/'.$data['social_app_id'].'/'. $twitter_account->social_media_account_id.'/'.CpCreateSqlService::LIKED] ? 'checked' : '')?>>リプライ有</label></li>
                            <li><label><input type="checkbox" name='search_social_account_is_replied_count/<?php assign($data['social_app_id'] .'/'. $twitter_account->social_media_account_id.'/'.CpCreateSqlService::NOT_LIKE)?>' 
                                        <?php assign($data['search_social_account_interactive']['search_social_account_is_replied_count/'.$data['social_app_id'].'/'. $twitter_account->social_media_account_id.'/'.CpCreateSqlService::NOT_LIKE] ? 'checked' : '')?>>リプライ無</label></li>
                        </ul>
                        <div class="range">
                            <p><span class="iconReply">リプライ</span></p>
                            <p>
                                <?php write_html($this->formText(
                                    $data['search_tw_tweet_reply_count_type_name'].'/'.$data['social_app_id'].'/'.$twitter_account->social_media_account_id.'/from',
                                    $this->POST ? PHPParser::ACTION_FORM : $data['search_social_account_interactive'][$data['search_tw_tweet_reply_count_type_name'].'/'.$data['social_app_id'].'/'.$twitter_account->social_media_account_id.'/from'],
                                    array('maxlength'=>'20', 'class' => 'inputNum', $disabled=>$disabled)
                                )); ?>回
                                <span class="dash">～</span>
                                <?php write_html($this->formText(
                                $data['search_tw_tweet_reply_count_type_name'].'/'.$data['social_app_id'].'/'.$twitter_account->social_media_account_id.'/to',
                                    $this->POST ? PHPParser::ACTION_FORM : $data['search_social_account_interactive'][$data['search_tw_tweet_reply_count_type_name'].'/'.$data['social_app_id'].'/'.$twitter_account->social_media_account_id.'/to'],
                                    array('maxlength'=>'20', 'class' => 'inputNum', $disabled=>$disabled)
                                )); ?>回
                            </p>
                         <!-- /.range --></div>
                        <!-- /.settingDetail --></p>
                    </form>
                    <!-- /.refinementItem --></div>
            <?php endforeach; ?>
            <!-- /.setting --></div>
        <!-- /.refinementWrap --></div>
    <!-- /.customaudiencRefinement --></div>