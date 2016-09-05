<?php
/**
 * ResultOnlyResponse.php@api
 * User: ishidatakeshi
 * Date: 2014/09/12
 * Time: 12:30
 */

namespace Merazoma;


use curely\core\response\JSON;

class ResultOnlyResponse extends JSON {
    private $result = null;
    const OK = 'OK';
    const NG = 'NG';

    public function __construct ($result) {
        $this->setResult($result);
    }
    /**
     * @param null $result
     * @throws \InvalidArgumentException
     */
    public function setResult($result)
    {
        if ($result !== self::OK && $result !== self::NG)
            throw new \InvalidArgumentException('OK or NG');
        $this->result = $result;
    }

    /**
     * @return null
     */
    public function getResult()
    {
        return $this->result;
    }
}