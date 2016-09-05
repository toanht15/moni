<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwActionPluginBase' );

class CheckManagerLogin extends aafwActionPluginBase {
    protected $HookPoint = 'First';
    protected $Priority  = 2; // SetFactoriesのあと

    public function doService(){

        if ($this->Action->NeedManagerLogin) {
            if (Util::isAcceptRemote() && !Util::isPersonalMachine()) return 404;
            if ($this->Action->isLoginManager()) {
                list( $p, $g, $s, $c, $f, $e, $sv, $r ) = $this->Action->getParams();

                //セッションからログインアカウントを取得
                $manager_session = $_SESSION['managerUserId'];
                $conditions = array();
                $conditions['mail_address'] = $manager_session;
                $conditions['del_flg'] = 0;

                $get_manager = $this->Action->createService('ManagerService');
                $manager_data = $get_manager->getManagerFromHash($conditions);

                $r['manager'] = $manager_data;
                $this->Action->rewriteParams($p, $g, $s, $c, $f, $e, $sv, $r);
                return;
            } else {
                return 'redirect: /account/';
            }
        }
    }
}