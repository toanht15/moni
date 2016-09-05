<nav class="categoryNav">
    <ul class="tablink1">
        <?php if ($this->values['ActionForm']['action'] === 'static_html_entries'): ?>
            <li class="current"><span>ページ一覧</span></li>
        <?php else: ?>
            <li><a href="<?php assign(Util::rewriteUrl('admin-blog', 'static_html_entries')) ?>">ページ一覧</a></li>
        <?php endif; ?>

        <?php if ($this->values['ActionForm']['action'] === 'create_static_html_entry_form'): ?>
            <li class="current"><span>ページ作成</span></li>
        <?php else: ?>
            <li><a href="<?php assign(Util::rewriteUrl('admin-blog', 'create_static_html_entry_form')) ?>">ページ作成</a></li>
        <?php endif; ?>
        <?php if($data['can_use_embed_page']): ?>
            <?php if ($this->values['ActionForm']['action'] === 'create_static_html_embed_page_form'): ?>
                <li class="current"><span>埋込ページ作成</span></li>
            <?php else: ?>
                <li><a href="<?php assign(Util::rewriteUrl('admin-blog', 'create_static_html_embed_page_form')) ?>">埋込ページ作成</a></li>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($this->values['ActionForm']['action'] === 'static_html_categories'): ?>
            <li class="current"><span>カテゴリ</span></li>
        <?php else: ?>
            <li><a href="<?php assign(Util::rewriteUrl('admin-blog', 'static_html_categories')) ?>">カテゴリ</a></li>
        <?php endif; ?>
        <!-- /.tablink1 --></ul>
    <!-- /.categoryNav --></nav>