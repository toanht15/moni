<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class BrandContract extends aafwEntityBase {
    const SESSION_PREVIEW_MODE = 2;
    const SESSION_TIMEOUT = 300;
    const PREVIEW_PREFIX = 'preview';
    const CLOSED_PAGE_PREVIEW_KEY = 'closed_page_preview';

    const PLAN_NON               = 0;
    const PLAN_MANAGER_STANDARD  = 1;
    const PLAN_MANAGER_CP_LITE   = 2;
    const PLAN_PROMOTION_BRAND   = 3;
    const PLAN_PROMOTION_MONIPLA = 4;

    //運用主体
    const OPERATION_TYPE_ALLIED = 1;
    const OPERATION_TYPE_CLIENT = 2;

    //本番用として使う予定のアカウントか
    const FOR_PRODUCTION_FLG_OFF = 0;
    const FOR_PRODUCTION_FLG_ON = 1;

    public static $PLAN_LIST = array(
        self::PLAN_NON => '指定なし',
        self::PLAN_MANAGER_STANDARD => 'Manager - スタンダード',
        self::PLAN_MANAGER_CP_LITE => 'Manager - キャンペーンライト',
        self::PLAN_PROMOTION_BRAND => 'Promotion',//Promotion - ブランド型
        self::PLAN_PROMOTION_MONIPLA => 'Promotion',//Promotion - モニプラ型
    );

    public static $OPERATION_LIST = array(
        self::OPERATION_TYPE_CLIENT => 'クライアント',
        self::OPERATION_TYPE_ALLIED => 'アライド'
    );

    public static $FOR_PRODUCTION_FLG_LIST = array(
        self::FOR_PRODUCTION_FLG_OFF => 'いいえ（テスト・デモ用）',
        self::FOR_PRODUCTION_FLG_ON => 'はい（本番用）'
    );

    protected $_Relations = array(
        'Brands' => array(
            'brand_id' => 'id'
        )
    );

    /**
     * クローズステータス取得
     * @return mixed
     */
    public function getCloseStatus() {
        if ( $this->isPast($this->contract_end_date) & !$this->isPast($this->display_end_date)) { // クローズ中
            return BrandContracts::MODE_CLOSED;
        }elseif($this->isPast($this->display_end_date)) { // 表示終了
            return BrandContracts::MODE_SITE_CLOSED;
        }else{ // 公開
            return BrandContracts::MODE_OPEN;
        }
    }
}
