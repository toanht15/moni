<div class="sortBox jsAreaToggleTarget" data-search_type="<?php assign(CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.$data['social_app_id'].'/'. $data['social_media_id'])?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
    <p class="snsAccountName"><?php assign(Util::cutTextByWidth($data['social_account_name'],150)); ?></p>
    
        <?php if($data['social_app_id'] == SocialApps::PROVIDER_FACEBOOK):?>
            <ul>
            <li><label><input type="checkbox" name='search_social_account_interactive/<?php assign($data['social_app_id'] .'/'. $data['social_media_id'] .'/'.CpCreateSqlService::LIKED)?>' <?php assign($data['search_social_account_interactive']['search_social_account_interactive/'.$data['social_app_id'].'/'. $data['social_media_id'].'/'.CpCreateSqlService::LIKED] ? 'checked' : '')?>>Facebookページにいいね！有</label></li>
            <li><label><input type="checkbox" name='search_social_account_interactive/<?php assign($data['social_app_id'] .'/'. $data['social_media_id'].'/'.CpCreateSqlService::NOT_LIKE)?>' <?php assign($data['search_social_account_interactive']['search_social_account_interactive/'.$data['social_app_id'].'/'. $data['social_media_id'].'/'.CpCreateSqlService::NOT_LIKE] ? 'checked' : '')?>>Facebookページにいいね！無</label></li>
            </ul>
            
        <?php elseif($data['social_app_id'] == SocialApps::PROVIDER_TWITTER):?>
            <ul>
            <li><label><input type="checkbox" name='search_social_account_interactive/<?php assign($data['social_app_id'] .'/'. $data['social_media_id'] .'/'.CpCreateSqlService::FOLLOWED)?>' <?php assign($data['search_social_account_interactive']['search_social_account_interactive/'.$data['social_app_id'].'/'. $data['social_media_id'].'/'.CpCreateSqlService::FOLLOWED] ? 'checked' : '')?>>Twitterフォロー有</label></li>
            <li><label><input type="checkbox" name='search_social_account_interactive/<?php assign($data['social_app_id'] .'/'. $data['social_media_id'].'/'.CpCreateSqlService::NOT_FOLLOW)?>' <?php assign($data['search_social_account_interactive']['search_social_account_interactive/'.$data['social_app_id'].'/'. $data['social_media_id'].'/'.CpCreateSqlService::NOT_FOLLOW] ? 'checked' : '')?>>Twitterフォロー無</label></li>
            </ul>
        <?php else:?>
        <?php endif;?>
    <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-sns_action_key="<?php assign(CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE); ?>" data-clear_type="<?php assign(CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.$data['social_app_id'].'/'. $data['social_media_id'])?>" data-search_no="<?php assign($this->search_no)?>">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign(CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE.'/'.$data['social_app_id'].'/'. $data['social_media_id'])?>" data-search_no="<?php assign($this->search_no)?>">絞り込む</a></span>
    </p>
<!-- /.sortBox --></div>