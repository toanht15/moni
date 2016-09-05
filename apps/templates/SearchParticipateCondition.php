<div class="sortBox jsAreaToggleTarget" data-search_type="<?php assign(CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION.'/'.$data['action']->id)?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
    <div class="range jsCheckToggleWrap">
        <?php write_html($this->parseTemplate("SearchCampaignActionStatusList.php", array("search_conditions" => $data['search_participate_condition'], "action" => $data["action"], "search_no" => $this->search_no))) ?>
        <?php if ($data['action']->type == CpAction::TYPE_INSTANT_WIN): ?>
            <?php write_html($this->formHidden('switch_type/'.CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION.'/'.$data['action']->id, $data['search_participate_condition']['switch_type/'.CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION.'/'.$data['action']->id]==CpCreateSqlService::QUERY_TYPE_AND ? CpCreateSqlService::QUERY_TYPE_AND : CpCreateSqlService::QUERY_TYPE_OR))?>
            <p class="switchWrap">and<a href="javascript:void(0)" class="toggle_switch <?php assign($data['search_participate_condition']['switch_type/'.CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION.'/'.$data['action']->id] == CpCreateSqlService::QUERY_TYPE_AND ? 'left' : 'right')?>"
                data-switch_type="<?php assign(CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION.'/'.$data['action']->id)?>">toggle_switch</a>or</p>
        <?php endif; ?>

        <!-- /.range --></div>
    <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-clear_type="<?php assign(CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION.'/'.$data['action']->id)?>" data-search_no="<?php assign($this->search_no)?>">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign(CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION.'/'.$data['action']->id)?>" data-search_no="<?php assign($this->search_no)?>">絞り込む</a></span>
    </p>
<!-- /.sortBox --></div>
