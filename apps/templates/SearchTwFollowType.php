<div class="sortBox jsAreaToggleTarget" data-search_type="<?php assign(CpCreateSqlService::SEARCH_TW_FOLLOW_TYPE . '/' . $data['action_id']) ?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
    <div class="range jsCheckToggleWrap">
        <ul>
            <?php $data["search_no"] = $this->search_no ?>
            <?php write_html($this->parseTemplate('SearchTwFollowTypeList.php', $data)) ?>
        </ul>

        <!-- /.range --></div>
    <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-clear_type="<?php assign(CpCreateSqlService::SEARCH_TW_FOLLOW_TYPE . '/' . $data['action_id']) ?>" data-search_no="<?php assign($this->search_no)?>">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign(CpCreateSqlService::SEARCH_TW_FOLLOW_TYPE . '/' . $data['action_id']) ?>" data-search_no="<?php assign($this->search_no)?>">絞り込む</a></span>
    </p>
    <!-- /.sortBox --></div>
