<?php $disable = ($data['reservation']->canEdit())?'':'disabled'  ?>

<?php $time_hh = array() ?>
<?php for ($i = 0; $i < 24; $i++): ?>
    <?php if ($i < 10): ?>
        <?php $j = '0' . $i; ?>
    <?php else: ?>
        <?php $j = $i; ?>
    <?php endif; ?>
    <?php $time_hh[$j] = $j; ?>
<?php endfor; ?>

<?php $time_mm = array(); ?>
<?php for ($i = 0; $i < 60; $i++): ?>
    <?php if ($i < 10): ?>
        <?php $j = '0' . $i; ?>
    <?php else: ?>
        <?php $j = $i; ?>
    <?php endif; ?>
    <?php $time_mm[$j] = $j; ?>
<?php endfor; ?>

<form id="actionForm" name="actionForm" action="<?php assign(Util::rewriteUrl( 'admin-cp', 'save_message_option' )); ?>" method="POST" >
    <?php write_html($this->csrf_tag()); ?>
    <?php write_html($this->formHidden('reservation_id', $data['reservation']->id)) ?>
    <?php write_html($this->formHidden('cp_action_id', $data['action_id'])) ?>
    <?php write_html($this->formHidden('status', '', array('id' => 'status'))) ?>

    <section class="campaignEditCont">

        <div class="sendDate">
            <h1>配信予定</h1>
            <ul>
                <?php write_html($this->formRadio('delivery_type', PHPParser::ACTION_FORM, array($disable => $disable), array('1' => '即時配信', '2' => '予約配信'))); ?>

                <?php write_html($this->formText('delivery_date', PHPParser::ACTION_FORM, array('maxlength' => '10', 'class' => 'jsDate inputDate', 'placeholder' => '年/月/日', $disable => $disable))); ?>
                <?php write_html($this->formSelect('delivery_time_hh', PHPParser::ACTION_FORM, array('class' => 'inputTime', $disable => $disable), $time_hh)); ?><span class="coron">:</span>
                <?php write_html($this->formSelect('delivery_time_mm', PHPParser::ACTION_FORM, array('class' => 'inputTime', $disable => $disable), $time_mm)); ?>

                <?php if ( $this->ActionError && !$this->ActionError->isValid('delivery_type')): ?>
                    <p class="attention1"><?php assign ( $this->ActionError->getMessage('delivery_type') )?></p>
                <?php endif; ?>

                <?php if ( $this->ActionError && !$this->ActionError->isValid('delivery_date')): ?>
                    <p class="attention1"><?php assign ( $this->ActionError->getMessage('delivery_date') )?></p>
                <?php endif; ?>

            </ul>
        <!-- /.sendDate --></div>

        <div class="mailOption">
        <h1>メール通知</h1>
            <ul>
                <li><?php write_html($this->formRadio('send_mail_flg', PHPParser::ACTION_FORM, array($disable => $disable), array('1' => 'あり', '2' => 'なし'))); ?></li>

                <?php if ( $this->ActionError && !$this->ActionError->isValid('send_mail_flg')): ?>
                    <p class="attention1"><?php assign ( $this->ActionError->getMessage('send_mail_flg') )?></p>
                <?php endif; ?>


            </ul>
          <!-- /.mailOption --></div>
        <!-- /.messageOptionWrap  --></section>

        <footer class="moduleCheck">
            <ul>
                <?php if($data['reservation']->status == CpMessageDeliveryReservation::STATUS_FIX): ?>
                    <li class="btn1"><a href="javascript:void(0)" id="submitReservationUnFix" data-mid="reservation-unfix" data-action="reservation_id=<?php assign($data['reservation']->id); ?>&status=1"
                                        data-url="<?php assign(Util::rewriteUrl('admin-cp','api_change_reservation_status.json'))?>">確定解除</a></li>
                <?php else: ?>
                    <li class="btn2"><a href="javascript:void(0)" id="submitReservationDraft" class="small1">下書き保存</a></li>
                    <li class="btn3"><a href="javascript:void(0)" id="submitReservationFix">内容確定</a></li>
                <?php endif; ?>
            </ul>
    <!-- /.moduleChedck --></footer>

</form>
