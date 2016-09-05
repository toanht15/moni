<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');
class StaticHtmlInstagrams extends aafwEntityStoreBase {
    protected $_TableName = 'static_html_instagrams';
    protected $_EntityName = 'StaticHtmlInstagram';
    protected $_DeleteType = aafwEntityStoreBase::DELETE_TYPE_PHYSICAL;

    const NUMBER_IMAGE_PER_PAGE_SP = 6;

    public static $number_image_per_pages = array(
        StaticHtmlEntries::LAYOUT_NORMAL => 6,
        StaticHtmlEntries::LAYOUT_LP     => 12,
        StaticHtmlEntries::LAYOUT_FULL   => 12
    );

    public function insert($template_id, $template) {
        $record = $this->createEmptyObject();
        $record->template_id = $template_id;
        $record->api_url = $template->api_url;
        $this->save($record);
    }

    public function getRecordByTemplateId($template_id) {
        $recordObj = $this->findOne(array('template_id' => $template_id));
        return array(
            'api_url' => $recordObj->api_url,
        );
    }
}