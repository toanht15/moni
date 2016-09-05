<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class User extends aafwEntityBase {

    const PROVISIONAL_FLG_OFF = 0;
    const PROVISIONAL_FLG_ON = 1;

    protected $_Relations = array(
        'BrandsUsersRelations' => array(
            'id' => 'user_id',
        ),
        'BrandcoSocialAccounts' => array(
            'id' => 'user_id',
        )
    );
}
