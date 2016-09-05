<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwParserBase' );
/****************************
 * XMLパーサーってか、simplexmlのラッパー
 ****************************/
class XMLParser extends aafwParserBase {
  public function getContentType(){
    return 'text/xml';
  }

  /****************************
   * XMLか、XMLのパスをパースする
   ****************************/
  public function in($data) {
//    return XMLParser::toArray( preg_match( '/^<\?xml/', $data ) ? simplexml_load_string( $data ) : simplexml_load_file( $data ) );
    if( $this ){
      //return $this->toArray( preg_match( '/<\?xml/', $data ) ? simplexml_load_string( $data ) : simplexml_load_file( $data ) );
      return $this->toArray( $data );
    } else {
//      return XMLParser::toArray( preg_match( '/<\?xml/', $data ) ? simplexml_load_string( $data ) : simplexml_load_file( $data ) );
      return XMLParser::toArray( $data );
    }
  }

  /***********************************
   * データ構造をXML文字列にパースする
   ***********************************/
  public function out( $data ) {
    $str = '';
    if( $data['rss']['channel'] ){
      $str = '<?xml version="1.0" encoding="UTF-8" ?><rss version="2.0">' . XMLParser::toXML( $data['rss'] ) . '</rss>';
    }
    elseif ( $data['XML_STRING'] ) {
      $str = $data['XML_STRING'];
    }
    else {
      $str = '<?xml version="1.0" encoding="UTF-8" ?><content>' . "\n" . XMLParser::toXML( $data ) . '</content>';
    }
    $str = str_replace ( array("\0B" , "\x0B" , "\0", "\x01", "\x02", "\x03", "\x04", "\x05", "\x06", "\x07", "\x08", "\x0b", "\x0c", "\x0e", "\x0f"), '', $str);
    return $str;
  }

  ///
  /// 暫定対処。これは酷い^^;
  ///
  public function toXML( $data, $name  = null ){
    if( !is_array( $data ) ) {
			if ( is_object( $data ) ) {
				if ( is_subclass_of( $data, 'aafwPhysicalEntityBase' ) ) return $data->getValues();
				else                                                     return null;
			} elseif( preg_match( '/(?:[\>\<\'"]|\n)/', $data ) ) {
        return '<![CDATA[' . $data .']]>';
      } else {
        return htmlspecialchars ( $data );
      }
    }
    $ret = '';
    foreach( $data as $key => $value ){
      if( is_numeric( $key )  ) {
				$value = XMLParser::toXML( $value, $key );
				if ( !is_null( $value )  ) {
					if ( $name ) $ret .= '<' . $name . '>' . $value . '</' . $name . '>';
					else         $ret .= $value;
				}
			} else {
				if ( is_array ( $value ) ){
					$value = XMLParser::toXML( $value, $key );
					if ( !is_null( $value ) ) $ret .= $value;
				}
				else {
					$value = XMLParser::toXML( $value, $key );
					if ( !is_null ( $value ) ) $ret .= '<' . $key . '>' . $value . '</' . $key . '>' . "\n";
				}
			}
    }
    return $ret;
  }


  /***********************************
   * XMLオブジェクト構造を再帰的に配列化
   ***********************************/
  public function toArray( $obj ){
    if( !preg_match( '/<\?xml/', $obj ) ) $obj = file_get_contents($obj);
    $xml = new XMLToArray( $obj );
    return $xml->createArray();
/*
    $ret = array();
    foreach( $obj->children() as $child ){
      $current = $child->children() ? XMLParser::toArray( $child ) : (string)$child ;
      if( !$child->getName()  ){
        $ret[] = $current;
      } else {
        if( $ret[$child->getName()] ){
          if( $ret[$child->getName()][0] ) {
            $ret[$child->getName()][] = $current;
          } else {
            $buf = $ret[$child->getName()];
            $ret[$child->getName()]   = array();
            $ret[$child->getName()][] = $buf;
            $ret[$child->getName()][] = $current;
          }
        } else {
          $ret[$child->getName()] = $current;
        }
      }
    }
    return $ret;
  */
  }
}

/**
 * Author   : MA Razzaque Rupom (rupom_315@yahoo.com, rupom.bd@gmail.com)
 * Version  : 1.0
 * Date     : 02 March, 2006
 * Purpose  : Creating Hierarchical Array from XML Data
 * Released : Under GPL
 */

class XmlToArray
{

	var $xml='';

	/**
	* Default Constructor
	* @param $xml = xml data
	* @return none
	*/

	function XmlToArray($xml)
	{
	   $this->xml = $xml;
	}

	/**
	* _struct_to_array($values, &$i)
	*
	* This is adds the contents of the return xml into the array for easier processing.
	* Recursive, Static
	*
	* @access    private
	* @param    array  $values this is the xml data in an array
	* @param    int    $i  this is the current location in the array
	* @return    Array
	*/

	function _struct_to_array($values, &$i)
	{
		$child = array();
		if (isset($values[$i]['value'])) array_push($child, $values[$i]['value']);

		while ($i++ < count($values)) {
			switch ($values[$i]['type']) {
				case 'cdata':
            	array_push($child, $values[$i]['value']);
				break;

				case 'complete':
					$name = $values[$i]['tag'];
					if(!empty($name)){
					$child[$name]= ($values[$i]['value'])?($values[$i]['value']):'';
					if(isset($values[$i]['attributes'])) {
						$child[$name] = $values[$i]['attributes'];
					}
				}
          	break;

				case 'open':
					$name = $values[$i]['tag'];
					$size = isset($child[$name]) ? sizeof($child[$name]) : 0;
					$child[$name][$size] = $this->_struct_to_array($values, $i);
				break;

				case 'close':
            	return $child;
				break;
			}
		}
		return $child;
	}//_struct_to_array

	/**
	* createArray($data)
	*
	* This is adds the contents of the return xml into the array for easier processing.
	*
	* @access    public
	* @param    string    $data this is the string of the xml data
	* @return    Array
	*/
	function createArray()
	{
		$xml    = $this->xml;
		$values = array();
		$index  = array();
		$array  = array();
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parse_into_struct($parser, $xml, $values, $index);
		xml_parser_free($parser);
		$i = 0;
		$name = $values[$i]['tag'];
		$array[$name] = isset($values[$i]['attributes']) ? $values[$i]['attributes'] : '';
		$array[$name] = $this->_struct_to_array($values, $i);
		return $array;
	}//createArray


}//XmlToArray

