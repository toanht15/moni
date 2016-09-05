<?php foreach($data['conditions'] as $condition): ?>
    <?php unset($condition['not_flg']); ?>

    <?php foreach($condition as $key => $sub_condition): ?>
            <?php $match = false; ?>

            <?php foreach (SocialAccountService::$availableSocialAccount as $social_id): ?>
                <?php if (preg_match('/^search_social_account\/'.$social_id.'\//', $key) || preg_match('/^search_friend_count\/'.$social_id.'/', $key)): ?>
                    <li class="<?php assign(SocialAccountService::$socialBigIcon[$social_id]) ?>"><?php assign(SegmentCreateSqlService::toText($sub_condition)) ?><a href="javascript:void(0)" class="iconBtnDelete" data-clear_type="<?php assign($key) ?>">削除</a></li>
                    <?php $match = true; break; ?>
                <?php  endif; ?>
            <?php endforeach; ?>

            <?php if (!$match): ?>
                <li><?php assign(SegmentCreateSqlService::toText($sub_condition)) ?><a href="javascript:void(0)" class="iconBtnDelete" data-clear_type="<?php assign($key) ?>">削除</a></li>
            <?php endif; ?>
    <?php endforeach; ?>

<?php endforeach; ?>
