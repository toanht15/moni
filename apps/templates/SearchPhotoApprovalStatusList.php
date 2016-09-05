<li>
    <label>
        <?php $name = 'search_photo_approval_status/' . $data['action_id'] . '/' . PhotoUser::APPROVAL_STATUS_DEFAULT ?>
        <input type="checkbox" name="<?php assign($name) ?>" value="<?php assign(PhotoUser::APPROVAL_STATUS_DEFAULT) ?>"
            <?php assign($data['search_photo_approval_status'][$name] === strval(PhotoUser::APPROVAL_STATUS_DEFAULT) ? 'checked' : '')?>>未承認
    </label>
</li>
<li>
    <label>
        <?php $name = 'search_photo_approval_status/' . $data['action_id'] . '/' . PhotoUser::APPROVAL_STATUS_APPROVE ?>
        <input type="checkbox" name="<?php assign($name) ?>" value="<?php assign(PhotoUser::APPROVAL_STATUS_APPROVE) ?>"
            <?php assign($data['search_photo_approval_status'][$name] === strval(PhotoUser::APPROVAL_STATUS_APPROVE) ? 'checked' : '')?>>承認
    </label>
</li>
<li>
    <label>
        <?php $name = 'search_photo_approval_status/' . $data['action_id'] . '/' . PhotoUser::APPROVAL_STATUS_REJECT ?>
        <input type="checkbox" name="<?php assign($name) ?>" value="<?php assign(PhotoUser::APPROVAL_STATUS_REJECT) ?>"
            <?php assign($data['search_photo_approval_status'][$name] === strval(PhotoUser::APPROVAL_STATUS_REJECT) ? 'checked' : '')?>>非承認
    </label>
</li>