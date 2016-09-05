<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );

class InstantWinPrizes extends aafwEntityStoreBase {

    protected $_TableName = 'instant_win_prizes';
    protected $_EntityName = "InstantWinPrize";

    const ONCE_FLG_ON = 1; // 1回きりの参加可能
    const ONCE_FLG_OFF = 2; // 複数回参加可能
    const IMAGE_DEFAULT = 1; // デフォルトの画像を使用
    const IMAGE_UPLOAD = 2; // アップロードした画像を使用
    const PRIZE_STATUS_STAY = 1; // 次のステップに進めない
    const PRIZE_STATUS_PASS = 2; // 次のステップに進める
}
