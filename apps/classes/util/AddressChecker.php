<?php

/**
 * 重複住所をチェックするクラス
 *
 * Class TokenWithoutSimilarCharGenerator
 */
class AddressChecker {

    /**
     * @param  array $addresses ['user_id' => '', 'address1' => '', 'address2' => '', 'address3' => '']を要素にもつ配列
     * @return array ['住所' => [ユーザID1, ユーザID2 …]]を要素にもつ配列
     */
    public function checkDuplicate($addresses=[]) {

        $checkAddress = $this->convertToCheckArray($addresses);

        $dupliHash = array_filter($checkAddress, function($elm) {
            return count($elm) > 1;
        });

        return $dupliHash;
    }

    public function checkDuplicateWithDuplicateCountMoreThanZero($addresses) {

        $checkAddress = $this->convertToCheckArray($addresses);

        $dupliHash = array_filter($checkAddress, function($elm) {
            return count($elm) >= 1;
        });

        return $dupliHash;
    }

    public function convertToCheckArray($addresses=[]) {

        if (!is_array($addresses)) {
            return [];
        }

        $checkAddresses = [];

        foreach ($addresses as $address) {
            $key = $this->mergeAddress($address);
            if (!isset($checkAddresses[$key])) {
                $checkAddresses[$key] = [];
            }
            $checkAddresses[$key][] = $address['user_id'];
        }

        return $checkAddresses;
    }

    public function normalize($str) {
        if ($str === '') return '';
        $str = trim($str);
        // R 「半角」英字を「全角」に変換します.
        // N 「半角」数字を「全角」に変換します.
        // S 「半角」スペースを「全角」に変換します（U+0020 -> U+3000）
        // H 「半角カタカナ」を「全角ひらがな」に変換します.
        // K 「半角カタカナ」を「全角カタカナ」に変換します.
        // C 「全角ひらがな」を「全角カタカナ」に変換します.
        $str = mb_convert_kana($str, 'RNSHKC', 'UTF-8');
        // 英字小文字を大文字に変更します.
        $str = mb_convert_case($str, MB_CASE_UPPER, 'UTF-8');

        return $str;
    }

    public function mergeAddress($addresses=[]) {
        $mergeAddress = '';
        $mergeAddress .= $this->normalize($addresses['address1']);
        $mergeAddress .= $this->normalize($addresses['address2']);
        $mergeAddress .= $this->normalize($addresses['address3']);
        return $mergeAddress;
    }
}

