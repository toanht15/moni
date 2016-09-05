<?php
AAFW::import('jp.aainc.aafw.base.aafwGETActionBase');

class mobile_app_current_version extends aafwGETActionBase {

    public $Secure = false;

    protected $AllowContent = array('JSON');
    public $deviceToken;

    /**
     * @var APIManager
     */
    public $apiManager;

    const IOS_VERSION     = '5.02';
    const ANDROID_VERSION = '3.1.7';

    public function doThisFirst()
    {
        $this->apiManager  = APIManager::getInstance();
        $this->deviceToken = $this->device_token;
    }

    public function validate () {
        if (!$this->apiManager->validDeviceToken($this->deviceToken)) {
            $this->apiManager->errorResponse(APIManager::INVALID_DEVICE_TOKEN);
            return false;
        }

		return true;
	}

	function doAction() {
        $response = array();

        $version = '';
        if ($this->apiManager->isIPhone($this->deviceToken)) {
            $version = self::IOS_VERSION;

        } else if ($this->apiManager->isAndroid($this->deviceToken)) {
            $version = self::ANDROID_VERSION;
        }

        $response['ver'] = $version;

        $this->apiManager->outputResult(array('data' => $response));
    }
}

class APIManager
{
    private static $instance = null;

    const INVALID_ACCESS_TOKEN = 'invalid_access_token';
    const INVALID_DEVICE_TOKEN = 'invalid_device_token';
    const INVALID_ARGUMENT = 'invalid_argument';

    const API_SUCCESS = 'success';
    const API_FAILURE = 'failure';

    const DEVICE_TOKEN_IPHONE  = '6d425d447257cbd31be4d8d5d5c6620e8201197d2cd2f55d36c0d9c85cae58da';
    const DEVICE_TOKEN_ANDROID = 'cbc208538ab33f2264f011ad3367f0d91c2cd77b331fd2147f2ec4d00479f5ed';

    public static $deviceTokenArray = array(
        self::DEVICE_TOKEN_IPHONE,
        self::DEVICE_TOKEN_ANDROID,
    );

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new APIManager();
        }
        return self::$instance;
    }

    /**
     * MoniplaClientのメソッドを叩いた結果が成功しているかどうかの判定
     *
     * @param object $result
     * @return bool 成功していればtrue
     */
    public function isValidResponse( $result )
    {
        return $result->result->status == Thrift_APIStatus::SUCCESS;
    }

    /**
     * device_tokenのvalidate
     * @param $deviceToken
     * @return bool
     */
    public function validDeviceToken( $deviceToken ) {
        if( trim( $deviceToken ) === '' ) {
            return false;
        }

        if( !in_array( $deviceToken, self::$deviceTokenArray ) ) {
            return false;
        }
        return true;
    }

    /**
     * JSON形式で出力する
     * @param array $response
     * @param string $callback
     */
    public function outputResult( $response, $callback = null )
    {
        header( "Content-Type: text/javascript; charset=utf-8" );

        $encodedResponse = json_encode($response);
        if( $callback ) {
            echo $callback . "(" . $encodedResponse . ");";
        } else {
            echo $encodedResponse;
        }
        $this->apiExit();
    }

    /**
     * @param string $message
     */
    public function errorResponse( $message ) {
        $errorResult = array(
            'data' => array(
                'status' => self::API_FAILURE,
                'error'  => array(
                    'message' => $message,
                ),
            ),
        );
        $this->outputResult( $errorResult );
    }

    /**
     * メソッド内でexit()するとテスト書けなくなるから
     * これをモック化すればテスト書けるよって状態にする
     */
    public function apiExit()
    {
        exit();
    }

    /**
     * androidの判別
     * @param $deviceToken
     * @return bool
     */
    public function isAndroid($deviceToken)
    {
        return $deviceToken === self::DEVICE_TOKEN_ANDROID;
    }

    /**
     * iPhoneの判別
     * @param $deviceToken
     * @return bool
     */
    public function isIPhone($deviceToken)
    {
        return $deviceToken === self::DEVICE_TOKEN_IPHONE;
    }

}