<?php
abstract class aafwActionPluginBase{
  protected $Action     = null;
  protected $Priority   = null;
  protected $AllowSites = null;
  protected $Enabled    = true;

  public function __construct( $ac ){ $this->Action = $ac;     }
  public function getPriority()     { return $this->Priority ? $this->Priority : 99999 ; }
  public function getHookPoint()    { return $this->HookPoint; }
  public function canRun () {
    if ( !$this->Enabled )           return false;
    if ( !$this->AllowSites )        return true;
    if ( !$this->Action->getSite() ) return true;
    if ( !is_array ( $this->AllowSites ) ) $this->AllowSites = array ( $this->AllowSites );
    return in_array ( $this->Action->getSite(), $this->AllowSites );
  }

  //  abtract function doService();
}
