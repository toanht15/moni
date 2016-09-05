<?php
foreach ($data['cp'] as $cp_key => $group_array) {
    write_html(aafwWidgets::getInstance()->loadWidget('CpDataDownloadModal')->render(
        array(
            'brand_id' => $this->brand->id,
            'cp_id' => $cp_key,
            'group_array' => $group_array,
            'pageStatus' => $data['pageStatus'],
            'isFromPublicCp' => $data['isFromPublicCp']
        )
    ));
}
?>
