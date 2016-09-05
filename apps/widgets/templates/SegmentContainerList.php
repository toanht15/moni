<div class="segmentItemList jsSContainerList">
    <ul class="segmentItemListInner jsSContainerList">
        <?php foreach ($data['segments'] as $segment): ?>
            <?php write_html(aafwWidgets::getInstance()->loadWidget('SegmentContainer')->render(array('segment' => $segment,'sp_ids_array' => $data['sp_ids_array']))); ?>
        <?php endforeach ?>
        <!-- /.segmentItemList --></ul>

    <?php foreach ($data['segments'] as $segment): ?>
        <?php write_html(aafwWidgets::getInstance()->loadWidget('SegmentProvisionTooltipList')->render(array('segment' => $segment))); ?>
    <?php endforeach ?>

<!-- /.segmentItemList --></div>



