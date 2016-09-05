<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class update_code_auth extends BrandcoPOSTActionBase {
    protected $ContainerName = 'update_code_auth';
    protected $Form = array(
        'package' => 'admin-code-auth',
        'action' => 'edit_code_auth_codes/{code_auth_id}?p={page}',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $code_auth_service;
    protected $code_auth_codes;

    protected $ValidatorDefinition = array(
        'name' => array(
            'required' => true,
            'type' => 'str',
            'length' => 255
        ),
        'page' => array(
            'required' => true,
            'type' => 'num',
            'range' => array(
                '>' => 0,
            )
        ),
        'code_auth_id' => array(
            'required' => true,
            'type' => 'num',
            'range' => array(
                '>' => 0,
            )
        ),
        'limit' => array(
            'required' => true,
            'type' => 'num',
            'range' => array(
                '>' => 0,
            )
        )
    );

    public function validate() {
        $code_auth_validator = new CodeAuthValidator($this->POST['code_auth_id'], $this->getBrand()->id);

        if (!$code_auth_validator->isValidCodeAuthId()) {
            return '404';
        }

        $this->code_auth_service = $this->createService('CodeAuthenticationService');
        $order = array(
            'name' => 'id',
            'direction' => "asc"
        );

        $this->code_auth_codes = $this->code_auth_service->getCodesByCodeAuthId($this->POST['code_auth_id'], $this->POST['page'], $this->POST['limit'], $order);

        foreach ($this->code_auth_codes as $code_auth_code) {
            $max_plus = $this->POST['max_num_plus/' . $code_auth_code->id];

            if ($max_plus && (!$this->isNumeric($max_plus) || $max_plus < 0 || ($code_auth_code->max_num + $max_plus)  >= CodeAuthenticationCode::MAX_NUM_LIMIT)) {
                $this->Validator->setError('max_num_plus/' . $code_auth_code->id, 'INVALID_LIMIT');
            }

            if (!$this->POST['expire_date/' . $code_auth_code->id]) {
                if (!$this->POST['non_expire_date/' . $code_auth_code->id]) {
                    $this->Validator->setError('expire_date/' . $code_auth_code->id, 'NOT_REQUIRED');
                }
            } else {
                $expire_date = strtotime($this->POST['expire_date/' . $code_auth_code->id]);
                if (!$expire_date || ($expire_date < strtotime('today'))) {
                    $this->Validator->setError('expire_date/' . $code_auth_code->id, 'INVALID_DATE');
                }
            }
        }

        return $this->Validator->isValid();
    }

    function doAction() {

        try{
            $this->code_auth_service->code_auths->begin();

            $code_auth = $this->code_auth_service->getCodeAuthById($this->POST['code_auth_id']);
            $code_auth->name = $this->POST['name'];
            $code_auth->description = $this->POST['description'];
            $this->code_auth_service->updateCodeAuth($code_auth);

            foreach ($this->code_auth_codes as $code_auth_code) {
                $code_auth_code->expire_date = $this->POST['non_expire_date/' . $code_auth_code->id] ? '0000-00-00 00:00:00' : date_create($this->POST['expire_date/' . $code_auth_code->id])->format('Y-m-d H:i:s');
                $code_auth_code->max_num += $this->POST['max_num_plus/' . $code_auth_code->id] ? $this->POST['max_num_plus/' . $code_auth_code->id] : 0;

                $this->code_auth_service->updateCodeAuthCode($code_auth_code);
            }

            $this->code_auth_service->code_auths->commit();

            $mid = 'updated';
            $this->Data['saved'] = 1;
        } catch (Exception $e) {
            $this->code_auth_service->code_auths->rollback();
            $mid = 'failed';

            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error($e);
        }

        return 'redirect: '. Util::rewriteUrl('admin-code-auth', 'code_auth_codes', array($this->POST['code_auth_id']), array('mid' => $mid, 'p' => $this->POST['page']));
    }
}
