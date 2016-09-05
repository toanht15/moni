<?php
require_once 'com/gmo_pg/client/output/ErrHolder.php';

/**
 * <b>API返却パラメータ文字列パーサ</b>
 * 
 * GMO-PGの決済サーバーから返却された文字列をパースするためのクラス
 * 
 * @package com.gmo_pg.client
 * @subpackage common
 * @see commonPackageInfo.php
 * @author GMO PaymentGateway
 * @version 1.0
 * @created 01-01-2008 00:00:00
 */
class ParamParser {
       
	/**
	 * パラメータ文字列解析
	 *
	 * @param  string $params    パラメータ文字列
	 * @return array paramsMap パラメータ文字列の連想配列
	 */
	function parse($params) {
	    // nullの場合は処理を行わない
	    if (is_null($params)) {
	        return null;
	    }
	    
	    // パラメータ文字列の分割
        $queryArray = explode('&', $params);
        
        // 分割した文字列を解析し、key,valueの形で格納する。
        $paramsMap = array();
	    foreach ($queryArray as $value) {
	        $splitArray = explode('=', $value, 2);  // 要素の最初の'='で2分割を行う  
	        if (2 == count($splitArray)) {
	            $paramsMap[$splitArray[0]] = $splitArray[1];
	            
	        }
	    }   
		return $paramsMap;
	}
	
	/**
	 * CSVパラメータ文字列解析
	 *
	 * @param  string $params    パラメータ文字列
	 * @return array paramsMap パラメータ文字列の連想配列
	 */
	function parseCsv($params) {
		// nullの場合は処理を行わない
		if (is_null($params)) {
			return null;
		}
		 
		// パラメータ文字列の分割
		$queryArray = explode('&', $params);
		
		if (1 == count($queryArray)) {
			$paramsMap = array();
			$paramsMap["csvResponse"] = $params;
			return $paramsMap;
		}
		return $this->parse($params);
	}
	
	/**
	 * Exec特殊パラメータ文字列解析
	 *
	 * @param  string $params    パラメータ文字列
	 * @return array paramsMap パラメータ文字列の連想配列
	 */
	function execSpecialParse($params) {
		
		$paramsMap = array();
		
		// 既知のキー名の配列を定義
		$keys = array("ACS=", "ACSUrl=", "PaReq=", "MD=");
		
		//それぞれのキー名の位置を検出する
		$positions = array();
		foreach ($keys as $key) {
			$position = mb_strpos($params, $key);
			$positions[$key] = $position;
		}
		
		// キー名出現位置でソート
		asort($positions);
		
		//　キー名毎のkeyとvalueのセットを切り取る
		$startPosition = 0;
		$endPosition = 0;
		for ($counter = 0; $counter < count($keys); $counter++) {
			$startPosition = $endPosition;
			$endPosition = $positions[$keys[$counter]];
			if ($endPosition - $startPosition === 0) {
				// 初回ループはスルー
				continue;
			}
			$value = mb_substr($params, $startPosition, ($endPosition - $startPosition) );
			$this->splitKeyValue($value, $paramsMap);
		}
		
		// 最後に残った要素の処理
		$value = mb_substr($params, $endPosition);
		$this->splitKeyValue($value, $paramsMap);
		
		return $paramsMap;
	}
	
	/**
	 * ”key=value”の形式になっている文字列を分割してparamMapに連想配列の要素として登録する。
	 * @param string $value
	 * @param array &$paramsMap 配列の参照渡し
	 */
	function splitKeyValue($value, &$paramsMap) {
		
		if(empty($value)) {
			return;
		}
		
		$splitArray = explode('=', $value, 2);  // 要素の最初の'='で2分割を行う
		if (2 == count($splitArray)) {
			// 文字列の末尾にパラメータ毎の区切り文字が残っている可能性があるので、あれば削除する。
			if (mb_substr($splitArray[1], -1) === '&') {
				$splitedValue = mb_substr($splitArray[1], 0, -1);
			} else {
				$splitedValue = $splitArray[1];
			}
			
			$paramsMap[$splitArray[0]] = $splitedValue;
		}
	}

	/**
	 * エラー情報解析
	 *
	 * @param  string $errCode  エラーコード文字列
	 * @param  string $errInfo  エラー詳細文字列
	 * @return array errList  errHolderを格納したリスト
	 * 
	 * @see ErrHolder
	 */
	function errParse($errCode, $errInfo) {
	    $unKnown = 'unKnown';
	    
	    // 文字列を'|'で分割
        $errCodeArray = explode("|", $errCode);  // errCodeの配列
        $errInfoArray = explode("|", $errInfo);  // errInfoの配列

	    // 配列の長さを格納
	    $codeLength = count($errCodeArray);
	    $infoLength = count($errInfoArray);
	    
	    // 配列サイズが異なる場合、大きい側をサイズとして扱う
	    $length = ($codeLength >= $infoLength) ? $codeLength : $infoLength;
	    $errList = array();
	    
		for ($i = 0; $i < $length; $i++) {
	        $errHolder = new ErrHolder();
	        
	        // errCode/Infoが不足している場合は'unKnown'文字列で埋める
	        if ($i > $codeLength - 1) {
	            $errHolder->setErrCode($unKnown);
	            $errHolder->setErrInfo($errInfoArray[$i]);
	        } elseif ($i > $infoLength - 1) {
	            $errHolder->setErrCode($errCodeArray[$i]);
	            $errHolder->setErrInfo($unKnown);
	        } else {
	            // 通常は配列値をセットする
	            $errHolder->setErrCode($errCodeArray[$i]);
	            $errHolder->setErrInfo($errInfoArray[$i]);
	        }
            $errList[] = $errHolder;
		}
		
		return $errList;
	}
}
?>