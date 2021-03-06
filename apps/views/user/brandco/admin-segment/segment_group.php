<?php write_html(aafwWidgets::getInstance()->loadWidget("BrandcoHeader")->render($data["pageStatus"])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget("BrandcoAccountHeader")->render($data["pageStatus"])) ?>

<?php $disabled = $data['segment_info']['is_active_segment'] ? 'disabled' : '' ?>

<article>
    <form name="save_segment_form" action="<?php assign(Util::rewriteUrl('admin-segment', 'save_segment_group')); ?>" method="POST">
        <h1 class="hd1">セグメントグループ</h1>
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('segment_id', $data['segment_info']['id'])) ?>
        <?php write_html($this->formHidden('segment_type', Segment::TYPE_SEGMENT_GROUP)) ?>
        <?php write_html($this->formHidden('segment_status', PHPParser::ACTION_FORM, array('class' => 'jsSStatus'))) ?>

        <div class="conditionSegmentWrap jsErrorMsgWrap">
            <dl class="conditionSegmentMeta">
                <dt class="require1"><label>セグメントグループ名</label></dt>
                <dd>
                    <?php write_html($this->formText('name', PHPParser::ACTION_FORM, array('class' => 'jsSNameInput', $disabled => $disabled, 'maxlength' => 255))) ?>
                    <span class="iconError1 jsSNameInputError" style="display: none;"></span>
                    <span class="jsCheckToggleWrap">
                        <span class="sub">
                            <?php write_html($this->formCheckBox('description_flg', array($this->getActionFormValue('description_flg')), array('class' => 'jsCheckToggle', $disabled => $disabled), array(Segment::SEGMENT_DESCRIPTION_FLG_ON => 'メモ'))) ?>
                            <?php $attr_array = array('class' => 'jsCheckToggleTarget', $disabled => $disabled, 'maxlength' => '255');
                                if ($this->getActionFormValue('description_flg') == Segment::SEGMENT_DESCRIPTION_FLG_OFF) {
                                    $attr_array['style'] = 'display:none';
                                } ?>
                            <?php write_html($this->formText('description', PHPParser::ACTION_FORM, $attr_array)); ?>
                        </span></span></dd>
                <!-- /.conditionSegmentMeta --></dl>

            <div class="remainingBox">
                <h2 class="hd2">残りのユーザー</h2><p class="segmentMember"><span class="num jsRemainingUserCount"><img src="<?php assign($this->setVersion('/img/base/amimeLoading.gif'))?>" alt="loading" class="loadingImg"></span>名</p>
                <!-- /.remainingBox --></div>

            <div class="segmentWaap">
                <ul class="segmentList ui-sortable <?php if (!$data['segment_info']['is_active_segment']): ?>jsSegmentProvisionList<?php endif ?>">
                    <?php foreach ($data['default_sps'] as $default_provision): ?>
                        <?php write_html($this->parseTemplate("segment/DefaultSegmentProvisionContainer.php", array(
                            'segment_info' => $data['segment_info'],
                            'provision' => $default_provision
                        ))); ?>
                    <?php endforeach ?>
                </ul>
                <!-- /.segmentWaap --></div>

            <section class="segmentAncondition jsSPCheckToggleWrap">
                <?php write_html($this->formCheckBox('unconditional_flg', array($data['unconditional_flg']), array('class' => 'jsSPCheckToggle', $disabled => $disabled), array(Segment::UNCONDITIONAL_SEGMENT_FLG_ON => '未条件セグメントを使う'))) ?>
                <?php if ($data['segment_info']['is_active_segment']): ?>
                    <?php write_html($this->formHidden('unconditional_flg', $data['unconditional_flg'])); ?>
                <?php endif ?>
                <p class="supplement1">※残りのユーザーを未条件セグメントに設定する。</p>
                <div class="jsSPCheckToggleTarget" <?php if (!$data['unconditional_flg']): ?>style="display: none;"<?php endif ?>>
                    <ul class="segmentList">
                        <?php write_html($this->parseTemplate("segment/UnconditionalSegmentProvisionContainer.php", array(
                            'segment_info' => $data['segment_info']
                        ))); ?>
                        <!-- /.segmentList --></ul>
                    <!-- /.jsCheckToggleTarget --></div>
                <!-- /.segmentAncondition --></section>

            <div class="jsSPCheckToggleWrap">
                <?php write_html($this->formCheckBox('unclassified_flg', array($data['unclassified_flg']), array('class' => 'jsSPCheckToggle', $disabled => $disabled), array(Segment::UNCLASSIFIED_SEGMENT_FLG_ON => '除外セグメントを使う'))) ?>
                <?php if ($data['segment_info']['is_active_segment']): ?>
                    <?php write_html($this->formHidden('unclassified_flg', $data['unclassified_flg'])); ?>
                <?php endif ?>
                <p class="supplement1">※上記のセグメントから除外させる条件を設定する。</p>
                <div class="jsSPCheckToggleTarget" <?php if (!$data['unclassified_flg']): ?>style="display: none;"<?php endif ?>>
                    <ul class="segmentList">
                        <?php write_html($this->parseTemplate("segment/UnclassifiedSegmentProvisionContainer.php", array(
                            'segment_info' => $data['segment_info'],
                            'provision' => $data['unclassified_sp']
                        ))); ?>
                        <!-- /.segmentList --></ul>
                    <!-- /.jsCheckToggleTarget --></div>
                <!-- /.jsCheckToggleWrap --></div>

            <?php write_html($this->parseTemplate('segment/CreateSegmentConfirmButton.php', array('is_active_segment' => $data['segment_info']['is_active_segment']))) ?>
    </form>
</article>

<div class="modal1 jsModal" id="segmentProvisionConditionSelector">
    <!-- /.modal1 --></div>
<?php write_html($this->parseTemplate('segment/CreateSegmentConfirmBox.php')) ?>
<?php write_html($this->parseTemplate('segment/SegmentProvisionCreatorConfirmBox.php')) ?>
<?php write_html($this->parseTemplate('segment/SegmentGroupLimitExceededMessage.php', array('segment_limit' => $data['segment_limit'],'segment_type' => Segment::TYPE_SEGMENT_GROUP))) ?>

<?php write_html($this->parseTemplate('segment/JsEmbedList.php')) ?>

<?php $params = array_merge($data['pageStatus'], array('script' => array('admin-segment/SegmentCommonService','admin-segment/SegmentGroupService'))) ?>
<?php write_html($this->parseTemplate("BrandcoFooter.php", $params)); ?>
