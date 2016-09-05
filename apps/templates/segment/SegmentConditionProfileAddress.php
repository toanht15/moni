<?php
$service_factory = new aafwServiceFactory();
$prefecture_service = $service_factory->create('PrefectureService');
$prefectures = $prefecture_service->getAllPrefectures();
?>
<ul class="status">
    <?php foreach ($prefectures as $prefecture): ?>
        <li>
            <?php write_html($this->formCheckbox('search_profile_address/' . $prefecture->id, 1, array('checked' => $data['condition_data']['search_profile_address/' . $prefecture->id] ? 'checked' : ''), array('1' => $prefecture->name))) ?>
        </li>
    <?php endforeach; ?>
    <!-- /.status --></ul>