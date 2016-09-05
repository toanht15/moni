<?php if ($data['next_action_id']): ?>
<ul class="pager2">
    <li class="next"><a href="<?php write_html(Util::rewriteUrl('admin-cp', $data['current_page'], array($data['cp_id'], $data['next_action_id']))) ?>" class="iconNext1">次のモジュールへ</a></li>
    <!-- /.pager2 --></ul>
<?php endif; ?>

</article>