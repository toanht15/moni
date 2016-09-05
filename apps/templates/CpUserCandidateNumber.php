<?php
    if(!$this->check_no) {
        $this->check_no = 0;
    }
    $this->check_no += 1;

$service_factory = new aafwServiceFactory();
/** @var ManagerService $manager_service */
$manager_service = $service_factory->create('ManagerService');
?>
<div class="checkedUserWrap jsAreaToggleWrap">
    <?php if($data['exist_target']):?>
        <p class="checkedUser" data-checked=""><span class="slectedBox">選択中<strong class="jsCountArea">---</strong>人</span><span class="btn3_area"><span class="jsAreaToggle" data-update_type="update" data-check_no=<?php assign($this->check_no)?>>対象の変更</span></span></p>
        <?php write_html($this->formHidden('check_info', 'update/'.$this->check_no))?>
    <?php else:?>
        <p class="checkedUser" data-checked=""><span class="slectedBox">選択中<strong class="jsCountArea">---</strong>人</span><span class="btn3_area"><span class="jsAreaToggle" data-update_type="insert" data-check_no=<?php assign($this->check_no)?>>対象に入れる</span></span></p>
        <?php write_html($this->formHidden('check_info', 'insert/'.$this->check_no))?>
    <?php endif;?>
<!-- 自動抽選 -->
<?php if ($data['is_include_type_announce']): ?>
    <p class="selectRandom">
      <small class="or">or</small>
      <span class="btn3">
          <?php if($data['fixed_target']):?>
              <span class="small1 jsOpenModal">抽選する</span>
          <?php else: ?>
              <a class="small1" data-update_type="random_insert" href="javascript:void(0)">抽選する</a>
          <?php endif; ?>
      </span>
    <!-- /.selectRandom --></p>
    <p class="iconHelp">
      <span class="text"></span>
      <span class="textBalloon1">
        <span>絞り込まれたリスト全体から、<br>自動抽選で対象を抽出します</span>
      <!-- /.textBalloon1 --></span>
    <!-- /.iconHelp --></p>
<?php endif ?>

    <!--当選者確定 - 解除 -->
    <?php if ($data['has_fix_target_step']): ?>
        <p class="elected">
            <small class="next">⇒</small>
            <?php if ($data['selected_target'] && !$data['fixed_target'] && !$manager_service->isAgentLogin()): ?>
                <span class="btn4">
                    <a class="small1 jsOpenModal" data-update_type="fix_target" href="javascript:void(0)">当選者確定</a>
                </span>
            <?php elseif ($data['fixed_target'] && $data['manager_permission']): ?>
                <span class="btn1">
                 <a class="small1 jsOpenModal" data-update_type="cancel_fix_target" href="javascript:void(0)">当選者解除</a>
                </span>
            <?php elseif ($data['fixed_target'] && !$data['manager_permission']): ?>
                <span class="btn4">
                    <span class="small1 jsOpenModal">当選者確定</span>
                </span>
            <?php else: ?>
                <span class="btn4">
                    <span class="small1 jsOpenModal">当選者確定</span>
                </span>
            <?php endif; ?>

            <?php if ($data['fixed_target'] && !$data['delivered_target_message']): ?>
                <?php write_html($this->formHidden('hide_update_target_button', '1')) ?>
            <?php endif; ?>
            <?php write_html($this->formHidden('fix_target_type')) ?>
            <!-- /.elected--></p>
    <?php endif; ?>

    <div class="checkedUserAction jsAreaToggleTarget">
        <ul>
            <li style="display:none" data_checkbox_type="add"><label><?php write_html($this->formRadio('checkedUser1/'.$this->check_no, PHPParser::ACTION_FORM, array('checked'=>'checked'), array(CpMessageDeliveryService::ADD_TARGET=>'対象ユーザーにする')))?></label></li>
            <li style="display:none" data_checkbox_type="delete"><label><?php write_html($this->formRadio('checkedUser1/'.$this->check_no, PHPParser::ACTION_FORM, array(), array(CpMessageDeliveryService::DELETE_TARGET=>'対象ユーザーから外す')))?></label></li>
        </ul>
        <p class="btnSet">
            <span class="btn2"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-update_type="cancel">キャンセル</a></span>
            <span class="btn3"><a href="javascript:void(0)" class="small1 jsAreaToggle" data-submit="updateFanTarget" data-check_no=<?php assign($this->check_no)?>>OK</a></span>
        </p>
    <!-- /.checkedUserAction --></div>
<!-- /.checkedUserWrap --></div>
