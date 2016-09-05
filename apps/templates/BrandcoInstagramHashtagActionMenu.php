
<p class="batchAction">
    <span style="margin-right: 25px;border-right: 1px solid #9d9d9d"><?php write_html($this->formCheckbox2('instagram_hashtag_check_all', null, array('class' => 'jsInstagramHashtagCheckAll', 'data-instagram_hashtag_check_class' => 'jsInstagramHashtagCheck'), array('1' => '全選択'))) ?></span>
    <?php write_html($this->formRadio('multi_instagram_hashtag_approval_status_' . $data['menu_order'],
        InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE,
        array('class' => 'jsMultiInstagramHashtagApprovalStatus'),
        array(InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT => '未承認', InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE => '承認', InstagramHashtagUserPost::APPROVAL_STATUS_REJECT => '非承認'))); ?>
    <?php write_html($this->formCheckBox2('',
        PhotoEntry::TOP_STATUS_HIDDEN,
        array('class' => 'jsMultiInstagramHashtagTopStatus', 'disabled' => $disabled, 'data-default_value' => $instagram_hashtag_approval_flg)
        )) ?>
    <span class="btn3"><a href="javascript:void(0);" class="small1 jsInstagramHashtagActionFormSubmit<?php assign($data['menu_order']) ?>">適用</a></span>
    <!-- /.batchAction --></p>