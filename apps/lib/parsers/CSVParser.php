<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwParserBase');

class CSVParser extends aafwParserBase {

    private $file_name;

    public function getContentType(){
        return 'application/x-csv';
    }

    public function getDisposition(){
        if (!$this->file_name) {
            $this->file_name = date( 'YmdHis' );
        }
        return 'Content-Disposition: attachment; filename="' . $this->file_name . '.csv"';
    }

    public function setCSVFileName($name) {
        $this->file_name = $name;
    }

    public function in( $data ) {
        $str  = null;
        $sep  = null;
        $flds = null;
        if ( is_array ( $data  ) ) {
            if     ( $data['path'] ) $str = $data['path'];
            elseif ( $data['csv']  ) $str = $data['csv'];
            if     ( $data['sep'] )  $sep = $data['sep'];
            if     ( $data['flds'] ) $flds = $data['flds'];
        }
        if   ( is_file ( $data  ) ) {
            $str = file_get_contents( $data );
        }

        if ( !$str ) throw new Exception ( 'CSVをパース出来ません' );
        if ( !$sep ) $sep = ',';

        if ( is_file ( $str ) ) $str = file_get_contents ( $str );
        $str    = mb_convert_encoding ( $str, 'UTF8', 'UTF8,SJIS,EUC-JP' );
        $length = mb_strlen ( $str, 'UTF-8' );
        $buf    = '';
        $cols   = array ();
        $rows   = array ();
        for ( $i = 0; $i <  $length; $i++ ) {
            $char = mb_substr ( $str, $i, 1, 'UTF8' );
            if ( $char == "'" || $char == '"' ) {
                $quote  = $char;
                for ( $i += 1 ; $i < $length; $i++ ) {
                    $char = mb_substr ( $str, $i, 1, 'UTF8' );

                    if ( $char == '\\' )       { $buf .= mb_substr ( $str, ++$i, 1, 'UTF8' ); }
                    elseif ( $quote == $char ) { $quote = null; break; }
                    else                       { $buf .= $char; }
                }
            }
            elseif ( $char == $sep ) {
                if ( $flds ) {
                    if ( $flds[count($cols)] ) $cols[$flds[count($cols)]] = $buf;
                    else                       $cols[count( $cols )]      = $buf;
                }
                else                         $cols[] = $buf;
                $buf = '';
            }
            elseif ( $char == "\n" ) {
                if ( $buf ) {
                    if ( $flds ) {
                        if ( $flds[count($cols)] ) $cols[$flds[count($cols)]] = $buf;
                        else                       $cols[count( $cols )]      = $buf;
                    }
                    else                         $cols[] = $buf;
                }
                $buf = '';
                if ( $cols ) $rows[] = $cols;
                $cols   = array ();
            }
            elseif ( preg_match ( '#\S#', $char ) ) {
                $buf .= $char;
            }
        }
        if ( $buf )  {
            if ( $flds ) {
                if ( $flds[count($cols)] ) $cols[$flds[count($cols)]] = $buf;
                else                       $cols[count( $cols )]      = $buf;
            }
            else                         $cols[] = $buf;
        }
        if ( $cols ) {
            $rows[] = $cols;
        }
        return $rows;
    }

    public function out ( $data, $excel_type = "" ) {
        $header     = array();
        $list       = array();
        $rows       = array();
        $controller = array();
        if( $data['header'] ) $header = $data['header'];
        if( $data['list'] )   $list   = $data['list'];
        else                  $list   = $data;
        if ( $header ) {
            if ( preg_grep ( '#\D#', array_keys ( $header ) ) ) {
                $tmp = array();
                foreach ( $header as $key => $value ) {
                    $tmp[]        = $this->editColumnValue ( $key, $excel_type );
                    $controller[] = $value;
                }
                $rows[] = join ( ',', $tmp );
            }
            else {
                array_unshift ( $list, $header );
            }
        }

        foreach ( $list as $row ) {
            $buf = array();
            $row_org = $row;
            if ( !is_array ( $row ) ) {
                if     ( $row instanceof aafwPhysicalEntityBase ) $row = $row->getValues();
                elseif ( $row instanceof stdClass               ) $row = (array) $row;
                elseif ( $row instanceof aafwEntityBase         ) $row = $row->toArray();
                else                                              continue;
            }
            if ( $controller ) {
                $tmp = array();
                foreach ( $controller as $i )  {
                    if     ( is_object   ( $i ) && get_class ( $i ) == 'Closure' ) $tmp[] = $i ( $row_org );
                    elseif ( !is_numeric ( $i ) && preg_match ( '#lambda#', $i ) ) $tmp[] = $i ( $row_org );
                    elseif ( !is_null ( $row[$i] ) )                               $tmp[] = $row[$i];
                    else                                                           $tmp[] = $i;
                }
                $row = $tmp;
            }
            foreach( $row as $key => $col ) $buf[] = $this->editColumnValue ( $col, $excel_type );
            $rows[] = join( ',', $buf );
        }

        $result = join( "\n", $rows ) . "\n";
        if ( $data['__ENCODING__']  ) $result = mb_convert_encoding (  $result, $data['__ENCODING__'], 'UTF8' );
        return $result;
    }

    private function editColumnValue ( $col, $excel_type ) {
        if(is_int($col) || is_float($col) ){
            $excel_type = "0";
        }
        $col = trim( $col );
        $col = str_replace( '"', '""' , $col );
        $col = str_replace( "\r\n", ' ', $col );
        $col = str_replace( "\r"  , ' ', $col );
        $col = str_replace( "\n"  , ' ', $col );
        if( $excel_type == "1" && strpos($col, ',') === false) $col = '="' . $col . '"';
        else                     $col = '"'  . $col . '"';
        return $col;
    }
}

