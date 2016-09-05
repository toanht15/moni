<div class="customaudienceRefinement jsModuleContWrap">
    <div class="categoryLabel jsModuleContTile close">
        <p><span class="iconFB2">Facebook</span>Facebookアクション</p>
        <!-- /.categoryLabel --></div>
    <div class="refinementWrap jsModuleContTarget close">
        <div class="setting">
            <?php foreach($data['facebook_accounts'] as $facebook_account): ?>
                <?php  
                    $disabled = $data['search_social_account_interactive']['search_social_account_is_liked_count/'.$data['social_app_id'].'/'. $facebook_account->social_media_account_id.'/'.CpCreateSqlService::LIKED] ? '' : 'disabled';
                ?>
                <div class="refinementItem">
                    <form>
                        <?php write_html($this->formHidden("search_type", CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.SocialApps::PROVIDER_FACEBOOK.'/'. $facebook_account->social_media_account_id)) ?>
                        <p class="settingLabel"><?php assign(Util::cutTextByWidth($facebook_account->name,300)); ?></p>
                        <p class="settingDetail">
                        <ul>
                            <li><label><input type="checkbox" name='search_social_account_interactive/<?php assign(SocialApps::PROVIDER_FACEBOOK .'/'. $facebook_account->social_media_account_id .'/'.CpCreateSqlService::LIKED)?>' >Facebookページにいいね！有</label></li>
                            <li><label><input type="checkbox" name='search_social_account_interactive/<?php assign(SocialApps::PROVIDER_FACEBOOK .'/'. $facebook_account->social_media_account_id .'/'.CpCreateSqlService::NOT_LIKE)?>' >Facebookページにいいね！無</label></li>
                        </ul>
                        <ul>
                            <li><label><input type="checkbox" class = "jscheckIsLikeCount" data-element_id = "<?php assign($data['social_app_id'] .'/'. $facebook_account->social_media_account_id)?>" name='search_social_account_is_liked_count/<?php assign($data['social_app_id'] .'/'. $facebook_account->social_media_account_id .'/'.CpCreateSqlService::LIKED)?>' 
                                            <?php assign($data['search_social_account_interactive']['search_social_account_is_liked_count/'.$data['social_app_id'].'/'. $facebook_account->social_media_account_id.'/'.CpCreateSqlService::LIKED] ? 'checked' : '')?>>投稿にいいね！有</label></li>
                            <li><label><input type="checkbox" name='search_social_account_is_liked_count/<?php assign($data['social_app_id'] .'/'. $facebook_account->social_media_account_id.'/'.CpCreateSqlService::NOT_LIKE)?>' 
                                            <?php assign($data['search_social_account_interactive']['search_social_account_is_liked_count/'.$data['social_app_id'].'/'. $facebook_account->social_media_account_id.'/'.CpCreateSqlService::NOT_LIKE] ? 'checked' : '')?>>投稿にいいね！無</label></li>
                        </ul>
                        <div class="range">
                            <p><span class="iconLike">いいね</span></p>
                            <p>
                                <?php write_html($this->formText(
                                    $data['search_fb_posts_like_count_type_name'].'/'.$data['social_app_id'].'/'.$facebook_account->social_media_account_id.'/from',
                                    $this->POST ? PHPParser::ACTION_FORM : $data['search_social_account_interactive'][$data['search_fb_posts_like_count_type_name'].'/'.$data['social_app_id'].'/'.$facebook_account->social_media_account_id.'/from'],
                                    array('maxlength'=>'20', 'class' => 'inputNum', $disabled=>$disabled)
                                )); ?>回
                                <span class="dash">～</span>
                                <?php write_html($this->formText(
                                    $data['search_fb_posts_like_count_type_name'].'/'.$data['social_app_id'].'/'.$facebook_account->social_media_account_id.'/to',
                                    $this->POST ? PHPParser::ACTION_FORM : $data['search_social_account_interactive'][$data['search_fb_posts_like_count_type_name'].'/'.$data['social_app_id'].'/'.$facebook_account->social_media_account_id.'/to'],
                                    array('maxlength'=>'20', 'class' => 'inputNum', $disabled=>$disabled)
                                )); ?>回
                            </p>
                            <!-- /.range --></div>
                        <ul>
                            <li><label><input type="checkbox" class = "jscheckIsCommentCount" data-element_id = "<?php assign($data['social_app_id'] .'/'. $facebook_account->social_media_account_id)?>" name='search_social_account_is_commented_count/<?php assign($data['social_app_id'] .'/'. $facebook_account->social_media_account_id .'/'.CpCreateSqlService::LIKED)?>' 
                                        <?php assign($data['search_social_account_interactive']['search_social_account_is_commented_count/'.$data['social_app_id'].'/'. $facebook_account->social_media_account_id.'/'.CpCreateSqlService::LIKED] ? 'checked' : '')?>>投稿にコメント有</label></li>
                            <li><label><input type="checkbox" name='search_social_account_is_commented_count/<?php assign($data['social_app_id'] .'/'. $facebook_account->social_media_account_id.'/'.CpCreateSqlService::NOT_LIKE)?>' 
                                        <?php assign($data['search_social_account_interactive']['search_social_account_is_commented_count/'.$data['social_app_id'].'/'. $facebook_account->social_media_account_id.'/'.CpCreateSqlService::NOT_LIKE] ? 'checked' : '')?>>投稿にコメント無</label></li>
                        </ul>
                        <div class="range">
                            <p><span class="iconComment">コメント</span></p>
                            <p>
                                <?php write_html($this->formText(
                                    $data['search_fb_posts_comment_count_type_name'].'/'.$data['social_app_id'].'/'.$facebook_account->social_media_account_id.'/from',
                                    $this->POST ? PHPParser::ACTION_FORM : $data['search_social_account_interactive'][$data['search_fb_posts_comment_count_type_name'].'/'.$data['social_app_id'].'/'.$facebook_account->social_media_account_id.'/from'],
                                    array('maxlength'=>'20', 'class' => 'inputNum', $disabled=>$disabled)
                                )); ?>回
                                <span class="dash">～</span>
                                <?php write_html($this->formText(
                                $data['search_fb_posts_comment_count_type_name'].'/'.$data['social_app_id'].'/'.$facebook_account->social_media_account_id.'/to',
                                    $this->POST ? PHPParser::ACTION_FORM : $data['search_social_account_interactive'][$data['search_fb_posts_comment_count_type_name'].'/'.$data['social_app_id'].'/'.$facebook_account->social_media_account_id.'/to'],
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