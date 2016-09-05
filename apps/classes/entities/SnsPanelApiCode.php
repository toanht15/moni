<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class SnsPanelApiCode extends aafwEntityBase {
    protected $_Relations = array(
        "brands" => array(
            "brand_id" => "id"
        )
    );
}