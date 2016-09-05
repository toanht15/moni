<div class="sortBox jsAreaToggleTarget" data-search_type="<?php assign(CpCreateSqlService::SEARCH_MESSAGE_READ_RATIO)?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
    <ul class="order">
        <li><a href="javascript:void(0)" data-order="<?php assign(CpUserListService::ORDER_ASC)?>">[A-Z↓] 昇順</a></li>
        <li><a href="javascript:void(0)" data-order="<?php assign(CpUserListService::ORDER_DESC)?>">[Z-A↑] 降順</a></li>
    <!-- /.order --></ul>
    <div class="range jsCheckToggleWrap">
        <p>
            <?php write_html($this->formText(
                'search_message_ratio_from',
                $this->POST ? PHPParser::ACTION_FORM : $data['search_condition']['search_message_ratio_from'],
                array('maxlength'=>'4', 'class'=>'inputNum')
            )); ?>%
            <span class="dash">～</span>
            <?php write_html($this->formText(
                'search_message_ratio_to',
                $this->POST ? PHPParser::ACTION_FORM : $data['search_condition']['search_message_ratio_to'],
                array('maxlength'=>'4', 'class'=>'inputNum')
            )); ?>%
        </p>
    <!-- /.range --></div>
    <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-clear_type="<?php assign(CpCreateSqlService::SEARCH_MESSAGE_READ_RATIO)?>" data-search_no="<?php assign($this->search_no)?>">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign(CpCreateSqlService::SEARCH_MESSAGE_READ_RATIO)?>" data-search_no="<?php assign($this->search_no)?>">絞り込む</a></span>
    </p>
<!-- /.sortBox --></div>
