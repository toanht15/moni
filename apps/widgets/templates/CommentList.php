
<tbody class="jsCommentList">
<?php foreach ($data['comment_list'] as $comment): ?>
    <tr id="comment_container_<?php assign($comment->id) ?>" class="jsCommentContainer" data-id="<?php assign($comment->id) ?>">
        <td class="check">
            <input type="checkbox" class="jsItemCheck" value="<?php assign($comment->id) ?>" name="cur_form_ids[]" <?php assign($comment->isDiscard() ? "disabled" : "") ?>>
            <!-- /.check --></td>
        <td class="postData">
            <?php if ($comment->no) write_html("No." . $comment->no . "<br>") ?><?php assign($comment->created_time) ?>
            <!-- /.postData --></td>
        <td class="userData">
            <img src="<?php assign($comment->from->profile_image_url ?: $this->setVersion('/img/base/imgUser1.jpg')) ?>" width="20" height="20" alt="" onerror="this.src='<?php assign($this->setVersion('/img/base/imgUser1.jpg'));?>';"><?php assign($comment->from->name) ?><br>
            <?php if ($comment->anonymous_flg == CommentUserRelation::ANONYMOUS_FLG_OFF) assign("ID " . $comment->from->no) ?>
            <!-- /.userData --></td>
        <td class="action">
            <span><span class="actionLike">いいね数</span><?php assign($comment->like_count) ?></span>
            <?php if ($comment->object_type == CommentUserRelation::OBJECT_TYPE_COMMENT): ?>
                <span><span class="actionReply">返信</span><?php assign($comment->comment_count) ?></span>
            <?php endif ?>
            <!-- /.action --></td>
        <td class="share">
            <?php foreach ($data['cp_share_sns_list'][$comment->comment_plugin_id] as $social_media_id): ?>
                <?php $sa_id = 'sa_id_' . $social_media_id;
                $sa_friend_count = 'sa_friend_count_' . $social_media_id; ?>
                <?php if (Util::isNullOrEmpty($comment->from->$sa_id)) continue; ?>

                <?php $shared_flg = in_array($social_media_id, $comment->share_sns_list) ? true : false ?>
                <?php $icon = $shared_flg ? SocialAccountService::$socialSmallIcon[$social_media_id] : SocialAccountService::$socialSmallIcon[$social_media_id] . "_off" ?>
                <span>
                    <span class="<?php assign($icon) ?>"><?php assign(SocialAccountService::$socialAccountLabel[$social_media_id]) ?>シェア済み</span>(<?php assign($comment->from->$sa_friend_count) ?>)<br>
                    <?php if (!Util::isNullOrEmpty($data['share_url_list'][$comment->id][$social_media_id])): ?>
                        <a href="<?php assign($data['share_url_list'][$comment->id][$social_media_id]) ?>" target="_blank">投稿を見る</a>
                    <?php endif ?>
                </span>
            <?php endforeach ?>
            <!-- /.share --></td>
        <td class="postBody jsTextContainer">
            <div class="postText">
                <?php write_html($comment->comment_text) ?>
                <!-- /.postText --></div>
            <!-- /.postBody --></td>
        <td class="status">
            <?php if ($comment->isDiscard()): ?>
                <span class="statusInner">
                    <p class="userDelete">ユーザーによって削除されました</p>
                    <!-- /.statusInner --></span>
            <?php else: ?>
                <?php $switch_class = $comment->status == CommentUserRelation::NOTE_STATUS_VALID ? 'on' : 'off' ?>
                <span class="statusInner">
                    <a href="javascript:void(0);" class="switch_large jsToggleStatus <?php assign($switch_class) ?>"><span class="switchInner"><span class="selectON">公開</span><span class="selectOFF">非公開</span></span></a>
                    <!-- /.statusInner --></span>
                <span class="statusInner">
                    <?php if (!Util::isNullOrEmpty($comment->comment_url)): ?>
                        <a href="<?php assign($comment->comment_url) ?>" target="_blank" class="pluginPreview">確認</a>
                    <?php endif ?>
                    <!-- /.statusInner --></span>
            <?php endif ?>
            <span class="statusInner">
                <a href="#note_modal" class="postMemo jsOpenNoteModal">
                    <?php if ($comment->note_status == CommentUserRelation::NOTE_STATUS_VALID): ?>
                        <span class="inner jsNoteIcon on">メモを書く</span>
                        <span class="textBalloon1"><span class="jsNoteContent"><?php assign($comment->note) ?></span><!-- /.textBalloon1 --></span>
                    <?php else: ?>
                        <span class="inner jsNoteIcon off">メモを書く</span>
                        <span class="textBalloon1"><span class="jsNoteContent" style="display: none;"></span><!-- /.textBalloon1 --></span>
                    <?php endif ?>
                    <!-- /.postMemo --></a>
                <!-- /.statusInner --></span>
            <!-- /.status --></td>
    </tr>
<?php endforeach ?>
</tbody>