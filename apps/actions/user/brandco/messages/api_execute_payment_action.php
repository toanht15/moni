<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');

class api_execute_payment_action extends ExecuteActionBase{

    protected $ContainerName = "api_execute_payment_action";
    protected $AllowContent = array('JSON','PHP');

    function validate() {
        return true;
    }

    function doAction() {
        $result = parent::doAction();
        //APIから呼び出しの時は通常通り、json返して、普通のPOSTアクションで来た場合(購入後に来る)場合はキャンペーンスレッドページにリダイレクトしてあげる
        if($this->POST['is_api_call']){
            return $result;
        }else{
            $cp_user_service = $this->createService('CpUserService');
            $cp_user = $cp_user_service->getCpUserById($this->cp_user_id);
            return "redirect :".Util::rewriteUrl("messages", "thread",array($cp_user->cp_id));
        }
    }


    function saveData() {

    }
} 
