<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');
class StaticHtmlTemplates extends aafwEntityStoreBase {
    protected $_TableName = 'static_html_templates';
    protected $_DeleteType = aafwEntityStoreBase::DELETE_TYPE_PHYSICAL;
}