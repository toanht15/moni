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

    <?php write_html($this->formHidden('cp_id', $data['pageData']['cp_id'])); ?>
    <?php write_html($this->formHidden('cp_action_type', CpAction::TYPE_POPULAR_VOTE)); ?>
    <?php write_html($this->formHidden('cp_action_id', $data['pageData']['action_id'])); ?>

    <section class="campaignPhotoWrap jsCampaignPopularVoteList">
        <?php write_html(aafwWidgets::getInstance()->loadWidget('CpPopularVoteList')->render($data['pageData'])) ?>
        <!-- /.campaignPhotoWrap --></section>

</article>

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<?php write_html($this->parseTemplate('MessageDeliveryConfirmBox.php', array(
    'reservation' => null,
    'cp_id' => $data['pageData']['cp_id'],
    'pageStatus' => $data['pageStatus'],
))) ?>

<?php $script = array('admin-cp/PopularVoteCampaignService','admin-cp/CpMenuService') ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', array_merge($data['pageStatus'], array('script' => $script)))); ?>
