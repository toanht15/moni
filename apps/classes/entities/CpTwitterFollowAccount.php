<?php
/**
 * User: t-yokoyama
 * Date: 15/03/10
 * Time: 13:32
 */

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpTwitterFollowAccount extends aafwEntityBase {

    protected $_Relations = array(

        'CpTwitterFollowActions' => array(
            'action_id' => 'id',
        ),
        'BrandSocialAccounts' => array(
            'brand_social_account_id' => 'id',
        ),
    );
}
