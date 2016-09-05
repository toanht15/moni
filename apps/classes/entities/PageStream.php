<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class PageStream extends aafwEntityBase {

    protected $_Relations = array(
        'Brands' => array(
            'brand_id' => 'id',
        ),
        'PageEntries' => array(
            'id' => 'stream_id',
        )
    );
}