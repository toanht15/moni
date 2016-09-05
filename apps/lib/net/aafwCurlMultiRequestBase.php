<?php
AAFW::import('jp.aainc.aafw.net.aafwCurlRequestBase');

abstract class aafwCurlMultiRequestBase extends aafwCurlRequestBase {
    protected $parameters = null;

    public function getParameters() {
        return $this->parameters;
    }

    public function setParameters($params) {
        $this->parameters = $params;
    }

    abstract public function prepareMultiExec();

    public function send() {
        return $this->request($this->prepareMultiExec());
    }
}
