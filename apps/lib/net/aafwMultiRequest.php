<?php
AAFW::import('jp.aainc.aafw.net.aafwCurlMultiRequestBase');

class aafwMultiRequest {

    private $tasks = array();
    private $multiHandler = null;
    private $reference = array();
    private $running = null;

    public function __construct() {
    }

    public function add($task) {
        $this->tasks[] = $task;
    }

    public function count() {
        return count($this->tasks);
    }

    public function request() {
        $this->initCurlMulti();
        foreach ($this->tasks as $row) {
            $this->addHandleToCurl($row->prepareMultiExec());
        }
        $this->multiExec();
        $results = $this->getResults();
        $this->release();
        return $results;
    }

    public function getRunning() {
        return $this->running;
    }

    public function setRunning($running) {
        $this->running = $running;
    }

    public function multiExec() {
        do {
            $this->exec();
        } while ($this->running > 0);
    }

    public function exec() {
        curl_multi_exec($this->multiHandler, $this->running);
    }


    public function initCurlMulti() {
        $this->multiHandler = curl_multi_init();
    }

    public function getResults() {
        $results = array();
        foreach ($this->reference as $row) {
            $results[] = curl_multi_getcontent($row);
        }
        return $results;
    }

    public function release() {
        foreach ($this->reference as $row) {
            curl_multi_remove_handle($this->multiHandler, $row);
        }
        $this->tasks = array();
    }

    public function addHandleToCurl($curl) {
        $this->reference[] = $curl;
        curl_multi_add_handle($this->multiHandler, $curl);
    }

}
