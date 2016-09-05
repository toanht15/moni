<?php
AAFW::import ( 'jp.aainc.aafw.text.aafwTemplateTag' );
AAFW::import ( 'jp.aainc.aafw.mail.aafwMail' );
AAFW::import ( 'jp.aainc.aafw.base.aafwObject' );
class aafwCLIController extends aafwObject {
    public function run( $argv ){
        $class_path = array_shift ( $argv );
        $params = $this->parseArgs ( $argv );
        $class_name = AAFW::import ( $class_path );

        if ($class_name[0] == 'ManagerKpi') {
           aafwLog4phpLogger::getDefaultLogger()->info('aafwCLIController#run:' . $class_name[0]);
        }

        if     ( preg_match ( '#actions#', $class_path ) ) $this->doAction ( $class_name, $params );
        elseif ( preg_match ( '#classes#', $class_path ) ) $this->doService ( $class_name, $params );
    }

    public function doAction ( $class_name, $params ) {
        $action = new $class_name[0];
        foreach ( $params as $key => $val ) {
            $action->$key = $val;
        }

        $action->disablePlugins();
        $result = $action->run();
        if ( !$result ) return null;
        if ( preg_match ( '#^mail:(.+)#', $result, $tmp ) ) {
            $path = AAFW_DIR . '/views/' .$tmp[1];
            $contents = preg_split ( '#\n#', file_get_contents ( $path ) );
            $subject = array_shift ( $contents );
            $mail = new aafwMail ( $subject, join ( "\n", $contents ) );
            $to = aafwApplicationConfig::getInstance()->query ('@cli.MailAddress');
            $mail->send ( $to, $action->getData() );
        }
        elseif ( is_file ( $path = AAFW_DIR . '/views/' . $result ) ) {
            $template = new aafwTemplateTag ( $path,   $action->getData()  );
            print $template->evalTag();
        }
        else {
            throw new aafwException ( 'viewがありません' );
        }
    }

    public function doService ( $class_name, $params ) {
        $service = new $class_name[0];
        $service->setConfig ( aafwApplicationConfig::getInstance() );
        if ( in_array ( 'setup', get_class_methods ( $service ) ) ) $service->setup();
        if ( !$service->_canMultiProcess) {
            $service->doService ( $params );
        }
        elseif ( !$params['page'] ) {
            $service->runMultiProcess ();
        }
        else {
            $service->runChildProcess ( $params['page'] );
        }
    }

    public function parseArgs ( $argv ){
        $result = array ();
        foreach ( $argv as $arg ) {
            $strlen = mb_strlen ( $arg, 'UTF8' );
            $i = 0;
            $key = '';
            for ( $i = 0; $i < $strlen; $i++ ) {
                $char = mb_substr ( $arg, $i, 1, 'UTF8' );
                if ( $char == '=' ) break;
                $key .= $char;
            }

            $val = '';
            for ( $i += 1; $i < $strlen; $i++ ) {
                $char = mb_substr ( $arg, $i, 1, 'UTF8' );
                $val .= $char;
            }

            if ( $key && $val ) {
                $result[$key] = $val;
            }
        }
        return $result;
    }
}
