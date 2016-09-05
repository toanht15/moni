<?php
AAFW::import ('jp.aainc.aafw.base.aafwObject');

class ManagerUsersValidator extends aafwObject {

    const VALID_TEXT = 1;
    const VALID_MAIL_ADDRESS = 2;
    const VALID_CHOICE = 3;
    const VALID_NUMBER = 4;

    /** @var aafwLog4phpLogger $logger */
    protected $logger;
    /** @var aafwLog4phpLogger $hipchat_logger */
    protected $hipchat_logger;
    /** @var aafwErrorMessages $messages */
    protected $messages;

    protected $errors = array();

    public function __construct() {
        $this->messages = aafwErrorMessages::getInstance();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipchatLogger();
    }

    /***********************************************************
     * isValid
     ***********************************************************/
    /**
     * @param $params
     * @param array $definitions
     * @return bool
     */
    public function isValid($params, $definitions = array()) {
        foreach ($definitions as $name => $definition) {
            $val = $params[$name];
            switch ($definition['type']) {
                case self::VALID_TEXT:
                    $this->isValidText($name, $val, $definition['expected'], $definition['required']);
                    break;
                case self::VALID_MAIL_ADDRESS:
                    $this->isValidMailAddress($name, $val, $definition['expected'], $definition['required']);
                    break;
                case self::VALID_CHOICE:
                    $this->isValidChoice($name, $val, $definition['expected'], $definition['required']);
                    break;
                case self::VALID_NUMBER:
                    $this->isValidNumber($name, $val, $definition['expected'], $definition['required']);
                    break;
            }
        }

        return !count($this->getErrors());
    }

    /**
     * @param $name
     * @param $val
     * @param $length
     * @param $required
     * @return bool
     */
    public function isValidText($name, $val, $length, $required) {
        if ($required) {
            if (strlen($val) === 0) {
                $this->setError($name, 'NOT_INPUT_TEXT');

                return false;
            } elseif (!is_string($val)) {
                $this->setError($name, 'INPUT_STRING');

                return false;
            } elseif (mb_strlen($val, 'utf-8') > $length) {
                $this->setError($name, 'INPUT_WITHIN_' . $length);

                return false;
            }
        }

        return true;
    }

    /**
     * @param $name
     * @param $val
     * @param $length
     * @param $required
     * @return bool
     */
    public function isValidMailAddress($name, $val, $length, $required) {
        if ($required) {
            if (strlen($val) === 0) {
                $this->setError($name, 'NOT_INPUT_TEXT');

                return false;
            } elseif (!is_string($val)) {
                $this->setError($name, 'INPUT_STRING');

                return false;
            } elseif (mb_strlen($val, 'utf-8') > $length) {
                $this->setError($name, 'INPUT_WITHIN_' . $length);

                return false;
            } elseif (!$this->isMailAddress($val)) {
                $this->setError($name, 'NOT_MATCH_TYPE');

                return false;
            }
        }

        return true;
    }

    /**
     * @param $name
     * @param $val
     * @param $expected
     * @param bool|true $required
     * @return bool
     */
    public function isValidChoice($name, $val, $expected, $required) {
        if ($required) {
            if (strlen($val) === 0) {
                $this->setError($name, 'NOT_CHOOSE');

                return false;
            } elseif (!in_array($val, $expected)) {
                $this->setError($name, 'NOT_CHOOSE');

                return false;
            }
        }

        return true;
    }

    public function isValidNumber($name, $val, $expected, $required) {
        if ($required) {
            if (strlen($val) === 0) {
                $this->setError($name, 'NOT_CHOOSE');

                return false;
            } elseif ($val > $expected) {
                $this->setError($name, 'INPUT_LESS_THAN');

                return false;
            }
        }
    }

    /***********************************************************
     * setter, getter
     ***********************************************************/
    /**
     * @param $key
     * @param $val
     */
    public function setError($key, $val) {
        $this->errors[$key] = $val;
    }

    /**
     * @return mixed
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * @param $error
     * @return mixed
     */
    public function getErrorMessage($error) {
        return $this->messages->getErrorMessage($error);
    }

    /**
     * @param $key
     * @return null
     */
    public function getError($key) {
        return $this->errors[$key] ?: null;
    }

    /**
     * @return array
     */
    public function getErrorMessages() {
        $error_messages = array();
        foreach ($this->errors as $key => $value) {
            $error_messages[$key] = $this->getErrorMessage($value);
        }

        return $error_messages;
    }
}