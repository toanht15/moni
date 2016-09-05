<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoHeader')->render($data['pageStatus'])) ?>
<?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoAccountHeader')->render($data['pageStatus'])) ?>

<article>
    <?php write_html($this->parseTemplate('AdminBlogHeader.php',array('can_use_embed_page' => $data['can_use_embed_page']))) ?>
    <h1 class="hd1">ページ一覧
        <span class="btn3"><a href="<?php assign(Util::rewriteUrl('admin-blog', 'create_static_html_entry_form')) ?>" class="small1">新規作成</a></span>
    </h1>

    <form method="POST" name="actionForm">
        <?php write_html($this->csrf_tag()); ?>
        <section class="batchAction">
            <select name="selectMenu" id="selectMenu">
                <option value="" selected>一括操作</option>
                <option value="public">公開</option>
                <option value="draft">非公開（下書き）</option>
                <option value="delete">削除</option>
            </select>

            <p class="btn3" id="post_save"><a href="javascript:void(0);" class="small1">反映</a></p>
        <!-- /.batchAction --></section>

        <table class="pageList1">
            <thead>
            <tr>
                <th class="check"><input type="checkbox" id="page_group" class="checkAll"></th>
                <th class="pageTitle">タイトル</th>
                <th class="mainCategory">カテゴリ</th>
                <th class="limit">制限</th>
                <th class="layout">レイアウト</th>
                <th class="user">最終編集者</th>
                <th class="lastUpdate">最終更新</th>
                <th class="releaseDate">公開日時</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data['staticHtmlEntries'] as $entry): ?>
                <tr>
                    <td class="check"><input type="checkbox" name="entries[]" class="page_group" value="<?php assign($entry->id) ?>"></td>
                    <td class="pageTitle">
                        <?php if ($entry->hidden_flg == '0'): ?>
                            <span class="<?php assign('iconCheck3') ?>" id="static_type<?php assign($entry->id) ?>">
                        <?php else: ?>
                            <span class="<?php assign('iconDraft1') ?>" id="static_type<?php assign($entry->id) ?>">
                        <?php endif; ?>
                                <?php if ($entry->isEmbedPage()): ?>
                                    <a href="<?php assign(Util::rewriteUrl('admin-blog', 'edit_static_html_embed_page_form', array($entry->id), array('p' => $params['p']))) ?>">
                                        <?php assign($entry->title) ?>
                                    </a>
                                <?php elseif ($entry->layout_type == StaticHtmlEntries::LAYOUT_PLAIN && !$data['pageStatus']['isLoginManager']): ?>
                                    <span><?php assign($entry->title) ?></span>
                                <?php else: ?>
                                    <a href="<?php assign(Util::rewriteUrl('admin-blog', 'edit_static_html_entry_form', array($entry->id), array('p' => $params['p']))) ?>">
                                        <?php assign($entry->title) ?>
                                    </a>
                                <?php endif; ?>
                        </span>
                    </td>
                    <td class="mainCategory"><?php assign($entry->categories) ?></td>
                    <?php if ($entry->extra_body): ?>
                        <td class="limit"><img src="<?php assign($this->setVersion('/img/icon/iconLock3.png')) ?>" width="14" height="13" alt="公開制限"></td>
                    <?php else: ?>
                        <td class="limit"></td>
                    <?php endif; ?>
                    <td class="layout">
                        <?php if ($entry->isEmbedPage()): ?>
                            埋込
                        <?php else: ?>
                            <img src="<?php assign($this->setVersion(StaticHtmlEntries::$layout_src[$entry->layout_type])); ?>" width="20" height="13" alt="960px">
                        <?php endif; ?>
                    </td>
                    <td class="user"><?php assign($entry->update_user); ?></td>
                    <td class="lastUpdate"><?php assign($entry->updated_at); ?></td>
                    <td class="releaseDate"><?php assign(date('Y/m/d H:i', strtotime($entry->public_date))); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        <!-- /.pageList1 --></table>
    </form>

    <?php write_html(aafwWidgets::getInstance()->loadWidget('BrandcoModalPager')->render(array(
        'TotalCount' => $data['totalEntriesCount'],
        'CurrentPage' => $this->params['p'],
        'Count' => $data['pageLimited'],
    ))) ?>

</article>

<?php write_html($this->parseTemplate('BrandcoFooter.php', array_merge($data['pageStatus'], array('script' => array('admin-blog/StaticHtmlEntriesService'))))); ?>
