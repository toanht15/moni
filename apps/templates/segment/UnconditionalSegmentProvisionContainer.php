<li class="segmentItem">
    <?php if (!$data['segment_info']['is_active_segment']): ?>
        <p class="segmentNoMove">位置固定</p>
    <?php endif ?>
    <div class="segmentItemInner">
        <div class="segmentNames">
            <p><span class="segmentName">未条件セグメント</span></p>
            <p class="segmentMember"><span class="jsUnconditionalUserCount"><img src="<?php assign($this->setVersion('/img/base/amimeLoading.gif'))?>" alt="loading" class="loadingImg"></span>名</p>
            <!-- /.segmentNames --></div>
        <p class="supplement1">※一覧からアクションを行えます。</p>
        <!-- /.segmentItemInner --></div>
    <!-- /.segmentItem --></li>