<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpTweetAction extends aafwEntityBase {
    const MAX_TEXT_LENGTH = 140;
    const PHOTO_TEXT_LENGTH = 24;
    const URL_TEXT_LENGTH = 23;
    const MAX_PHOTO_NUM = 4;
    const NEW_LINE_LENGTH = 1;

    const PHOTO_REQUIRE = 1;            //画像必須
    const PHOTO_OPTION_DISPLAY = 0;     //画像任意(画像アップロードフォーム表示)
    const PHOTO_OPTION_HIDE = 2;        //画像アップロードフォーム非表示

    const PANEL_TYPE_AVAILABLE  = 0;        // 検閲なし、ツイート投稿のデフォルトは出力
    const PANEL_TYPE_HIDDEN     = 1;        // 検閲あり、ツイート投稿のデフォルトは非出力

    protected $_Relations = array(
        'CpActions' => array(
            'cp_action_id' => 'id',
        ),
    );
}
