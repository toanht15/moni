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
class StorageClient {

    const ACL_PRIVATE = 'private';
    const ACL_PUBLIC_READ = 'public-read';
    const ACL_PUBLIC_READ_WRITE = 'public-read-write';
    const ACL_AUTHENTICATED_READ = 'authenticated-read';

    private static $instance = null;
    private $s3;
    private $bucket_name;
    private $settings;

    public function __construct() {
        $this->settings = aafwApplicationConfig::getInstance();
        $this->bucket_name = $this->settings->query('@storage.AmazonS3.BucketName');
        $this->s3 = S3Client::factory(array(
            'key' => $this->settings->query('@storage.AmazonS3.AccessKey'),
            'secret' => $this->settings->query('@storage.AmazonS3.SecretKey'),
            'region' => Region::AP_NORTHEAST_1
        ));
    }

    public static function getInstance(){
        if (self::$instance == null) self::$instance = new StorageClient();
        return self::$instance;
    }

    /**
     * @param $key
     * @param $file_info
     * @param string $acl
     * @param bool $extension
     * @return mixed
     * @throws Aws\S3\Exception\S3Exception
     */
    public function putObject($key, $file_info = array(), $acl = self::ACL_PUBLIC_READ, $extension = true) {
        if (!$key || !count($file_info)) throw new S3Exception();

        $imgKey = $this->settings->query('@storage.AmazonS3.ImagePath') . $key . ($extension ? '.' . $file_info['extension'] : '');

        $conditions = array(
            'Body' => EntityBody::factory(fopen($file_info['path'], 'r')),
            'Key' => $imgKey,
            'Bucket' => $this->bucket_name,
            'ACL' => $acl
        );

        $result = $this->s3->putObject($conditions);
        return $result['ObjectURL'];
    }

    /**
     * @param $key
     * @param array $file_info
     * @param string $acl
     * @param bool $extension
     * @return mixed
     * @throws Aws\S3\Exception\S3Exception
     */
    public function putTmpObject($key, $file_info = array(), $acl = self::ACL_PUBLIC_READ, $extension = true) {
        if (!$key || !count($file_info)) throw new S3Exception();

        $tmpKey = $this->settings->query('@storage.AmazonS3.TmpPath') . $key . ($extension ? '.' . $file_info['extension'] : '');

        $conditions = array(
            'Body' => EntityBody::factory(fopen($file_info['path'], 'r')),
            'Key' => $tmpKey,
            'Bucket' => $this->bucket_name,
            'ACL' => $acl
        );

        $result = $this->s3->putObject($conditions);
        return $result['ObjectURL'];
    }

    /**
     * @param $source_key
     * @param $target_key
     * @return mixed
     * @throws S3Exception
     */
    public function copyObject($source_key, $target_key) {
        if (!$source_key || !$target_key) throw new S3Exception();

        $conditions = array(
            'Bucket' => $this->bucket_name,
            'Key' => $target_key,
            'CopySource' => "{$this->bucket_name}/{$source_key}",
            'ACL' => self::ACL_PUBLIC_READ
        );

        $this->s3->copyObject($conditions);

        $this->deleteObject($source_key);
        return $this->getObjectUrl($target_key);
    }

    /**
     * @param $key
     * @param null $expires
     * @return string
     * @throws Aws\S3\Exception\S3Exception
     */
    public function getObjectUrl($key, $expires = null) {
        if (!$key) throw new S3Exception();
        return $this->s3->getObjectUrl($this->bucket_name, $key, $expires);
    }

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

    /**
     * @param $key
     * @return string
     */
    public function getPrefixByKey($key){
        return $this->getSettingImagePath() . $this->toHash($key);
    }

    /**
     * @param $key
     * @return Model
     * @throws Aws\S3\Exception\S3Exception
     */
    public function deleteObject($key) {
        if (!$key) throw new S3Exception();
        $conditions = array(
            'Bucket' => $this->bucket_name,
            'Key' => $key
        );
        return $this->s3->deleteObject($conditions);
    }

    /**
     * @param array $objects
     * @throws Aws\S3\Exception\S3Exception
     */
    public function deleteObjects(array $objects = array()){
        if (count($objects)) throw new S3Exception();
        $conditions = array(
            'Bucket' => $this->bucket_name,
            'Objects' => $objects
        );
        $this->s3->deleteObjects($conditions);
    }

    /**
     * @param $url
     * @return bool|string
     */
    public function getImageKey($url){
        if (!$url) return false;
        return mb_strstr($url, $this->settings->query('@storage.AmazonS3.ImagePath'));
    }

    /**
     * @param $url
     * @return bool|string
     */
    public function getSettingImagePath(){
        return $this->settings->query('@storage.AmazonS3.ImagePath');
    }

    /**
     * @param $url
     * @return bool|string
     */
    public function getTmpKey($url){
        if (!$url) return false;
        return mb_strstr($url, $this->settings->query('@storage.AmazonS3.TmpPath'));
    }

    /**
     * @param $path
     * @return array|bool
     */
    public function getImageInfo($path) {
        $ret = getimagesize($path);
        if ($ret) {
            return array(
                'width' => $ret[0],
                'height' => $ret[1],
                'type' => str_replace('image/', '', $ret['mime']),
            );
        } else {
            return false;
        }
    }

    /**
     *
     * @param $key
     * @return string
     */
    public static function toHash($key) {
        $keys = explode('/',$key);
        $hashedKeys = array();
        foreach ($keys as $key) {
            if ((is_numeric($key) || ($key == end($keys))) && $key != ""){
                $hashedKeys[] = md5($key);
            }else{
                $hashedKeys[] = $key;
            }
        }
        return implode('/', $hashedKeys);
    }

    public static function getUniqueId() {
        return sha1(uniqid() . mt_rand());
    }

    public static function getMiddleImageUrl($url) {
        $photo_path = pathinfo($url);
        if (substr($photo_path['filename'], -2) == '_m') {
            return $url;
        } else {
            return $photo_path['dirname'] . '/' . $photo_path['filename'] . '_m.' . $photo_path['extension'];
        }
    }

    public static function getRegularImageUrl($url) {
        $photo_path = pathinfo($url);
        if (substr($photo_path['filename'], -2) == '_r') {
            return $url;
        } else {
            return $photo_path['dirname'] . '/' . $photo_path['filename'] . '_r.' . $photo_path['extension'];
        }
    }
}
