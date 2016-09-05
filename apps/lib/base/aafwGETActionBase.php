<?php
/*********************************
 * MVCのCのCの中身
 * @author t.ishida
 * @cre    2008/02/24
 **********************************/
AAFW::import ( 'jp.aainc.aafw.aafwValidatorBase' );
AAFW::import ( 'jp.aainc.aafw.aafwValidator' );
AAFW::import ( 'jp.aainc.aafw.aafwApplicationConfig' );
AAFW::import ( 'jp.aainc.aafw.base.aafwActionBase' );

abstract class aafwGETActionBase extends aafwActionBase {
  public function doService( ) {

    if ( $form = $this->getActionContainer('ValidateError')) {
      foreach ( $form as $key => $value ) $this->Data['ActionForm'][$key] = $value;
    } elseif ( $form = $this->getActionContainer('Result') ) {
      foreach ( $form as $key => $value ) $this->Data['ActionForm'][$key] = $value;
    }

    if ( $form = $this->getActionContainer('Errors') ) {
      $this->Data['ActionError'] =  $form;
    }

    $ret = $this->doAction();

    if ( is_array ( $this->Data ) && $form = $this->GET ) {
      foreach ( $form as $key => $value ) $this->Data['ActionForm'][$key] = $value;
    }

    if ( $form = $this->getActionContainer('Result') ) {
      foreach ( $form as $key => $value ) $this->Data['ActionForm'][$key] = $value;
    }

    if ( $form = $this->getActionContainer('ValidateError') ) {
      foreach ( $form as $key => $value ) $this->Data['ActionForm'][$key] = $value;
    }

    if ( $form = $this->getActionContainer('Errors') ) {
      $this->Data['ActionError'] =  $form;
      $this->resetActionContainerByKey('Errors');
    }
    return $ret;
  }
  abstract function doAction ();
}
