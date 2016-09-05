<div class="sortBox jsAreaToggleTarget" data-search_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE_STATUS)?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
        <ul>
            <?php $key = 'search_questionnaire_status' ?>
            <li><label><input type="checkbox" name='<?php assign($key.'/'.BrandsUsersRelation::SIGNUP_WITHOUT_INFO.'/'.$this->search_no)?>' <?php assign($data[$key][$key.'/'.BrandsUsersRelation::SIGNUP_WITHOUT_INFO] ? 'checked' : '')?>>未取得</label></li>
            <li><label><input type="checkbox" name='<?php assign($key.'/'.BrandsUsersRelation::SIGNUP_WITH_INFO.'/'.$this->search_no)?>' <?php assign($data[$key][$key.'/'.BrandsUsersRelation::SIGNUP_WITH_INFO] ? 'checked' : '')?>>取得済み</label></li>
            <li><label><input type="checkbox" name='<?php assign($key.'/'.BrandsUsersRelation::FORCE_WITH_INFO.'/'.$this->search_no)?>' <?php assign($data[$key][$key.'/'.BrandsUsersRelation::FORCE_WITH_INFO] ? 'checked' : '')?>>要再取得</label></li>
        </ul>
    <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-clear_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE_STATUS)?>" data-search_no="<?php assign($this->search_no)?>">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE_STATUS)?>" data-search_no="<?php assign($this->search_no)?>">絞り込む</a></span>
    </p>
<!-- /.sortBox --></div>
