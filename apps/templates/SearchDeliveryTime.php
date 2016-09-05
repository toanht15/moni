<div class="sortBox jsAreaToggleTarget" data-search_type="<?php assign(CpCreateSqlService::SEARCH_DELIVERY_TIME.'/'.$data['action']->id)?>">
    <p class="boxCloseBtn"><a href="javascript:void(0)" class="jsAreaToggle">閉じる</a></p>
    <div class="range jsCheckToggleWrap">
        <ul>
            <?php
                $service_factory = new aafwServiceFactory();
                /** @var CpMessageDeliveryService $delivery_service */
                $delivery_service = $service_factory->create('CpMessageDeliveryService');
                $delivery_reservations = $delivery_service->getDeliveredCpMessageDeliveryReservationByCpActionId($data['action']->id);
            ?>
            <?php foreach ($delivery_reservations as $delivery_reservation): ?>
                <li>
                    <?php write_html($this->formCheckbox(
                        'search_delivery_time/'.$data['action']->id.'/'.$delivery_reservation->id,
                        $this->POST ? PHPParser::ACTION_FORM : $data['search_delivery_time'][CpCreateSqlService::PARTICIPATE_COMPLETE],
                        array('checked' => $data['search_delivery_time']['search_delivery_time/'.$data['action']->id.'/'.$delivery_reservation->id] ? "checked" : ""),
                        array($delivery_reservation->id => date("Y/m/d H:i", strtotime($delivery_reservation->updated_at)))
                    ))?>
                </li>
            <?php endforeach; ?>
            <li>
                <?php write_html($this->formCheckbox(
                    'search_delivery_time/'.$data['action']->id,
                    $this->POST ? PHPParser::ACTION_FORM : $data['search_delivery_time'][CpCreateSqlService::PARTICIPATE_COMPLETE],
                    array('checked' => $data['search_delivery_time']['search_delivery_time/'.$data['action']->id] ? "checked" : ""),
                    array(CpCreateSqlService::DID_NOT_SEND => '未送信')
                ))?>
            </li>
        </ul>

        <!-- /.range --></div>
    <p class="btnSet">
        <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-clear_type="<?php assign(CpCreateSqlService::SEARCH_DELIVERY_TIME.'/'.$data['action']->id)?>" data-search_no="<?php assign($this->search_no)?>">リセット</a></span>
        <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-search_type="<?php assign(CpCreateSqlService::SEARCH_DELIVERY_TIME.'/'.$data['action']->id)?>" data-search_no="<?php assign($this->search_no)?>">絞り込む</a></span>
    </p>
<!-- /.sortBox --></div>
