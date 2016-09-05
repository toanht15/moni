<?php
class signup_redirect extends aafwGETActionBase{

    function validate() {
        return true;
    }

    function doAction() {
        $this->Data['cp_id'] = $this->GET['cp_id'];
        $this->Data['beginner_flg'] = $this->GET['beginner_flg'];
        return "user/brandco/auth/signup_redirect.php";
    }
}