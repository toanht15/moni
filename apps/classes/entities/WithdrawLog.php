<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
/**
 * @property mixed directory_name
 */
class WithdrawLog extends aafwEntityBase {

    protected $_Relations = array(
        'BrandsUsersRelations' => array(
            'id' => 'brand_user_relation_id'
        )
    );

    static $withdraw_reason = array(
        "仕組みがわかりづらかったから",
        "サイトに面白みを感じないから",
        "今後キャンペーンの参加予定がないから",
        "メールが頻繁に来るから",
        "その他"
    );
}
