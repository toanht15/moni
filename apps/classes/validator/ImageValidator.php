<?php

class ImageValidator {

	public $imageInfo = null;
	public $error_code = self::NO_ERROR;
	
	//************エラーコード**************
	const NO_ERROR			= 0;	//エラーなし
	const ALLOW_TYPE_ERROR	= 1;	//拡張子エラー
	const COLOR_MODE_ERROR	= 2;	//カラーモードエラー
	const SIZE_ERROR		= 3;	//サイズが不適切
	const SQUARE_ERROR		= 4;	//正方形ではない

	public function __construct($imagePath) {
		$this->imageInfo = @getimagesize($imagePath);
	}
	
	/**
	 * エラーコードを返却する
	 * @return error_code
	 */
	public function getErrorCode(){
		return $this->error_code;
	}
	
	/**
	 * 画像のカラーモードがRGBかチェックする
	 * @return 真偽値
	 */
	public function isRGB() {
		if(!$this->imageInfo){
			return false;
		}

		//channelsが4のときはCMYKでそれ以外はRGBと見なす
		//PNG画像はchannelsのパラメーターがないので・・・
		if ( isset( $this->imageInfo['channels']) && $this->imageInfo['channels'] == 4) {
			return false;
		}
		return true;
	}
	
	/**
	 * 画像の縦、横サイズが指定サイズ以上かをチェックする
	 * @param int    $width     横幅(0指定でwidthはチェックしない)
	 * @param int    $height    縦幅(0指定でheightはチェックしない)　
	 * @return 真偽値
	 */
	public function isLargerSize($width=0,$height=0){
		$width = intval($width);
		$height = intval($height);
		if(!$this->imageInfo){
			return false;
		}
		
		if( $this->imageInfo[0] >= $width && $this->imageInfo[1] >= $height ){
			return true;
		}
		return false;
	}
	/**
	 * 画像の縦、横サイズが指定サイズ以下かをチェックする
	 * @param int    $width     横幅(0指定でwidthはチェックしない)
	 * @param int    $height    縦幅(0指定でheightはチェックしない)　
	 * @return 真偽値
	 */
	public function isSmallerSize($width=0,$height=0){
		$width = intval($width);
		$height = intval($height);
		if(!$this->imageInfo){
			return false;
		}
		//横幅または縦幅が指定サイズより大きければfalse
		if( $width > 0 && $this->imageInfo[0] > $width ){
			return false;
		}
		if( $height > 0 && $this->imageInfo[1] > $height ){
			return false;
		}

		return true;
	}
	/**
	 * 画像の縦、横サイズが指定サイズと一致するかチェックする
	 * @param int    $width     横幅(0指定でwidthはチェックしない)
	 * @param int    $height    縦幅(0指定でheightはチェックしない)　
	 * @return 真偽値
	 */
	public function isEqualSize($width=0,$height=0){
		$width = intval($width);
		$height = intval($height);
		
		if(!$this->imageInfo){
			return false;
		}
		if( $width > 0 && $this->imageInfo[0] != $width ){
			return false;
		}
		if( $height > 0 && $this->imageInfo[1] != $height ){
			return false;
		}
		return true;
	}

	/**
	 * 画像の拡張子が許可したものかチェックする
	 * @param arrayまたはint $allowType 許可する拡張子配列(array(XX,YY,ZZ･･･))
	 * @return 真偽値
	 * デフォルトの許可拡張子(jpeg,png,gif)
	 */

	public function isAllowExtensions($allowExtensions = null) {

		if(!$this->imageInfo){
			return false;
		}
		if( !$allowExtensions ){
			$allowExtensions = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG);
		}
		if( is_scalar($allowExtensions) ){
			return $allowExtensions == $this->imageInfo[2];
		}elseif( is_array ($allowExtensions) && in_array($this->imageInfo[2], $allowExtensions) ){
			return true;
		}
		return false;
	}
	
	/**
	 * 画像が正方形かどうかチェックする
	 * @return 真偽値
	 */
	public function isSquare(){
		if(!$this->imageInfo){
			return false;
		}
		//縦サイズと横サイズが一致しているかチェック
		if($this->imageInfo[0] == $this->imageInfo[1] ){
			return true;
		}
		return false;
	}
	
	/**
	 * 引数の比較演算子に応じて、isEqualSize,isLargerSize,isSmallerSizeを呼び出す
	 * @param int $width チェックする横幅
	 * @param string $compareType 比較演算子 ( == , >= , <= )
	 * @return 真偽値
	 */
	public function isValidWidth($width, $compareType){
		if ($compareType == '==') {
			if ($this->isEqualSize($width, 0)) return true;
		}elseif ($compareType == '>=') {
			if ($this->isLargerSize($width, 0)) return true;
		}elseif ($compareType == '<=') {
			if ($this->isSmallerSize($width, 0)) return true;
		}
		return false;
	}
	
	/**
	 * 引数の比較演算子に応じて、isEqualSize,isLargerSize,isSmallerSizeを呼び出す
	 * @param int $height チェックする縦幅
	 * @param string $compareType 比較演算子 ( == , >= , <= )
	 * @return 真偽値
	 */
	public function isValidHeight($height, $compareType){
		if ($compareType == '==') {
			if ($this->isEqualSize(0, $height)) return true;
		}elseif ($compareType == '>=') {
			if ($this->isLargerSize(0, $height)) return true;
		}elseif ($compareType == '<=') {
			if ($this->isSmallerSize(0, $height)) return true;
		}
		return false;
	}
	
	
	/**
	 * 画像のチェックを一括で行う<br/>とりあえず対応している比較演算子は( == , <= , >= )の3種類<br/>
	 * 画像が適切出ない場合はエラーコードをセットして、falseを返す
	 * @param array $checkParams						 Example:	array('width'=>'XX:==','height'=>'YY:>=',allowExtensions=array(1,2,3)  )
	 * @param bool $checkSquare 正方形チェックを行うかフラグ			default:false
	 * @param bool $checkAllowExtension 拡張子をチェックするかフラグ default:true
	 * @param bool $checkRGB RGB判定を行うかフラグ					default:true
	 * @return 真偽値
	 */
	public function isValidImage($checkParams, $checkSquare = false, $checkAllowExtensions = true, $checkRGB = true){

		if($checkAllowExtensions){
			if(!$this->isAllowExtensions( isset($checkParams['allowExtensions']) ? $checkParams['allowExtensions'] : null )){
				$this->error_code = self::ALLOW_TYPE_ERROR;
				return false;
			}
		}
		if($checkRGB){
			if(!$this->isRGB()) {
				$this->error_code = self::COLOR_MODE_ERROR;
				return false;
			}
		}
		if($checkSquare){
			if(!$this->isSquare()){
				$this->error_code = self::SQUARE_ERROR;
				return false;
			}
		}

		if(isset( $checkParams['width'] )){
			$checkParamsWidth = explode(":", $checkParams['width']);
			if( count($checkParamsWidth) == 2 && $checkParamsWidth[0] > 0 ){
				if(!$this->isValidWidth($checkParamsWidth[0], $checkParamsWidth[1])){
					$this->error_code = self::SIZE_ERROR;
					return false;
				}
			}
		}
		if(isset( $checkParams['height'] )){
			$checkParamsHeight = explode(":", $checkParams['height']);
			if( count($checkParamsHeight)==2 && $checkParamsHeight[0] > 0 ){
				if(!$this->isValidHeight($checkParamsHeight[0], $checkParamsHeight[1])){
					$this->error_code = self::SIZE_ERROR;
					return false;
				}
			}
		}
		return true;
	}
	
	/**
	 * isValidImageでセットされたエラーコードに応じたメッセージを返却する
	 * @param string $lang 言語コード
	 * @return string validateMessage
	 */
	public function getValidateMessage($lang){
		switch ($this->error_code) {
			case self::ALLOW_TYPE_ERROR:
				return Translator::_tlValidateMessage($lang,'upload_image_extention2');
			case self::COLOR_MODE_ERROR:
				return Translator::_tlValidateMessage($lang,'upload_image_rgb');
			case self::SIZE_ERROR:
			case self::SQUARE_ERROR:
				return Translator::_tlValidateMessage($lang,'upload_image_size_error2');
		}
	}

}
