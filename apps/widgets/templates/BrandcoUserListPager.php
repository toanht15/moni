<?php if($this->TotalCount): ?>
    <?php if($this->search_condition[CpCreateSqlService::SEARCH_QUERY_USER_TYPE]): ?>
        <?php if($this->search_condition[CpCreateSqlService::SEARCH_QUERY_USER_TYPE] == CpCreateSqlService::QUERY_USER_TARGET.'/'.$this->reservation_id.'/'.$this->action_id): ?>
            <p><?php assign($data['show_segment_tooltip'] ? 'セグメントに属する' : '') ?><strong>「送信対象」</strong><strong><?php assign(number_format($this->TotalCount)) ?></strong>件中<strong><?php assign(number_format($this->Count * ($this->CurrentPage - 1) + 1)) ?></strong>件～<strong><?php assign(number_format(($this->Count * $this->CurrentPage < $this->TotalCount) ? $this->Count * $this->CurrentPage : $this->TotalCount)) ?></strong>件表示しています<a data-query_user="<?php assign(CpCreateSqlService::QUERY_USER_ALL) ?>" href="javascript:void(0)"><small>全ユーザーを表示</small></a>
                <?php if($data['show_segment_tooltip']): ?>
                    <?php write_html($this->parseTemplate('segment/SegmentMessageActionTooltip.php', array(
                        'user_count' => number_format($this->TotalCount)
                    ))) ?>
                <?php endif; ?>
            </p>
        <?php elseif($this->search_condition[CpCreateSqlService::SEARCH_QUERY_USER_TYPE] == CpCreateSqlService::QUERY_USER_SENT.'/'.$this->action_id): ?>
            <p><?php assign($data['show_segment_tooltip'] ? 'セグメントに属する' : '') ?><strong>「送信済」</strong><strong><?php assign(number_format($this->TotalCount)) ?></strong>件中<strong><?php assign(number_format($this->Count * ($this->CurrentPage - 1) + 1)) ?></strong>件～<strong><?php assign(number_format(($this->Count * $this->CurrentPage < $this->TotalCount) ? $this->Count * $this->CurrentPage : $this->TotalCount)) ?></strong>件表示しています<a data-query_user="<?php assign(CpCreateSqlService::QUERY_USER_ALL) ?>" href="javascript:void(0)"><small>全ユーザーを表示</small></a>
                <?php if($data['show_segment_tooltip']): ?>
                    <?php write_html($this->parseTemplate('segment/SegmentMessageActionTooltip.php', array(
                        'user_count' => number_format($this->TotalCount)
                    ))) ?>
                <?php endif; ?>
            </p>
        <?php endif; ?>
    <?php else: ?>
        <p><?php assign($data['show_segment_tooltip'] ? 'セグメントに属する' : '') ?><strong><?php assign(number_format($this->TotalCount)) ?></strong>件中<strong><?php assign(number_format($this->Count * ($this->CurrentPage - 1) + 1)) ?></strong>件～<strong><?php assign(number_format(($this->Count * $this->CurrentPage < $this->TotalCount) ? $this->Count * $this->CurrentPage : $this->TotalCount)) ?></strong>件表示しています
            <?php if($data['show_segment_tooltip']): ?>
                <?php write_html($this->parseTemplate('segment/SegmentMessageActionTooltip.php', array(
                    'user_count' => number_format($this->TotalCount)
                ))) ?>
            <?php endif; ?>
        </p>
    <?php endif; ?>

    <?php if ($this->TotalCount > $this->Count): ?>
        <ul>
            <?php if ($this->CurrentPage > 1): /* << < */?>
                <li class="first"><a href="javascript:void(0)" data-page="1">最初のページヘ</a></li>
                <li class="prev"><a href="javascript:void(0)" data-page="<?php assign($this->CurrentPage - 1) ?>">前のページへ</a></li>
            <?Php endif; ?>
            <?php for ($i = $this->Start; $i <= $this->End; $i++): ?>
                <?php if ($this->CurrentPage == $i): ?>
                    <li><span><?php assign($i) ?></span></li>
                <?php else: ?>
                    <li><a href="javascript:void(0)" data-page="<?php assign($i) ?>"><?php assign($i) ?></a></li>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if ($this->CurrentPage < $this->TotalPage): /* 99 > >> */?>
                <li class="next"><a href="javascript:void(0)" data-page="<?php assign($this->CurrentPage + 1) ?>">次のページへ</a></li>
                <li class="last"><a href="javascript:void(0)" data-page="<?php assign($this->TotalPage) ?>">最後のページヘ</a></li>
            <?php endif; ?>
        </ul>
    <?php endif; ?>
<?php else: ?>
    <?php if($params['search_condition'][CpCreateSqlService::SEARCH_QUERY_USER_TYPE]): ?>
        <?php if($params['search_condition'][CpCreateSqlService::SEARCH_QUERY_USER_TYPE] == CpCreateSqlService::QUERY_USER_TARGET.'/'.$params['reservation_id'].'/'.$params['action_id']): ?>
            <p><strong>この条件での送信対象ユーザは存在しません</strong><a data-query_user="<?php assign(CpCreateSqlService::QUERY_USER_ALL) ?>" href="javascript:void(0)"><small>全ユーザーを表示</small></a></p>
        <?php elseif($params['search_condition'][CpCreateSqlService::SEARCH_QUERY_USER_TYPE] == CpCreateSqlService::QUERY_USER_SENT.'/'.$params['action_id']): ?>
            <p><strong>この条件での送信済ユーザは存在しません</strong><a data-query_user="<?php assign(CpCreateSqlService::QUERY_USER_ALL) ?>" href="javascript:void(0)"><small>全ユーザーを表示</small></a></p>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
