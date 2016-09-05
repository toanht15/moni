<?php
/**
 * Client.php@api
 * User: ishidatakeshi
 * Date: 2014/09/12
 * Time: 12:51
 */

namespace Merazoma;


use Merazoma\driver\Driver;

class Client {
    /**
     * @var Driver
     */
    private $driver = null;

    function __construct($driver)
    {
        $this->driver = $driver;
    }


    public function api ($method, $url, $params) {
        $method = strtolower($method);
        $result = null;
        if     ($method === 'get')    $result = $this->get($url, $params);
        elseif ($method === 'post')   $result = $this->post($url, $params);
        elseif ($method === 'delete') $result = $this->delete($url, $params);
        elseif ($method === 'put')    $result = $this->put($url, $params);
        else                          throw new \InvalidArgumentException('invalid method');
        return json_decode($result);
    }

    public function get ($url, $params) {
        return $this->driver->get($url, $params);
    }

    public function post ($url, $params) {
        return $this->driver->post($url, $params);
    }

    public function delete ($url, $params) {
        return $this->driver->delete($url, $params);
    }

    public function put ($url, $file) {
        return $this->driver->put($url, $file);
    }
}