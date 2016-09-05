<div class="sortBox jsAreaToggleTarget" data-search_type="<?php assign(CpCreateSqlService::SEARCH_PHOTO_SHARE_SNS . '/' . $data['action_id'])?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
    <ul>
        <?php write_html($this->parseTemplate('SearchPhotoShareSNSList.php', array("search_condition" => $data["search_photo_share_sns"], "action_id" => $data["action_id"]))) ?>
    </ul>
    <?php write_html($this->formHidden('switch_type/'.CpCreateSqlService::SEARCH_PHOTO_SHARE_SNS.'/'.$data['action_id'], $data['search_photo_share_sns']['switch_type/'.CpCreateSqlService::SEARCH_PHOTO_SHARE_SNS.'/'.$data['action_id']] == CpCreateSqlService::QUERY_TYPE_AND ? CpCreateSqlService::QUERY_TYPE_AND : CpCreateSqlService::QUERY_TYPE_OR))?>
    <p class="switchWrap">
        and
        <a href="javascript:void(0)" class="toggle_switch <?php assign($data['search_photo_share_sns']['switch_type/'.CpCreateSqlService::SEARCH_PHOTO_SHARE_SNS.'/'.$data['action_id']] == CpCreateSqlService::QUERY_TYPE_AND ? 'left' : 'right')?>"
                                data-switch_type="<?php assign(CpCreateSqlService::SEARCH_PHOTO_SHARE_SNS.'/'.$data['action_id'])?>">toggle_switch</a>
        or
    </p>
    <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-clear_type="<?php assign(CpCreateSqlService::SEARCH_PHOTO_SHARE_SNS . '/' . $data['action_id'])?>" data-search_no="<?php assign($this->search_no)?>">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign(CpCreateSqlService::SEARCH_PHOTO_SHARE_SNS . '/' .$data['action_id'])?>" data-search_no="<?php assign($this->search_no)?>">絞り込む</a></span>
    </p>
 <!-- /.sortBox --></div>