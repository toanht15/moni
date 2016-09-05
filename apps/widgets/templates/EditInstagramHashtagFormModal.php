<?php $disabled = $data['instagram_hashtag_user_post']->approval_status == InstagramHashtagUserPost::APPROVAL_STATUS_REJECT ?>
<form method="POST" action="<?php assign(Util::rewriteUrl('admin-cp', 'edit_instagram_hashtag')) ?>" name="edit_instagram_hashtag_form">
    <?php write_html($this->formHidden('dtl_instagram_hashtag_action_id', $data['instagram_hashtag_action_id'])) ?>
    <?php write_html($this->formHidden('dtl_instagram_hashtag_user_post_id', $data['instagram_hashtag_user_post']->id)) ?>
    <?php write_html($this->csrf_tag()) ?>

    <div class="photoPage">
        <figure><img src="<?php assign($data['instagram_hashtag_user_post']->standard_resolution); ?>" onerror="this.src='<?php assign($this->setVersion('/img/campaign/imgDeletePhoto.png')) ?>'" alt="<?php assign(json_decode($data['instagram_hashtag_user_post']->post_text)); ?>" onerror="this.src='<?php assign($data['instagram_hashtag_user_post']->standard_resolution); ?>';"></figure>
        <div class="postWrap">
            <h1 class="postTitle"><?php assign(json_decode($data['instagram_hashtag_user_post']->detail_data)->caption->text); ?></h1>
            <p class="postData"><img src="<?php assign($data['user']->profile_image_url); ?>" alt="<?php assign(!empty($data['is_hide_personal_info']) ? '' : $data['user']->name); ?>" onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';"><span class="timeStamp"><?php assign(date('Y/m/d', strtotime($data['instagram_hashtag_user']->created_at))) ?></span></p>
            <!-- /.postWrap --></div>
        <!-- /.photoPage --></div>

    <table class="photoData">
        <tbody>
        <tr>
            <th>投稿先</th>
            <td><?php assign($data['cp']->getTitle()) ?></td>
        </tr>
        <tr>
            <th>ユーザネーム投稿日時</th>
            <td><?php assign(date('Y/m/d H:i', strtotime($data['instagram_hashtag_user']->created_at))) ?></td>
        </tr>
        <tr>
            <th>Instagram投稿日時</th>
            <td>
                <?php assign(date('Y/m/d H:i', json_decode($data['instagram_hashtag_user_post']->detail_data)->created_time)) ?>
                <?php if ($data['instagram_hashtag_user_post']->reverse_post_time_flg): ?>
                    (ユーザネーム登録前に投稿されています)
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>Instagram投稿URL</th>
            <td>
                <a target="_blank" href="<?php assign($data['instagram_hashtag_user_post']->link) ?>"><?php assign($data['instagram_hashtag_user_post']->link) ?></a>
            </td>
        </tr>
        <?php if (empty($data['is_hide_personal_info'])): ?>
            <tr>
                <th>Instagramユーザネーム</th>
                <td>
                    <?php assign($data['instagram_hashtag_user']->instagram_user_name) ?>
                    <?php if ($data['instagram_hashtag_user']->duplicate_flg): ?>
                        (重複)
                    <?php endif; ?>
                </td>
            </tr>
        <?php endif; ?>
        <?php if (empty($data['is_hide_personal_info'])): ?>
            <tr>
                <th>ユーザー名</th>
                <td>
                    <img src="<?php assign($data['user']->profile_image_url); ?>" alt="<?php assign($data['user']->name); ?>" class="userImg" onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';"><?php assign($data['user']->name); ?>
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <th>ステータス</th>
            <td>
                <span class="<?php assign($data['instagram_hashtag_user_post']->getApprovalStatusClass()) ?>"><?php assign($data['instagram_hashtag_user_post']->getApprovalStatus()) ?></span>
                <?php if ($data['page_type'] != 'show_user_list'): ?>
                    <?php write_html($this->formRadio('dtl_instagram_hashtag_approval_status',
                        $data['instagram_hashtag_user_post']->approval_status,
                        array('class' => 'jsDtlInstagramHashtagApprovalStt'),
                        array(InstagramHashtagUserPost::APPROVAL_STATUS_DEFAULT => '未承認', InstagramHashtagUserPost::APPROVAL_STATUS_APPROVE => '承認', InstagramHashtagUserPost::APPROVAL_STATUS_REJECT => '非承認'))); ?>
                    <span class="btn3"><a href="javascript:void(0);" class="small1" onclick="document.edit_instagram_hashtag_form.submit()">適用</a></span>
                <?php endif; ?>
            </td>
        </tr>
        </tbody>
        <!-- /.photoData --></table>

    <?php if ($data['page_type'] != 'show_user_list'): ?>
        <?php if ($data['pageData']['prev_id'] || $data['pageData']['next_id']): ?>
            <ul class="pager2">
                <?php if ($data['pageData']['prev_id']): ?>
                    <li class="prev"><a href="#instagram_hashtag_edit_modal" class="iconPrev1 jsPrevInstagramHashtagEditModal" data-instagram_hashtag_user_post_id=<?php assign($data['pageData']['prev_id']) ?> >前の投稿</a></li>
                <?php endif; ?>
                <?php if ($data['pageData']['next_id']): ?>
                    <li class="next"><a href="#instagram_hashtag_edit_modal" class="iconNext1 jsNextInstagramHashtagEditModal" data-instagram_hashtag_user_post_id=<?php assign($data['pageData']['next_id']) ?> >次の投稿</a></li>
                <?php endif; ?>
                <!-- /.pager3 --></ul>
        <?php endif; ?>
    <?php endif; ?>
</form>
