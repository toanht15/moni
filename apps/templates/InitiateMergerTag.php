<?php if( config('Stage') === 'product' ): ?>
    <?php if($data['platform_user_id']):?>
        <img src="https://sync.im-apps.net/imid/set?cid=9613&tid=mid&uid=<?php assign($data['platform_user_id']) ?>" style="z-index: -1;opacity: 0;position: absolute;bottom: 0;"/>
    <?php endif;?>
    <?php if($data['brand_id'] && $data['cp_id']):?>
        <img src="https://atm.im-apps.net/a/beacon.gif?cid=9613&c1=<?php assign($data['brand_id']); ?>&c2=<?php assign($data['cp_id']); ?>" style="z-index: -1;opacity: 0;position: absolute;bottom: 0;"/>
    <?php endif;?>
<?php endif; ?>