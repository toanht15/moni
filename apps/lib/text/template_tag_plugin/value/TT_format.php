<?php
AAFW::import ( 'jp.aainc.aafw.text.template_tag_plugin.ITemplateTagPlugin' );
/**
 * 値の編集用プラグイン
 * @author ishida
 */
class TT_format implements ITemplateTagPlugin {

	/**
	 * デフォルト関数
	 * @return <type> そのまま返す
	 */
	public function __call( $name, $args ){
		print $name;
		return $args[1];
	}

	/**
	 * プラグインの種別を返す
	 * @return <str> value|loop|if
	 */
	public function getPluginType(){
		return 'value';
	}

	/**
	 * 適用したいアトリビュートの名前を返す
	 * @return <str> 適用したいアトリビュートの名前
	 */
	public function getAttrName(){
		return 'format';
	}

	public function prepareMethod( $data ){
		return $data;
	}
	/**
	 * 適用したいアトリビュートの名前を返す
	 * @return <str> 適用したいアトリビュートの名前
	 */
	public function doMethod( $param,$value ){
		if ( !preg_match ( '#^(\w+)#', $param, $tmp ) ) return $value;
		$method = strtolower ( $tmp[1] );
		return $this->$method ( $param, $value );
	}

	/**
	 * カンマ編集して返す
	 * @return <str> カンマ編集した結果
	 */
	private function currency( $param, $value ){
		return strrev( preg_replace ( '#(\d{3})(?=\d)#', '$1,' , strrev( $value ) ) );
	}

	/**
	 * 日付フォーマット
	 * @return <str> 適用したいアトリビュートの名前
	 */
	private function date( $param, $value ){
		if ( !preg_match ( '#\[(.+?)\]#', $param, $tmp ) ) return $value;
		return date( $tmp[1], strtotime( $value ) );
	}
}
