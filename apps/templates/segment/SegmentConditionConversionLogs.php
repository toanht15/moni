<?php
$service_factory = new aafwServiceFactory();
/** @var ConversionService $conversion_service */
$conversion_service = $service_factory->create('ConversionService');
$conversion = $conversion_service->getConversionById($data['target_id']);
$search_range_text = CpCreateSqlService::$search_range_keys[$data['condition_type']];
?>
<p>
    <?php write_html($this->formText(
        $search_range_text . '_from/' . $conversion->id,
        $data['condition_data'][$search_range_text . '_from/' . $conversion->id],
        array('maxlength' => '20', 'class' => 'inputNum')
    ));
    assign($data["unit_label"]); ?>
    <span class="dash">〜</span>
    <?php write_html($this->formText(
        $search_range_text . '_to/' . $conversion->id,
        $data['condition_data'][$search_range_text . '_to/' . $conversion->id],
        array('maxlength' => '20', 'class' => 'inputNum')
    ));
    assign($data["unit_label"]); ?>
</p>