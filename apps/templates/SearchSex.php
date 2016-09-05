<div class="sortBox jsAreaToggleTarget" data-search_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_SEX)?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
        <ul>
            <li><label><input type="checkbox" name='search_profile_sex/<?php assign(UserAttributeService::ATTRIBUTE_SEX_MAN.'/'.$this->search_no)?>' <?php assign($data['search_profile_sex']['search_profile_sex/'.UserAttributeService::ATTRIBUTE_SEX_MAN] ? 'checked' : '')?>><span class="iconSexM">男性</span>男性</label></li>
            <li><label><input type="checkbox" name='search_profile_sex/<?php assign(UserAttributeService::ATTRIBUTE_SEX_WOMAN.'/'.$this->search_no)?>' <?php assign($data['search_profile_sex']['search_profile_sex/'.UserAttributeService::ATTRIBUTE_SEX_WOMAN] ? 'checked' : '')?>><span class="iconSexF">女性</span>女性</label></li>
            <li><label><input type="checkbox" name='search_profile_sex/<?php assign(UserAttributeService::ATTRIBUTE_SEX_UNKWOWN.'/'.$this->search_no)?>' <?php assign($data['search_profile_sex']['search_profile_sex/'.UserAttributeService::ATTRIBUTE_SEX_UNKWOWN] ? 'checked' : '')?>><span class="iconSexN">未設定</span>未設定</label></li>
        </ul>
    <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-clear_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_SEX)?>" data-search_no="<?php assign($this->search_no)?>">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign(CpCreateSqlService::SEARCH_PROFILE_SEX)?>" data-search_no="<?php assign($this->search_no)?>">絞り込む</a></span>
    </p>
<!-- /.sortBox --></div>
