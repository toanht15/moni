<div class="sortBox jsAreaToggleTarget jsCheckToggleWrap" data-search_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.$data['social_media_type'])?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
    <?php if($data['social_media_type'] == SocialAccountService::SOCIAL_MEDIA_FACEBOOK):?>
        <ul class="order">
            <li><a href="javascript:void(0)" data-order="<?php assign(CpUserListService::ORDER_ASC)?>">[A-Z↓] (友達数)昇順</a></li>
            <li><a href="javascript:void(0)" data-order="<?php assign(CpUserListService::ORDER_DESC)?>">[Z-A↑] (友達数)降順</a></li>
            <!-- /.order --></ul>
    <?php endif; ?>
    <?php if($data['social_media_type'] == SocialAccountService::SOCIAL_MEDIA_TWITTER || $data['social_media_type'] == SocialAccountService::SOCIAL_MEDIA_INSTAGRAM):?>
        <ul class="order">
            <li><a href="javascript:void(0)" data-order="<?php assign(CpUserListService::ORDER_ASC)?>">[A-Z↓] (フォロワー数)昇順</a></li>
            <li><a href="javascript:void(0)" data-order="<?php assign(CpUserListService::ORDER_DESC)?>">[Z-A↑] (フォロワー数)降順</a></li>
            <!-- /.order --></ul>
    <?php endif; ?>

    <ul>
        <li>
            <?php $is_link = 'search_social_account/' . $data['social_media_type'] . '/' . CpCreateSqlService::LINK_SNS;?>
            <?php $not_link = 'search_social_account/' . $data['social_media_type'] . '/' . CpCreateSqlService::NOT_LINK_SNS;?>
            <?php write_html($this->formCheckbox(
                $is_link . '/' . $this->search_no,
                $this->POST ? PHPParser::ACTION_FORM : $data['search_social_account']['social_media_type'],
                array('checked' => $data['search_social_account'][$is_link] ? "checked" : "", 'class' => 'jsCheckToggle'),
                array('1' => '連携済み')
            ))?>
        </li>
        <li>
            <?php write_html($this->formCheckbox(
                $not_link . '/' . $this->search_no,
                $this->POST ? PHPParser::ACTION_FORM : $data['search_social_account']['social_media_type'],
                array('checked' => $data['search_social_account'][$not_link] ? "checked" : "", 'class' => 'jsNotToggleTarget'),
                array('1' => '未連携')
            ))?>
        </li>
    </ul>
    <?php if($data['social_media_type'] == SocialAccountService::SOCIAL_MEDIA_FACEBOOK):?>
        <div class="range jsCheckToggleTarget" style=<?php assign($data['search_social_account'][$is_link] ? "" : "display: none")?>>
            <p>友達数</p>
            <p>
                <?php write_html($this->formText(
                    'search_friend_count_from/' . $data['social_media_type'],
                    $this->POST ? PHPParser::ACTION_FORM : $data['search_social_account']['search_friend_count_from/'.$data['social_media_type']],
                    array('class'=>'inputNum')
                )); ?>人
                <span class="dash">～</span>
                <?php write_html($this->formText(
                    'search_friend_count_to/' . $data['social_media_type'],
                    $this->POST ? PHPParser::ACTION_FORM : $data['search_social_account']['search_friend_count_to/'.$data['social_media_type']],
                    array('class'=>'inputNum')
                )); ?>人
            </p>
            <!-- /.range --></div>
    <?php endif; ?>
    <?php if($data['social_media_type'] == SocialAccountService::SOCIAL_MEDIA_TWITTER || $data['social_media_type'] == SocialAccountService::SOCIAL_MEDIA_INSTAGRAM):?>
        <div class="range jsCheckToggleTarget"  style=<?php assign($data['search_social_account'][$is_link] ? "" : "display: none")?>>
            <p>フォロワー数</p>
            <p>
                <?php write_html($this->formText(
                    'search_friend_count_from/' . $data['social_media_type'],
                    $this->POST ? PHPParser::ACTION_FORM : $data['search_social_account']['search_friend_count_from/'.$data['social_media_type']],
                    array('class'=>'inputNum')
                )); ?>人
                <span class="dash">～</span>
                <?php write_html($this->formText(
                    'search_friend_count_to/' . $data['social_media_type'],
                    $this->POST ? PHPParser::ACTION_FORM : $data['search_social_account']['search_friend_count_to/'.$data['social_media_type']],
                    array('class'=>'inputNum')
                )); ?>人
            </p>
            <!-- /.range --></div>
    <?php endif; ?>
    <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-clear_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.$data['social_media_type'])?>" data-search_no="<?php assign($this->search_no)?>">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT.'/'.$data['social_media_type'])?>" data-search_no="<?php assign($this->search_no)?>">絞り込む</a></span>
    </p>
    <!-- /.sortBox --></div>
