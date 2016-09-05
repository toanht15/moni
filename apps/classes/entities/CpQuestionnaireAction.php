<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class CpQuestionnaireAction extends aafwEntityBase {

    const PANEL_TYPE_AVAILABLE  = 0;        // 検閲なし、ツイート投稿のデフォルトは出力
    const PANEL_TYPE_HIDDEN     = 1;        // 検閲あり、ツイート投稿のデフォルトは非出力
}
