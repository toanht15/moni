<div class="customaudienceTooltip jsHoverTooltip" id="<?php assign($data['tooltip_id'])?>">
    <div class="customaudiencePreview">
        <dl class="selectedStatus">
            <dt>現在のユーザー</dt>
            <dd class="selectedUser"><strong><?php assign($data['users_count'] ?: 0) ?></strong>名</dd>
            <!-- /.selectedStatus --></dl>
        <?php if ($data['segment_provision']): ?>
            <p>設定した条件</p>
            <?php if($data['segment_provision']->provision): ?>
                <ul class="selectedRefinement">
                    <?php foreach($data['segment_provision']->getProvisionTextArray() as $key => $condition): ?>
                        <?php
                            $first_flg = true;
                            if (!SegmentService::isLegalProvisionCondition($key)) continue;
                            foreach ($condition as $sub_condition) {
                                write_html($this->parseTemplate('segment/SegmentProvisionTooltipCondition.php', array(
                                    'condition' => $sub_condition, 'or_condition_flg' => !$first_flg
                                )));
                                $first_flg = false;
                            }
                        ?>
                    <?php endforeach; ?>
                    <!-- /.selectedRefinement --></ul>
            <?php else: ?>
                未設定
            <?php endif ?>
        <?php elseif (!Util::isNullOrEmpty($data['segment_description'])): ?>
            <p>メモ</p>
            <p><?php assign($data['segment_description']) ?></p>
        <?php endif ?>
        <!-- /.userInfoFilterPreview --></div>
    <!-- /.tooltip --></div>