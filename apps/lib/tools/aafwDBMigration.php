<?php
AAFW::import ( 'jp.aainc.aafw.db.DB' );
AAFW::import ( 'jp.aainc.aafw.db.aafwDatabaseDiff' );
class aafwDBMigration {
    public static function getShortName () {
        return 'migrate';
    }

    public static function doService ( $argv ) {
        if ( !is_file ( $argv[0] ) )
            throw new Exception ( 'Newのファイルが見付かりません' . $argv[0] . '|' . getcwd() ) ;

        if ( !is_file ( $argv[1] ) )
            throw new Exception ( 'oldのファイルが見付かりません' . $argv[1] . '|' . getcwd() ) ;

        $new = DB::getInstance('migrator','w');
        $old = DB::getInstance('migrator','r');
        $new->clearDatabase();
        $old->clearDatabase();
        $new->executeMulti ( file_get_contents ( $argv[0] ) );
        $old->executeMulti ( file_get_contents ( $argv[1] ) );
        $diff = new aafwDatabaseDiff ( $new, $old );
        print $diff->toScript() ;
    }
}
