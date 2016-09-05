<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class MoniplaFreeItem extends aafwEntityBase {

    protected $_Relations = array(
        'MoniplaFreeItemRelations' => array(
            'free_item_id' => 'monipla_free_item',
        ),
    );
}
