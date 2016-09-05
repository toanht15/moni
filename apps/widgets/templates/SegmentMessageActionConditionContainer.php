<li class="segmentItem jsSegment <?php assign($data['segment']->isSegmentGroup() ? 'jsSegmentContainerToggleWrap': ''); ?>" id="segment_<?php assign($data['segment']->id) ?>" data-segment_id="<?php assign($data['segment']->id) ?>">
    <div class="segmentItemInner" data-tooltip="#tooltip_segment_<?php assign($data['segment']->id); ?>">
        <label class="itemSelect"><input type="checkbox" class="<?php assign($data['segment']->isSegmentGroup() ? 'jsSegmentCheck': ''); ?> jsSegmentCheckbox"
                <?php assign($data['segment']->isConditionalSegment() ? 'name=sp_ids_'.$data['segment']->id.'[]' : '')?>
                <?php assign($data['segment']->isConditionalSegment() ? 'value='.$data["segment_provision_conditional"]->id : '')?>
                <?php assign( $data['segment_provisions']->total() == count($data['segment_condition_session'][$data['segment']->id]) ? 'checked' : '') ?>
            >
        </label>
        <p class="segmentName">
            <span class="name"><?php assign($data['segment']->name) ?></span>
            <!-- /.segmentName --></p>
        <p class="status">
            <span class="segmentNum"><?php assign($data['sp_data']['total']['counter_text']) ?></span>名
            <!-- /.status --></p>
        <!-- /.segmentItemInner --></div>

    <?php if ($data['segment']->isSegmentGroup()): ?>
        <ul class="subSegment jsSegmentContainerToggleTarget">
            <?php foreach ($data['segment_provisions'] as $cur_sp): ?>
                <li class="segmentItem">
                    <div class="segmentItemInner jsSProvisionCheck" data-tooltip="#tooltip_provision_<?php assign($cur_sp->id);?>">
                        <label class="itemSelect">
                            <input type="checkbox" class="jsSProvisionCheck jsSegmentCheckbox" name="sp_ids_<?php assign($data['segment']->id) ?>[]" value="<?php assign($cur_sp->id) ?>"
                                   <?php assign(in_array($cur_sp->id, $data['provision_id_array']) ? 'checked' : '') ?>
                                   id="sp_id_<?php assign($cur_sp->id) ?>"
                                >
                        </label>
                        <p class="segmentName">
                            <span class="name"><?php assign($cur_sp->name) ?></span>
                            <!-- /.segmentName --></p>
                        <p class="status">
                            <span class="segmentNum"><?php assign($data['sp_data'][$cur_sp->id]['counter_text']) ?></span>名
                            <!-- /.status --></p>
                        <!-- /.segmentItemInner --></div>
                </li>
            <?php endforeach ?>
        <!-- /.subSegment --></ul>
    <?php endif; ?>
<!-- /.segmentItem --></li>
