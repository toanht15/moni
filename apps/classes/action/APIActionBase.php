<?php

AAFW::import('jp.aainc.aafw.base.aafwActionBase');

abstract class APIActionBase extends aafwActionBase {

    const HEADER_SESSION_TOKEN_NAME = "Sessiontoken";

    protected $AllowContent = array('JSON');
    protected $allow_methods = [];

    const HTTP_METHOD_GET    = "GET";
    const HTTP_METHOD_POST   = "POST";
    const HTTP_METHOD_PUT    = "PUT";
    const HTTP_METHOD_DELETE = "DELETE";

    const DEFAULT_PAGE_NUMBER = 1;
    const DEFAULT_PAGE_COUNT  = 10;

    protected function isMethodAllowed($method) {
        if (in_array($method, $this->allow_methods)) {
            return true;
        }
        return false;
    }

    protected function getMethod() {
        return $this->SERVER['REQUEST_METHOD'];
    }

    public function doService() {
        try {

            $method = $this->getMethod();

            if (!$this->isMethodAllowed($method)) {
                return 403;
            }
            return $this->doAction();

        } catch (Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error($e);
            throw $e;
        }
    }

    public abstract function doAction();

    public function getAllHeaders() {
        $headers = '';
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

    public function getSessionToken() {
        $headers = $this->getAllHeaders();
        $logger = aafwLog4phpLogger::getDefaultLogger();
        $logger->info($headers);
        return $headers[self::HEADER_SESSION_TOKEN_NAME];
    }

    // Utility Method

    public function getPage($page) {
        if (!$this->isRealInteger($page) || $page < 1) {
            return self::DEFAULT_PAGE_NUMBER;
        } else {
            return $page;
        }
    }

    public function getCount($count) {
        if (!$this->isRealInteger($count) || $count < 1 || $count > self::DEFAULT_PAGE_COUNT) {
            return self::DEFAULT_PAGE_COUNT;
        } else {
            return $count;
        }
    }
}