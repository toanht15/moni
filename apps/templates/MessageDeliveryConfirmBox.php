<?php if ($data['reservation']): ?>
    <div class="modal1 jsModal" id="modal1">
        <section class="modalCont-small jsModalCont">
            <h1>確認</h1>
            <?php if($data['reservation']->delivery_type == CpMessageDeliveryReservation::DELIVERY_TYPE_IMMEDIATELY): ?>
                <p><strong class="attention1">即時配信されます</strong></p>
            <?php else: ?>
                <p><strong class="attention1"><?php assign($data['reservation']->deliveryDateString()); ?></strong>に配信されます</p>
            <?php endif; ?>

            <p class="btnSet">
                <span class="btn2"><a href="#closeModal" class="middle1">キャンセル</a></span>
                <span class="btn3"><a href="javascript:void(0)" id="submitReservationSchedule" data-mid="reservation-schedule" data-action="reservation_id=<?php assign($data['reservation']->id); ?>&status=3"
                                      data-url="<?php assign(Util::rewriteUrl('admin-cp','api_change_reservation_status.json'))?>">送信</a></span>
            </p>
        </section>
    <!-- /.modal1 --></div>

    <div class="modal1 jsModal" id="jsAnnounceDeliveryFix">
        <section class="modalCont-small jsModalCont">
            <h1>確認</h1>
            <p><strong class="attention1">当選者を確定します。よろしいですか？<br>賞品の発送をもって当選とする為、ユーザへの通知は行われません。</strong></p>

            <p class="btnSet">
                <span class="btn2">
                    <a href="#closeModal" class="middle1">キャンセル</a>
                </span>
                <span class="btn3">
                    <a href="javascript:void(0)" id="fixAnnounceDeliveryUser"
                                      data-action="reservation_id=<?php assign($data['reservation']->id); ?>&status=<?php assign(CpMessageDeliveryReservation::STATUS_SCHEDULED) ?>&delivery_type=<?php assign(CpMessageDeliveryReservation::DELIVERY_TYPE_NONE); ?>"
                                      data-mid="fixed-announce-delivery-user"
                                      data-url="<?php assign(Util::rewriteUrl('admin-cp', 'api_change_reservation_status.json')) ?>"
                        >確定</a>
                </span>
            </p>
        </section>
    </div>
<?php endif ?>

<div class="modal1 jsModal" id="modal_disable_edit_action">
    <section class="modalCont-small jsModalCont">
        <h1>確認</h1>
        <p><strong class="attention1"></strong></p>
        <p class="btnSet">
        <span class="btn3">
            <a href="javascript:void(0)" class="middle1" id="openSkeletonModal">OK</a>
        </span>
        </p>
    </section>
</div>
<div id="downloadModalArea">
<?php write_html(aafwWidgets::getInstance()->loadWidget('CpDataDownloadModal')->render(array(
    'brand_id' => $data['pageStatus']['brand']->id,
    'cp_id' => $data['cp_id'],
    'group_array' => null,
    'pageStatus' => $data['pageStatus'],
))); ?>
</div>
<?php write_html($this->parseTemplate('SkeletonModalTemplate.php', array('cp_id' => $data['cp_id']))) ?>
<?php write_html($this->parseTemplate('CpDemoConfirmBoxTemplate.php')) ?>
