<?php $service_factory = new aafwServiceFactory();
$manager_service = $service_factory->create('ManagerService'); ?>
<p class="batchAction">
    <span style="margin-right: 25px;border-right: 1px solid #9d9d9d"><?php write_html($this->formCheckbox2('photo_check_all', null, array('class' => 'jsPhotoCheckAll', 'data-photo_check_class' => 'jsPhotoCheck'), array('1' => '全選択'))) ?></span>
    <?php write_html($this->formRadio('multi_photo_approval_status_' . $data['menu_order'],
        PhotoUser::APPROVAL_STATUS_APPROVE,
        array('class' => 'jsMultiPhotoApprovalStatus'),
        array(PhotoUser::APPROVAL_STATUS_DEFAULT => '未承認', PhotoUser::APPROVAL_STATUS_APPROVE => '承認', PhotoUser::APPROVAL_STATUS_REJECT => '非承認'))); ?>
    <?php if(!$manager_service->isAgentLogin()): ?>
    <?php write_html($this->formCheckBox2('',
        PhotoEntry::TOP_STATUS_HIDDEN,
        array('class' => 'jsMultiPhotoTopStatus', 'disabled' => $disabled, 'data-default_value' => $photo_hidden_flg),
        array(PhotoEntry::TOP_STATUS_AVAILABLE => 'TOPパネルに表示'))) ?>
    <?php endif ?>
    <span class="btn3"><a href="javascript:void(0);" class="small1 jsPhotoActionFormSubmit<?php assign($data['menu_order']) ?>">適用</a></span>
    <!-- /.batchAction --></p>
