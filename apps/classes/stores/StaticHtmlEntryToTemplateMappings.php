<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');
class StaticHtmlEntryToTemplateMappings extends aafwEntityStoreBase {
    protected $_TableName = 'static_html_entry_to_template_mappings';
    protected $_DeleteType = aafwEntityStoreBase::DELETE_TYPE_PHYSICAL;
}