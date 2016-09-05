<p class="showItemNum">
    表示件数
    <select class="showItemNum" name="fan_limit">
        <option value="<?php assign(CpCreateSqlService::DISPLAY_20_ITEMS) ?>" <?php if ($data['limit'] == CpCreateSqlService::DISPLAY_20_ITEMS) assign('selected') ?>>
            <?php assign(CpCreateSqlService::DISPLAY_20_ITEMS) ?>件
        </option>
        <option value="<?php assign(CpCreateSqlService::DISPLAY_50_ITEMS) ?>" <?php if ($data['limit'] == CpCreateSqlService::DISPLAY_50_ITEMS) assign('selected') ?>>
            <?php assign(CpCreateSqlService::DISPLAY_50_ITEMS) ?>件
        </option>
        <option value="<?php assign(CpCreateSqlService::DISPLAY_100_ITEMS) ?>" <?php if ($data['limit'] == CpCreateSqlService::DISPLAY_100_ITEMS) assign('selected') ?>>
            <?php assign(CpCreateSqlService::DISPLAY_100_ITEMS) ?>件
        </option>
    </select>
    <span class="btn2"><a href="javascript:void(0)" class="small1" id="applyFanLimit">反映</a></span>
<!-- /.showItemNum --></p>
