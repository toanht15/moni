<?php $search_range_text = SegmentCreateSqlService::$search_range_keys[$data['condition_type']] ?>
<p>
    <?php write_html($this->formText(
        $search_range_text . '_from',
        $data['condition_data'][$search_range_text . '_from'],
        array('maxlength' => '20', 'class' => 'inputNum')
    ));
    assign($data["unit_label"]); ?>
    <span class="dash">〜</span>
    <?php write_html($this->formText(
        $search_range_text . '_to',
        $data['condition_data'][$search_range_text . '_to'],
        array('maxlength' => '20', 'class' => 'inputNum')
    ));
    assign($data["unit_label"]); ?>
</p>