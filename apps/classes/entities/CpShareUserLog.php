<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpShareUserLog extends aafwEntityBase {

    //ログの種別
    const TYPE_SHARE       = "1";  // シェアした
    const TYPE_SKIP        = "2";  // スキップした
    const TYPE_UNREAD      = "3";  //連携していなかった
    const TYPE_ERROR       = "4"; //thrift関連のエラーで落ちた時

    const STATUS_SHARE       = "シェア";  // シェアした
    const STATUS_SKIP        = "スキップ";  // スキップした
    const STATUS_UNREAD      = "未連携";  //連携していなかった
    const STATUS_ERROR       = "エラー"; //thrift関連のエラーで落ちた時

    const SEARCH_EXISTS      = 1;
    const SEARCH_NOT_EXISTS  = 2;
}