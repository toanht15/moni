<?php
namespace Merazoma;
use curely\core\web\Request;
class RestRouting extends \curely\core\web\standard\Routing {
    private $requestMethod = null;

    public function resolveMethodName($requestUri)
    {
        $method = strtolower($this->requestMethod);
        $result = null;
        if ($method === 'get' || $method === 'post' || $method === 'put' || $method === 'delete') {
            $result = $method;
        }
        return $result;
    }


    /**
     * @inject $_SERVER['REQUEST_METHOD']
     * @param null $requestMethod
     */
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