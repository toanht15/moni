<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus']))?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<article>
    <?php write_html($this->parseTemplate('ActionHeader.php',array(
        'cp_id' => $data['pageData']['cp_id'],
        'action_id' => $data['pageData']['action_id'],
        'user_list_page' => true,
        'pageStatus' => $data['pageStatus'],
        'enable_archive' => false,
        'isHideDemoFunction' => false,
    ))); ?>

    <?php write_html($this->formHidden('action_panel_hidden_url', Util::rewriteUrl('admin-cp', 'api_change_action_panel_hidden_flg.json'))) ?>
    <?php write_html($this->formHidden('photo_edit_modal_url', Util::rewriteUrl('admin-cp', 'api_get_photo_edit_modal.json'))) ?>
    <?php write_html($this->formHidden('photo_list_url', Util::rewriteUrl('admin-cp', 'api_get_photo_list.json'))) ?>
    <?php write_html($this->formHidden('cp_id', $data['pageData']['cp_id'])); ?>
    <?php write_html($this->formHidden('cp_action_type', CpAction::TYPE_PHOTO)); ?>

    <section class="campaignPhotoWrap jsCampaignPhotoList">
        <?php write_html(aafwWidgets::getInstance()->loadWidget('CpPhotoList')->render($data['pageData'])) ?>
        <!-- /.campaignPhotoWrap --></section>

</article>

<div class="modal1 jsModal" id="photo_edit_modal">
    <section class="modalCont-large jsModalCont">

        <div class="modalCampaignPhoto jsPhotoEditModal">
            <!-- /.modalCampaignPhoto --></div>

        <p><a href="#closeModal" class="modalCloseBtn">キャンセル</a></p>
    </section>
    <!-- /.modal1 --></div>

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<?php write_html($this->parseTemplate('MessageDeliveryConfirmBox.php', array(
    'reservation' => null,
    'cp_id' => $data['pageData']['cp_id'],
    'pageStatus' => $data['pageStatus'],
))) ?>

<?php $script = array('admin-cp/PhotoCampaignService','admin-cp/CpMenuService') ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', array_merge($data['pageStatus'], array('script' => $script)))); ?>
