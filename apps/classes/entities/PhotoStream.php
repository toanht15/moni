<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class PhotoStream extends aafwEntityBase {
    protected $_Relations = array(
        'Brands' => array(
            'brand_id' => 'id',
        ),
        'PhotoEntries' => array(
            'id' => 'stream_id',
        )
    );
}
