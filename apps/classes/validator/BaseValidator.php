<?php

abstract class BaseValidator {

    protected $errors;
    protected $validator;

    public function __construct() {
        $this->errors = array();
        $this->validator = new aafwValidatorBase();
    }

    public abstract function validate();

    public function isValid() {
        if (count($this->errors) === 0) {
            return true;
        }
        return false;
    }

    public function setErrors($errors) {
        $this->errors = $errors;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function setValidator($validator) {
        $this->validator = $validator;
    }

    public function getValidator() {
        return $this->validator;
    }
}