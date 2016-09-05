<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');

class FixCodeAuthenticationCodes extends BrandcoBatchBase {

    function executeProcess() {
        if($this->argv == null || count($this->argv) != 1 || Util::isNullOrEmpty($this->argv['code_auth_id'])){
            echo "code_auth_idを入力してください！ \n";
            return;
        }

        $code_auth_id = $this->argv['code_auth_id'];
        /** @var CodeAuthenticationService $code_authentication_service */
        $code_authentication_service = $this->service_factory->create("CodeAuthenticationService");
        $code_auth_codes = $code_authentication_service = $code_authentication_service->getAllCodesByCodeAuthId($code_auth_id);

        $code_auth_codes_store = aafwEntityStoreFactory::create("CodeAuthenticationCodes");

        try {
            $code_auth_codes_store->begin();
            foreach ($code_auth_codes as $code_auth_code) {
                if (strlen($code_auth_code->code) < 8) {
                    $code_auth_codes_store->delete($code_auth_code);
                }
            }
            $code_auth_codes_store->commit();

        } catch (Exception $e) {
            $code_auth_codes_store->rollback();
            $this->logger->error('FixCodeAuthenticationCodes@executeProcess Error ' . $e);
        }
    }

}