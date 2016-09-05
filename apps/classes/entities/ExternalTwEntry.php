<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class ExternalTwEntry extends aafwEntityBase
{
    protected $_Relations = array(
        'ExternalTwStreams' => array(
            'stream_id' => 'id'
        )
    );
}