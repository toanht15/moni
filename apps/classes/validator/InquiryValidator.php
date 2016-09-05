<?php
AAFW::import('jp.aainc.classes.services.InquiryService');
AAFW::import('jp.aainc.classes.services.InquiryBrandService');

class InquiryValidator extends aafwObject {

    const VALID_TEXT = 1;
    const VALID_MAIL_ADDRESS = 2;
    const VALID_CHOICE = 3;
    const VALID_SECTION = 4;

    const SERVICE_TYPE_INQUIRY_BRAND = 2;
    const SERVICE_TYPE_INQUIRY = 1;

    const ENTITY_TYPE_INQUIRY_BRAND = 1;
    const ENTITY_TYPE_INQUIRY_ROOM = 2;
    const ENTITY_TYPE_INQUIRY = 3;
    const ENTITY_TYPE_INQUIRY_USER = 4;
    const ENTITY_TYPE_INQUIRY_MESSAGE = 5;
    const ENTITY_TYPE_INQUIRY_ROOMS_MESSAGES_RELATION = 6;
    const ENTITY_TYPE_INQUIRY_SECTION = 7;
    const ENTITY_TYPE_INQUIRY_TEMPLATE_CATEGORY = 8;
    const ENTITY_TYPE_INQUIRY_TEMPLATE = 9;
    const ENTITY_TYPE_INQUIRY_BRAND_RECEIVER = 10;

    /** @var aafwLog4phpLogger $logger */
    protected $logger;
    /** @var aafwLog4phpLogger $hipchat_logger */
    protected $hipchat_logger;
    /** @var aafwErrorMessages $messages */
    protected $messages;

    private $errors = array();
    private $services = array();
    private $entity_caches = array();
    private $entity_info = array(
        self::ENTITY_TYPE_INQUIRY_BRAND => array(
            'service_type' => self::SERVICE_TYPE_INQUIRY_BRAND,
            'model_type' => InquiryBrandService::MODEL_TYPE_INQUIRY_BRAND
        ),
        self::ENTITY_TYPE_INQUIRY_ROOM => array(
            'service_type' => self::SERVICE_TYPE_INQUIRY,
            'model_type' => InquiryService::MODEL_TYPE_INQUIRY_ROOMS
        ),
        self::ENTITY_TYPE_INQUIRY => array(
            'service_type' => self::SERVICE_TYPE_INQUIRY,
            'model_type' => InquiryService::MODEL_TYPE_INQUIRIES
        ),
        self::ENTITY_TYPE_INQUIRY_USER => array(
            'service_type' => self::SERVICE_TYPE_INQUIRY,
            'model_type' => InquiryService::MODEL_TYPE_INQUIRY_USERS
        ),
        self::ENTITY_TYPE_INQUIRY_MESSAGE => array(
            'service_type' => self::SERVICE_TYPE_INQUIRY,
            'model_type' => InquiryService::MODEL_TYPE_INQUIRY_MESSAGES
        ),
        self::ENTITY_TYPE_INQUIRY_ROOMS_MESSAGES_RELATION => array(
            'service_type' => self::SERVICE_TYPE_INQUIRY,
            'model_type' => InquiryService::MODEL_TYPE_INQUIRY_ROOMS_MESSAGES_RELATIONS
        ),
        self::ENTITY_TYPE_INQUIRY_SECTION => array(
            'service_type' => self::SERVICE_TYPE_INQUIRY_BRAND,
            'model_type' => InquiryBrandService::MODEL_TYPE_INQUIRY_SECTIONS
        ),
        self::ENTITY_TYPE_INQUIRY_TEMPLATE_CATEGORY => array(
            'service_type' => self::SERVICE_TYPE_INQUIRY_BRAND,
            'model_type' => InquiryBrandService::MODEL_TYPE_INQUIRY_TEMPLATE_CATEGORIES
        ),
        self::ENTITY_TYPE_INQUIRY_TEMPLATE => array(
            'service_type' => self::SERVICE_TYPE_INQUIRY_BRAND,
            'model_type' => InquiryBrandService::MODEL_TYPE_INQUIRY_TEMPLATES
        ),
        self::ENTITY_TYPE_INQUIRY_BRAND_RECEIVER => array(
            'service_type' => self::SERVICE_TYPE_INQUIRY_BRAND,
            'model_type' => InquiryBrandService::MODEL_TYPE_INQUIRY_BRAND_RECEIVERS
        ),
    );

    public function __construct() {
        $this->services = array(
            self::SERVICE_TYPE_INQUIRY_BRAND => $this->getService('InquiryBrandService'),
            self::SERVICE_TYPE_INQUIRY => $this->getService('InquiryService'),
        );

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
        foreach ($definitions as $definition) {
            if (!$definition['name']) {
                return false;
            }

            $val = $params[$definition['name']];
            switch ($definition['type']) {
                case self::VALID_TEXT:
                    $this->isValidText($definition['name'], $val, $definition['expected'], $definition['required']);
                    break;
                case self::VALID_MAIL_ADDRESS:
                    $this->isValidMailAddress($definition['name'], $val, $definition['expected'], $definition['required']);
                    break;
                case self::VALID_CHOICE:
                    $this->isValidChoice($definition['name'], $val, $definition['expected'], $definition['required']);
                    break;
                case self::VALID_SECTION:
                    $this->isValidSection($definition['name'], $val, $definition['expected'], $definition['required']);
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
        if ($required && strlen($val) === 0) {
            $this->setError($name, 'NOT_INPUT_TEXT');

            return false;
        } else {
            if (!is_int($length)) {
                $this->setError($name, 'ERROR_OTHER_ERROR');
                $this->logger->error('InquiryValidator#isValidText $length is not integer');
                $this->hipchat_logger->error('InquiryValidator#isValidText $length is not integer');

                return false;
            } else if (!is_string($val)) {
                $this->setError($name, 'INPUT_STRING');

                return false;
            } else if (mb_strlen($val, 'utf-8') > $length) {
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
        if ($required && strlen($val) === 0) {
            $this->setError($name, 'NOT_INPUT_TEXT');

            return false;
        } else {
            if (!is_int($length)) {
                $this->setError($name, 'ERROR_OTHER_ERROR');
                $this->logger->error('InquiryValidator#isValidMailAddress $length is not integer');
                $this->hipchat_logger->error('InquiryValidator#isValidMailAddress $length is not integer');

                return false;
            } else if (!is_string($val)) {
                $this->setError($name, 'INPUT_STRING');

                return false;
            } else if (mb_strlen($val, 'utf-8') > $length) {
                $this->setError($name, 'INPUT_WITHIN_' . $length);

                return false;
            } else if (!$this->isMailAddress($val)) {
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
        if ($required && strlen($val) === 0) {
            $this->setError($name, 'NOT_CHOOSE');

            return false;
        } else {
            if (!is_array($expected)) {
                $this->setError($name, 'ERROR_OTHER_ERROR');
                $this->logger->error('InquiryValidator#isValidChoice $expected is not array');
                $this->hipchat_logger->error('InquiryValidator#isValidChoice $expected is not array');

                return false;
            } else if (!in_array($val, $expected)) {
                $this->setError($name, 'NOT_CHOOSE');

                return false;
            }
        }

        return true;
    }

    /**
     * @param $name
     * @param $val
     * @param $expected
     * @param $required
     * @return bool
     */
    public function isValidSection($name, $val, $expected, $required) {
        if ($required && strlen($val) === 0) {
            $this->setError($name, 'NOT_CHOOSE');

            return false;
        } else if (!$required && $val == 0) {
        } else {
            if (!in_array($expected, InquirySection::$levels)) {
                $this->setError($name, 'ERROR_OTHER_ERROR');
                $this->logger->error('InquiryValidator#isValidSection $expected is not level');
                $this->hipchat_logger->error('InquiryValidator#isValidSection $expected is not level');

                return false;
            } else if (!$this->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_SECTION, array('id' => $val))) {
                $this->setError($name, 'INVALID_CHOICE');

                return false;
            }
        }

        return true;
    }

    /***********************************************************
     * isExisted
     ***********************************************************/
    /**
     * @param $entity_type
     * @param $params
     * @return bool
     */
    public function isExistedRecord($entity_type, $params) {
        $entity_info = $this->entity_info[$entity_type];
        if ($this->hasCache($entity_type, $params)) {
            return true;
        }

        if ($entity = $this->services[$entity_info['service_type']]->getRecord($entity_info['model_type'], $params)) {
            $this->setEntityCache($entity_type, $entity);

            return true;
        }

        return false;
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

    /**
     * @param $entity_type
     * @param $entity
     */
    public function setEntityCache($entity_type, $entity) {
        $this->entity_caches[$entity_type] = $entity;
    }

    /**
     * @param $entity_type
     * @return null
     */
    public function getEntityCache($entity_type) {
        return $this->entity_caches[$entity_type] ?: null;
    }

    /**
     * @param $entity_type
     * @param $params
     * @return bool
     */
    public function hasCache($entity_type, $params) {
        if ($entity = $this->getEntityCache($entity_type)) {
            $checked = true;
            foreach ($params as $key => $value) {
                $checked = $checked && ($entity->$key == $value);
            }

            if ($checked) {
                return true;
            }
        }

        return false;
    }
}
