<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');

class SegmentProvisions extends aafwEntityStoreBase {
    protected $_DeleteType = aafwEntityStoreBase::DELETE_TYPE_PHYSICAL;
    protected $_TableName = 'segment_provisions';
    protected $_EntityName = "SegmentProvision";

}