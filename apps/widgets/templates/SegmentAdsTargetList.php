<div class="segmentPreviewWrap">
    <div class="segmentPreview">
        <p class="segmentTotal">
            <strong><?php assign($data['sp_data']['total']['counter_text'])?><span>人</span></strong>
            <span class="supplement1">(セグメント間重複 <?php assign($data['sp_data']['total']['duplicate'])?>人)</span>
            <!-- /.segmenttotal --></p>

        <ul class="segmentItem">
            <?php foreach ($data['provision_ids'] as $provision_id): ?>
                <li class="listItem" data-tooltip="#tooltip_ads_provision_<?php assign($provision_id) ?>">
                    <p class="itemTitle"><?php assign($data['sp_data'][$provision_id]['value']->name) ?></p>
                    <p class="itemNum"><?php assign($data['sp_data'][$provision_id]['counter_text']) ?>人</p>
                </li>
            <?php endforeach; ?>
            <!-- /.segmentItem --></ul>
        <!-- /.segmentPreview --></div>

    <?php foreach ($data['provision_ids'] as $provision_id): ?>
        <?php write_html($this->parseTemplate('segment/SegmentProvisionTooltip.php', array(
            'users_count' => $data['sp_data'][$provision_id]['counter_text'],
            'segment_provision' => $data['sp_data'][$provision_id]['value'],
            'tooltip_id' => 'tooltip_ads_provision_'.$provision_id,
        ))) ?>
    <?php endforeach; ?>
<!-- /.segmentWrap --></div>
