<?php
/** @var BrandPageSetting $pageSetting */
$pageSetting = BrandInfoContainer::getInstance()->getBrandPageSetting();
if($pageSetting->rtoaster != ''){
    write_html($pageSetting->rtoaster);

    if($this->getAction()->isLogin() && $_COOKIE['_rt_uid']) {
        $brandsUsersRtoasters = $this->getAction()->getModel('BrandsUsersRtoasters');
        $brandsUsersRtoaster = $brandsUsersRtoasters->findOne(array('brands_users_relation_id' => $this->getAction()->getBrandsUsersRelation()->id));

        if(!$brandsUsersRtoaster) {
            $brandsUsersRtoaster = $brandsUsersRtoasters->createEmptyObject();

            $brandsUsersRtoaster->brands_users_relation_id = $this->getAction()->getBrandsUsersRelation()->id;
        }

        if($brandsUsersRtoaster->uid != $_COOKIE['_rt_uid']) {
            $brandsUsersRtoaster->uid = $_COOKIE['_rt_uid'];

            $brandsUsersRtoasters->save($brandsUsersRtoaster);
        }
    }

} ?>
