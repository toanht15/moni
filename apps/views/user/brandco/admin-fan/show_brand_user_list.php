<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<?php $staticURL = aafwApplicationConfig::getInstance()->query('Static.Url') ?>

<article data-input_place="brand_user">
    <h1 class="hd1">
        <span class="hdInnerText">ユーザーリスト</span>
    </h1>
</article>

<div class="modal2 modal1 jsModal <?php assign($data['isSocialLikesEmpty'] ? 'jsShowModal' : '' );?>" id="socialLikeAlert">
    <section class="modalCont-small jsModalCont" id="jsModalCont">
        <p><span class="attention1">Facebookいいね！のデータにつきましては現在連携中です。</span></p>
        <p class="btnSet"><span class="btn3"><a href="#closeModal" class="middle1">OK</a></span></p>
    </section>
</div>

<?php write_html($this->formHidden('list_url', Util::rewriteUrl('admin-fan', 'api_get_search_brand_fan.json'))) ?>
<?php write_html($this->formHidden('search_url', Util::rewriteUrl('admin-fan', 'api_search_brand_fan.json'))) ?>
<?php write_html($this->formHidden('isManager', $data['isManager'])) ?>
<?php write_html($this->formHidden('update_rate', Util::rewriteUrl('admin-cp', 'api_fan_rate.json'))) ?>

<link rel="stylesheet" href="<?php assign(aafwApplicationConfig::getInstance()->query('Static.Url')) ?>/css/jqueryUI.css">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>

<script type="text/javascript" src="<?php assign($this->setVersion('/js/raty/jquery.raty.js'))?>"></script>
<?php $script = array('admin-cp/FanRateService','admin-fan/ShowUserListService') ?>
<?php $param = array_merge($data['pageStatus'], array('script' => $script)) ?>


<?php write_html($this->parseTemplate('BrandcoFooter.php', $param)); ?>
