<?php
//AAFW::import( 'org.fww.DB.fwwDAOBuilder' );
/**
 * DBの構造からアプリをジェネレートする
 * @package org.fww.tools
 * @access public
 * @author t_ishida
 **/
class aafwGenerator {
  private $Catalog = array();
  private $DB = null;
  
  public static function getShortName () {
    return 'gen';
  }  

  public static function showHelp(){ ?>
DBの構造からアプリをジェネレートする
未実装
<?php  }
  public static function doService( $argv ){
    throw new Exception ('これはまだ使えない');
    if( !$argv[2] ) return ;
    $gen = new aafwGenerator( $argv[2] );
    $gen->generate();
  }
  /**
   * コンストラクタ
   * @param データソース名
   **/
  public function __construct( $dsn = null ){
    //$this->DB = fwwDAOBuilder::getConnection( $dsn );
    //foreach( $this->DB->getTables() as $tbl ){
    //  $this->Catalogs[$tbl] = $this->DB->getCatalog( $tbl );
    //}
  }

  /**
   * アプリケーション全体をジェネレート
   * @param データソース名
   **/
  public function generate( ){
    $navi = '';
    foreach( $this->DB->getTables() as $tbl ){
      $this->createActions( $tbl );
      $this->createViews( $tbl );
      $this->createEntity( $tbl );
      $navi .= '<li>' . $tbl . "\n" .
        '<ul>' . 
          '<li><a href="/"' . $tbl . '>list</a>' . "\n" .
          '<li><a href="/"' . $tbl . '/editor>editor</a>' . "\n" .
         '</ul>' . "\n" .
      '</li>' . "\n";
    }
    $php  = '';
    $php .= '<?php' . "\n";
    $php .= 'FWW::import( \'org.fww.core.fwwActionBase\' );' . "\n";
    $php .= 'class index extends fwwActionBase{' . "\n";
    $php .= '  public function validate() {' . "\n";
    $php .= '    return true;' . "\n";
    $php .= '  }' . "\n";
    $php .= '  public function doService(){' . "\n";
    $php .= '    if( @!$this->Get[\'package\'] && @!$this->Get[\'action\'] ) return \'views.index\';' . "\n";
    $php .= '    $path = $this->Get[\'__path\'];' . "\n";
    $php .= '    list( $package_name, $view_name ) = array( \'\', \'\' );' . "\n";
    $php .= '    FWW::isPackage( \'views.\' . $path[0] )   && $package_name = array_shift( $path );' . "\n";
    $php .= '    !( $view_name  = array_shift( $path ) ) && $view_name = \'index\';' . "\n";
    $php .= '    $view_name = preg_replace( \'#\.[^\.]+$#\', \'\',  $view_name );' . "\n";
    $php .= '    $this->Data[\'exts\'] = $path;' . "\n";
    $php .= '    if ( FWW::isProgram("views.${package_name}.${view_name}" ) ) return "views.${package_name}.${view_name}";' . "\n";
    $php .= '    else                                                         return \'404\';' . "\n";
    $php .= '  }' . "\n";
    $php .= '}' . "\n";
    file_put_contents(  FWW::$WebSettings['ActionPath']   . '/index.php', $php );

    $html = '';
    $html .= $navi;
    file_put_contents(  FWW::$WebSettings['TemplatePath'] . '/index.php', $html );
  }
  
  /**
   * テーブル定義からアクションを作成する
   * @param テーブル名
   **/
  public function createActions( $tbl ){
    $root_path = FWW::$WebSettings['ActionPath'] . '/' . $tbl;
    if( !is_dir( $root_path ) ) mkdir( $root_path );
    foreach( preg_grep( '#create.+Action$#', get_class_methods( $this ) ) as $method ){
      if( preg_match( '#create(.+)Action$#', $method, $tmp ) ){
        $fname = $root_path . '/' . strtolower( $tmp[1] ) . '.php';
        file_put_contents( $fname, $this->$method( $tbl ) );
      }
    }
  }
  
  /**
   * テーブル定義からviewを作成する
   * @param テーブル名
   **/
  public function createViews( $tbl ){
    $root_path = FWW::$WebSettings['TemplatePath'] . '/' . $tbl;
    if( !is_dir( $root_path ) ) mkdir( $root_path );
    foreach( preg_grep( '#create.+View$#', get_class_methods( $this ) ) as $method ){
      if( preg_match( '#create(.+)View$#', $method, $tmp ) ){
        $fname = $root_path . '/' . strtolower( $tmp[1] ) . '.php';
        say( $fname . 'を作成中'  );
        file_put_contents( $fname, $this->$method( $tbl ) );
      }
    }
  }
  
  /**
   * テーブル定義からエンティティクラスを作成する
   * @param テーブル名
   **/
  public function createEntity( $tbl ){
    $str  = '';
    $str .='<?php' . "\n";
    $str .='FWW::import( \'org.fww.DB.fwwEntity\' );' . "\n";
    $str .='class ' .$tbl .' extends fwwEntity{}' . "\n";
    if( !is_dir( FWW::$WebSettings['ClassPath'] ) ) mkdir ( FWW::$WebSettings['ClassPath'] );
    $root_path = FWW::$WebSettings['ClassPath'] . '/entities';
    if( !is_dir( $root_path ) ) mkdir( $root_path );
    file_put_contents( $root_path . '/' . $tbl . '.php', $str );
  }

  /**
   * テーブル定義からDBに保存するためのactionを作成する
   * @param テーブル名
   * @return 生成した文字列
   **/
  public function createSaveAction( $tbl ){
    $str = '<?php' . "\n";
    $str .= 'FWW::import( \'org.fww.core.fwwActionBase\' );' . "\n";
    $str .= 'FWW::import( \'classes.entities.' . $tbl . '\' );' . "\n";
    $str .= 'class save extends fwwActionBase{' . "\n";
    $str .= '  protected $ErrorPage  = \'views.' . $tbl . '.editor\';' . "\n";
    $str .= '  protected $Definition = array(' . "\n";
    foreach( $this->Catalogs[$tbl]['flds'] as $fld ){
      $type = '';
      if    ( preg_match( '#char|text#i', $fld['data_type'] ) )                         $type = 'str';
      elseif( preg_match( '#int|num|decimal|long|float|double#i', $fld['data_type'] ) ) $type = 'num';
      elseif( preg_match( '#date|time|#i', $fld['data_type'] ) )                        $type = 'date';
      else                                                                              continue;
      $str .= '      "' . $fld['name'] . '" => array(' . "\n";
      $fld['not_null']  && $str .= "        'required'  => 1,\n";
      $fld['length']    && $str .= "        'length'    => "  . $fld['length']     . " ,\n";
      $fld['data_type'] && $str .= "        'type'      => '" . $type  . "',\n";
      $str .= '      ),' . "\n";
    }
    $str .= '    );' . "\n";
    $str .= '  public function doService(){' . "\n";
    $str .= '    $obj = new ' . $tbl .'();'   . "\n"; 
    foreach( $this->Catalogs[$tbl]['flds'] as $fld ){
      $str .= '    $obj->' . $fld['name'] . ' = $this->' . $fld['name'] .  ";\n";
    }
    $str .= '    $obj->save();' . "\n";
    $str .= '    return \'redirect: /' . $tbl . '/index/saved\';' . "\n";
    $str .= '  }' . "\n";
    $str .= '}'   . "\n";
    return $str;
  }

  /**
   * テーブル定義から一覧表示用のアクションを生成する
   * @param テーブル名
   * @return 生成した文字列
   **/
  public function createIndexAction( $tbl ){
    $str  = '<?php' . "\n";
    $str .= 'FWW::import( \'org.fww.core.fwwActionBase\' );' . "\n";
    $str .= 'FWW::import( \'classes.entities.' . $tbl . '\' );' . "\n";
    $str .= 'class index extends fwwActionBase{' . "\n";
    $str .= '  public function validate(){ return true; }' . "\n";
    $str .= '  public function doService(){' . "\n";
    $str .= '    $obj = new ' . $tbl .'();'   . "\n";
    $str .= '    $this->Data[\'list\']     = $obj->searchALL( "id", array( "page" => $this->p, "count" => 10 ) );' . "\n";
    $str .= '    $this->Data[\'max_page\'] = $obj->countALL() / 10 + ( $obj->countALL() % 10 ? 1 : 0 );' . "\n";
    $str .= '    return \'views.' . $tbl . '.list\'; ' . "\n";
    $str .= '  }' . "\n";
    $str .= '}'   . "\n";
    return $str;
  }
  
  /**
   * テーブル定義から新規登録・更新用のactionを生成する
   * @param テーブル名
   * @return 生成した文字列
   **/
  public function createEditorAction( $tbl ){
    $str  = '<?php' . "\n";
    $str .= 'FWW::import( \'org.fww.core.fwwActionBase\' );' . "\n";
    $str .= 'FWW::import( \'classes.entities.' . $tbl . '\' );' . "\n";
    $str .= 'class editor extends fwwActionBase{' . "\n";
    $str .= '  public function validate(){ return true; }' . "\n";
    $str .= '  public function doService(){' . "\n";
    $str .= '    $obj = new ' . $tbl .'();'   . "\n";
    $str .= '    if( $this->id ) $obj->search( $this->id );' . "\n";
    $str .= '    $this->Data = $obj->get();'    . "\n";
    $str .= '    return \'views.' . $tbl . '.editor\'; '       . "\n";
    $str .= '  }' . "\n";
    $str .= '}'   . "\n";
    return $str;
  }

  
  /**
   * 一覧のviewを生成する
   * @param テーブル名
   * @return 生成した文字列
   **/
  public function createListView( $tbl ){
    $str .= '<html>' . "\n";
    $str .= '<head><title>list of ' . $tbl . '</title></head>' . "\n";
    $str .= '<body>' . "\n";
    $str .= '<?php if( $this->list->count() ): ?>' . "\n";
    $str .= '<table>' . "\n";
    $str .= '<tr>' . "\n";
    foreach( $this->Catalogs[$tbl]['flds'] as $fld ){
      $str .= '  <th>'.  $fld['name'] . '</th>' . "\n";
    }
    $str .= '</tr>' . "\n";
    $str .= '<?php while( $row = $this->list->getNext() ):?>' . "\n";
    $str .= '<tr>' . "\n";
    foreach( $this->Catalogs[$tbl]['flds'] as $fld ){
      if( $fld['name'] == 'id' ) $str .= '  <td><a href="/' . $tbl . '/editor?id=<?php $row->id( \'asign\' )?>"><?php $row->id(\'asign\')?></td>' . "\n";
      else                       $str .= '  <td><?php $row->'.  $fld['name'] . '(\'asign\')?></td>' . "\n";
    }
    $str .= '</tr>'. "\n";
    $str .= '<?php endwhile;?>' . "\n";
    $str .= '</table>' . "\n";
    $str .= '<?php endif;?>' . "\n";
    $str .= '<p>' . "\n";
    $str .= '  <?php if( $this->params->p > 1 ):?>' . "\n";
    $str .= '  <a href="/' . $tbl .'/index?p=<?php echo $this->params->p - 1 ?>">prev</a>&nbsp;' . "\n";
    $str .= '  <?php endif;?>' . "\n";
    $str .= '  <?php if( $this->max_page < $this->params->p  ):?>' . "\n";
    $str .= '  <a href="/' . $tbl .'/index?p=<?php echo ($this->params->p ? $this->params->p  : 1 ) + 1 ?>">next</a>' . "\n";
    $str .= '  <?php endif;?>' . "\n";
    $str .= '</p>' . "\n";;
    $str .= '</body>' . "\n";
    $str .= '</html>' . "\n";
    return $str;
  }
  
  /**
   * テーブル定義から編集用のフォームを生成する
   * @param テーブル名
   * @return 生成した文字列
   **/
  public function createEditorView( $tbl ){
    $str .= '<html>' . "\n";
    $str .= '<head><title>editor of ' . $tbl . '</title></head>' . "\n";
    $str .= '<body>' . "\n";
    $str .= '<h2>' . $tbl . '</h2>' . "\n";
    $str .= '<?php if( $this->Errors ): ?><p>you have input error!</p><?php endif;?>' . "\n";
    $str .= '<form action="/' .  $tbl.  '/save" method="post">' . "\n";
    $str .= '<table>' . "\n";
    foreach( $this->Catalogs[$tbl]['flds'] as $fld ){
      $str .= '<tr>' . "\n";
      if( $fld['name'] == 'id' ) $str .= '  <th>ID</th><td><?php $this->id(\'asign\')?><?php echo $this->id( \'formHidden\', \'id\' )?></td>' . "\n";
      else                       $str .= '  <th>'. $fld['name']. '</th><td><?php $this->'.  $fld['name'] . '(\'formText\', \'' . $fld['name'].'\')?></td>' . "\n";
      $str .= '</tr>' . "\n";
    }
    $str .= '</table>' . "\n";
    $str .= '<p><input type="submit" value="save" /><input type="reset" value="undo" /></p>' . "\n";
    $str .= '</form>' . "\n";
    $str .= '</body>' . "\n";
    $str .= '</html>' . "\n";
    return $str;
  }
}
