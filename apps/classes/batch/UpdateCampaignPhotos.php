<?php
require_once dirname(__FILE__) . '/../../config/define.php';

class UpdateCampaignPhotos {
    private $logger;
    private $service_factory;

    public function __construct() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->service_factory = new aafwServiceFactory();
    }

    public function doProcess() {
        $store_factory = new aafwEntityStoreFactory();
        $storage_client = StorageClient::getInstance();
        $settings = aafwApplicationConfig::getInstance();

        $photo_user_store = $store_factory->create('PhotoUsers');
        $photo_users = $photo_user_store->findAll();

        foreach ($photo_users as $photo_user) {
            try {
                $cropped_photo_url = $photo_user->getCroppedPhoto();

                $temp_photo_user['name'] = '/tmp/' . pathinfo($photo_user->photo_url, PATHINFO_BASENAME);
                $context = stream_context_create(array(
                    'http' => array(
                        'method' => 'GET',
                        'header' => 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.104 Safari/537.36',
                    )
                ));
                file_put_contents($temp_photo_user['name'], file_get_contents($photo_user->photo_url, false, $context));

                $file_validator = new FileValidator($temp_photo_user, FileValidator::FILE_TYPE_IMAGE);
                $file_validator->isValidFile();

                ImageCompositor::cropSquareImage($temp_photo_user['name']);

                $temp_key_arr = explode($settings->query('@storage.AmazonS3.ImagePath'), $cropped_photo_url);
                $cropped_photo_key = $temp_key_arr[1];
                if (!$cropped_photo_key) continue;

                $storage_client->putObject($cropped_photo_key, $file_validator->getFileInfo(), StorageClient::ACL_PUBLIC_READ, false);

                unlink($temp_photo_user['name']);
            } catch (Exception $e) {
                $this->logger->error('UpdateCampaignPhotos@doProcess Error ' . $e);
            }
        }
    }
}