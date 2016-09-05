<?php
require_once dirname(__FILE__) . '/../../config/define.php';

class DeleteConversionRecordOverMonth
{
    public $logger;
    public $service_factory;

    public function __construct()
    {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->service_factory = new aafwServiceFactory();
    }

    public function doProcess()
    {
        /** @var ConversionService $conversion_service */
        $conversion_service = $this->service_factory->create('ConversionService');

        $id = null;
        try {
            //コンバージョンログをチェックして削除する
            $conversion_logs = $conversion_service->getConversionLogsOverMonth();
            $conversion_service->deleteConversionLogs($conversion_logs);

            //brand_user_conversionをチェックして削除する
//            $brand_user_conversions = $conversion_service->getBrandUserConversionOverMonth();
//            $conversion_service->deleteBrandUserConversion($brand_user_conversions);

        } catch (Exception $e) {
            $this->logger->error($e);
        }
    }
}