<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<article>
<h1 class="hd1">キャンペーン作成</h1>
<section class="makeStepTypeWrap">
<?php write_html($this->csrf_tag()); ?>
<?php if ($data['draft_cp']): ?>
    <section class="makeStepType">
        <h1><label><input type="radio" name="makeStepTypeRadio">下書きキャンペーン</label></h1>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('CpMakeStepType')->render(
        array(
            'type' => Cp::SKELETON_DRAFT,
            'cps' => $data['draft_cp'],
            'cp_count' => $data['draft_cp_count'],
            'brand' => $data['brand'],
        )
    )) ?>
        <!-- /.makeStepType --></section>
<?php endif; ?>


<section class="makeStepType">
    <h1><label><input type="radio" name="makeStepTypeRadio">おすすめキャンペーンセットを使う</label></h1>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('CpMakeTemplate')->render(array())) ?>
    <!-- /.makeStepType --></section>

<section class="makeStepType">
    <h1><label><input type="radio" name="makeStepTypeRadio" checked="checked">基本セットを使う</label></h1>
    <?php write_html($this->parseTemplate('CpBasicSkeleton.php', $data['pageStatus'])); ?>
<!-- /.makeStepType --></section>

<section class="makeStepType">
    <h1><label><input type="radio" name="makeStepTypeRadio">常設キャンペーンセット(インセンティブ無し)</label></h1>
    <?php write_html($this->parseTemplate('CpPermanentSkeleton.php', array('pageStatus' => $data['pageStatus'],'canUsePaymentModule'=>$data['can_use_payment_module']))); ?>
<!-- /.makeStepType --></section>

<?php if ($data['published_cps']): ?>
    <section class="makeStepType">
        <h1><label><input type="radio" name="makeStepTypeRadio">過去のキャンペーンのコピーを使う</label></h1>
    <?php write_html(aafwWidgets::getInstance()->loadWidget('CpMakeStepType')->render(
        array(
            'type' => Cp::SKELETON_COPY,
            'cps' => $data['published_cps'],
            'cp_count' => $data['published_cp_count'],
            'brand' => $data['brand'],
        )
    )) ?>
        <!-- /.makeStepType --></section>
<?php endif; ?>

<!--    <section class="makeStepType">-->
<!--        <h1><label><input type="radio" name="makeStepTypeRadio">新しいキャンペーンを作る</label></h1>-->
<!--        --><?php //write_html(aafwWidgets::getInstance()->loadWidget('CpCreateNewSkeleton')->render()) ?>
<!--    </section>-->

<!-- /.makeStepType --></section>

</article>

<div class="modal2 jsModal" id="modal1">
    <section class="modalCont-small jsModalCont" id="jsModalCont">
        <h1>確認</h1>
        <p><span class="attention1">このキャンペーンを削除しますか？</span></p>
        <p class="btnSet"><span class="btn2"><a href="#closeModal" class="middle1">キャンセル</a></span><span class="btn4"><a id="delete_cp" href="javascript:void(0)" class="middle1">削除する</a></span></p>
    </section>
</div>

<?php $script = array('admin-cp/EditSettingSkeletonService'); ?>
<?php $param = array_merge($data['pageStatus'], array('script' => $script)) ?>

<?php write_html($this->parseTemplate('BrandcoFooter.php', $param)); ?>
