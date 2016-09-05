<?php
require_once('aws.phar');

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\Common\Enum\Region;
use Guzzle\Http\EntityBody;
use Guzzle\Service\Resource;

/**
 * Class S3Manager
 * S3Client wrapper
 */
abstract class BaseStorageClient {

    protected $s3;
    protected $bucket_name;

    /**
     * @param $key
     * @param $save_as save file path to tmp directory
     * @return Model
     * @throws Aws\S3\Exception\S3Exception
     */
    public function getObject($key, $save_as = null) {
        if (!$key) throw new S3Exception();
        $conditions = array(
            'Bucket' => $this->bucket_name,
            'Key' => $key
        );
        if ($save_as) $conditions['SaveAs'] = $save_as;
        return $this->s3->getObject($conditions);
    }

    /**
     * ディレクトリ配下のオブジェクト一覧取得
     * @param $prefix
     * @return Model
     */
    public function listObjects($prefix) {
        $conditions = array(
            'Bucket' => $this->bucket_name,
            'Prefix' => $prefix
        );
        return $this->s3->listObjects($conditions)['Contents'];
    }

    abstract public function getPrefixDataFile($params);

}
