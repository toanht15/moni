<div class="sortBox jsAreaToggleTarget" data-search_type="<?php assign(StaticHtmlStampRallyService::SEARCH_BY_CP_STATUS)?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
    <ul>
        <li><label><input type="checkbox" name='search_cp_status[]'  data-status="<?php assign(Cp::STATUS_DEMO) ?>">デモ公開中</label></li>
        <li><label><input type="checkbox" name='search_cp_status[]'  data-status="<?php assign(Cp::STATUS_DRAFT) ?>">下書き</label></li>
        <li><label><input type="checkbox" name='search_cp_status[]'  data-status="<?php assign(Cp::STATUS_SCHEDULE) ?>">公開予約</label></li>
    </ul>
    <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-clear_type="<?php assign(StaticHtmlStampRallyService::SEARCH_BY_CP_STATUS)?>">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign(StaticHtmlStampRallyService::SEARCH_BY_CP_STATUS)?>">絞り込む</a></span>
    </p>
<!-- /.sortBox --></div>