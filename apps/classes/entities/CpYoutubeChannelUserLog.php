<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class CpYoutubeChannelUserLog extends aafwEntityBase {

    const STATUS_FOLLOWED  = 1; // 新規登録
    const STATUS_FOLLOWING = 2; // 登録済み
    const STATUS_SKIP      = 3; // スキップ
    const STATUS_ERROR     = 4; // エラー（ログには入らなくなった）

    public static $youtube_status_string = [
        self::STATUS_FOLLOWED  => '新規登録',
        self::STATUS_FOLLOWING => '既存登録',
        self::STATUS_SKIP      => 'スキップ'
    ];

}
