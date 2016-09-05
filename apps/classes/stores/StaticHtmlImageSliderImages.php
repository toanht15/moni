<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');
class StaticHtmlImageSliderImages extends aafwEntityStoreBase {
    protected $_TableName = 'static_html_image_slider_images';
    protected $_DeleteType = aafwEntityStoreBase::DELETE_TYPE_PHYSICAL;

    public function getRecordsBySliderId($slider_Id) {
        $ret = array();
        $images = $this->find(array("conditions" => array("static_html_image_slider_id" => $slider_Id), "order" => "no asc"));
        foreach($images as $image) {
            $template = array();
            $template['image_url'] = $image->image_url;
            $template['caption'] = $image->caption;
            $template['link'] = $image->link;
            $ret[] = $template;
        }
        return $ret;
    }
}