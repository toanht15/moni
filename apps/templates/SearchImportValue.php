<div class="sortBox jsAreaToggleTarget" data-search_type="<?php assign(CpCreateSqlService::SEARCH_IMPORT_VALUE.'/'.$data['definition']->id)?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
    <ul>
        <?php $name_key = 'search_import_value/'; ?>
        <?php foreach(json_decode($data['definition']->value_set) as $key => $value): ?>
            <li>
                <?php write_html($this->formCheckbox(
                    $name_key.$data['definition']->id.'/'.$key.'/'.$this->search_no,
                    $this->POST ? PHPParser::ACTION_FORM : $data['search_import_value'][$data['definition']->id.'/'.$key],
                    array('checked' => $data['search_import_value'][$name_key.$data['definition']->id.'/'.$key] ? 'checked' : ''),
                    array('1' => $value)
                ))?>
            </li>
        <?php endforeach;?>
        <li>
            <?php write_html($this->formCheckbox(
                $name_key.$data['definition']->id.'/'.CpCreateSqlService::NOT_SET_VALUE.'/'.$this->search_no,
                $this->POST ? PHPParser::ACTION_FORM : $data['search_import_value'][$data['definition']->id.'/'.CpCreateSqlService::NOT_SET_VALUE],
                array('checked' => $data['search_import_value'][$name_key.$data['definition']->id.'/'.CpCreateSqlService::NOT_SET_VALUE] ? 'checked' : ''),
                array('1' => '未設定')
            ))?>
        </li>
    </ul>
    <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-clear_type="<?php assign(CpCreateSqlService::SEARCH_IMPORT_VALUE.'/'.$data['definition']->id)?>" data-search_no="<?php assign($this->search_no)?>">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign(CpCreateSqlService::SEARCH_IMPORT_VALUE.'/'.$data['definition']->id)?>" data-search_no="<?php assign($this->search_no)?>">絞り込む</a></span>
    </p>
<!-- /.sortBox --></div>
