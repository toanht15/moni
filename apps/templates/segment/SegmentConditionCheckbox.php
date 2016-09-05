<?php
$key_name = $data['choice_data']['key_name'] . '/';
if ($data['choice_data']['target_id']) {
    $key_name .=  $data['choice_data']['target_id'] . '/';
}
?>
<ul class="status">
    <?php foreach ($data['choice_data']['choices'] as $key => $choice): ?>
        <li>
            <?php write_html($this->formCheckbox(
                $key_name . $key,
                $key,
                array('checked' => isset($data['condition_data'][$key_name . $key]) ? 'checked' : ''),
                array($key => $choice))) ?>
        </li>
    <?php endforeach ?>
    <!-- /.status --></ul>