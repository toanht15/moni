<div class="table-responsive">
    <table class="table table-striped">
        <tbody>
        <?php if ($data['sql_selector']):
            foreach ($data['sql_selector']['selectors'] as $sql_selector): ?>
                <tr>
                    <td><a href="<?php assign(Util::rewriteUrl( 'sql_selector', 'detail', array($sql_selector->id), array(), '', true )); ?>"><?php assign($sql_selector->title);?></a></td>
                    <td><?php write_html($this->nl2brAndHtmlspecialchars($sql_selector->description)); ?></td>
                    <?php $data_flg = true; ?>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="2"> データがありません。</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>


