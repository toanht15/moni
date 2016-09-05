<?php

/**
 * Interface CSVExtractor
 */
interface CSVExtractor {

    /**
     * @param array $filter
     * @return array|generator csvData
     */
    public function getCsvData($filter);
}