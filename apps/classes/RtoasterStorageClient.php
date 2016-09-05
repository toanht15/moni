<?php

use Aws\S3\S3Client;
use Aws\Common\Enum\Region;

class RtoasterStorageClient extends BaseStorageClient {

    public function __construct() {
        $this->bucket_name = 'cf-outbound';
        $this->s3 = S3Client::factory(array(
            'key' => 'AKIAJCMOIU5AV6SGZJAQ',
            'secret' => '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',
            'region' => Region::AP_NORTHEAST_1
        ));
    }
    
    public function getPrefixDataFile($params) {
       return 'P100004/segment_list_data_'.date('Ymd', strtotime($params['date']));
    }
}
