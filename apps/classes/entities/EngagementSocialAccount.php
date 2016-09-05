<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class EngagementSocialAccount extends aafwEntityBase {

    protected $_Relations = array(
        'BrandSocialAccounts' => array(
            'brand_social_account_id' => 'id'
        ),
        'CpEngagementActions' => array(
            'cp_engagement_action_id' => 'id',
        ),
    );

}