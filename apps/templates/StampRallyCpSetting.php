<p>※選択したキャンペーンは応募開始日時順に掲載されます。</p>

<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoCpDataListPager')->render(array(
    'TotalCount' => $data['cp_list']['pager']['count'],
    'CurrentPage' => $data['pager']['page'],
    'Count' => $data['pager']['count'],
))) ?>

<?php write_html($this->parseTemplate('StampRallyCpListItemCount.php', array(
    'limit'         => $data['pager']['count'],
    'totalCpCount'  => $data['cp_list']['pager']['count'],
))) ?>

<form id="frmSearchCp" name="frmSearchCp">
    <?php write_html(aafwWidgets::getInstance()->loadWidget('StampRallyCpList')->render(array(
        'search_condition' => $data['search_condition'],
        'cp_list' => $data['cp_list'],
        'cp_status_joined_image' => $data['cp_status_joined_image'],
        'cp_status_finished_image' => $data['cp_status_finished_image']
    ))) ?>
    <?php write_html($this->csrf_tag()); ?>
</form>

<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoCpDataListPager')->render(array(
    'TotalCount' => $data['cp_list']['pager']['count'],
    'CurrentPage' => $data['pager']['page'],
    'Count' => $data['pager']['count'],
))) ?>

<?php write_html($this->parseTemplate('StampRallyCpListItemCount.php', array(
    'limit'         => $data['pager']['count'],
    'totalCpCount'  => $data['cp_list']['pager']['count'],
))) ?>