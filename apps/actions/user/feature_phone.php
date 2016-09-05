<?php
AAFW::import('jp.aainc.aafw.aafwApplicationConfig');
AAFW::import('jp.aainc.aafw.base.aafwGETActionBase');
class feature_phone extends aafwGETActionBase {

  public function validate () {
    return true;
  }

  function doAction() {
    $this->Data['url'] = $this->GET['url'];

    return 'user/feature_phone.php';
  }
}