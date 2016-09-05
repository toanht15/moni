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

    <?php write_html($this->formHidden('tweet_action_panel_hidden_url', Util::rewriteUrl('admin-cp', 'api_change_tweet_action_panel_hidden_flg.json'))) ?>
    <?php write_html($this->formHidden('tweet_list_url', Util::rewriteUrl('admin-cp', 'api_get_tweet_list.json'))) ?>
    <?php write_html($this->formHidden('cp_id', $data['pageData']['cp_id'])); ?>
    <?php write_html($this->formHidden('cp_action_type', CpAction::TYPE_TWEET)); ?>

    <section class="campaignPhotoWrap jsCampaignTweetList">
        <?php write_html(aafwWidgets::getInstance()->loadWidget('CpTweetList')->render($data['pageData'])) ?>
        <!-- /.campaignPhotoWrap --></section>

</article>

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<?php write_html($this->parseTemplate('MessageDeliveryConfirmBox.php', array(
    'reservation' => null,
    'cp_id' => $data['pageData']['cp_id'],
    'pageStatus' => $data['pageStatus'],
))) ?>

<?php $script = array('admin-cp/TweetCampaignService','admin-cp/CpMenuService') ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', array_merge($data['pageStatus'], array('script' => $script)))); ?>
