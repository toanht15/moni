<?php

AAFW::import("jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase");

class UTTestAction extends BrandcoGETActionBase {

    public $result;

    function validate(){
    }
    function doAction(){
        return $this->result;
    }
}