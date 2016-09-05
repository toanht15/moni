<ul class="pager2">
    <?php if ($data['prev_action']): ?>
        <?php $prev_action_detail = $data['prev_action']->getCpActionDetail() ?>
        <li class="prev"><a href="<?php assign(Util::rewriteUrl('admin-cp', 'edit_action_base', array($data['prev_action']->id)))?>" class="iconPrev1"><?php assign($prev_action_detail['title']) ?>へ</a></li>
    <?php endif; ?>
    <?php if ($data['next_action']): ?>
        <?php $next_action_detail = $data['next_action']->getCpActionDetail(); ?>
        <li class="next"><a href="<?php assign(Util::rewriteUrl('admin-cp', 'edit_action_base', array($data['next_action']->id))) ?>" class="iconNext1"><?php assign($next_action_detail['title']) ?>へ</a></li>
    <?php endif; ?>
    <!-- /.pager2 --></ul>