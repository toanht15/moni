<?php write_html($this->parseTemplate('segment/SegmentProvisionTooltip.php', array(
    'segment_description' => $data['segment']->description,
    'users_count' => $data['sp_data']['total']['counter_text'],
    'segment_provision' => $data['segment_provision_conditional'],
    'tooltip_id' => 'tooltip_segment_'.$data['segment']->id
))) ?>
<?php if ($data['segment']->isSegmentGroup()): ?>
    <?php foreach ($data['segment_provisions'] as $cur_sp): ?>
        <?php write_html($this->parseTemplate('segment/SegmentProvisionTooltip.php', array(
            'users_count' => $data['sp_data'][$cur_sp->id]['counter_text'],
            'segment_provision' => $cur_sp,
            'tooltip_id' => 'tooltip_provision_'.$cur_sp->id
        ))) ?>
    <?php endforeach; ?>
<?php endif;?>