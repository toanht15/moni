<?php

namespace Monipla\Core;

/**
 * Created by IntelliJ IDEA.
 * User: ishidatakeshi
 * Date: 2013/03/12
 * Time: 11:41
 * To change this template use File | Settings | File Templates.
 */

require_once 'vendor/thrift/Thrift.php';
require_once 'vendor/thrift/packages/Monipla/Monipla.php';
require_once $GLOBALS['THRIFT_ROOT'].'/packages/Monipla/Monipla.Monipla.client.php';

/**
 * Class MoniplaCore
 * @unserializable true
 */
class MoniplaCore
{
    public $retrySpan = 1;
    /**
     * @var MoniplaCore
     */
    private static $instance = null;
    /**
     * @var \TBufferedTransport
     */
    private $transport = null;
    /**
     * @var \TBinaryProtocol
     */
    private $protocol = null;
    /**
     * @var \MoniplaClient
     */
    private $client = null;

    /**
     * @var \TSocket
     */
    private $socket = null;

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     *
     */
    public function initialize()
    {
        $this->socket = new \TSocket (TCORE_SERVER, TCORE_PORT);
        $this->socket->setSendTimeout(3 * 1000);
        $this->socket->setRecvTimeout(15 * 1000);
        $this->transport = new \TBufferedTransport ($this->socket);
        $this->protocol = new \TBinaryProtocol($this->transport);
        $this->client = new \MoniplaClient($this->protocol);
        $this->transport->open();
    }

    /**
     *
     */
    public function batchInitialize()
    {
        if ($this->socket->isOpen()) $this->socket->close();
        if ($this->transport->isOpen()) $this->transport->close();
        $this->socket = new \TSocket (TCORE_SERVER, TCORE_PORT);
        $this->socket->setSendTimeout(3 * 1000);
        $this->socket->setRecvTimeout(600 * 1000);
        $this->transport = new \TBufferedTransport ($this->socket);
        $this->protocol = new \TBinaryProtocol($this->transport);
        $this->client = new \MoniplaClient($this->protocol);
        $this->transport->open();
    }

    /**
     *
     */
    public function reinitialization()
    {
        if ($this->socket->isOpen()) $this->socket->close();
        if ($this->transport->isOpen()) $this->transport->close();
        try {
            $this->initialize();
        } catch (\Exception $e) {
            sleep($this->retrySpan);
        }
    }

    /**
     * @param $arg
     */
    public function setClient($arg)
    {
        $this->client = $arg;
    }

    /**
     * @param $arg
     */
    public function setProtocol($arg)
    {
        $this->protocol = $arg;
    }

    /**
     * @param $arg
     */
    public function setTransport($arg)
    {
        $this->transport = $arg;
    }

    /**
     * @return null
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return null
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @return null
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @return MoniplaCore|null
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            if (!defined('TCORE_SERVER') || !defined('TCORE_PORT')) {
                $path = __DIR__ . '/MoniplaCoreSettings.php';
                if (!is_file($path)) {
                    throw new \Exception('MoniplaCoreの設定ファイルがありません');
                }
                include_once $path;
            }
            self::$instance = new MoniplaCore();
            self::$instance->initialize();
        }
        return self::$instance;
    }

    /**
     * @param $name
     * @param $args
     * @throws \Exception
     * @return null
     */
    public function __call($name, $args)
    {
        $result = null;
        $args = $this->convertParams($args);
        $code = $this->createCode($name, $args);
        while (true) {
            try {
                eval ($code);
                break;
            } catch (\Exception $e) {
                if ($i++ >= 3) throw new \Exception('call failed!| message=' . $e->getMessage() . ", args=" . json_encode($args, JSON_PRETTY_PRINT));
                $this->reinitialization();
            }
        }

        $apiResult = $this->findAPIResult($result);
        if ($apiResult && $apiResult->status != $GLOBALS['E_APIStatus']['SUCCESS']) {
            if ($apiResult->errors && $apiResult->errors[0]->message != 'レコードが見付かりませんでした') {
                $trace = debug_backtrace();
                $caller = $trace[1]['file'];
                log_error('The Thrift call has failed!: caller=' . $caller . ', args=' . json_encode($args) .
                    ', name=' . $name . ', result=' . json_encode($result));
            }
        }
        return $result;
    }

    public function convertParams($args)
    {
        for ($i = 0; $i < count($args); $i++) {
            if (isset($args[$i]['class']) && isset($args[$i]['fields'])) {
                $args[$i] = $this->createObject($args[$i]);
            } elseif (is_array($args[$i])) {
                $args[$i] = $this->convertParams($args[$i]);
            }
        }
        return $args;
    }

    /**
     * @param $name
     * @param $args
     * @return string
     */
    public function createCode($name, $args)
    {
        $code = '$result = $this->client->' . $name . '(';
        for ($i = 0; $i < count($args); $i++) {
            $code .= '$args[' . $i . '],';
        }
        $code = preg_replace('#,$#', '', $code);
        $code .= ');';
        return $code;
    }

    /**
     * @param $arg
     * @return mixed
     */
    public function createObject($arg)
    {
        $obj = new $arg['class'];
        foreach ($arg['fields'] as $key => $val) {
            if (is_array($val) && isset ($val['class']) && isset ($val['fields'])) {
                $obj->$key = $this->createObject($val);
            } else {
                $obj->$key = $val;
            }
        }
        return $obj;
    }

    /**
     * @param $result
     * @return array
     */
    public function findAPIResult($result)
    {
        $apiResult = null;
        if (is_object($result)) {
            if ($result instanceof \Thrift_APIResult) {
                $apiResult = $result;
            } elseif (in_array("result", get_class_vars(get_class($result))) && $result->result instanceof \Thrift_APIResult) {
                $apiResult = $result->result;
            }
        } elseif (is_array($result) && isset ($result['result']) && $result['result'] instanceof \Thrift_APIResult) {
            $apiResult = $result['result'];
        }
        return $apiResult;
    }

    public function resolveSignedRequest($content, $secretKey)
    {
        list ($signature, $rawPayload) = preg_split('#\.#', trim($content));
        if (!$signature || !$rawPayload || $signature != hash_hmac("sha1", $rawPayload, $secretKey)) {
            throw new \Exception ('リクエストが不正です: payload=' . $content);
        }
        $payload = base64_decode(str_pad(strtr($rawPayload, '-_', '+/'), strlen($rawPayload) % 4, '=', STR_PAD_RIGHT));
        return json_decode($payload);
    }

    public function getUserRecursiveById($id)
    {
        $result = $this->getUserRecursive(array(
            'class' => 'Thrift_UserQuery',
            'fields' => array(
                'id' => $id
            )));

        $return = null;
        if ($result->result->status == \Thrift_APIStatus::SUCCESS) {
            $result->user->socialAccounts = array_map(function ($elm) {
                return (object)((array)$elm);
            }, $result->user->socialAccounts);
            $ids = $result->ids ? $result->ids : array($result->user->id);
            $return = (object)array(
                'ids'    => $ids,
                'user'   => $result->user,
                'result' => \Thrift_APIStatus::SUCCESS,
                'merged' => $result->ids && $result->ids[count($result->ids) - 1] != $id,
                'errors' => array(),
            );
        } else {
            $return = (object)array(
                'ids'    => null,
                'user'   => null,
                'result' => \Thrift_APIStatus::FAIL,
                'merged' => null,
                'errors' => array(),
            );
        }
        return $return;
    }

    public function duplicateMailAddress($mailAddress)
    {
        if (!trim($mailAddress)) return false;
        $result = $this->getDuplicates($mailAddress);
        return $result->duplicated;
    }

    public function mergeDuplicates($mailAddress)
    {
        if (!trim($mailAddress)) return null;
        $result = $this->getDuplicates($mailAddress);
        $params = array();
        foreach ($result->user as $user) {
            $params[] = array(
                'class' => 'Thrift_SocialAccount',
                'fields' => array(
                    'socialMediaType' => 'Platform',
                    'socialMediaAccountID' => $user->id,
                    'name' => 'ユーザー名を入力してください',
                    'validated' => 1,
                ));
        }
        return $this->mergeAccount($params);
    }

    public function getDuplicates($mailAddress)
    {
        if (!trim($mailAddress)) return null;
        $result = $this->getUsersByMailAddress(array(
            'class' => 'Thrift_UserQuery',
            'fields' => array(
                'mailAddress' => $mailAddress,
            )));
        return $result;
    }

    public function getUserByAccessToken($accessToken)
    {
        if (!trim($accessToken)) return (object)array('accessToken' => null, 'refreshToken' => null);
        $result = $this->getUser(array(
            'class' => 'Thrift_AccessTokenParameter',
            'fields' => array(
                'accessToken' => $accessToken
            )));
        return $result;
    }

    public function resolveLoginToken($loginToken)
    {
        if (!trim($loginToken)) return null;
        $result = $this->checkBackdoorLogin(array(
            'class' => 'Thrift_LoginTokenParameter',
            'fields' => array(
                'token' => $loginToken,
            )));
        return $result;
    }

    public function getApplicationByClientId($clientId)
    {
        if (!trim($clientId)) return null;
        $result = $this->getApplication( array (
            'class'  => 'Thrift_ApplicationQuery',
            'fields' => array (
                'clientId' => $clientId,
            )));
        return $result;
    }

    public function buildReturnUrl($app, $url)
    {
        if (!preg_match('#^/#',$url)) return null;
        $result = $this->getApplicationByClientId($app);
        $destination = null;
        if ($result->result->status != \Thrift_APIStatus::SUCCESS) return null;
        if (!$result->redirectUri)                                 return null;
        $elements = parse_url($result->redirectUri);
        return $elements['scheme'] . '://' . $elements['host'] . $url;
    }

    public function checkValidScope($scope)
    {
        $result = $this->isValidScope(array(
            'class' => 'Thrift_OAuthScopeQuery',
            'fields' => array(
                'scopes' => $scope,
            ),
        ));
        if ($result->result->status != \Thrift_APIStatus::SUCCESS || !$result->valid) return false;
        return true;
    }


    /**
     *
     */
    public function __destruct()
    {
        if ($this->transport && $this->transport->isOpen()) {
            $this->transport->close();
        }
    }

    /**
     * @param int $seconds
     */
    public function setRetrySpan($seconds)
    {
        $this->retrySpan = $seconds;
    }

    /**
     * @return int
     */
    public function getRetrySpan()
    {
        return $this->retrySpan;
    }

    /**
     * セッションで管理するために配列に詰め替え
     * @param $userInfo
     * @return array
     */
    public  function castSocialAccounts($userInfo) {
        if (!$userInfo)          return array();
        if (is_array($userInfo)) return $userInfo;
        if ($userInfo->result->status != \Thrift_APIStatus::SUCCESS) return array();
        if ($userInfo->socialAccounts) {
            $userInfo->socialAccounts = array_map (function($elm){ return (object)((array)$elm); }, $userInfo->socialAccounts );
        }  elseif (property_exists($userInfo, 'socialAccounts')) {
            $userInfo->socialAccounts = array();
        }
        return (array) $userInfo;
    }
}
