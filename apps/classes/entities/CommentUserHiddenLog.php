<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CommentUserHiddenLog extends aafwEntityBase {

    protected $_Relations = array(
        'Users' => array(
            'user_id' => 'id'
        )
    );
}
