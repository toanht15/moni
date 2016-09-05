<div class="sortBox jsAreaToggleTarget" data-search_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_AGE)?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
    <ul class="order">
        <li><a href="javascript:void(0)" data-order="<?php assign(CpUserListService::ORDER_ASC)?>">[A-Z↓] 昇順</a></li>
        <li><a href="javascript:void(0)" data-order="<?php assign(CpUserListService::ORDER_DESC)?>">[Z-A↑] 降順</a></li>
    <!-- /.order --></ul>
    <div class="range jsCheckToggleWrap">
        <p>
            <?php write_html($this->formText(
                'search_profile_age_from',
                $this->POST ? PHPParser::ACTION_FORM : $data['search_profile_age']['search_profile_age_from'],
                array('maxlength'=>'3', 'class'=>'inputNum', 'placeholder'=>'0')
            )); ?>歳
            <span class="dash">～</span>
            <?php write_html($this->formText(
                'search_profile_age_to',
                $this->POST ? PHPParser::ACTION_FORM : $data['search_profile_age']['search_profile_age_to'],
                array('maxlength'=>'3', 'class'=>'inputNum', 'placeholder'=>'99')
            )); ?>歳
            <label class="check"><input type="checkbox" class="jsCheckToggle" name='search_profile_age_not_set/<?php assign($this->search_no) ?>' <?php assign($data['search_profile_age']['search_profile_age_not_set'] ? 'checked' : '')?>>未設定</label>
        </p>
    <!-- /.range --></div>
    <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-clear_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_AGE)?>" data-search_no="<?php assign($this->search_no)?>">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_AGE)?>" data-search_no="<?php assign($this->search_no)?>">絞り込む</a></span>
    </p>
<!-- /.sortBox --></div>
