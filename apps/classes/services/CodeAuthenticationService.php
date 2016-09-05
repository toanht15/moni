<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CodeAuthenticationService extends aafwServiceBase {

    public $code_auths;
    private $code_auth_codes;

    private $logger;

    public function __construct() {
        $this->code_auths = $this->getModel('CodeAuthentications');
        $this->code_auth_codes = $this->getModel('CodeAuthenticationCodes');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    // Code Authentications

    /**
     * @return mixed
     */
    public function createEmptyCodeAuth() {
        return $this->code_auths->createEmptyObject();
    }

    /**
     * @param $code_auth
     */
    public function createCodeAuth($code_auth) {
        $this->code_auths->save($code_auth);
    }

    /**
     * @param $code_auth
     */
    public function updateCodeAuth($code_auth) {
        $this->code_auths->save($code_auth);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getCodeAuthById($id) {
        return $this->code_auths->findOne($id);
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function countCodeAuthsByBrandId($brand_id) {
        $filter = array(
            'brand_id' => $brand_id
        );

        return $this->code_auths->count($filter);
    }

    /**
     * @param $brand_id
     * @param $page
     * @param $page_limit
     * @param $order
     * @return mixed
     */
    public function getCodeAuthsByBrandId($brand_id, $page, $page_limit, $order) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id
            ),
            'order' => $order,
            'pager' => array(
                'page' => $page,
                'count' => $page_limit
            )
        );

        return $this->code_auths->find($filter);
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function getAllCodeAuthsByBrandId($brand_id) {
        $filter = array(
            'brand_id' => $brand_id
        );

        return $this->code_auths->find($filter);
    }

    /**
     * @param $code_auth_id
     * @return array
     */
    public function getCodeAuthStatisticByCodeAuthId($code_auth_id) {
        $total = $this->getSumOfCodeAuthCode($code_auth_id, 'max_num');
        $reserved_num = $this->getSumOfCodeAuthCode($code_auth_id, 'reserved_num');

        return array($reserved_num, $total);
    }

    /**
     * @param $code_auth_id
     * @param $column_name
     * @return mixed
     */
    public function getSumOfCodeAuthCode($code_auth_id, $column_name) {
        $filter = array(
            'code_auth_id' => $code_auth_id
        );

        return $this->code_auth_codes->getSum($column_name, $filter);
    }

    // Code Authentication Codes

    /**
     * @return mixed
     */
    public function createEmptyCodeAuthCode() {
        return $this->code_auth_codes->createEmptyObject();
    }

    /**
     * @param $code_auth_code
     */
    public function createCodeAuthCode($code_auth_code) {
        $this->code_auth_codes->save($code_auth_code);
    }

    /**
     * @param $code_auth_code
     */
    public function updateCodeAuthCode($code_auth_code) {
        $this->code_auth_codes->save($code_auth_code);
    }

    /**
     * @param $code_auth_id
     * @param $code_array
     */
    public function createCodeAuthCodes($code_auth_id, $code_array) {
        if (count($code_array) === 0) return;

        try {
            $this->code_auth_codes->begin();

            foreach ($code_array as $code) {
                if (!trim($code)) continue;

                $pattern = explode(",", trim($code));

                $code_auth_code = $this->createEmptyCodeAuthCode();
                $code_auth_code->code_auth_id = $code_auth_id;
                $code_auth_code->code = $pattern[0];
                $code_auth_code->expire_date = !$pattern[2] ? '0000-00-00 00:00:00' : date_create($pattern[2])->format('Y-m-d H:i:s');
                $code_auth_code->max_num = !$pattern[1]  ? 1 : $pattern[1];
                $this->createCodeAuthCode($code_auth_code);
            }

            $this->code_auth_codes->commit();
        } catch (Exception $e) {
            $this->code_auth_codes->rollback();
            $this->logger->error('Cant create code authentication codes code_auth_id = ' . $code_auth_id);
            $this->logger->error($e);
        }
    }

    /**
     * @param $code_auth_id
     * @return mixed
     */
    public function countCodesByCodeAuthId($code_auth_id) {
        $filter = array(
            'code_auth_id' => $code_auth_id
        );

        return $this->code_auth_codes->count($filter);
    }

    /**
     * @param $code_auth_id
     * @param $page
     * @param $page_limit
     * @param $order
     * @return mixed
     */
    public function getCodesByCodeAuthId($code_auth_id, $page, $page_limit, $order) {
        $filter = array(
            'conditions' => array(
                'code_auth_id' => $code_auth_id
            ),
            'order' => $order,
            'pager' => array(
                'page' => $page,
                'count' => $page_limit
            )
        );

        return $this->code_auth_codes->find($filter);
    }

    /**
     * @param $code_auth_id
     */
    public function getAllCodesByCodeAuthId($code_auth_id) {
        $filter = array(
            'code_auth_id' => $code_auth_id
        );

        return $this->code_auth_codes->find($filter);
    }

    /**
     * @param $code
     * @param $code_auth_id
     * @return mixed
     */
    public function getCodeAuthCodeByCodeAndCodeAuthId($code, $code_auth_id) {
        $filter = array(
            'code' => $code,
            'code_auth_id' => $code_auth_id
        );

        return $this->code_auth_codes->find($filter);
    }

    // Common function

    /**
     * @param $code_auth_id
     */
    public function deleteCodeAuthAndCodeAuthCodes($code_auth_id) {
        try {
            $this->code_auth_codes->begin();

            $code_auth_codes = $this->getAllCodesByCodeAuthId($code_auth_id);
            foreach ($code_auth_codes as $code_auth_code) {
                $this->code_auth_codes->delete($code_auth_code);
            }

            $code_auth = $this->getCodeAuthById($code_auth_id);
            $this->code_auths->delete($code_auth);

            $this->code_auth_codes->commit();
        } catch (Exception $e) {
            $this->code_auth_codes->rollback();
            $this->logger->error('CodeAuthenticationService@deleteCodeAuthAndCodeAuthCodes Error ' . $e);
        }
    }
}