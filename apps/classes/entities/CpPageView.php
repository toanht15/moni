<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpPageView extends aafwEntityBase {

    const TYPE_CP_PAGE = 1;        //キャンペーントップページ
    const TYPE_LP_PAGE = 2;        //LPページ (キャンペーンのreference_urlを設定される場合)

    const STATUS_SUCCESS = 1;      //成功
    const STATUS_FAILED = 2;       //失敗

    protected $_Relations = array(
        'Cp' => array(
            'cp_id' => 'id',
        ),
    );
}