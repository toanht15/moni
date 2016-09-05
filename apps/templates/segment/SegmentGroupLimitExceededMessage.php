<div class="modal1 jsModal" id="segmentLimitExceededMsg">
    <section class="modalCont-small jsModalCont">
        <?php if($data['segment_type'] == Segment::TYPE_SEGMENT_GROUP):?>
            <p style="margin-bottom: 10px;">セグメントグループの作成可能数が不足しています</p>
            <p class="supplement1">
                ・セグメントグループの作成は<?php assign($data['segment_limit']) ?>個までです。<br>
                ・集計中のセグメントグループをアーカイブしてから、確定してください。
                <!-- /.suplement --></p>
        <?php elseif($data['segment_type'] == Segment::TYPE_CONDITIONAL_SEGMENT): ?>
            <p style="margin-bottom: 10px;">条件セグメントの作成可能数が不足しています</p>
            <p class="supplement1">
                ・条件セグメントの作成は<?php assign($data['segment_limit']) ?>個までです。<br>
                ・集計中の条件セグメントをアーカイブしてから、確定してください。
                <!-- /.suplement --></p>
        <?php endif; ?>
        <p class="btnSet">
            <span class="btn3 disableButton"><a href="#closeModal" class="middle1">OK</a></span>
        </p>
    </section>
    <!-- /.modal1 --></div>