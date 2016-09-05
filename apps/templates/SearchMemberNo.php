<div class="sortBox jsAreaToggleTarget" data-search_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_MEMBER_NO)?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
    <ul class="order">
        <li><a href="javascript:void(0)" data-order="<?php assign(CpUserListService::ORDER_ASC)?>">[A-Z↓] 昇順</a></li>
        <li><a href="javascript:void(0)" data-order="<?php assign(CpUserListService::ORDER_DESC)?>">[Z-A↑] 降順</a></li>
    <!-- /.order --></ul>
    <p>
        <?php write_html($this->formTextArea(
            'search_profile_member_no_from',
            $this->POST ? PHPParser::ACTION_FORM : $data['search_profile_member_no']['search_profile_member_no_from'],
            array('placeholder'=>'No.', 'class'=>'pluralItems jsReplaceLbComma', 'flg' => $data['flg'])
        )); ?>
        <small class="supplement1">※カンマ/改行区切りで複数指定可</small>
    </p>
    <p class="btnSet">
        <span class="btn2"><a flg="<?php $data['flg'] ? assign($data['flg'] . '_reset') : '' ; ?>" href="javascript:void(0)" class="small1 jsAreaToggle" data-clear_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_MEMBER_NO)?>" data-search_no="<?php assign($this->search_no)?>">リセット</a></span>
        <span class="btn3"><a flg="<?php  $data['flg'] ? assign($data['flg'] . '_submit') : '' ; ?>" href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_MEMBER_NO)?>" data-search_no="<?php assign($this->search_no)?>">絞り込む</a></span>
    </p>
<!-- /.sortBox --></div>
