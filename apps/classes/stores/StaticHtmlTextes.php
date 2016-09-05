<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');
class StaticHtmlTextes extends aafwEntityStoreBase {
    protected $_TableName = 'static_html_textes';
    protected $_EntityName = 'StaticHtmlText';
    protected $_DeleteType = aafwEntityStoreBase::DELETE_TYPE_PHYSICAL;

    public function insert($template_id, $template) {
        $record = $this->createEmptyObject();
        $record->template_id = $template_id;
        $record->text = $template->text;
        $this->save($record);
    }

    public function getRecordByTemplateId($template_id) {
        $recordObj = $this->findOne(array('template_id' => $template_id));
        return array('text' => $recordObj->text);
    }

}