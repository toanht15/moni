<?php

class StaticEntryValidator extends BrandcoValidatorBase {

    public function isOwner($entryId) {
        $entry = $this->service->getEntryById($entryId);
        if (!$entry) {
            return false;
        }
        if ($entry->brand_id != $this->brandId) {
            return false;
        }
        return true;
    }

    public function isCorrectEntryId($entryId) {
        if (!$this->isNumeric($entryId)) {
            return false;
        }
        if ($this->isEmpty($entryId)) {
            return false;
        }
        if (!$this->isOwner($entryId)) {
            return false;
        }
        return true;
    }
} 