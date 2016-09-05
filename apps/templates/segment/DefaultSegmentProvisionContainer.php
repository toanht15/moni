<li class="segmentItem jsSPContainer jsErrorMsgWrap">
    <?php if (!$data['segment_info']['is_active_segment']): ?>
        <?php if($data['segment_info']['segment_type'] == Segment::TYPE_CONDITIONAL_SEGMENT):?>
            <p class="segmentNoMove">位置固定</p>
        <?php else: ?>
            <p class="segmentMove">順番を入れ替える</p>
        <?php endif; ?>
    <?php endif ?>

    <div class="segmentItemInner">
        <?php if($data['segment_info']['segment_type'] == Segment::TYPE_SEGMENT_GROUP):?>
            <p class="iconError1 jsSPNameInputError" style="display: none;"></p>
        <?php endif; ?>
        <div class="segmentNames">
            <?php if($data['segment_info']['segment_type'] == Segment::TYPE_SEGMENT_GROUP):?>
                <?php if (!$data['provision']->id): ?>
                    <p><input type="text" name="spc_name" class="jsSPNameInput" /></p>
                <?php elseif ($data['segment_info']['is_active_segment']): ?>
                    <p><span class="segmentName jsSPName"><?php assign($data['provision']->name) ?></span></p>
                <?php else: ?>
                    <p>
                        <span class="segmentName"><?php assign($data['provision']->name) ?><input type="hidden" name="spc_name" value="<?php assign($data['provision']->name) ?>" class="jsSPNameInput" /></span>
                        <span class="edit jsTextareaToggle">編集する</span>
                    </p>
                <?php endif ?>
            <?php endif ?>
            <p class="segmentMember"><span class="jsSPUserCount"><img src="<?php assign($this->setVersion('/img/base/amimeLoading.gif'))?>" alt="loading" class="loadingImg"></span>名</p>
            <!-- /.segmentNames --></div>

        <div class="metricBoxWrap jsSyncTarget">
            <ul class="metricBoxs">
                <?php if (!$data['provision']->id || Util::isNullOrEmpty($data['provision']->provision)): ?>
                    <?php if (!$data['segment_info']['is_active_segment']): ?>
                        <li class="metricBoxOptionAdd jsSPCComponent">
                            <?php write_html($this->formHidden('spc', "", array('class' => 'jsSPCComponentValue'))); ?>
                            <a href="#segmentProvisionConditionSelector" data-type="and" class="jsOpenSegmentConditionModal"><span>追加する</span></a></li>
                    <?php else: ?>
                        <li class="metricBoxOptionNone jsSPCComponent">
                            <?php write_html($this->formHidden('spc', "", array('class' => 'jsSPCComponentValue'))); ?><p><span>無し</span></p></li>
                    <?php endif ?>
                <?php else: ?>
                    <?php $provision = json_decode($data['provision']->provision, true) ?>
                    <?php foreach ($provision as $key => $condition): ?>
                        <?php if (!SegmentService::isLegalProvisionCondition($key)) continue ?>

                        <?php if (empty($condition)): ?>
                            <?php if (!$data['segment_info']['is_active_segment']): ?>
                                <li class="metricBoxOptionAdd jsSPCComponent">
                                    <?php write_html($this->formHidden('spc', "", array('class' => 'jsSPCComponentValue'))); ?>
                                    <a href="#segmentProvisionConditionSelector" data-type="and" class="jsOpenSegmentConditionModal"><span>追加する</span></a></li>
                            <?php else: ?>
                                <li class="metricBoxOptionNone jsSPCComponent">
                                    <?php write_html($this->formHidden('spc', "", array('class' => 'jsSPCComponentValue'))); ?><p><span>無し</span></p></li>
                            <?php endif ?>
                        <?php else: ?>
                            <li class="jsSPCComponent">
                                <?php $index = 0 ?>
                                <?php $total_condition = count($condition) ?>
                                <?php $or_label_flg = false; ?>
                                <?php foreach($condition as $sub_key => $sub_condition): ?>
                                    <?php $or_condition_flg = $data['segment_info']['is_active_segment'] ? false : ++$index == $total_condition ?>
                                    <?php write_html(aafwWidgets::getInstance()->loadWidget('SegmentProvisionConditionComponent')->render(array(
                                        'condition_key' => $sub_key, 'condition_value' => $sub_condition,
                                        'or_condition_flg' => $or_condition_flg, 'or_label_flg' => $or_label_flg, 'brand_id' => $data['brand_id'], 'is_active_segment' => $data['segment_info']['is_active_segment']
                                    ))) ?>
                                    <?php $or_label_flg = true; ?>
                                <?php endforeach ?>
                            </li>
                        <?php endif ?>
                    <?php endforeach ?>
                <?php endif ?>

                <?php if ($data['segment_info']['is_set_condition'] && !$data['segment_info']['is_active_segment']): ?>
                    <li class="metricBoxAddWrap jsSPCComponent">
                        <p class="metricBoxAdd"><span><a href="#segmentProvisionConditionSelector" data-type="and" class="jsOpenSegmentConditionModal">追加する</a></span></p>
                        <!-- /.metricBoxAddWrap --></li>
                <?php endif ?>
                <!-- /.metricBoxs --></ul>

            <?php if($data['segment_info']['segment_type'] == Segment::TYPE_SEGMENT_GROUP && !$data['segment_info']['is_active_segment']): ?>
                <p class="option">
                    <a href="javascript:void(0);" class="groupDelete jsDeleteSPContainer">削除</a>
                    <!-- /.option --></p>
                <ul class="addOption">
                    <li class="add"><a href="#firstCreatorRule" class="jsAddSPContainer">新規追加</a></li>
                    <li class="copy"><a href="#firstCreatorRule" class="jsCloneSPContainer">複製</a></li>
                </ul>
            <?php endif ?>
            <!-- /.metricBoxWrap --></div>
        <!-- /.segmentItemInner --></div>
    <!-- /.segmentItem --></li>