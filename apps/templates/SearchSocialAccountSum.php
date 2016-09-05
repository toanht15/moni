<div class="sortBox jsAreaToggleTarget" data-search_type="<?php assign(CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_SUM)?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
    <ul class="order">
        <li><a href="javascript:void(0)" data-order="<?php assign(CpUserListService::ORDER_ASC)?>">[A-Z↓] 友達数昇順</a></li>
        <li><a href="javascript:void(0)" data-order="<?php assign(CpUserListService::ORDER_DESC)?>">[Z-A↑] 友達数降順</a></li>
    <!-- /.order --></ul>
    <div class="range">
        <p>友達数・フォロワー数</p>
        <p>
            <?php write_html($this->formText(
                'search_friend_count_sum_from',
                $this->POST ? PHPParser::ACTION_FORM : $data['search_social_sum_count']['search_friend_count_sum_from'],
                array('class'=>'inputFriends')
            )); ?>
            <span class="dash">～</span>
            <?php write_html($this->formText(
                'search_friend_count_sum_to',
                    $this->POST ? PHPParser::ACTION_FORM : $data['search_social_sum_count']['search_friend_count_sum_to'],
                array('class'=>'inputFriends')
            )); ?>
        </p>
    <!-- /.range --></div>
    <div class="range">
        <p>連携済みSNS数</p>
        <p>
            <?php write_html($this->formText(
                'search_link_sns_count_from',
                $this->POST ? PHPParser::ACTION_FORM : $data['search_social_sum_count']['search_link_sns_count_from'],
                array('class'=>'inputFriends')
            )); ?>
            <span class="dash">～</span>
            <?php write_html($this->formText(
                'search_link_sns_count_to',
                $this->POST ? PHPParser::ACTION_FORM : $data['search_social_sum_count']['search_link_sns_count_to'],
                array('class'=>'inputFriends')
            )); ?>
        </p>
    <!-- /.range --></div>
    <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-clear_type="<?php assign(CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_SUM)?>" data-search_no="<?php assign($this->search_no)?>">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign(CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_SUM)?>" data-search_no="<?php assign($this->search_no)?>">絞り込む</a></span>
    </p>
<!-- /.sortBox --></div>
