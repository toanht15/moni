<?php
/**
 * Driver.php@api
 * User: ishidatakeshi
 * Date: 2014/09/12
 * Time: 16:21
 */

namespace Merazoma\driver;
abstract class Driver {
    private $host = null;

    public function __construct ($host) {
        $this->setHost($host);
    }

    public abstract function get($url, $params);
    public abstract function post($url, $params);
    public abstract function delete($url, $params);
    public abstract function put($url, $file);

    /**
     * @param null $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return null
     */
    public function getHost()
    {
        return $this->host;
    }

}