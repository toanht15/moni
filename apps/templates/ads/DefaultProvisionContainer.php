<li class="segmentItem jsSPContainer jsErrorMsgWrap">
    <div class="segmentItemInner">
        <div class="metricBoxWrap jsSyncTarget">
            <ul class="metricBoxs">
                <?php if (Util::isNullOrEmpty($data['search_history']) || Util::isNullOrEmpty($data['search_history']->search_condition)): ?>
                    <li class="metricBoxOptionAdd jsSPCComponent"><p>
                        <?php write_html($this->formHidden('spc', "", array('class' => 'jsSPCComponentValue'))); ?>
                        <a href="#segmentProvisionConditionSelector" data-type="and" class="jsOpenSegmentConditionModal"></a></p>
                    </li>
                <?php else: ?>
                    <?php
                        $provision = json_decode($data['search_history']->search_condition, true);
                        $is_set_condition = false;
                    ?>
                    <?php foreach ($provision as $key => $condition): ?>
                        <?php if (!SegmentService::isLegalProvisionCondition($key)) continue ?>
                        <?php $is_set_condition = true;?>

                        <?php if (empty($condition)): ?>
                            <li class="metricBoxOptionAdd jsSPCComponent">
                                <?php write_html($this->formHidden('spc', "", array('class' => 'jsSPCComponentValue'))); ?>
                                <a href="#segmentProvisionConditionSelector" data-type="and" class="jsOpenSegmentConditionModal"><span>追加する</span></a></li>
                        <?php else: ?>
                            <li class="jsSPCComponent">
                                <?php $index = 0 ?>
                                <?php $total_condition = count($condition) ?>
                                <?php $or_label_flg = false; ?>
                                <?php foreach($condition as $sub_key => $sub_condition): ?>
                                    <?php $or_condition_flg = (++$index == $total_condition) ?>
                                    <?php write_html(aafwWidgets::getInstance()->loadWidget('SegmentProvisionConditionComponent')->render(
                                        array(
                                            'condition_key' => $sub_key,
                                            'condition_value' => $sub_condition,
                                            'or_condition_flg' => $or_condition_flg,
                                            'or_label_flg' => $or_label_flg,
                                            'brand_id' => $data['brand_id'],
                                            'is_active_segment' => false,
                                    ))) ?>
                                    <?php $or_label_flg = true; ?>
                                <?php endforeach ?>
                            </li>
                        <?php endif ?>
                    <?php endforeach ?>
                <?php endif ?>

                <?php if ($is_set_condition): ?>
                    <li class="metricBoxAddWrap jsSPCComponent">
                        <p class="metricBoxAdd"><span><a href="#segmentProvisionConditionSelector" data-type="and" class="jsOpenSegmentConditionModal">追加する</a></span></p>
                        <!-- /.metricBoxAddWrap --></li>
                <?php endif;?>
                <!-- /.metricBoxs --></ul>
            <!-- /.metricBoxWrap --></div>
        <!-- /.segmentItemInner --></div>
<!-- /.segmentItem --></li>