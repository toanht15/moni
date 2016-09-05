<?php
class Router {

    public function run() {

        list($file_path, $class_name) = $this->getPathAndClassName();

        //指定したクラス名のみ実施する
        if (in_array($class_name, array('OpenEmailTracker','OpenUserMailTracker'))) {
            require_once ($file_path);
            $class = new $class_name;
            $class->run();
        }
    }

    public function convertCamel ( $str ) {
        $ret = '';
        if ( preg_match ('#[a-z]#', $str ) ) {
            $count = strlen ( $str );
            for( $i = 0; $i < $count; $i++ ){
                if     ( !$i )                           $ret .= strtoupper ( $str[$i] );
                elseif ( preg_match( '#_#', $str[$i] ) ) $ret .= strtoupper ( $str[++$i] );
                else                                     $ret .= strtolower ( $str[$i] );
            }
        }
        else {
            $ret = $str;
        }
        return $ret;
    }

    public function getPathAndClassName() {
        $file_base_path = dirname(__FILE__) . '/../actions/';
        $request_uri = @parse_url($_SERVER['REQUEST_URI']);
        $file_path = trim($request_uri['path'], '/');
        if (!$file_path) {
            return null;
        }
        $file_path = substr($file_path, -4, strlen($file_path)) == '.php' ? $file_path : $file_path.'.php';
        $request_parse = explode('/',trim($request_uri['path'],'/'));
        $file_name = $request_parse[count($request_parse)-1];
        $class_name = $this->convertCamel(explode('.php', $file_name)[0]);
        return array($file_base_path.$file_path, $class_name);
    }
}

try {
    $router = new Router();
    $router->run();
} catch (Exception $e) {
    error_log("Router Error" . $e);
    throw $e;
}