<?php
/**
 * Created by PhpStorm.
 * User: katoriyusuke
 * Date: 15/07/30
 * Time: 22:23
 */

AAFW::import('jp.aainc.aafw.base.aafwActionPluginBase');
AAFW::import('jp.aainc.classes.CpInfoContainer');
AAFW::import('jp.aainc.classes.RequestUserInfoContainer');

class CheckCpClosed extends aafwActionPluginBase {
    protected $HookPoint  = 'First';
    protected $Priority   = 1;
    protected $AllowSites = array('user');
    private $cps;

    public function doService() {
        if (!$this->Action->checkCpClosed) return;

        list($p, $g, $s, $c, $f, $e, $sv, $r) = $this->Action->getParams();
        $cpId = $g['exts'][0];

        $cp = CpInfoContainer::getInstance()->getCpById($cpId);
        if (!$cp) {
            return "404";
        }

        if (RequestuserInfoContainer::getInstance()->getStatusByCp($cp) === Cp::CAMPAIGN_STATUS_CP_PAGE_CLOSED) {
            return 'redirect:' .Util::rewriteUrl('', 'closed_campaigns', array($cp->id));
        }
    }
}