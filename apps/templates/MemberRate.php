<img src="<?php assign($this->setVersion($data['image_url']));?>" width="<?php assign($data['rate'] == BrandsUsersRelationService::BLOCK ? 18 : 24 )?>" height="<?php assign($data['rate'] == BrandsUsersRelationService::BLOCK ? 18 : 24 )?>" alt="<?php assign($data['rate'] == BrandsUsersRelationService::BLOCK ? 'block user' : 'rating '.$data['rate'] )?>"><?php assign($data['rate_value']) ?>
<p class="ratingBox">
    <a class="ratingBlock" id="<?php assign($data['brand_user_id']) ?>">ブロック</a>
    <span class="starRating" id="<?php assign($data['brand_user_id']) ?>" data-score="<?php assign($data['rate']) ?>"></span>
</p>
