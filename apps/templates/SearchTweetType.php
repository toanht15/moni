<div class="sortBox jsAreaToggleTarget" data-search_type="<?php assign(CpCreateSqlService::SEARCH_TWEET_TYPE . '/' . $data['action_id']) ?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
    <div class="range jsCheckToggleWrap">
        <ul>
            <?php foreach ($data['tweet_types'] as $key => $value): ?>
                <li>
                    <?php write_html($this->formCheckbox(
                        'search_tweet_type/' . $data['action_id'] . '/' . $key . '/' . $this->search_no,
                        $this->POST ? PHPParser::ACTION_FORM : $data['search_tweet']['search_tweet_type/' . $data['action_id'] . '/' . $key],
                        array('checked' => $data['search_tweet']['search_tweet_type/' . $data['action_id'] . '/' . $key] ? "checked" : ""),
                        array('1' => $value)
                    ))?>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- /.range --></div>
    <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-clear_type="<?php assign(CpCreateSqlService::SEARCH_TWEET_TYPE . '/' . $data['action_id']) ?>" data-search_no="<?php assign($this->search_no)?>">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign(CpCreateSqlService::SEARCH_TWEET_TYPE . '/' . $data['action_id']) ?>" data-search_no="<?php assign($this->search_no)?>">絞り込む</a></span>
    </p>
    <!-- /.sortBox --></div>
