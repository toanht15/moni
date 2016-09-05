<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class MoniplaFreeItemRelation extends aafwEntityBase {

    protected $_Relations = array(
        'MoniplaFreeItems' => array(
            'monipla_free_item' => 'free_item_id'
        )
    );
}
