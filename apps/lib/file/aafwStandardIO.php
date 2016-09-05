<?php
class aafwStandardIO {
    public function __construct (){
    }

    public function readLine ( $label ) {
        $line = '';
        $f = fopen ('php://stdin', 'a+');
        while ( true ) {
            fwrite ( $f, "$label ");
            $line = fgets ( $f, 4096 );
            $line = trim ( $line );
            if ( $line ) break;
            fwrite ( $f, $line );
        }
        fclose ( $f );
        return $line;
    }
}

