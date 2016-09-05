<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
/**
 * @property mixed directory_name
 */
class EngagementLog extends aafwEntityBase {

    const UNREAD_FLG    = '0';    // Facebook未連携
    const LIKED_FLG      = '1';    // いいねしていない
    const PREV_LIKED_FLG   = '2';    // 過去にいいね済み
    const SKIP_FLG      = '3';    // いいねスキップ
}
