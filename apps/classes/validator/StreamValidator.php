<?php

class StreamValidator extends BrandcoValidatorBase {

    public function isOwner($streamId) {
        if (!$this->isNumeric($streamId)) {
            return false;
        }
        if ($this->isEmpty($streamId)) {
            return false;
        }
        $stream = $this->service->getStreamById($streamId);
        if (!$stream) {
            return false;
        }
        if ($stream->brand_id != $this->brandId) {
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
        $entry = $this->service->getEntryById($entryId);
        if (!$entry) {
            return false;
        }
        if (!$this->isOwner($entry->stream_id)) {
            return false;
        }
        return true;
    }

} 