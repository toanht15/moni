<?php

/**
 * 類似した文字を除いたトークンを生成するクラス
 *
 * Class TokenWithoutSimilarCharGenerator
 */
class TokenWithoutSimilarCharGenerator {

    /**
     * @param $len
     *
     * @return string
     */
    public function generateToken($len) {
        // 類似文字を除いた英数字の配列
        // (除外文字: i,j,l,I,J,o,O,Z,0,1,2)
        $chars = array(
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
            'A', 'C', 'D', 'E', 'F', 'G', 'H', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y',
            '3', '4', '5', '6', '7', '9'
        );
        $charsMaxIdx = count($chars) - 1;
        mt_srand((double)microtime() * 1000000);

        $str = '';
        for ($i = 0; $i < $len; $i++) {
            // ランダムに配列の要素を参照して連結する
            $idx = mt_rand(0, $charsMaxIdx);
            $str .= $chars[$idx];
        }

        return $str;
    }
}
