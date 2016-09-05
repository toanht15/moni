<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class CpInstagramFollowActionLog extends aafwEntityBase {
//    const STATUS_COOPERATED     = 1; // 新規連携
//    const STATUS_COOPERATING    = 2; // 連携済み
//    const STATUS_NOT_COOPERATED = 3; // 未連携(離脱)
//    const STATUS_SKIP           = 4; // 未連携(スキップ)

    // 2015年8月頭の改修（embedを全ユーザに表示）
    const STATUS_COOPERATING    = 5; // 連携済み
    const STATUS_NOT_COOPERATED = 6; // 未連携
}
