<?php
abstract class aafwControllerPluginBase{
  protected $Controller = null;
  protected $Priority   = null;
  protected $AllowSites = null;
  protected $Enabled    = true;

  public function __construct( $ct ){ $this->Controller = $ct; }
  public function getHookPoint()    { return $this->HookPoint; }
  public function getPriority()     { return $this->Priority ? $this->Priority : 99999 ; }
  public function canRun () {
    if ( !$this->Enabled )               return false;
    if ( !$this->AllowSites )            return true;
    if ( !$this->Controller->getSite() ) return true;
    if ( !is_array ( $this->AllowSites ) ) $this->AllowSites = array ( $this->AllowSites );
    return in_array ( $this->Controller->getSite(), $this->AllowSites );
  }
  abstract function doService();
}
