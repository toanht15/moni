<?php 
    $disabled = $data['search_social_account_interactive']['search_social_account_is_commented_count/'.$data['social_app_id'].'/'. $data['social_media_id'].'/'.CpCreateSqlService::LIKED] ? '' : 'disabled';
?>

<div class="sortBox jsAreaToggleTarget" data-search_type="<?php assign(CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.$data['social_app_id'].'/'. $data['social_media_id'])?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
    <p class="snsAccountName"><?php assign(Util::cutTextByWidth($data['social_account_name'],150)); ?></p>
    
            <ul>
            <li><label><input type="checkbox" class = "jscheckIsCommentCount" data-element_id = "<?php assign($data['social_app_id'] .'/'. $data['social_media_id'])?>" name='search_social_account_is_commented_count/<?php assign($data['social_app_id'] .'/'. $data['social_media_id'] .'/'.CpCreateSqlService::LIKED)?>' 
                        <?php assign($data['search_social_account_interactive']['search_social_account_is_commented_count/'.$data['social_app_id'].'/'. $data['social_media_id'].'/'.CpCreateSqlService::LIKED] ? 'checked' : '')?>>投稿にコメント有</label></li>
            <li><label><input type="checkbox" name='search_social_account_is_commented_count/<?php assign($data['social_app_id'] .'/'. $data['social_media_id'].'/'.CpCreateSqlService::NOT_LIKE)?>' 
                        <?php assign($data['search_social_account_interactive']['search_social_account_is_commented_count/'.$data['social_app_id'].'/'. $data['social_media_id'].'/'.CpCreateSqlService::NOT_LIKE] ? 'checked' : '')?>>投稿にコメント無</label></li>
            </ul>
            <div class="range">
                <p><span class="iconComment">コメント</span></p>
                <p>
                    <?php write_html($this->formText(
                        $data['search_fb_posts_comment_count_type_name'].'/'.$data['social_app_id'].'/'.$data['social_media_id'].'/from',
                        $this->POST ? PHPParser::ACTION_FORM : $data['search_social_account_interactive'][$data['search_fb_posts_comment_count_type_name'].'/'.$data['social_app_id'].'/'.$data['social_media_id'].'/from'],
                        array('maxlength'=>'20', 'class' => 'inputNum',$disabled => $disabled)
                    )); ?>回
                    <span class="dash">～</span>
                    <?php write_html($this->formText(
                        $data['search_fb_posts_comment_count_type_name'].'/'.$data['social_app_id'].'/'.$data['social_media_id'].'/to',
                        $this->POST ? PHPParser::ACTION_FORM : $data['search_social_account_interactive'][$data['search_fb_posts_comment_count_type_name'].'/'.$data['social_app_id'].'/'.$data['social_media_id'].'/to'],
                        array('maxlength'=>'20', 'class' => 'inputNum',$disabled => $disabled)
                    )); ?>回
                </p>
             <!-- /.range --></div>
          
    <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-sns_action_key = "<?php assign(CpCreateSqlService::SEARCH_FB_POSTS_COMMENT_COUNT); ?>" data-clear_type="<?php assign(CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.$data['social_app_id'].'/'. $data['social_media_id'])?>" data-search_no="<?php assign($this->search_no)?>">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign(CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.$data['social_app_id'].'/'. $data['social_media_id'])?>" data-search_no="<?php assign($this->search_no)?>">絞り込む</a></span>
    </p>
<!-- /.sortBox --></div>