<div class="sortBox jsAreaToggleTarget" data-search_type="<?php assign(CpCreateSqlService::SEARCH_DUPLICATE_ADDRESS)?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
    <ul>
        <li><label><input type="checkbox" id='have_duplicate_address_checkbox' name='search_duplicate_address/<?php assign($data['duplicate_address_show_type'].'/'.CpCreateSqlService::HAVE_ADDRESS.'/'.$this->search_no)?>' <?php assign($data['search_duplicate_address']['search_duplicate_address/'.$data['duplicate_address_show_type'].'/'.CpCreateSqlService::HAVE_ADDRESS] ? 'checked' : '')?>>住所取得済み</label></li>
        <li><label><input type="checkbox" name='search_duplicate_address/<?php assign($data['duplicate_address_show_type'].'/'.CpCreateSqlService::NOT_HAVE_ADDRESS.'/'.$this->search_no)?>' <?php assign($data['search_duplicate_address']['search_duplicate_address/'.$data['duplicate_address_show_type'].'/'.CpCreateSqlService::NOT_HAVE_ADDRESS] ? 'checked' : '')?>>住所未取得</label></li>
    </ul>
    <div class="range jsCheckToggleWrap">
        <p>同一住所ユーザー数</p>
        <p>
            <?php write_html($this->formText('search_duplicate_address_from',
                $this->POST ? PHPParser::ACTION_FORM : $data['search_duplicate_address']['search_duplicate_address_from'],
                array('maxlength'=>'20', 'class' => 'inputNum' ,'disabled' => $data['search_duplicate_address']['search_duplicate_address/'.$data['duplicate_address_show_type'].'/'.CpCreateSqlService::HAVE_ADDRESS] ? '' : 'disabled')
            )); ?>
            <span class="dash">～</span>
            <?php write_html($this->formText('search_duplicate_address_to',
                $this->POST ? PHPParser::ACTION_FORM : $data['search_duplicate_address']['search_duplicate_address_to'],
                array('maxlength'=>'20', 'class' => 'inputNum','disabled' => $data['search_duplicate_address']['search_duplicate_address/'.$data['duplicate_address_show_type'].'/'.CpCreateSqlService::HAVE_ADDRESS] ? '' : 'disabled')
            )); ?>
            <p><small>※重複なしは「1」になります</small></p>
            <?php if($data['cp_id']): ?>
                <?php write_html($this->formHidden('search_duplicate_address_by_cp_id', $data['cp_id']))?>
            <?php endif; ?>
        </p>
    <!-- /.range --></div>
    <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-clear_type="<?php assign(CpCreateSqlService::SEARCH_DUPLICATE_ADDRESS)?>" data-search_no="<?php assign($this->search_no)?>">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign(CpCreateSqlService::SEARCH_DUPLICATE_ADDRESS)?>" data-search_no="<?php assign($this->search_no)?>">絞り込む</a></span>
    </p>
<!-- /.sortBox --></div>

