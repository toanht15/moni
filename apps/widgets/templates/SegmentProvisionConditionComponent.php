<div class="metricBox jsAreaToggleWrap" data-spsc_key="<?php assign($data['condition_key']) ?>">
    <?php write_html($this->formHidden('spc', $data['condition_data']['json_data'], array('class' => 'jsSPCComponentValue'))); ?>
    <p class="metricName" title="<?php assign($data['condition_data']['title']) ?>">
        <?php assign($data['condition_data']['title']) ?></p>

    <div class="input"><?php assign(Util::cutTextByWidth($data['condition_data']['content'], 130)) ?>
        <a href="javascript:void(0);" class="btnArrowB1 jsCloneToggle">絞り込む</a>

        <div class="sortBox jsAreaToggleTarget" style="display: none;">
            <?php write_html($this->formHidden('not_condition_flg', $data['condition_value']['not_flg'])) ?>
            <?php write_html($this->formHidden('or_condition_flg', $data['or_condition_flg'] ? 'on' : '')) ?>
            <?php write_html($this->formHidden('or_label_flg', $data['or_label_flg'] ? 'on' : '')) ?>

            <p class="boxCloseBtn"><a href="javascript:void(0);" class="jsAreaToggle">閉じる</a></p>

            <p class="metricDetailTitle" title="<?php assign($data['condition_data']['title']) ?>">
                <?php assign($data['condition_data']['title']) ?></p>
            <span class="iconError1 jsSPConditionError" style="display: none"></span>

            <p>値を選択してください。</p>

            <div class="range <?php if ($data['is_active_segment']): ?>disabled<?php endif ?>">
                    <?php write_html($data['condition_view']) ?>
                <!-- /.range --></div>
            <?php if (!$data['is_active_segment']): ?>
                <p class="btnSet"><span class="btn3"><a href="javascript:void(0);" class="small1 jsAreaToggle jsUpdateSPCCondition">確定</a></span></p>
            <?php endif ?>
            <!-- /.sortBox --></div>
    </div>
    <?php if (!$data['is_active_segment']): ?>
        <p class="delete jsResetSPCComponent"><a>削除する</a></p>
    <?php endif ?>

    <?php if($data['or_condition_flg'] === true): ?>
        <p class="or"><a href="#segmentProvisionConditionSelector" data-type="or" class="jsOpenSegmentConditionModal">or</a></p>
    <?php endif ?>

    <?php if ($data['or_label_flg'] === true): ?>
        <p class="labelOr"><span>or</span></p>
    <?php endif ?>

    <?php if ($data['condition_value']['not_flg'] == 'on'): ?>
        <p class="labelNot"><span>not</span></p>
    <?php endif ?>

    <!-- /.metricBox --></div>