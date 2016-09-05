<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.lib.base.aafwObject');

class SyncPageEntries {
    private $logger;
    private $service_factory;
    private $brand_id;

    public function __construct($brand_id) {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->service_factory = new aafwServiceFactory();
        $this->brand_id = is_numeric($brand_id) ? $brand_id : -1;
    }

    public function doProcess() {
        try {
            $page_stream_service = $this->service_factory->create('PageStreamService');
            $static_html_entry_service = $this->service_factory->create('StaticHtmlEntryService');

            if ($this->brand_id != -1) {
                $page_streams[] = $page_stream_service->getStreamByBrandId($this->brand_id);
            } else {
                $page_streams = $page_stream_service->getAllStream();
            }

            foreach ($page_streams as $page_stream) {
                $static_html_entries = $static_html_entry_service->getEntries($page_stream->brand_id);

                if (!$static_html_entries) continue;

                foreach ($static_html_entries as $static_html_entry) {
                    $entry = $page_stream_service->getEntryByStaticHtmlEntryId($static_html_entry->id);
                    if ($entry) continue;

                    // Create new page entry based on static html entry
                    $page_entry = $page_stream_service->createEmptyEntry();

                    $page_entry->stream_id = $page_stream->id;
                    $page_entry = $page_stream_service->staticHtmlToPageEntry($page_entry, $static_html_entry);

                    $page_stream_service->updateEntry($page_entry);
                }
            }
        } catch (Exception $e) {
            $this->logger->error('SyncPageEntries#doProcess Error');
            $this->logger->error($e);
        }
    }
} 