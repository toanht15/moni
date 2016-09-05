<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class CpInstagramFollowUserLog extends aafwEntityBase {
    const FOLLOWED     = 1; // 新規フォロー
    const FOLLOWING    = 2; // 既存フォロー
    const NOT_FOLLOWED = 3; // 未フォロー

    const UNCHECKED = 0; // 未確認
    const CHECKED   = 1; // 確認済み
}
