<?php
/*********************************
 * MVCのCのCの中身
 * @author t.ishida
 * @cre    2008/02/24
 **********************************/
AAFW::import ( 'jp.aainc.aafw.aafwValidatorBase' );
AAFW::import ( 'jp.aainc.aafw.aafwValidator' );
AAFW::import ( 'jp.aainc.aafw.aafwApplicationConfig' );
AAFW::import ( 'jp.aainc.aafw.base.aafwActionBase' );

abstract class aafwPOSTActionBase extends aafwActionBase {
  protected $ContainerName = '';
  protected $Form          = array (
    'package'  => '',
    'action'   => '',
    );
  public function doService ( ) {
    $this->ActionForm = $this->getActionContainer('Result');
    $ret = $this->doAction();
    if ( $this->Data['saved'] ) {
      $this->resetActionContainerByKey('Result');
    } else if($this->Data['destroyContainer']) {
      $this->resetActionContainerByName();
    } else {
      $this->setActionContainers(
          array(
          'Result' => $this->POST
          )
      );
    }
    // TODO: save モードの時にはワンタイムトークンを自動比較するとかそういう専用のベースを設けるとか
    return $ret;
  }

  abstract function doAction ();

  // オーバーライド
  public function run () {
    if ( !$this->issetContainerName() )
      throw new Exception ('ContainerNameを設定してください');

    $methods = get_class_methods( $this );
    $this->resetActionContainerByKey('ValidateError');

    if ( !$this->force && $this->GET['req'] =='PHP' && !preg_match ('#(?:redirect|404|not found|403|forbidden)#', $ret ) )
      throw new Exception ('POSTアクションではリダイレクトしてください');

    if ( !$this->force && $this->GET['req'] =='PHP' &&  !$this->Form['action'] )
      throw new Exception ( 'HTMLアクセスでformのactionの指定が有りません' );

    if ( $this->SERVER['REQUEST_METHOD'] == 'GET' ) return 403;

    ///
    /// まずはじめに呼ばれるメソッド
    ///
    if( in_array( 'doThisFirst', $methods ) ){
      $ret = $this->doThisFirst();
      if( preg_match( '#^redirect|404|403#i', $ret ) ) {
        if( count( $this->_Plugins['Finally'] ) )
          foreach( $this->_Plugins['Finally'] as $c ) $c->doService();
        return $ret;
      }
    }

    ///
    /// プラグイン( First )
    ///
    if( count( $this->_Plugins['First'] ) ){
      foreach( $this->_Plugins['First'] as $c ){
        $ret = $c->doService();
        if( preg_match( '#^redirect|404|403#i', $ret ) ){
          if( count( $this->_Plugins['Finally'] ) )
            foreach( $this->_Plugins['Finally'] as $c ) $c->doService();
          return $ret;
        }
      }
    }

    ///
    /// validation前に呼ばれるメソッド
    ///
    if( in_array( 'beforeValidate', $methods ) ){
      if( preg_match( '#^redirect|404|403#i', $ret = $this->beforeValidate() ) ) {
        if( count( $this->_Plugins['Finally'] ) )
          foreach( $this->_Plugins['Finally'] as $c ) $c->doService();
        return $ret;
      }
    }

    ///
    /// バリデーション
    ///
    if( $this->Validator ){
      $this->Validator->setParams(
        $this->POST,
        $this->GET,
        $this->SESSION,
        $this->COOKIE,
        $this->FILES,
        $this->ENV,
        $this->SERVER,
        $this->REQUEST ,
        $this->Settings
      );
      if( preg_match( '#(?:redirect|404|not found|403|forbidden)#', $ret = $this->Validator->validate() ) ) return  $ret;
      if( !$ret ){
        $this->Data = $this->Validator->getData();
        if( count( $this->_Plugins['Finally'] ) ){
          foreach( $this->_Plugins['Finally'] as $c ){
            $c->doService();
          }
        }

        $this->setActionContainers(
            array(
                'Result' => array(),
                'ValidateError' => $this->POST,
                'Errors' => $this->Validator
            )
        );

        return $this->getFormURL();
      }
    } else {
      if( in_array( 'validate', $methods ) ){
        if ( $this->ValidatorDefinition ) {
          $validator = new aafwValidator( $this->ValidatorDefinition );
          $this->Validator = $this->Data['validator'] = $validator;
          if ( !$validator->validate ( $this->REQUEST ) ){
            if( count( $this->_Plugins['Finally'] ) )
              foreach( $this->_Plugins['Finally'] as $c ) $c->doService();

            $this->setActionContainers(
                array(
                    'Result' => array(),
                    'ValidateError' => $this->POST,
                    'Errors' => $validator
                )
            );

            return $this->getFormURL();
          }
        }
        if( preg_match( '#(?:redirect|404|not found|403|forbidden)#', $ret = $this->validate() ) ) {
          if( count( $this->_Plugins['Finally'] ) )
            foreach( $this->_Plugins['Finally'] as $c ) $c->doService();
          return $ret;
        }
        if( !$ret )  {
          if ( $this->Validator ) $this->Errors = $this->Validator;

          $this->setActionContainers(
              array(
                  'Result' => array(),
                  'ValidateError' => $this->POST,
                  'Errors' => $this->Errors
              )
          );

          return $this->getFormURL();
        }
      }
      elseif ( $this->ValidatorDefinition ) {
        $validator = new aafwValidator( $this->ValidatorDefinition );
        if ( !$validator->validate ( $this->REQUEST ) ){
          $this->Validator = $this->Data['validator'] = $validator;
          if( count( $this->_Plugins['Finally'] ) )
            foreach( $this->_Plugins['Finally'] as $c ) $c->doService();

          $this->setActionContainers(
              array(
                  'Result' => array(),
                  'ValidateError' => $this->POST,
                  'Errors' => $validator
              )
          );

          return $this->getFormURL();
        }
      } else{
        throw new Exception( get_class( $this ) . 'にvalidateメソッドを実装して下さい。' );
      }
    }

    ///
    /// validation後に呼ばれるメソッド
    ///
    if( in_array( 'afterValidate'  , $methods ) )   $this->afterValidate();

    ///
    /// プラグイン( BeforeService )
    ///
    if( count( $this->_Plugins['BeforeService'] ) ){
      foreach( $this->_Plugins['BeforeService'] as $c ){
        $ret = $c->doService();
        if( preg_match( '#^redirect *:#i', $ret ) ){
          if( count( $this->_Plugins['Finally'] ) )
            foreach( $this->_Plugins['Finally'] as $c ) $c->doService();
          return $ret;
        }
      }
    }

    ///
    /// 主処理前に呼ばれるメソッド
    ///
    if( in_array( 'beforeDoService', $methods ) )   $this->beforeDoService();

    ///
    /// 主処理
    ///
    $action_ret = $this->doService();
    if( !$action_ret )   return $this->ErrorPage ;

    ///
    /// 主処理後に呼ばれるメソッド
    ///
    if( in_array( 'afterDoService', $methods ) )    $this->afterDoService();

    ///
    /// プラグイン( Last )
    ///
    if( count( $this->_Plugins['Last'] ) ){
      foreach( $this->_Plugins['Last'] as $c ){
        $ret = $c->doService();
        if( preg_match( '#^redirect|404|403#i', $ret ) ){
          if( count( $this->_Plugins['Finally'] ) )
            foreach( $this->_Plugins['Finally'] as $c ) $c->doService();
          return $ret;
        }
      }
    }

    ///
    /// プラグイン( Finally )
    ///
    if( count( $this->_Plugins['Finally'] ) ){
      foreach( $this->_Plugins['Finally'] as $c ){
        $ret = $c->doService();
        if( preg_match( '#^redirect|404|403#i', $ret ) ){
          return $ret;
        }
      }
    }

    ///
    /// 本当に最後に呼ばれるメソッド
    ///
    if( in_array( 'doThisLast', $methods ) )        $this->doThisLast();

    return $action_ret;
  }

  public function getFormURL () {
    $form_url = null;
    if ( $this->Form['package'] ) $form_url = 'redirect: /' . preg_replace ( '#^/#' , '', $this->Form['package'] ) . '/' . $this->Form['action'];
    else                          $form_url = 'redirect: /' . preg_replace ( '#^/#' , '', $this->Form['action'] );
    $params = $this->REQUEST;
    return preg_replace_callback ( '#\{(.+?)\}#', function ( $m ) use ( $params ) {
      return $params[$m[1]];
    }, $form_url );
  }
}
