<?php if (!$data['is_active_segment']): ?>
    <div class="sideAction">
        <p class="btn">
            <span class="btn1"><a href="javascript:void(0);" class="jsSaveSegmentConfirmBtn" data-s_status="<?php assign(Segment::STATUS_DRAFT) ?>">下書き保存</a></span>
            <span class="btn3"><a class="jsOpenSegmentConfirmModal">確定</a></span>
        </p>
        <!-- /.sideAction --></div>
<?php endif ?>
<ul class="pager2">
    <li class="prev"><a href="<?php assign(Util::rewriteUrl('admin-segment', 'segment_list')) ?>" class="iconPrev1">セグメント一覧</a></li>
    <!-- /.pager2 --></ul>
<!-- /.conditionSegmentWrap --></div>