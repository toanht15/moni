<p class="settingDetail">
    <?php if ($data["extern_key"]) {
        $key = CpCreateSqlService::$search_range_keys[$data['search_type']].'_from'.'/'.$data["extern_key"];
        $value_key = $data['search_type'].'/'.$data["extern_key"];
    } else {
        $key = CpCreateSqlService::$search_range_keys[$data['search_type']].'_from';
        $value_key = $data['search_type'];
    } ?>
    <?php write_html($this->formText(
        $key,
        $data[$value_key][$key],
        array('maxlength'=>'20', 'class' => 'inputNum')
    ));assign($data["unit_label"]); ?>
    <span class="dash">〜</span>

    <?php if ($data["extern_key"]) {
        $key = CpCreateSqlService::$search_range_keys[$data['search_type']].'_to'.'/'.$data["extern_key"];
    } else {
        $key = CpCreateSqlService::$search_range_keys[$data['search_type']].'_to';
    } ?>
    <?php write_html($this->formText(
        $key,
        $data[$value_key][$key],
        array('maxlength'=>'20', 'class' => 'inputNum')
    ));assign($data["unit_label"]); ?>
    <!-- /.settingDetail --></p>