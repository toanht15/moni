<tbody class="jsCommentPluginList">
<?php foreach ($data['comment_plugin_list'] as $comment_plugin): ?>
    <tr>
        <td>
            <a href="<?php write_html(Util::rewriteUrl('admin-comment', 'comment_list', array($comment_plugin->id))) ?>" class="<?php assign(CommentPlugin::$comment_plugin_status_label[$comment_plugin->status]) ?>">
                <?php assign(Util::cutTextByWidth($comment_plugin->title, 500)) ?></a><br>
            <?php if ($comment_plugin->last_comment_user): ?>最新コメント : <?php assign(date('Y/m/d H:i', strtotime($comment_plugin->last_comment_user->created_at))) ?><?php endif ?>
        </td>
        <td><span class="actionReply">返信</span><?php assign($comment_plugin->comment_count) ?></td>
        <td>
            <?php foreach ($comment_plugin->share_sns_list as $social_media_id): ?>
                <?php $share_info = $comment_plugin->share_info[$social_media_id] ?>
                <?php $share_text = $share_info['share_count'] ?: '0' ?>
                <?php $share_text .= $share_info['friend_count'] ? '（' . number_format($share_info['friend_count']) . '）' : '（0）' ?>
            <span class="<?php assign(SocialAccountService::$socialSmallIcon[$social_media_id]) ?>">
                <?php assign(SocialAccountService::$socialAccountLabel[$social_media_id]) ?></span><?php assign($share_text) ?><br>
            <?php endforeach ?></td>
        <td>
            <?php if (Util::isNullOrEmpty($comment_plugin->edit_url)): ?>ー
            <?php else: ?><a href="<?php assign($comment_plugin->edit_url) ?>">編集</a>
            <?php endif ?>
        </td>
        <td>
            <?php assign(CommentPlugin::$comment_plugin_type_options[$comment_plugin->type]) ?></td>
    </tr>
<?php endforeach ?>
</tbody>