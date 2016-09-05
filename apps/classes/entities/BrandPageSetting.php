<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class BrandPageSetting extends aafwEntityBase {

    protected $_Relations = array(
        'Brands' => array(
            'brand_id' => 'id',
        ),
    );

    const NOT_GET_ADDRESS = 0;
    const GET_ALL_ADDRESS = 1;
    const GET_STATE_ADDRESS = 2;
    const STATUS_PUBLIC = 1;
    const STATUS_PRIVATE = 2;
    const NOT_SHOW_AGREEMENT_CHECKBOX = 0;
    const SHOW_AGREEMENT_CHECKBOX = 1;

    public static $select_list_access = array(
        -1 => '指定なし',
        self::STATUS_PRIVATE => '非公開',
        self::STATUS_PUBLIC => '公開'
    );

    /**
     * @return bool
     */
    public function isProfileQuestionRequired() {
        $columns = ['name', 'sex', 'birthday', 'address', 'tel', 'restricted'];

        foreach($columns as $column) {
            if ($this->__get('privacy_required_' . $column)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isPersonalFormRequired() {
        return $this->isProfileQuestionRequired() || $this->agreement;
    }
}
