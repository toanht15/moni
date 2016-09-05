<?php

/**
 * Class Hash
 */
class Hash {
    /**
     * @param        $data
     * @param        $salt
     * @param int    $loopCount
     * @param string $algo
     *
     * @return string
     * @throws \Exception
     */
    public static function doHash($data, $salt, $loopCount = 1, $algo = 'sha256') {
        if (!$data || !$salt || !$loopCount || !$algo) {
            throw new \Exception(__FUNCTION__ ."引数が不正です");
        }
        $res = $data . $salt;

        for ($i = 1; $i <= $loopCount; $i++) {
            $res = hash($algo, $res);
        }
        return $res;
    }

    /**
     * criteoのドキュメントに則ったemailのバリデートとmd5ハッシュを行います
     * @param $email
     * @return string
     */
    public static function doHashMd5Email($email){
        $cleanEmail = strtolower(trim(utf8_encode($email)));
        if (filter_var($cleanEmail, FILTER_VALIDATE_EMAIL)) {
            return md5($cleanEmail);
        }
        return "";
    }
}
