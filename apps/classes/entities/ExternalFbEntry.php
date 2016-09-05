<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class ExternalFbEntry extends aafwEntityBase
{
    protected $_Relations = array(
        'ExternalFbStreams' => array(
            'stream_id' => 'id'
        )
    );
}