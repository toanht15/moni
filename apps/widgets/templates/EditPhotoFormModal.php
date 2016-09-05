<?php $disabled = $data['photo_user']->approval_status == PhotoUser::APPROVAL_STATUS_REJECT ?>
<form method="POST" action="<?php assign(Util::rewriteUrl('admin-cp', 'edit_photo')) ?>" name="edit_photo_form">
    <?php write_html($this->formHidden('dtl_photo_action_id', $data['photo_action_id'])) ?>
    <?php write_html($this->formHidden('dtl_photo_user_id', $data['photo_user']->id)) ?>
    <?php write_html($this->csrf_tag()) ?>

    <div class="photoPage">
        <figure><img src="<?php assign($data['photo_user']->photo_url); ?>" alt="<?php assign($data['photo_user']->photo_title); ?>" onerror="this.src='<?php assign($data['photo_user']->photo_url); ?>';"></figure>
        <div class="postWrap">
            <h1 class="postTitle"><?php assign($data['photo_user']->photo_title); ?></h1>
            <p class="postText"><?php assign($data['photo_user']->photo_comment); ?></p>
            <p class="postData"><img src="<?php assign($data['user']->profile_image_url); ?>" alt="<?php assign(!empty($data['is_hide_personal_info']) ? '' : $data['user']->name); ?>" onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';"><span class="timeStamp"><?php assign(date('Y/m/d', strtotime($data['photo_user']->created_at))) ?></span></p>
            <!-- /.postWrap --></div>
        <!-- /.photoPage --></div>

    <table class="photoData">
        <tbody>
        <tr>
            <th>投稿先</th>
            <td><?php assign($data['cp']->getTitle()) ?></td>
        </tr>
        <tr>
            <th>投稿日時</th>
            <td><?php assign(date('Y/m/d H:i', strtotime($data['photo_user']->created_at))) ?></td>
        </tr>
        <tr>
            <th>URL</th>
            <td><a target="_blank" href="<?php assign(Util::rewriteUrl('photo', 'detail', array($data['photo_user']->getPhotoEntry()->id))) ?>"><?php assign(Util::rewriteUrl('photo', 'detail', array($data['photo_user']->getPhotoEntry()->id))) ?></a></td>
        </tr>
        <tr>
            <th>ユーザー名</th>
            <td>
                <img src="<?php assign($data['user']->profile_image_url); ?>"
                     alt="<?php assign(!empty($data['is_hide_personal_info']) ? '' : $data['user']->name); ?>" class="userImg"
                     onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';">
                <?php assign(!empty($data['is_hide_personal_info']) ? '' : $data['user']->name); ?>
            </td>
        </tr>
        <tr>
            <th>ステータス</th>
            <td>
                <span class="<?php assign($data['photo_user']->getApprovalStatusClass()) ?>"><?php assign($data['photo_user']->getApprovalStatus()) ?></span>
                <?php if ($data['page_type'] != 'show_user_list'): ?>
                    <?php write_html($this->formRadio('dtl_photo_approval_status',
                        $data['photo_user']->approval_status,
                        array('class' => 'jsDtlPhotoApprovalStt'),
                        array(PhotoUser::APPROVAL_STATUS_DEFAULT => '未承認', PhotoUser::APPROVAL_STATUS_APPROVE => '承認', PhotoUser::APPROVAL_STATUS_REJECT => '非承認'))); ?>
                    <?php $photo_hidden_flg = $data['photo_user']->getPhotoHiddenFlg() ?>
                    <?php if (!$data['isAgent']): ?>
                    <?php write_html($this->formCheckBox2('dtl_photo_top_status',
                        $photo_hidden_flg,
                        array('class' => 'jsDtlPhotoTopStt', 'disabled' => $disabled, 'data-default_value' => $photo_hidden_flg),
                        array(PhotoEntry::TOP_STATUS_AVAILABLE => 'TOPパネルに表示'))) ?>
                    <?php endif ?>
                    <span class="btn3"><a href="javascript:void(0);" class="small1" onclick="document.edit_photo_form.submit()">適用</a></span>
                <?php endif; ?>
            </td>
        </tr>
        </tbody>
        <!-- /.photoData --></table>

    <?php if ($data['pageData']['prev_id'] || $data['pageData']['next_id']): ?>
        <ul class="pager2">
            <?php if ($data['pageData']['prev_id']): ?>
                <li class="prev"><a href="#photo_edit_modal" class="iconPrev1 jsPrevPhotoEditModal" data-photo_user_id=<?php assign($data['pageData']['prev_id']) ?> >前の投稿</a></li>
            <?php endif; ?>
            <?php if ($data['pageData']['next_id']): ?>
                <li class="next"><a href="#photo_edit_modal" class="iconNext1 jsNextPhotoEditModal" data-photo_user_id=<?php assign($data['pageData']['next_id']) ?> >次の投稿</a></li>
            <?php endif; ?>
            <!-- /.pager3 --></ul>
    <?php endif; ?>
</form>
