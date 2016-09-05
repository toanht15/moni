<div class="sortBox jsAreaToggleTarget" data-search_type="<?php assign(CpCreateSqlService::SEARCH_CHILD_BIRTH_PERIOD.'/'.$data['relation_id'])?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
    <div class="range jsCheckToggleWrap">
        <p>
            <?php write_html($this->formText(
                'search_child_birth_period_from'.'/'.$data['relation_id'],
                $this->POST ? PHPParser::ACTION_FORM : $data['search_child_birth_period']['search_child_birth_period_from'.'/'.$data['relation_id']],
                array('class'=>'inputNum','placeholder'=>'年')
            )); ?>
            <span>/4月</span>
            <span class="dash">～</span>
            <?php write_html($this->formText(
                'search_child_birth_period_to'.'/'.$data['relation_id'],
                $this->POST ? PHPParser::ACTION_FORM : $data['search_child_birth_period']['search_child_birth_period_to'.'/'.$data['relation_id']],
                array('class'=>'inputNum','placeholder'=>'年')
            )); ?>
            <span>/3月</span>
        </p>
        <!-- /.range --></div>
    <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-clear_type="<?php assign(CpCreateSqlService::SEARCH_CHILD_BIRTH_PERIOD.'/'.$data['relation_id'])?>" data-search_no="<?php assign($this->search_no)?>">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign(CpCreateSqlService::SEARCH_CHILD_BIRTH_PERIOD.'/'.$data['relation_id'])?>" data-search_no="<?php assign($this->search_no)?>">絞り込む</a></span>
    </p>
    <!-- /.sortBox --></div>