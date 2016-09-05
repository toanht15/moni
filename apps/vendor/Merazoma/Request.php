<?php
/**
 * Request.php@api
 * User: ishidatakeshi
 * Date: 2014/09/16
 * Time: 17:40
 */

namespace Merazoma;


class Request {
    private $request = null;
    private $requestMethod = null;

    /**
     * @inject $_SERVER['REQUEST_METHOD']
     * @param null $method
     */
    public function __construct ($method) {
        $this->requestMethod = strtoupper($method);
        $this->request = $_REQUEST;
        if ($this->requestMethod === 'PUT') {
            $this->request['@PUT_BODY'] = rawurldecode(file_get_contents('php://input'));
        }
        elseif ($this->requestMethod === 'DELETE') {
            $request = array();
            parse_str(file_get_contents('php://input'), $request);
            foreach ($request as $key => $val) $this->request[$key] = $val;
        }
    }

    public function get ($key)
    {
        return isset($this->request) ? $this->request[$key] : null;
    }

    public function setRequestMethod($requestMethod)
    {
        $this->requestMethod = $requestMethod;
    }

    /**
     * @return null
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }
}