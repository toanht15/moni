<form id="actionForm" name="actionForm" action="<?php assign(Util::rewriteUrl( 'admin-cp', 'api_change_reservation_status' )); ?>" method="POST" >
    <?php write_html($this->csrf_tag()); ?>
<section class="campaignEditCont">
    <div class="messageReserved">
        <?php if($data["reservation"]->delivery_type == CpMessageDeliveryReservation::DELIVERY_TYPE_NONE): ?>
            <?php if($data["reservation"]->isScheduled()):?>
                <h1>確定処理中</h1>
                <p class="attention1">データの更新処理を行っております。<br />処理が終了するまでお待ちください。</p>
                <dl>
                    <dt>対象</dt>
                    <dd><?php assign(number_format($data["targets_count"])); ?>人</a></dd>
                </dl>
            <?php else:?>
                <h1>確定処理失敗</h1>
                <p class="attention1">確定処理に失敗しました。<br />システム管理者へ問い合わせて下さい。</p>
            <?php endif;?>
        <?php else: ?>
            <?php if($data["reservation"]->isScheduled()):?>
            <h1>配信予約済</h1>
            <?php elseif($data["reservation"]->isDelivering()):?>
            <h1>メッセージ配信中</h1>
            <?php elseif($data["reservation"]->isFailedDelivering()):?>
            <h1>配信失敗</h1>
            <p class="attention1">メッセージの配信に失敗しました。<br />システム管理者へ問い合わせて下さい。</p>
            <?php endif;?>
            <dl>
                <dt>配信対象</dt>
                <dd><?php assign(number_format($data["targets_count"])); ?>人</a></dd>
                <dt>配信予定</dt>
                <?php if($data['reservation']->delivery_type == CpMessageDeliveryReservation::DELIVERY_TYPE_IMMEDIATELY): ?>
                <dd>即時配信</a></dd>
                <?php else: ?>
                <dd><?php assign($data["reservation"]->deliveryDateString()); ?></a></dd>
                <?php endif; ?>
                <dt>メール通知</dt>
                <dd>あり</dd>
            </dl>
        <?php endif; ?>
    <!-- /.messageReserved --></div>
<!-- /.campaignEditCont  --></section>
</form>
<?php if($data["reservation"]->delivery_type != CpMessageDeliveryReservation::DELIVERY_TYPE_NONE && $data["reservation"]->isScheduled()):?>
<footer class="moduleCheck">
    <ul>
        <li class="btn1"><a href="javascript:void(0)" id="submitReservationUnSchedule" data-mid="reservation-unschedule" data-action="reservation_id=<?php assign($data['reservation']->id); ?>&status=2"
                            data-url="<?php assign(Util::rewriteUrl('admin-cp','api_change_reservation_status.json'))?>">予約解除</a></li>
    </ul>
<!-- /.moduleCheck --></footer>
<?php endif;?>