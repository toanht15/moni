<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityStoreBase' );
class MoniplaFreeItemRelations extends aafwEntityStoreBase {

    protected $_TableName = 'monipla_free_item_relations';
    protected $_EntityName = 'MoniplaFreeItemRelation';

    protected $_Relations = array(
        'MoniplaFreeItemData' => array(
            'monipla_free_item' => 'free_item_id'
        )
    );
}
