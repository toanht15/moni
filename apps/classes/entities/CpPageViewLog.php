<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpPageViewLog extends aafwEntityBase {

    const STATUS_CRAWL = 1;         //取得中
    const STATUS_FAILED = 2;        //失敗
    const STATUS_FINISH = 3;        //完了

    protected $_Relations = array(
        'Cp' => array(
            'cp_id' => 'id',
        ),
    );
}