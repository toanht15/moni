<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwActionPluginBase' );

class CheckLogin extends aafwActionPluginBase {
	protected $HookPoint = 'First';
	protected $Priority  = 2; // SetFactoriesのあと

	public function doService(){

        if (method_exists($this->Action, 'isLogin') //ログインメソードが存在している
            && !$this->Action->isLogin()            //ログインしていない
            && !$this->Action->isLoginPage          //ログイン系のページじゃない
            && method_exists($this->Action, 'getAllowContent')
            && (count(array_intersect(array('JSON', 'JS'), $this->Action->getAllowContent())) == 0) //ページじゃない
        ) {
            if ($this->Action->NeedRedirect) {
                $this->Action->setSession('loginRedirectUrl', Util::getCurrentUrl());

            } else {
                $this->Action->setSession('loginRedirectUrl', null);
            }
        }

        if ($this->Action->NeedAdminLogin) {
            if (!$this->Action->isLoginAdmin()) {
                return 403;
            }
        }

        if ($this->Action->NeedUserLogin) {

            if (!$this->Action->isLogin()) {

                if($this->isRedirectEmbedLogin()){
                    $pageUrl = $this->Action->getPageUrl();
                    return 'redirect: ' . Util::rewriteUrl('my', 'embed_login',array(),array('page_url' => $pageUrl),'',true);
                }

                //ログインしたら元の画面に戻す
                return 'redirect: ' . Util::rewriteUrl ('my', 'login');
            }
        }
	}

    private function isRedirectEmbedLogin(){

        if(method_exists($this->Action, 'isEmbedPage') && $this->Action->isEmbedPage() && $this->Action->canAddEmbedPage()){
           return true;
        }

        return false;
    }
}