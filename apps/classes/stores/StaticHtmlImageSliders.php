<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');
class StaticHtmlImageSliders extends aafwEntityStoreBase {
    protected $_TableName = 'static_html_image_sliders';
    protected $_DeleteType = aafwEntityStoreBase::DELETE_TYPE_PHYSICAL;

    public function delete($sliderObj) {
        if(!$sliderObj->id) return false;
        $imageSliderImages = $this->getModel('StaticHtmlImageSliderImages');
        $images = $imageSliderImages->find(array('static_html_image_slider_id' => $sliderObj->id));
        foreach( $images as $image) {
            $imageSliderImages->delete($image);
        }
        parent::delete($sliderObj);

    }

    public function insert($template_id, $template) {
        $imageSliderImages = $this->getModel('StaticHtmlImageSliderImages');

        $record = $this->createEmptyObject();
        $record->template_id = $template_id;
        $record->slider_pc_image_count = $template->slider_pc_image_count;
        $record->slider_sp_image_count = $template->slider_sp_image_count;
        $record = $this->save($record);
        $static_html_image_slider_id = $record->id;
        $no = 1;
        foreach($template->item_list as $image) {
            $imageRecord = $imageSliderImages->createEmptyObject();
            $imageRecord->static_html_image_slider_id = $static_html_image_slider_id;
            $imageRecord->no = $no;
            $imageRecord->image_url = $image->image_url;
            $imageRecord->caption = $image->caption;
            $imageRecord->link = $image->link;
            $imageSliderImages->save($imageRecord);
            $no++;
        }

    }

    public function getRecordByTemplateId($template_id) {
        $imageSliderImages = $this->getModel('StaticHtmlImageSliderImages');
        $recordObj = $this->findOne(array('template_id' => $template_id));
        return array(
            'slider_pc_image_count' => $recordObj->slider_pc_image_count,
            'slider_sp_image_count' => $recordObj->slider_sp_image_count,
            'item_list' => $imageSliderImages->getRecordsBySliderId($recordObj->id)
        );
    }


}