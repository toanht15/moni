<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');
class StaticHtmlFloatImages extends aafwEntityStoreBase {
    protected $_TableName = 'static_html_float_images';
    protected $_DeleteType = aafwEntityStoreBase::DELETE_TYPE_PHYSICAL;

    public function insert($template_id, $template) {
        $record = $this->createEmptyObject();
        $record->template_id = $template_id;
        $record->position_type = $template->position_type;
        $record->smartphone_float_off_flg = $template->smartphone_float_off_flg;
        $record->image_url = $template->image_url;
        $record->caption = $template->caption;
        $record->text = $template->text;
        $record->link = $template->link;
        $this->save($record);
    }

    public function getRecordByTemplateId($template_id) {
        $recordObj = $this->findOne(array('template_id' => $template_id));
        return array(
            'position_type' => $recordObj->position_type,
            'smartphone_float_off_flg' => $recordObj->smartphone_float_off_flg,
            'image_url' => $recordObj->image_url,
            'caption' => $recordObj->caption,
            'text' => $recordObj->text,
            'link' => $recordObj->link
        );
    }

}