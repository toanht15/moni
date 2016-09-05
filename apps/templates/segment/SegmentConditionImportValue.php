<?php
$key_name = 'search_import_value/' . $data['target_id'] . '/';
$service_factory = new aafwServiceFactory();
$brand_service = $service_factory->create('BrandService');
$cur_definition = $brand_service->getBrandUserAttributeDefinitionById($data['target_id']);
?>
<ul class="status">
    <?php foreach(json_decode($cur_definition->value_set) as $key => $value): ?>
        <li>
            <?php write_html($this->formCheckbox(
                $key_name . $key,
                1,
                array('checked' => $data['condition_data'][$key_name . $key] ? 'checked' : ''),
                array('1' => $value)
            ))?>
        </li>
    <?php endforeach;?>
    <li>
        <?php write_html($this->formCheckbox(
            $key_name . CpCreateSqlService::NOT_SET_VALUE,
            1,
            array('checked' => $data['condition_data'][$key_name . CpCreateSqlService::NOT_SET_VALUE] ? 'checked' : ''),
            array('1' => '未設定')
        ))?>
    </li>
    <!-- /.status --></ul>