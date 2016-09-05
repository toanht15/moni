<?php
/**
 * 画像に文字列を合成
 **/

class ImageGenerator {

    private $_BaseTmpFilePath = null;
    private $_OutTmpFilePath  = null;
    private $_isBaseTmpFile   = false;
    private $_isOutTmpFile    = false;

    private $_FontFilePath    = '';
    private $_FontColor       = '';
    private $_Gravity         = '';

    private $_ArrDrawString   = array();

    /**
     * コンストラクタ
     **/
    public function __construct( $base = null, $options = array()){

        if( !is_file( $base )){
            if( $data = @file_get_contents( $base )){
                $base = '/tmp/' . uniqid();
                file_put_contents( $base, $data );
                $this->_isBaseTmpFile = true;
            } else{
                throw new Exception ( 'The path of the file is not specified' );
            }
        }
        $this->_BaseTmpFilePath = $base;

        if( !is_array( $options )) $options = array();
        foreach( $options as $key => $value ){
            if( !$value ) continue;
            $key        = "_$key";
            $this->$key = $value;
        }

        if( !$this->_FontFilePath ) $this->_FontFilePath = '../docroot_static/web_font/mplus-2p-regular.ttf';
        if( !$this->_FontColor )    $this->_FontColor    = '#696969';
        if( !$this->_Gravity )      $this->_Gravity      = imagick::GRAVITY_NORTHWEST;
        $this->_ArrDrawString = array();
    }

    /**
     * デストラクタ
     **/
    public function __destruct () {
        if( is_file( $this->_isBaseTmpFile )) unlink( $this->_BaseFilePath );
        if( is_file( $this->_isOutTmpFile ))  unlink( $this->_OutTmpFilePath );
    }

    /**
     * 1行表示の文字列情報をセットする
     * @param array $strInfo(String, FontSize, Align, Width, Height) 文字情報
     * @return bool
     * @throws Exception
     */
    public function setStringLine( $strInfo = array()){
        if( !count( $strInfo ) || !$strInfo['String'] ){
            throw new Exception ( 'There is no character information' );
        }
        $strInfo['Align']       = imagick::ALIGN_LEFT;
        $strInfo['Coordinate']  = true;
        $strInfo['String']      = mb_substr($strInfo['String'], 0, intval($strInfo['InputWidthSize']/$strInfo['FontSize']), 'UTF-8');
        $strInfo['Height']      = $strInfo['Height'] + ($strInfo['InputHeightSize'] - $strInfo['FontSize'])/2;
        $this->_ArrDrawString[] = $strInfo;
        return true;
    }

    /**
     * 改行表示の文字列情報をセットする
     * @param array $strInfo(String, FontSize, Width, Height, ParNum, Line, Space) 文字情報
     * @return bool
     * @throws Exception
     */
    public function setStringArea( $strInfo = array()){
        if( !count( $strInfo ) || !isset($strInfo['String']) || is_null($strInfo['String']) || $strInfo['String'] === false ){
            throw new Exception ( 'There is no character information' );
        }

        // 行分けが完了したデータ
        $arr_contents = array();
        // 行数
        $line = 1;
        // 表示用本文
        // 文字列を１文字ずつ配列に持つ
        $strings  = preg_split("/\R/", $strInfo['String']);

        //長い行に改行を追加する
        $contents = $this->addBreakLine($strings, intval($strInfo['ContentWidth']/$strInfo['FontSize']));

        // 始まりの座標
        $width  = $strInfo['Width'];
        $height = $strInfo['Height'] + 3;
        $space  = $strInfo['FontSize'] * 1.5;
        $max_row = intval(($strInfo['ContentHeight'] - $strInfo['FontSize']) / $space) + 1;

        foreach( $contents as $element ){
            // 本文中に改行がある場合はカウントリセット＋行数加算

            if( $line > 1 ) $height += $space;
            $arr_contents[$line] = array(
                'String'   => $element,
                'FontSize' => $strInfo['FontSize'],
                'Align'    => imagick::ALIGN_LEFT,
                'Width'    => $width,
                'Height'   => $height,
            );
            $line++;
            if ($line > $max_row) break;
        }

        foreach($arr_contents as $content) {
            $this->_ArrDrawString[] = $content;
        }
        return true;
    }

    /**
     * ファイルを描き出す
     * @param アウトプットするPATH
     * @return 合成したイメージPATH
     **/
    public function toDraw( $out = null ){
        if( !$out ){
            $this->_OutTmpFilePath = $out = '/tmp/' . uniqid();
            $this->_isOutTmpFile   = true;
        }

        // 画像の読み込み
        $imgBase = new Imagick( $this->_BaseTmpFilePath );
        // 文字色指定に使うクラス
        $pixel   = new ImagickPixel();
        // 文字列を書き込んでくれるクラス
        $imgDraw = new ImagickDraw();

        // フォントの指定
        $imgDraw->setFont( $this->_FontFilePath );

        // 文字色を指定
        $pixel->setColor( $this->_FontColor );
        $imgDraw->setFillColor( $pixel );

        // 座標の基点を否定
        $imgDraw->setGravity( $this->_Gravity );

        foreach( $this->_ArrDrawString as $strInfo ){
            $imgDraw = $this->addString( $strInfo, $imgDraw, $imgBase );
        }

        // 画像へ文字列を合成！
        $imgBase->drawImage( $imgDraw );
        // ファイルとして出力
        $imgBase->writeImage( $out );
        // お掃除
        $imgBase->destroy();
        // 出力PATHを返す
        return $out;
    }

    /**
     * 描き出す文字の情報を格納する
     * @param array $strInfo(String, FontSize, Align, Width, Height, Coordinate) 文字情報
     * @param null $imgDraw 文字情報のオブジェクト
     * @param null $imgBase 画像情報のオブジェクト
     * @return null 情報を格納した文字情報のオブジェクト
     * @throws Exception
     */
    public function addString( $strInfo = array(), $imgDraw = null, $imgBase = null ){
        if( !$imgDraw ){
            throw new Exception ( '[imgDraw] There is no object of character information' );
        }
        if( !$imgBase ){
            $imgBase  = new Imagick( $this->_BaseTmpFilePath );
        }

        // 文字の位置
        $imgDraw->setTextAlignment( $strInfo['Align'] );
        // フォントサイズ
        $imgDraw->setFontSize( $strInfo['FontSize']  );
        // 実際に表示したときのサイズ
        $metrics = $imgBase->queryFontMetrics( $imgDraw, $strInfo['String'] );
        // 表示する座標
        $width  = $strInfo['Width'] + 5;
        $height = $strInfo['Height'] + $metrics['ascender'];

        // 文字列の画像に対する位置と表示内容を指定
        $imgDraw->annotation( $width, $height, $strInfo['String'] );

        return $imgDraw;
    }

    private function addBreakLine($strings, $max_length) {
        $contents = array();
        foreach ($strings as $element) {
            while (mb_strlen($element, 'UTF-8') > $max_length) {
                $contents[] = mb_substr($element, 0, $max_length, 'UTF-8');
                $element = mb_substr($element, $max_length, null, 'UTF-8');
            }
            $contents[] = $element;
        }
        return $contents;
    }


}
