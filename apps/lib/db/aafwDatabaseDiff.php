<?php
class aafwDatabaseDiff {
    private $newDatabase  = null;
    private $oldDatabase  = null;
    private $newSchemes   = null;
    private $oldSchemes   = null;
    private $newIndex     = array ();
    private $oldIndex     = array ();
    private $schemeDiffs  = array ();
    private $indexDiffs   = array ();
    private $createTables = array ();
    private $dropedTables = array ();

    public function __construct ( $new, $old ) {
        $this->newDatabase = $new;
        $this->oldDatabase = $old;
        $this->fillTableDiff();
    }

    public function getNewTables  () {
        return $this->createTables;
    }

    public function getDropedTables (){
        return $this->dropedTables;
    }

    public function getSchemeDiffs  () {
        return $this->schemeDiffs;
    }

    public function fillTableDiff () {
        $this->newTables = $this->newDatabase->getTables ();
        $this->oldTables = $this->oldDatabase->getTables ();
        foreach ( $this->newTables as $tableName ) {
            $this->newSchemes[$tableName] = $this->newDatabase->getTableInfo ( $tableName );
            $this->newIndex[$tableName]   = $this->newDatabase->getIndexInfo ( $tableName );
            if ( !in_array ( $tableName, $this->oldTables ) ) {
                $this->createTables[] = $tableName;
            }
            else {
                $this->oldSchemes[$tableName]  = $this->oldDatabase->getTableInfo ( $tableName );
                $this->oldIndex[$tableName]    = $this->oldDatabase->getIndexInfo ( $tableName );
                $this->schemeDiffs[$tableName] = $this->getSchemeDiff ( $tableName );
                $this->indexDiffs[$tableName]  = $this->getIndexDiff ( $tableName );
            }
        }
        foreach ( $this->oldTables as $tableName ) {
            if ( !in_array ( $tableName, $this->newTables ) ) {
                $this->dropedTables[] = $tableName;
            }
        }
    }


    public function getSchemeDiff ( $tableName ) {
        $result = array (
            'changed' => array (),
            'add'     => array (),
            'drop'    => array (),
        );
        $newScheme = $this->newSchemes[$tableName];
        $oldScheme = $this->oldSchemes[$tableName];
        foreach ( $newScheme as $key => $value  ) {
            if ( !$oldScheme[$key] ) {
                $result['add'][] = $value;
            }
            elseif ( $oldScheme[$key] != $value ) {
                $result['changed'][] =  $value;
            }
        }
        foreach ( $oldScheme as $key => $value ) {
            if ( !$newScheme[$key] ) {
                $result['drop'][] = $value;
            }
        }
        return $result;
    }

    public function getIndexDiff ( $tableName ) {
        $result = array (
            'changed' => array (),
            'add'     => array (),
            'drop'    => array (),
        );
        $new = $this->newIndex[$tableName];
        $old = $this->oldIndex[$tableName];
        foreach ( $new as $key => $value  ) {
            if ( !$old[$key] ) {
                $result['add'][] = $value;
            }
            elseif ( $old[$key] != $value ) {
                $result['changed'][] =  $value;
            }
        }
        foreach ( $old as $key => $value ) {
            if ( !$new[$key] ) {
                $result['drop'][] = $value;
            }
        }
        return $result;
    }

    public function toCreateTable ( $tableName ) {
        $sql = '';
        $sql .= "CREATE TABLE `$tableName` (";
        foreach ( $this->newSchemes[$tableName] as $key => $value ) {
            $sql .= $this->createFiledDefinitions ( $tableName, $key );
            $sql .= ',';
        }
        $sql  = preg_replace ( '#,$#', '', $sql );
        $sql .= ');';
        return $sql;
    }

    public function toDropTable ( $tableName ) {
        return 'DROP TABLE `' . $tableName . '`;';
    }

    public function createFiledDefinitions ( $tableName, $key ) {
        $value = $this->newSchemes[$tableName][$key] ;
        $sql = '';
        $sql .= "`" . $value['column'] . "` " . $value['type_name'] . " " . ( $value['nullable'] ?  'NULL' : 'NOT NULL'  );
        if ( $value ['key'] ) {
            $sql .= ' ' . 'PRIMARY KEY';
        }
        if ( $value ['extra'] ) {
            $sql .= ' ' . $value['extra'];
        }
        return $sql;
    }

    public function toAddColumn ( $tableName, $key ) {
        $value = $this->newSchemes[$tableName][$key] ;
        $sql  = '';
        $sql .= "ALTER TABLE `$tableName` ADD " . $this->createFiledDefinitions ( $tableName, $key ) . ';';
        return $sql;
    }

    public function toChangeColumn ( $tableName, $key ) {
        $value = $this->newSchemes[$tableName][$key] ;
        $sql  = '';
        $sql .= "ALTER TABLE `$tableName` MODIFY " . $this->createFiledDefinitions ( $tableName, $key ) . ';';
        return $sql;
    }

    public function toDropColumn ( $tableName, $key ) {
        $value = $this->newSchemes[$tableName][$key] ;
        $sql  = '';
        $sql .= "ALTER TABLE `$tableName` DROP `$key`;";
        return $sql;
    }

    public function toDropIndex ( $definition ) {
        return "ALTER TABLE `" . $definition[0]['Table'] .  "` " .
            "DROP INDEX `" .  $definition[0]['Key_name'] . "`;";
    }

    public function toAddIndex ( $definition ) {
        $sql  = '';
        $sql .= "ALTER TABLE `" . $definition[0]['Table'] . "` ";
        $sql .= "ADD INDEX `"   . $definition[0]['Key_name'] . "` ";
        $sql .= "(" . join ( ',', array_map ( function ($elm) {
            return '`' . $elm['Column_name'] . '`';
        }, $definition ) ) . ');';
        return $sql;
    }

    public function toChangeIndex ( $definition ) {
        $sql = '';
        $sql .= $this->toDropIndex ( $definition ) . "\n";
        $sql .= $this->toAddIndex ( $definition ) . "\n";
        return $sql;
    }

    public function toScript () {
        $sql = '';
        foreach ( $this->createTables as $tableName )  $sql .= $this->toCreateTable ( $tableName ) . "\n";
        foreach ( $this->dropedTables as $tableName )  $sql .= $this->toDropTable ( $tableName ) . "\n";
        foreach ( $this->schemeDiffs as $tableName => $diffs ) {
            foreach ( $diffs['changed'] as $changed ) $sql .= $this->toChangeColumn ( $tableName, $changed['column'] ) . "\n";
            foreach ( $diffs['add'] as $add )         $sql .= $this->toAddColumn ( $tableName, $add['column'] )        . "\n";
            foreach ( $diffs['drop'] as $drop )       $sql .= $this->toDropColumn ( $tableName, $drop['column'] )      . "\n";
        }
        foreach ( $this->indexDiffs as $tableName => $diffs ) {
            foreach ( $diffs['changed'] as $changed )  $sql .= $this->toChangeIndex ( $changed ) . "\n";
            foreach ( $diffs['add'] as $add )          $sql .= $this->toAddIndex ( $add )        . "\n";
            foreach ( $diffs['drop'] as $drop )        $sql .= $this->toDropColumn ( $drop )     . "\n";
        }
        return $sql;
    }
}
