<p class="showItemNum">
    表示件数
    <select class="showItemNum" name="item_limit">
        <?php foreach ($data['item_count_list'] as $item_count): ?>
            <option value="<?php assign($item_count) ?>" <?php if ($data['limit'] == $item_count) assign('selected') ?>>
                <?php assign($item_count) ?>件
            </option>
        <?php endforeach ?>
    </select>
    <span class="btn2"><a href="javascript:void(0)" class="small1 jsUpdateItemList">反映</a></span>
    <!-- /.showItemNum --></p>