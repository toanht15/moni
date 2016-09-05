<?php
AAFW::import ( 'jp.aainc.aafw.text.template_tag_plugin.ITemplateTagPlugin' );
/**
 * Description of TT_loopjustify
 *
 * @author ishida
 */
class TT_loopjustify implements ITemplateTagPlugin{

	protected $MaxLength = array();

	/**
	 * プラグインの種別を返す
	 * @return <str> value|loop|if
	 */
	public function getPluginType(){
		return 'loop';
	}

	/**
	 * 適用したいアトリビュートの名前を返す
	 * @return <str> 適用したいアトリビュートの名前
	 */
	public function getAttrName(){
		return 'justify';
	}

	/**
	 * メソッドの事前処理 この場合各行の最大文字数を設定する
	 * @param
	 */
	public function prepareMethod( $data ){
		foreach ( $data as $row ) {
			foreach ( $row as $key => $value ){
				if ( strlen ( $this->MaxLength[$key] ) < strlen( $value ) ){
					$this->MaxLength[$key] = strlen( $value );
				}
			}
		}
	}

	/**
	 * @param アトリビュートのパラメータ
	 * @param 変換したい値
	 */
	public function doMethod( $param, $value ){
		if ( $param != 'true' ) return $value;
		$ret = '';
		foreach ( split( "\n", $value ) as $row ){

		}
	}
}
