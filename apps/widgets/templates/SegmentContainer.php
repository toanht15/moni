<li class="jsSegment segmentItem <?php assign($data['segment']->isSegmentGroup() ? 'jsSegmentToggleWrap': ''); ?>" id="segment_<?php assign($data['segment']->id) ?>" data-segment_id="<?php assign($data['segment']->id) ?>">
    <div class="segmentItemInner" data-tooltip="#tooltip_segment_<?php assign($data['segment']->id); ?>">
        <label class="itemSelect"><input type="checkbox" class="jsSegmentCheck"
                <?php assign($data['segment']->isConditionalSegment() ? 'name=sp_ids_'.$data['segment']->id.'[]' : '')?>
                <?php assign($data['segment']->isConditionalSegment() ? 'value='.$data["segment_provision_conditional"]->id : '')?>
                data-is_active="<?php assign($data['segment']->isActive() ? '1' : '')?>"
                <?php assign( $data['segment_provisions']->total() == count($data['sp_ids_array'][$data['segment']->id]) ? 'checked' : '') ?>
                >
        </label>
        <p class="segmentName jsHoverWrap">
            <span class="<?php assign($data['segment']->getSegmentTypeClass()) ?>"><?php assign($data['segment']->getSegmentTypeLabelText()); ?></span>
            <span class="name">
                <?php if ($data['segment']->isSegmentGroup()): ?>
                    <span class="arrowToggle jsSegmentToggle">開閉</span>
                <?php endif ?>
                <a href="<?php assign($data['segment_url']) ?>"><?php assign($data['segment']->name) ?></a></span>
            <span class="detail"><?php assign($data['segment']->description); ?></span>
        <!-- /.segmentName --></p>

        <p class="status">
            <?php if ($data['segment']->isActive()): ?>
                <?php if ($data['sp_data']['total']['status'] == SegmentProvisionUsersCount::USERS_COUNT_STATUS_UP): ?>
                    <span class="up"><span class="segmentNum"><?php assign($data['sp_data']['total']['counter_text']) ?></span>名
                        <span class="diff"><span>前日比</span>(+<?php assign($data['sp_data']['total']['diff']) ?>)</span></span>
                <?php elseif ($data['sp_data']['total']['status'] == SegmentProvisionUsersCount::USERS_COUNT_PROCESSING): ?>
                    <span class="segmentNum">集計中</span>
                <?php else: ?>
                    <span class="segmentNum"><?php assign($data['sp_data']['total']['counter_text']) ?></span>名
                <?php endif ?>
            <?php else: ?>
                <span class="iconDraft1">下書き</span>
            <?php endif ?>
            <!-- /.status --></p>

        <div class="option jsAreaToggleWrap">
            <p><a href="#" class="btnArrowB1 jsAreaToggle">絞り込む</a></p>
            <div class="optionAction jsAreaToggleTarget">
                <p class="boxCloseBtn"><a href="#" class="jsAreaToggle">閉じる</a></p>
                <ul class="btnList">
                    <!-- <li class="btn2"><a href="#" class="small1 jsAreaToggle">編集</a></li> -->
                    <li class="btn2"><a href="javascript:void(0);" class="small1 jsDuplicateSegment">コピー</a></li>
                    <li class="btn2"><a href="#archiveSet" class="small1 jsOpenArchiveModal" data-segment_status="<?php assign($data['segment']->status) ?>">アーカイブ</a>
                    </li>
                </ul>
                <!-- /.optionAction --></div>
            <!-- /.jsSegmentToggle --></div>
    <!-- /.segmentItemInner --></div>

    <?php if ($data['segment']->isSegmentGroup()): ?>
        <ul class="subSegment jsSegmentToggleTarget">
            <?php foreach ($data['segment_provisions'] as $cur_sp): ?>
                <li class="segmentItem">
                    <div class="segmentItemInner jsSProvisionCheck" data-tooltip="#tooltip_provision_<?php assign($cur_sp->id);?>">
                        <label class="itemSelect"><input type="checkbox" class="jsSProvisionCheck"
                                                         name="sp_ids_<?php assign($data['segment']->id) ?>[]" value="<?php assign($cur_sp->id) ?>"
                                                         data-is_active="<?php assign($data['segment']->isActive() ? '1' : '')?>"
                                                        <?php assign(in_array($cur_sp->id, $data['sp_ids_array'][$data['segment']->id]) ? 'checked' : '') ?>
                                >
                        </label>
                        <p class="segmentName jsHoverWrap">
                            <span class="name"><a href="<?php assign($data['segment_url']) ?>"><?php assign($cur_sp->name) ?></a></span>
                            <span class="detail"><?php assign($data['sp_data'][$cur_sp->id]['condition_brief_text']) ?></span>
                            <!-- /.segmentName --></p>
                        <p class="status">
                            <?php if ($data['segment']->isActive()): ?>
                                <?php if ($data['sp_data'][$cur_sp->id]['status'] == SegmentProvisionUsersCount::USERS_COUNT_STATUS_UP): ?>
                                    <span class="up"><span class="segmentNum"><?php assign($data['sp_data'][$cur_sp->id]['counter_text']) ?></span>名
                                        <span class="diff"><span>前日比</span>(+<?php assign($data['sp_data'][$cur_sp->id]['diff']) ?>)</span></span>
                                <?php elseif ($data['sp_data']['total']['status'] == SegmentProvisionUsersCount::USERS_COUNT_PROCESSING): ?>
                                    <span class="segmentNum">集計中</span>
                                <?php else: ?>
                                    <span class="segmentNum"><?php assign($data['sp_data'][$cur_sp->id]['counter_text']) ?></span>名
                                <?php endif ?>
                            <?php else: ?>
                                <span class="iconDraft1">下書き</span>
                            <?php endif ?>
                            <!-- /.status --></p>
                        <!-- /.segmentItemInner --></div>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</li>