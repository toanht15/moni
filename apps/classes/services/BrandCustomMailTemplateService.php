<?php
AAFW::import('jp.aainc.classes.services.StreamService');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

class BrandCustomMailTemplateService extends aafwServiceBase {

    private $brandCustomMailTemplates;
    private $logger;

    public function __construct() {
        $this->brandCustomMailTemplates = $this->getModel('BrandCustomMailTemplates');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    public function getBrandCustomMailByBrandId($brandId) {

        $filter = array(
            'conditions' => array(
                'brand_id' => $brandId,
            ),
        );

        $brandCustomMail = $this->brandCustomMailTemplates->findOne($filter);

        return $brandCustomMail;
    }

    public function createEmptyBrandCustomMail() {
        $page_settings = $this->brandCustomMailTemplates->createEmptyObject();
        return $page_settings;
    }

    public function setCustomMailTemplate($brandId, $customMailTemplate) {

        $brandCustomMailTemplate = $this->getBrandCustomMailByBrandId($brandId);

        if (!$brandCustomMailTemplate) {
            $brandCustomMailTemplate = $this->createEmptyBrandCustomMail();
            $brandCustomMailTemplate->brand_id = $brandId;
        }

        $brandCustomMailTemplate->sender_name = $customMailTemplate['sender_name'];
        $brandCustomMailTemplate->subject = $customMailTemplate['subject'];
        $brandCustomMailTemplate->body_plain = $customMailTemplate['body_plain'];

        $this->brandCustomMailTemplates->save($brandCustomMailTemplate);
    }
}