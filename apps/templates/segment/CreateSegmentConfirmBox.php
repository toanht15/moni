<div class="modal1 jsModal" id="createSegmentConfirmBox">
    <section class="modalCont-medium jsModalCont" style="display: block; opacity: 1; top: 30px;">
        <p style="margin-bottom: 10px;">セグメントを確定すると集計が開始されます。<br>集計を開始してよろしいですか？</p>
        <p class="supplement1">
            ・条件の変更を行うには集計を停止する必要があります。<br>
            ・条件を途中で変更すると集計したデータは上書きされます。
            <!-- /.suplement --></p>
        <ul class="btnSet">
            <li class="btn3 disableButton"><a href="javascript:void(0);" class="large1 jsSaveSegmentConfirmBtn" data-s_status="<?php assign(Segment::STATUS_ACTIVE) ?>">OK</a></li>
            <li class="btn2"><a href="#closeModal" class="large1">キャンセル</a></li>
        </ul>
    </section>
    <!-- /#segmentGroupConfirmBox --></div>