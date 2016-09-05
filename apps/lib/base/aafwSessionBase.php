<?php
/**
 * session‚Ì’ŠÛƒNƒ‰ƒX
 *
 *
 * @package   aafw
 * @author    allied architechts
 */

abstract class aafwSessionBase{
  abstract function start();
  abstract function getValues();
  abstract function setValues( $val );
  abstract function setSessionTime( $val );
  abstract function clear();

  /***************************************************
   * 1y,1w,1d,1h,1i,1s‚Ì‚æ‚¤‚È•¶Žš—ñ‚ð•b‚É–ß‚·
   ***************************************************/
  public function convertSecond( $span ){
    if( !preg_match( '/^(\d+)(\D)$/', $span, $tmp ) ) return -1;
    $x = $tmp[1];
    $y = strtoupper( $tmp[2] );
    $cmds = array(
      'Y' => 'YearToSec' ,
      'M' => 'MonToSec' ,
      'W' => 'WeekToSec' ,
      'D' => 'DayToSec' ,
      'H' => 'HourToSec' ,
      'I' => 'MinToSec' ,
      'S' => 'SecToSec' ,
      );
    if( !$cmds[$y] ) return -1;
    $meth = $cmds[$y];
    return $this->$meth( $x );
  }
  protected function YearToSec( $x ) { return 365 * $this->DayToSec( $x );  }
  protected function MonToSec( $x )  { return 30  * $this->DayToSec( $x );  }
  protected function WeekToSec( $x ) { return 7   * $this->DayToSec( $x );  }
  protected function DayToSec( $x )  { return 24  * $this->HourToSec( $x ); }
  protected function HourToSec( $x ) { return 60  * $this->MinToSec( $x );  }
  protected function MinToSec( $x )  { return 60  * $this->SecToSec( $x );  }
  protected function SecToSec( $x )  { return $x;  }
  
}