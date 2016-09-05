<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');

class DailyBatchRunner extends BrandcoBatchBase {
    private $clazz = null;

    public function __construct($classPath, $argv)
    {
        parent::__construct($argv);
        list($clazz) = AAFW::import($classPath);
        $this->clazz = $clazz;
    }

    public function getClazz()
    {
        return $this->clazz;
    }

    public function setClazz($clazz)
    {
        $this->clazz = $clazz;
    }

    public function executeProcess()
    {
        $errorMessage = "Invalid argument: The '%s' does not match the format Y-m-d.";

        $isDateSpecified = isset($this->argv['date']);
        if (isset($this->argv['date']) && !$this->isDate($this->argv['date'])) {
            echo sprintf($errorMessage, 'date');
            return;
        }

        if (isset($this->argv['since']) && !$this->isDate($this->argv['since'])) {
            echo sprintf($errorMessage, 'since');
            return;
        }

        if (isset($this->argv['until']) && !$this->isDate($this->argv['until'])) {
            echo sprintf($errorMessage, 'until');
            return;
        }

        $isRangeSpecified = $this->argv['since'] && $this->argv['until'];
        if ($isRangeSpecified &&
            strtotime($this->argv['since']) > strtotime($this->argv['until'])) {
            echo "Invalid argument: The 'since' must be a date before 'until'.";
            return;
        }

        // デフォルトは昨日分のみ
        $dateArray = [
            date('Y-m-d', strtotime('-1 day'))
        ];

        if ($isDateSpecified) {

            $dateArray = [
                date("Y-m-d", strtotime($this->argv['date']))
            ];
        } elseif($isRangeSpecified) {

            $dateArray = [];
            $since = strtotime($this->argv['since']);
            $until = strtotime($this->argv['until']);
            while ($since <= $until) {
                $date = date('Y-m-d', $since);
                $dateArray[] = $date;
                $since = strtotime("+1 day", $since);
            }
        }

        $clazz = $this->clazz;
        foreach($dateArray as $date) {
            $object = new $clazz();
            $object->doProcess($date);
        }

    }

    private function isDate($dateStr) {
        return $dateStr === date("Y-m-d", strtotime($dateStr));
    }

}