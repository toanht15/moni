<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class GiftCardUpload extends aafwEntityBase{
    protected $_Relations = array(
        'GiftCardConfigs' => array(
            'gift_card_config_id' => 'id',
        ),
    );
}
