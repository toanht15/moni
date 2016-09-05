<div class="sortBox jsAreaToggleTarget" data-search_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD)?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
    <ul class="order">
        <li><a href="javascript:void(0)" data-order="<?php assign(CpUserListService::ORDER_ASC)?>">[A-Z↓] 昇順</a></li>
        <li><a href="javascript:void(0)" data-order="<?php assign(CpUserListService::ORDER_DESC)?>">[Z-A↑] 降順</a></li>
    <!-- /.order --></ul>
    <div class="range jsCheckToggleWrap">
        <p>
            <?php write_html($this->formText(
                'search_profile_register_period_from',
                $this->POST ? PHPParser::ACTION_FORM : $data['search_profile_register_period']['search_profile_register_period_from'],
                array('class'=>'jsDate inputDate','placeholder'=>'年/月/日')
            )); ?>
            <span class="dash">～</span>
            <?php write_html($this->formText(
                'search_profile_register_period_to',
                $this->POST ? PHPParser::ACTION_FORM : $data['search_profile_register_period']['search_profile_register_period_to'],
                array('class'=>'jsDate inputDate','placeholder'=>'年/月/日')
            )); ?>
        </p>
    <!-- /.range --></div>
    <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-clear_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD)?>" data-search_no="<?php assign($this->search_no)?>">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_REGISTER_PERIOD)?>" data-search_no="<?php assign($this->search_no)?>">絞り込む</a></span>
    </p>
<!-- /.sortBox --></div>
