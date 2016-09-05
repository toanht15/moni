<?php write_html($this->parseTemplate('BrandcoModalHeader.php', array('brand' => $data['brand']))) ?>
<article class="modalInner-large">
    <?php write_html(aafwWidgets::getInstance()->loadWidget('AddActionSkeleton')->render($data)); ?>
    <footer>
        <p class="btnSet">
            <span class="btn2"><a id="cancelChanges">キャンセル</a></span>
            <span class="btn3"><a id="confirmSave">保存</a></span>
        </p>
    </footer>
</article>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<?php write_html($this->parseTemplate('BrandcoModalFooter.php', array('script' => array('admin-cp/EditCustomizeSkeletonService')))) ?>
