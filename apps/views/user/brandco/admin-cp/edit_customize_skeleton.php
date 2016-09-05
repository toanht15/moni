<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<article>

    <?php if($data['show_segment_message_action_alert']): ?>
        <div class="segmentPresetInfo">
            <p>セグメント機能からメッセージ作成中です</p>
            <!-- /.segmentPresetInfo --></div>
    <?php endif; ?>

    <h1 class="hd1"><?php assign(cp::$cp_type_array[$data['type']])?>フロー編集</h1>
    <section class="makeStepTypeWrap">
        <section class="makeStepType">
        <?php switch($data['type']){
            case cp::TYPE_CAMPAIGN:
                write_html(aafwWidgets::getInstance()->loadWidget('CpCreateNewSkeleton')->render($data));
                break;
            case cp::TYPE_MESSAGE:
                write_html(aafwWidgets::getInstance()->loadWidget('MsgCreateNewSkeleton')->render(array("cps_type" => $data['type'])));
                break;
        }
        ?>
            <!-- /.makeStepType --></section>
        <!-- /.makeStepTypeWrap --></section>
</article>

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<?php $script = array('admin-cp/EditCustomizeSkeletonService'); ?>
<?php $param = array_merge($data['pageStatus'], array('script' => $script)) ?>
<?php write_html($this->parseTemplate('BrandcoFooter.php', $param)); ?>
