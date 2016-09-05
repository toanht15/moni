<?php
$service_factory = new aafwServiceFactory();
$cp_service = $service_factory->create('CpFlowService');
$first_action = $cp_service->getFirstActionOfCp($data['message_info']['cp_action']->getCp()->id);
$actionData = $first_action->getCpActionData();
?>

<section class="message_end" id="message_<?php assign($data['message_info']['message']->id); ?>">
<section class="messageText">
    <p class="attention1">
        <strong><?php assign($actionData->title); ?></strong><br />の表示期間は終了となりました。
    </p>
    <p>期間外の為、これより先は表示・進むことができません。ご了承ください。</p>
</section>

<!-- /.message --></section>
<!-- /.messageWrap --></section>

<?php // 締め切り後はメディア導線を表示する ?>
<div id="jsShowMoniplaPR">
    <script type="text/javascript">if (typeof(UserMessageThreadMoniplaPRService) !== 'undefined') { UserMessageThreadMoniplaPRService.showMoniplaPR();}</script>
</div>