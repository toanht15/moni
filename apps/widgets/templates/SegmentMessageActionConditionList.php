<div class="userListSegment <?php assign($data['segments'] ? 'jsSegmentToggleWrap' : '' ) ?>">
<?php write_html($this->formHidden('provision_id_array', json_encode($data['provision_id_array']))) ?>
<?php if($data['segments']): ?>
    <p class="segmentItemsWrap">
        <a href="javascript:void(0)" class="addSegment jsSegmentToggle">
            <span class="inner">セグメントを追加(or条件)</span>
            <!-- /.addSegment --></a>
        
        <?php foreach($data['segment_provision_sessions'] as $provision): ?>
            <span class="segmentItemTag"><?php assign($provision->name) ?>
                <a href="javascript:void(0)" class="itemDelete jsDeleteSegmentConditionSession"
                   data-provision_id="<?php assign($provision->id)?>" data-segment_id="<?php assign($provision->segment_id)?>"
                   data-provision_name="<?php assign($provision->name) ?>" >削除
                </a>
            </span>
        <?php endforeach; ?>
        <!-- /.segmentItemsWrap --></p>

    <div class="userListSegmentDetail jsSegmentToggleTarget">
        <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsSegmentToggle">閉じる</a></p>
        <p class="selectedLabel"><span class="hogehoge">選択中の合計</span><span class="selectedNumber"><strong><?php assign($data['target_user_count'])?></strong>名</span></p>

        <div class="segmentItemList">
            <ul class="segmentItemListInner">
                <?php foreach ($data['segments'] as $segment): ?>
                    <?php write_html(aafwWidgets::getInstance()->loadWidget('SegmentMessageActionConditionContainer')->render(
                        array(
                            'segment' => $segment,
                            'provision_id_array' => $data['provision_id_array'],
                            'segment_condition_session' => $data['segment_condition_session']
                        )
                    )); ?>
                <?php endforeach ?>
                <!-- /.segmentItemListInner --></ul>

            <?php foreach ($data['segments'] as $segment): ?>
                <?php write_html(aafwWidgets::getInstance()->loadWidget('SegmentProvisionTooltipList')->render(array('segment' => $segment))); ?>
            <?php endforeach ?>

        <!-- /.segmentItemList --></div>
        <p class="btnSet"><span class="btn3"><a href="javascript:void(0)" class="small1 jsApplySegmentCondition">反映</a></span></p>
        <!-- /.userListSegmentDetail --></div>
    <!-- /.userListSegment --></div>
<?php else: ?>
    <!--Segment Conditionがない場合、Segment設定URLが表示される-->
        <p class="newSettingSegment">
            <a href="<?php assign(Util::rewriteUrl('admin-segment', 'segment_list')); ?>" class="font-all" target="_blank">セグメントを設定する</a>
              <span class="iconHelp">
                <span class="text">セグメントとは？</span>
                <span class="textBalloon1">
                  <span>条件に基づくユーザが属するセグメントを作成する機能です。<br>セグメントを作成すると、日別でセグメントに属するユーザの推移を観測できます。</span>
                <!-- /.textBalloon1 --></span>
                <!-- /.iconHelp --></span>
            <!-- /.newSettingSegment --></p>
<?php endif; ?>
<!-- /.userListSegment --></div>
