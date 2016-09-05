<li class="segmentItemNa jsSPContainer jsErrorMsgWrap">
    <?php if (!$data['segment_info']['is_active_segment']): ?>
        <p class="segmentNoMove">位置固定</p>
    <?php endif ?>

    <div class="segmentItemInner">
        <p class="iconError1 jsSPNameInputError" style="display: none;"></p>
        <div class="segmentNames">
            <?php if (!$data['provision']->id): ?>
                <p><input type="text" name="spc_name" class="jsSPNameInput" /></p>
            <?php elseif ($data['segment_info']['is_active_segment']): ?>
                <p><span class="segmentName jsSPName"><?php assign($data['provision']->name) ?></span></p>
            <?php else: ?>
                <p><input type="text" name="spc_name" value="<?php assign($data['provision']->name) ?>" class="jsSPNameInput" maxlength="255" /></p>
            <?php endif ?>
            <p class="segmentMember"><span class="jsUnclassifiedUserCount"><img src="<?php assign($this->setVersion('/img/base/amimeLoading.gif'))?>" alt="loading" class="loadingImg"></span>名</p>
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

                    <?php if (!$data['segment_info']['is_active_segment']): ?>
                        <li class="metricBoxAddWrap jsSPCComponent">
                            <p class="metricBoxAdd"><span><a href="#segmentProvisionConditionSelector" data-type="and" class="jsOpenSegmentConditionModal">追加する</a></span></p>
                            <!-- /.metricBoxAddWrap --></li>
                    <?php endif ?>
                <?php endif ?>
                <!-- /.metricBoxs --></ul>

            <?php if (!$data['segment_info']['is_active_segment']): ?>
                <div class="jsAreaToggleWrap">
                    <p class="option">
                        <a href="javascript:void(0);" class="groupDelete jsDeleteSPContainer">削除</a>
                        <!-- /.option --></p>
                    <!-- /.jsAreaToggleWrap --></div>
            <?php endif ?>
            <!-- /.metricBoxWrap --></div>
        <!-- /.segmentItemInner --></div>
    <!-- /.segmentItem --></li>