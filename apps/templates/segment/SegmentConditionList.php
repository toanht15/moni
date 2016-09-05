<?php foreach ($data['conditions'] as $condition): ?>
    <li><a href="javascript:void(0);"
           data-target_id="<?php assign($condition['target_id']) ?>"
           data-target_type="<?php assign($condition['target_type']) ?>"
           <?php if($condition['is_selected']): ?>class="selected"<?php endif ?>
            title="<?php assign($condition['condition_label']) ?>">
            <?php assign(SegmentCreatorService::getBriefConditionText($data['category_mode'], $condition['condition_label'])) ?></a></li>
<?php endforeach ?>