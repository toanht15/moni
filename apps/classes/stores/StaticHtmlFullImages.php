<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');
class StaticHtmlFullImages extends aafwEntityStoreBase {
    protected $_TableName = 'static_html_full_images';
    protected $_DeleteType = aafwEntityStoreBase::DELETE_TYPE_PHYSICAL;

    public function insert($template_id, $template) {
        $record = $this->createEmptyObject();
        $record->template_id = $template_id;
        $record->image_url = $template->image_url;
        $record->link = $template->link;
        $record->caption = $template->caption;
        $this->save($record);
    }

    public function getRecordByTemplateId($template_id) {
        $recordObj = $this->findOne(array('template_id' => $template_id));
        return array('image_url' => $recordObj->image_url, 'link' => $recordObj->link, 'caption' => $recordObj->caption);
    }

}