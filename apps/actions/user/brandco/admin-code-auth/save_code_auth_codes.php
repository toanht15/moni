<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class save_code_auth_codes extends BrandcoPOSTActionBase {
    protected $ContainerName = 'update_code_auth';
    protected $Form = array(
        'package' => 'admin-code-auth',
        'action' => 'edit_code_auth_codes/{code_auth_id}?mid=failed',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $ValidatorDefinition = array(
        'code_auth_codes' => array(
            'type' => 'str'
        ),
        'csv_file' => array(
            'type' => 'file'
        )
    );

    public function doThisFirst() {
        set_time_limit(1800);
        if (!$this->code_auth_codes && !$this->FILES['csv_file']) {
            $this->ValidatorDefinition['code_auth_codes']['required'] = true;
        }

    }

    public function validate() {
        $code_auth_validator = new CodeAuthValidator($this->POST['code_auth_id'], $this->getBrand()->id);

        if (!$code_auth_validator->isValidCodeAuthId()) {
            return '404';
        }

        $code_auth_validator->validate($this->POST['code_auth_codes']);
        if (!$code_auth_validator->isValid()) {
            $this->Validator->setError('code_auth_codes', $code_auth_validator->getErrors()[0]);
        }

        // csvファイルチェック
        if ($this->FILES['csv_file']) {
            $fileValidator = new FileValidator($this->FILES['csv_file'], FileValidator::FILE_TYPE_CSV);
            if (!$fileValidator->isValidFile()) {
                $this->Validator->setError('csv_file', 'NOT_MATCHES');
            } else {
                $code_auth_validator->validate(null, $this->FILES['csv_file']['name']);
                if (!$code_auth_validator->isValid()) {
                    $this->Validator->setError('csv_code_auth_codes', $code_auth_validator->getErrors()[0]);
                }
            }
        }

        return $this->Validator->isValid();
    }

    function doAction() {
        $code_auth_service = $this->createService('CodeAuthenticationService');

        if (!$this->isEmpty($this->POST['code_auth_codes'])) {
            $code_auth_service->createCodeAuthCodes($this->POST['code_auth_id'], Util::cutStringByLineBreak($this->POST['code_auth_codes']));
        }

        if ($this->FILES['csv_file']) {
            $data = '';
            try {
                $f = fopen($this->FILES['csv_file']['name'], 'rb');
                while (!feof($f)) {
                    $data .= fread($f, filesize($this->FILES['csv_file']['name']));
                }
                fclose($f);

                $data = Util::convertEncoding($data);
                $code_array = Util::cutStringByLineBreak($data);
                array_shift($code_array);//1行目は項目名のため削除

                $code_auth_service->createCodeAuthCodes($this->POST['code_auth_id'], $code_array);
            } catch (Exception $e) {
                $logger = aafwLog4phpLogger::getDefaultLogger();
                $logger->error($e);
            }
        }

        $this->Data['saved'] = 1;
        return 'redirect: '. Util::rewriteUrl('admin-code-auth', 'edit_code_auth_codes', array($this->POST['code_auth_id']), array('mid' => 'updated'));
    }
}
