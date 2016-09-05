<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['loginInfo']))?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['loginInfo'])) ?>

<article>
<?php write_html($this->parseTemplate('ActionHeader.php', array(
    'cp_id' => $data['cp']->id,
    'action_id' => $data['action_id'],
    'user_list_page' => true,
    'pageStatus' => $data['pageStatus'],
    'enable_archive' => false,
    'isHideDemoFunction' => false
))); ?>

<?php write_html($this->parseTemplate('CpUserListHeader.php', array(
    'cp_id' => $data['cp']->id,
    'action_id' => $data['action_id'],
    'current_page' => $data['current_page'],
    'reservation' => $data['reservation'],
    'is_group_fixed' => $data['is_group_fixed'],
    'brand' => $data['brand'],
    'is_include_type_announce' => $data['is_include_type_announce'],
    'fixed_target' => $data['fixed_target'],
))); ?>


<?php write_html($this->parseTemplate('EditOption.php', array(
    'cp_id' => $data['cp']->id,
    'action_id' => $data['action_id'],
    'reservation' => $data['reservation']
))) ?>

</article>

<link rel="stylesheet" href="<?php assign($this->setVersion('/css/jqueryUI.css'))?>">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>

<?php write_html($this->parseTemplate('MessageDeliveryConfirmBox.php', array(
    'reservation' => $data['reservation'],
    'cp_id' => $data['cp']->id,
    'pageStatus' => $data['pageStatus'],
))) ?>
<?php write_html($this->parseTemplate('CpDownloadList.php', array(
    'brand_id' => $data['brand']->id,
    'cp_id' => $data['cp']->id,
    'pageStatus' => $data['pageStatus'],
))) ?>

<?php $script = array('admin-cp/EditSettingMessageOptionService', 'admin-cp/CpMenuService'); ?>
<?php $param = array_merge($data['loginInfo'], array('script' => $script)) ?>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $param)); ?>
