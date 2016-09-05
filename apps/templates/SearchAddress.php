<div class="sortBox jsAreaToggleTarget" data-search_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_ADDRESS)?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
        <ul>
            <?php
                $service_factory = new aafwServiceFactory();
                $prefecture_service = $service_factory->create('PrefectureService');
                $prefectures = $prefecture_service->getAllPrefectures();
            ?>
            <?php foreach($prefectures as $prefecture): ?>
                <li>
                    <?php write_html($this->formCheckbox(
                        'search_profile_address/'.$prefecture->id.'/'.$this->search_no,
                        $this->POST ? PHPParser::ACTION_FORM : $data['search_profile_address'][$prefecture->id],
                        array('checked' => $data['search_profile_address']['search_profile_address/'.$prefecture->id] ? 'checked' : ''),
                        array('1' => $prefecture->name)
                    ))?>
                </li>
            <?php endforeach;?>
            <li>
                <?php write_html($this->formCheckbox(
                    'search_profile_address/'.CpCreateSqlService::NOT_SET_PREFECTURE.'/'.$this->search_no,
                    $this->POST ? PHPParser::ACTION_FORM : $data['search_profile_address'][CpCreateSqlService::NOT_SET_PREFECTURE],
                    array('checked' => $data['search_profile_address']['search_profile_address/'.CpCreateSqlService::NOT_SET_PREFECTURE] ? 'checked' : ''),
                    array('1' => '未設定')
                ))?>
            </li>
        </ul>
    <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-clear_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_ADDRESS)?>" data-search_no="<?php assign($this->search_no)?>">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_ADDRESS)?>" data-search_no="<?php assign($this->search_no)?>">絞り込む</a></span>
    </p>
<!-- /.sortBox --></div>
