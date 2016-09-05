<?php
AAFW::import('jp.aainc.classes.validator.FileValidator');

class MultipleFileValidator extends FileValidator {
    private $file_types;

    public function __construct($file, $file_types) {
        $this->file = $file;
        $this->file_types = $file_types;
        $this->setError(self::ERROR_NO_ERROR);
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @return bool
     */
    protected function isValidExtension() {
        $this->extension = substr($this->name, strrpos($this->name, '.') + 1);

        if (!$this->extension) {
            $this->setError(self::ERROR_FILE_EXTENSION_IS_INVALID);
            return false;
        }

        $this->extension = strtolower($this->extension);

        foreach ($this->file_types as $file_type) {
            if (in_array($this->extension, self::$file_type_extension_dict[$file_type])) {
                $this->file_type = $file_type;
                return true;
            }
        }

        $this->setError(self::ERROR_FILE_EXTENSION_IS_INVALID);
        return false;
    }
}