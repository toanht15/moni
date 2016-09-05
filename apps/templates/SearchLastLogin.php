<div class="sortBox jsAreaToggleTarget" data-search_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_LAST_LOGIN)?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
    <ul class="order">
        <li><a href="javascript:void(0)" data-order="<?php assign(CpUserListService::ORDER_ASC)?>">[A-Z↓] 昇順</a></li>
        <li><a href="javascript:void(0)" data-order="<?php assign(CpUserListService::ORDER_DESC)?>">[Z-A↑] 降順</a></li>
    <!-- /.order --></ul>
    <div class="range jsCheckToggleWrap">
        <p>
            <?php
                if($data['search_profile_last_login']['search_profile_last_login_from']) {
                    $search_profile_last_login_from = date('Y/m/d', strtotime($data['search_profile_last_login']['search_profile_last_login_from']));
                    $hh_search_profile_last_login_from = date('H', strtotime($data['search_profile_last_login']['search_profile_last_login_from']));
                    $mm_search_profile_last_login_from = date('i', strtotime($data['search_profile_last_login']['search_profile_last_login_from']));
                }
                if($data['search_profile_last_login']['search_profile_last_login_to']) {
                    $search_profile_last_login_to = date('Y/m/d', strtotime($data['search_profile_last_login']['search_profile_last_login_to']));
                    $hh_search_profile_last_login_to = date('H', strtotime($data['search_profile_last_login']['search_profile_last_login_to']));
                    $mm_search_profile_last_login_to = date('i', strtotime($data['search_profile_last_login']['search_profile_last_login_to']));
                }
            ?>
            <?php write_html($this->formText(
                'search_profile_last_login_from',
                $this->POST ? PHPParser::ACTION_FORM : $search_profile_last_login_from,
                array('class'=>'jsDate inputDate','placeholder'=>'年/月/日')
            )); ?>
            <select name='hh_search_profile_last_login_from/<?php assign($this->search_no) ?>' class="inputTime" data-time_type="hh">
                <?php for($hour = 0 ; $hour <= 23; $hour ++):?>
                    <option value="<?php assign($hour < 10 ? '0'.$hour : $hour)?>" <?php assign($hour == $hh_search_profile_last_login_from ? 'selected' : '')?>><?php assign($hour < 10 ? '0'.$hour : $hour)?></option>
                <?php endfor;?>
            </select
                  ><span class="coron">:</span
                  ><select name='mm_search_profile_last_login_from/<?php assign($this->search_no) ?>' class="inputTime" data-time_type="mm">
                <?php for($m = 0 ; $m <= 59; $m ++):?>
                    <option value="<?php assign($m < 10 ? '0'.$m : $m)?>" <?php assign($m == $mm_search_profile_last_login_from ? 'selected' : '')?>><?php assign($m < 10 ? '0'.$m : $m)?></option>
                <?php endfor;?>
                  </select>
            <span class="dash">～</span>
            <?php write_html($this->formText(
                'search_profile_last_login_to',
                $this->POST ? PHPParser::ACTION_FORM : $search_profile_last_login_to,
                array('class'=>'jsDate inputDate','placeholder'=>'年/月/日')
            )); ?>
            <select name='hh_search_profile_last_login_to/<?php assign($this->search_no) ?>' class="inputTime" data-time_type="hh">
                <?php for($hour = 0 ; $hour <= 23; $hour ++):?>
                    <option value="<?php assign($hour < 10 ? '0'.$hour : $hour)?>" <?php assign($hour == $hh_search_profile_last_login_to ? 'selected' : '')?>><?php assign($hour < 10 ? '0'.$hour : $hour)?></option>
                <?php endfor;?>
            </select
                  ><span class="coron">:</span
                  ><select name='mm_search_profile_last_login_to/<?php assign($this->search_no) ?>' class="inputTime" data-time_type="mm">
                <?php for($m = 0 ; $m <= 59; $m ++):?>
                    <option value="<?php assign($m < 10 ? '0'.$m : $m)?>" <?php assign($m == $mm_search_profile_last_login_to ? 'selected' : '')?>><?php assign($m < 10 ? '0'.$m : $m)?></option>
                <?php endfor;?>
                  </select>
        </p>
    <!-- /.range --></div>
    <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-clear_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_LAST_LOGIN)?>" data-search_no="<?php assign($this->search_no)?>">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_LAST_LOGIN)?>" data-search_no="<?php assign($this->search_no)?>">絞り込む</a></span>
    </p>
<!-- /.sortBox --></div>
