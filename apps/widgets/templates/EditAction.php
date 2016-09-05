<?php
$service_factory = new aafwServiceFactory();
/** @var CpFlowService $cp_flow_service */
$cp_flow_service = $service_factory->create('CpFlowService');

$cp_action_group = $cp_flow_service->getCpActionGroupByAction($data['action_id']);

//アクションを編集することができるかどうかチェックする
if ($cp_action_group->order_no != 1 || !$data['isAgent'] || $data['cp']->type == Cp::TYPE_MESSAGE){
    $can_edit_action = true;
} else {
    $can_edit_action = false;
}

?>

<section class="moduleEditWrap">
    <form id="actionForm" name="actionForm" action="<?php assign(Util::rewriteUrl('admin-cp', $data['cp_action_detail']['form_action'])); ?>" method="POST" enctype="multipart/form-data" >
        <?php write_html($this->csrf_tag()); ?>
        <?php write_html($this->formHidden('action_id', $data['action_id'])) ?>
        <?php $request_url = explode('?mid=',$_SERVER['REQUEST_URI']) ?>
        <?php write_html($this->formHidden('callback', Util::getHttpProtocol().'://'.Util::getMappedServerName().$request_url[0])) ?>
        <?php if ($data['ActionForm']['action'] == 'edit_action'): ?>
            <?php write_html($this->formHidden('path', $data['ActionForm']['action'].'/'.$data['cp_id'].'/'.$data['action_id'])) ?>
        <?php else: ?>
            <?php write_html($this->formHidden('path', $data['ActionForm']['action'].'/'.$data['action_id'])) ?>
        <?php endif; ?>
        <?php write_html($this->formHidden('save_type', '', array('id'=>'save_type'))) ?>
        <?php
        if ($data['reservation']) {
            $action = $cp_flow_service->getCpActionById($data['action_id']);
            $cp_member_count = $action->getMemberCount();
        }
        if ($data['cp']->type == Cp::TYPE_CAMPAIGN || ($data['cp']->type == Cp::TYPE_MESSAGE && $cp_member_count && $cp_member_count[CpUserService::CACHE_TYPE_SEND_MESSAGE] > 0)) {
            $disable = true;
        } else {
            $disable = !$data['action']->status;
        } ?>

            <?php write_html(aafwWidgets::getInstance()->loadWidget($data['cp_action_detail']['widget_class'])->render(array('cp_id'=> $data['cp_id'], 'ActionForm' => $data['ActionForm'], 'ActionError'=>$data['ActionError'], 'action_id'=>$data['action_id'], 'is_fan_list_page'=> $disable, 'pageStatus'=> $data['pageStatus']))) ?>
    </form>
<!-- /.moduleEditWrap --></section>
<!-- /.campaignEditCont --></section>

<footer class="moduleCheck">
<?php if($can_edit_action): ?>
    <ul>
        <?php if (($data['cp']->type == Cp::TYPE_CAMPAIGN && $data['action']->status == CpAction::STATUS_FIX)
            || ($data['cp']->type == Cp::TYPE_MESSAGE && $cp_member_count && $cp_member_count[CpUserService::CACHE_TYPE_SEND_MESSAGE] > 0)): ?>
            <li class="btn3"><a href="javascript:void(0)" id="submit" class="">更新</a></li>
        <?php else: ?>
            <?php if ($data['action']->status == CpAction::STATUS_DRAFT): ?>
                <li class="btn2"><a href="javascript:void(0)" id="submitDraft" class="small1">下書き保存</a></li>
                <li class="btn3"><a href="javascript:void(0)" id="submit" class="">内容確定</a></li>
            <?php elseif($data['action']->status == CpAction::STATUS_FIX): ?>
                <li class="btn1"><a href="javascript:void(0)" id="editButton" data-action="action_id=<?php assign($data['action']->id)?>"
                                    data-url= "<?php assign(Util::rewriteUrl('admin-cp','api_change_action_status.json')) ?>">確定解除</a></li>
            <?php endif; ?>
        <?php endif; ?>
    </ul>
    <p class="urlCopyWrap">
        <a href="javascript:void(0);" class="iconCopy1 jsCopyToClipboardBtn" data-clipboard-text="<?php assign(Util::rewriteUrl('messages', 'thread', array($data['cp_id']), array('scroll' => 'ca_' . $data['action_id']))) ?>">URLをコピー</a>
          <span class="iconHelp">
            <span class="text">ヘルプ</span>
            <span class="textBalloon1">
              <span>モジュールに直接遷移をさせるURLです。<br>未ログインの場合はログインが求められます。</span>
            <!-- /.textBalloon1 --></span>
          <!-- /.iconHelp --></span>
        <!-- /.urlCopyWrap --></p>
<?php endif ?>
<!-- /.moduleCheck --></footer>
