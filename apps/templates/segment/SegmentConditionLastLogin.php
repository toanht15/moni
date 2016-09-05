<?php $search_range_text = SegmentCreateSqlService::$search_range_keys[$data['condition_type']] ?>
<p>
    <?php write_html($this->formText(
        $search_range_text . '_from',
        $data['condition_data'][$search_range_text . '_from'] ? date('Y/m/d', strtotime($data['condition_data'][$search_range_text . '_from'])) : null,
        array('class' => 'jsDate inputDate', 'placeholder' => '年/月/日')
    )); ?>
    <span class="dash">〜</span>
    <?php write_html($this->formText(
        $search_range_text . '_to',
        $data['condition_data'][$search_range_text . '_to'] ? date('Y/m/d', strtotime($data['condition_data'][$search_range_text . '_to'])) : null,
        array('class' => 'jsDate inputDate', 'placeholder' => '年/月/日')
    )); ?>
</p>