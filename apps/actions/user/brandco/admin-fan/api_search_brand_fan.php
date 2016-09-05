<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.services.CpCreateSqlService');
AAFW::import('jp.aainc.classes.validator.SearchProfileValidator');
AAFW::import('jp.aainc.actions.user.brandco.admin-fan.SearchFanTrait');

class api_search_brand_fan extends BrandcoPOSTActionBase
{
    protected $ContainerName = 'show_brand_user_list';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $search_condition = array();

    private $search_session_key;

    use SearchFanTrait;

    public function beforeValidate() {

        if ($this->isFacebookMarketing) {
            //FB marketingの場合
            $this->search_session_key = "searchConditionFacebookMarketing";
        } else {
            $this->search_session_key = "searchBrandCondition";
        }

        if($this->search_no) {

            // アンケート等のキーは、[サーチタイプ/ID]で構成されているので、サーチタイプだけを取り出す。
            $this->split_search_key = explode('/', $this->POST['search_type']);

            $this->search_condition = $this->getSearchProfileCondition($this->search_type, $this->search_no, $this->POST, $this->nullable);

            if($this->split_search_key[0] <= CpCreateSqlService::SEARCH_SOCIAL_ACCOUNT_INTERACTIVE) {
                foreach($this->POST as $key => $value) {

                    if (preg_match('/^switch_type\//', $key)) {
                        if ($this->search_type == CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT) {
                            if ($key == 'switch_type/' . CpCreateSqlService::SEARCH_PROFILE_SOCIAL_ACCOUNT) {
                                $split_key = explode('/', $key);
                                // サーチ番号を除いてキーに入れる
                                $this->search_condition[$split_key[0] . '/' . $split_key[1]] = $value;
                            }
                        }
                        if (preg_match('/^' . CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE . '\//', $this->search_type)) {
                            $question_id = explode('/', $this->search_type)[1];
                            if (preg_match('/^switch_type\/' . CpCreateSqlService::SEARCH_PROFILE_QUESTIONNAIRE . '\/' . $question_id . '/', $key)) {
                                $this->search_condition[$key] = $value;
                            }
                        }
                    }
                }
            }
            if($this->split_search_key[0] == CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION) {
                foreach($this->POST as $key => $value) {
                    $action_id = explode('/', $this->search_type)[1];
                    if(preg_match('/^search_participate_condition\/'.$action_id.'\//', $key)) {
                        $split_key = explode('/', $key);
                        // サーチ番号を除いてキーに入れる
                        $this->search_condition[$split_key[0].'/'.$split_key[1].'/'.$split_key[2]] = $value;
                    }
                }
            }
        }
    }

    public function validate() {
        if($this->search_no) {
            $search_profile_validator = new SearchProfileValidator($this->search_condition, $this->search_type, $this->nullable);

            $search_profile_validator->validate();
            if(!$search_profile_validator->isValid()) {
                $errors = $search_profile_validator->getErrors();
                $json_data = $this->createAjaxResponse("ng", array(), $errors);
                $this->assign('json_data', $json_data);
                return false;
            }
        }
        return true;
    }

    function doAction() {
        //TODO ハードコーディング: カンコーブランドの追加カラムの絞り込み
        if($this->search_type == CpCreateSqlService::SEARCH_CHILD_BIRTH_PERIOD) {
            $this->search_condition = $this->convertChildBirthPeriodToSearchCondition($this->search_condition, $this->search_type);
        }

        if($this->search_no) {
            if ($this->search_condition && count($this->search_condition) > 0) {
                $session = $this->getBrandSession($this->search_session_key);
                $session[$this->search_type] = $this->search_condition;
                $this->setBrandSession($this->search_session_key, $session);
            } else {
                $session = $this->getBrandSession($this->search_session_key);
                unset($session[$this->search_type]);
                $this->setBrandSession($this->search_session_key, $session);
            }
        } elseif($this->order) {
            $this->setBrandSession('orderBrandCondition', null);
            $this->setBrandSession('orderBrandCondition', array($this->search_type => intval($this->order)));
        } else {
            $session = $this->getBrandSession($this->search_session_key);
            $session = $this->resetSnsActionSearchCondition($session, $this->POST['search_type'], $this->POST['sns_action_key']);
            $this->setBrandSession($this->search_session_key, $session);
        }

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }
}