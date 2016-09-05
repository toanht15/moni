<?php $disable = ($data['action']->status == CpAction::STATUS_FIX)?'disabled':''  ?>

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

        <?php write_html(aafwWidgets::getInstance()->loadWidget($data['cp_action_detail']['widget_class'])->render(array('cp_id'=> $data['cp_id'], 'ActionForm' => $data['ActionForm'], 'ActionError'=>$data['ActionError'], 'action_id'=>$data['action_id'], 'pageStatus'=>$data['pageStatus']))) ?>
    </form>
<!-- /.moduleEditWrap --></section>

<div class="moduleCheck">
    <ul>
        <?php if($data['action']->status == CpAction::STATUS_FIX): ?>
            <li class="btn1"><a href="javascript:void(0)" id="editButton" data-action="action_id=<?php assign($data['action']->id)?>"
                                data-url= "<?php assign(Util::rewriteUrl('admin-cp','api_change_action_status.json')) ?>">確定解除</a></li>

        <?php else: ?>
            <li class="btn2"><a href="javascript:void(0)" class="small1" id="submitDraft">下書き保存</a></li>
            <li class="btn3"><a href="javascript:void(0)" id="submit">内容確定</a></li>
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
    <!-- /.moduleCheck --></div>