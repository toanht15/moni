<?php
AAFW::import('jp.aainc.aafw.base.aafwGETActionBase');
class index extends aafwGETActionBase {

    public function validate() {
        return true;
    }

    function doAction() {
        $cp = $this->findCurrentSynCp();
        if( $cp ) {
            return 'redirect: ' . Util::rewriteUrl($cp->getBrand()->directory_name, 'campaigns', array($cp->id),$_GET);
        } else {
            return 'redirect: ' . Util::rewriteUrl('', $this->findSynBrand()->directory_name,array(),$_GET);
        }
    }

    /**
     * @return Brand
     */
    function findSynBrand() {
        return $this->getModel('brands')->findOne(config('SynBrandId'));
    }

    /**
     * @return Cp
     */
    public function findCurrentSynCp() {
        $now = date("Y-m-d H:i:s", time());
        $synCpRedirectDuration = $this->getModel('SynCpRedirectDurations')->findOne(
            array('start_at:<=' => $now, 'end_at:>' => $now)
        );
        if( !$synCpRedirectDuration ) {
            return null;
        }
        $synCp = $synCpRedirectDuration->getSynCp();
        if( !$synCp ) {
            return null;
        }
        return $synCp->getCp();
    }
}