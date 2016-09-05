<?php

class FileValidator {

    const DEFAULT_MAX_FILE_SIZE = 500485760;
    const IMAGE_FILE_MAX_FILE_SIZE = 5000000;

    const ERROR_NO_ERROR = 0; // エラーなし
    const ERROR_FILE_NOT_EXIST = 1; // ファイルが存在しない
    const ERROR_FILE_SIZE = 2; // サイズが不適切
    const ERROR_FILE_SIZE_OVER = 3; // サイズがオーバー
    const ERROR_FILE_IS_BROKEN = 4; // ファイルが壊れている
    const ERROR_FILE_NAME_IS_INVALID = 5; // ファイル名が不正
    const ERROR_FILE_IS_NOT_UPLOADED = 6; // アップロードされたファイルではない
    const ERROR_FILE_MIME_TYPE_IS_INVALID = 7; // mime_typeが不正
    const ERROR_FILE_EXTENSION_IS_INVALID = 8; // 拡張子が不正
    const ERROR_FILE_TYPE_IS_NOT_ALLOWED = 9; // 許可されたファイルではない
    const ERROR_OTHER_ERROR = 10; // その他のエラー

    protected static $error_message_keys_dict = array(
        self::ERROR_FILE_NOT_EXIST => 'ERROR_FILE_NOT_EXIST',
        self::ERROR_FILE_SIZE => 'ERROR_FILE_SIZE',
        self::ERROR_FILE_SIZE_OVER => 'ERROR_FILE_SIZE_OVER',
        self::ERROR_FILE_IS_BROKEN => 'ERROR_FILE_IS_BROKEN',
        self::ERROR_FILE_NAME_IS_INVALID => 'ERROR_FILE_NAME_IS_INVALID',
        self::ERROR_FILE_IS_NOT_UPLOADED => 'ERROR_FILE_IS_NOT_UPLOADED',
        self::ERROR_FILE_MIME_TYPE_IS_INVALID => 'ERROR_FILE_MIME_TYPE_IS_INVALID',
        self::ERROR_FILE_EXTENSION_IS_INVALID => 'ERROR_FILE_EXTENSION_IS_INVALID',
        self::ERROR_FILE_TYPE_IS_NOT_ALLOWED => 'ERROR_FILE_TYPE_IS_NOT_ALLOWED',
        self::ERROR_OTHER_ERROR => 'ERROR_OTHER_ERROR',
    );

    public static $error_messages_dict = array(
        self::ERROR_FILE_NOT_EXIST => 'ファイルを選択してください。',
        self::ERROR_FILE_SIZE => 'ファイルサイズが不適切です。',
        self::ERROR_FILE_SIZE_OVER => 'ファイルサイズは、10MBまでです。',
        self::ERROR_FILE_IS_BROKEN => 'ファイルが壊れています。',
        self::ERROR_FILE_NAME_IS_INVALID => 'ファイル名が不正です。',
        self::ERROR_FILE_IS_NOT_UPLOADED => 'アップロードされたファイルではありません。',
        self::ERROR_FILE_MIME_TYPE_IS_INVALID => 'mime_typeが不正です。',
        self::ERROR_FILE_EXTENSION_IS_INVALID => 'ファイルの拡張子が不正です。jpg、png、gif 形式のファイルをアップロードしてください。',
        self::ERROR_FILE_TYPE_IS_NOT_ALLOWED => '許可されたファイルではありません。',
        self::ERROR_OTHER_ERROR => 'エラーが発生しました。',
    );

    const FILE_TYPE_IMAGE = 0;
    const FILE_TYPE_PDF = 1;
    const FILE_TYPE_EXCEL = 2;
    const FILE_TYPE_POWER_POINT = 3;
    const FILE_TYPE_WORD = 4;
    const FILE_TYPE_OTHER = 5;
    const FILE_TYPE_CSV = 6;
    const FILE_TYPE_TEXT = 7;
    const FILE_TYPE_JS = 8;
    const FILE_TYPE_CSS = 9;
    const FILE_TYPE_ARCHIVE = 10;
    const FILE_TYPE_VIDEO = 11;
    const FILE_TYPE_WEB_FONT = 12;

    protected static $ms_office_file_type = array(
        self::FILE_TYPE_WORD,
        self::FILE_TYPE_EXCEL,
        self::FILE_TYPE_POWER_POINT,
    );

    protected static $file_type_extension_dict = array(

        self::FILE_TYPE_IMAGE => array(
            "jpg",
            "jpeg",
            "png",
            "gif",
            "ico",
        ),

        self::FILE_TYPE_WORD => array(
            "doc",
            "docx",
        ),

        self::FILE_TYPE_EXCEL => array(
            "xls",
            "xlsx",
        ),
        self::FILE_TYPE_POWER_POINT => array(
            "ppt",
            "pptx",
        ),

        self::FILE_TYPE_PDF => array(
            "pdf",
        ),

        self::FILE_TYPE_CSV => array(
            "csv",
        ),

        self::FILE_TYPE_TEXT => array(
            'txt'
        ),

        self::FILE_TYPE_JS => array(
            'js'
        ),

        self::FILE_TYPE_CSS => array(
            'css'
        ),

        self::FILE_TYPE_VIDEO => array(
            'mp4'
        ),

        self::FILE_TYPE_ARCHIVE => array(
            'zip'
        ),

        self::FILE_TYPE_WEB_FONT => array(
            'woff',
            'ttf',
            'otf',
            'eot',
            'svg',
            'svgz'
        )
    );

    // TODO
    // http://www.tagindex.com/html5/basic/mimetype.html
    // http://technet.microsoft.com/ja-jp/library/ee309278(v=office.12).aspx
    protected static $allowed_mime_type_dict = array(

        "jpg" => array(
            "image/jpeg",
            "image/jpg",
            "image/jp_",
            "application/jpg",
            "application/x-jp",
            "image/pjpeg",
            "image/pipeg",
            "image/vnd.swiftview-jpeg",
        ),

        "jpeg" => array(
            "image/jpeg",
            "image/jpg",
            "image/jp_",
            "application/jpg",
            "application/x-jp",
            "image/pjpeg",
            "image/pipeg",
            "image/vnd.swiftview-jpeg",
        ),

        "png" => array(
            "image/png",
            "application/png",
            "application/x-png",
        ),

        "gif" => array(
            "image/gif",
        ),

        "pdf" => array(
            "application/pdf",
            "application/x-pdf",
            "application/acrobat",
            "applications/vnd.pdf",
            "text/pdf",
            "text/x-pdf",
        ),

        "xls" => array(
            "application/vnd.ms-excel",
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        ),

        "xlsx" => array(
            "application/vnd.ms-excel",
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        ),

        "ppt" => array(
            "application/vnd.ms-powerpoint",
            "application/vnd.openxmlformats-officedocument.presentationml.presentation",
        ),

        "pptx" => array(
            "application/vnd.ms-powerpoint",
            "application/vnd.openxmlformats-officedocument.presentationml.presentation",
        ),

        "doc" => array(
            "application/msword",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        ),

        "docx" => array(
            "application/msword",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        ),

        "office" => array(
            "application/msword",
            "application/vnd.ms-office",
        ),

        'csv' => array(
            'text/csv',
            'text/plain',
            'application/csv',
            'text/comma-separated-values',
            'application/excel',
            'application/vnd.ms-excel',
            'application/vnd.msexcel',
            'text/anytext',
            'application/octet-stream',
            'application/txt',
        ),

        'txt' => array(
            'text/plain'
        ),

        'js' => array(
            'text/plain',
            'text/javascript',
            'application/javascript'
        ),

        'css' => array(
            'text/plain',
            'text.css'
        ),

        'mp4' => array(
            'video/mp4'
        ),

        'zip' => array(
            'application/zip',
            'application/x-zip-compressed'
        ),

        'woff' => array(
            'application/font-woff',
            'application/octet-stream'
        ),

        'ttf' => array(
            'application/x-font-ttf',
            'application/font-sfnt'
        ),

        'otf' => array(
            'application/x-font-opentype',
            'application/vnd.ms-opentype'
        ),

        'eot' => array(
            'application/vnd.ms-fontobject'
        ),

        'svg' => array(
            'image/svg+xml',
            'text/plain',
        ),

        'svgz' => array(
            'image/svg+xml',
            'text/plain',
        ),

    );

    protected static $oo_xml_tag_mime_type = array(
        "word" => "application/msword",
        "xl" => "application/vnd.ms-excel",
        "ppt" => "application/vnd.ms-powerpoint"
    );

    protected $error_code;
    protected $error_message_key;
    protected $error_message;
    protected $file;
    protected $file_type;

    protected $path;
    protected $name;
    protected $extension;
    protected $size;
    protected $mime_type;
    protected $logger;

    public function __construct($file, $file_type) {
        $this->file = $file;
        $this->file_type = $file_type;
        $this->setError(self::ERROR_NO_ERROR);
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $error_code
     */
    protected function setError($error_code) {
        $this->error_code = $error_code;
        $this->error_message_key = self::$error_message_keys_dict[$error_code];
        $this->error_message = self::$error_messages_dict[$error_code];
    }

    /**
     * @return bool
     */
    protected function isValidErrorCode() {

        // ファイルのエラーコードをチェック
        $error = $this->file['error'];

        if ($error == null) {
            return true;
        }

        // 配列は除外する。
        if (is_array($error)) {
            $this->setError(self::ERROR_OTHER_ERROR);
            return false;
        }

        switch ($error) {

            // エラーじゃない場合
            case UPLOAD_ERR_OK:
                break;

            case UPLOAD_ERR_NO_FILE:
                $this->setError(self::ERROR_FILE_NOT_EXIST);
                break;

            case UPLOAD_ERR_PARTIAL:
                $this->setError(self::ERROR_FILE_IS_BROKEN);
                break;

            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $this->setError(self::ERROR_FILE_SIZE);
                break;

            case UPLOAD_ERR_NO_TMP_DIR:
            case UPLOAD_ERR_CANT_WRITE:
            case UPLOAD_ERR_EXTENSION:
                $this->setError(self::ERROR_OTHER_ERROR);
                break;
        }

        if ($this->error_code != self::ERROR_NO_ERROR) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function isValidFileName() {

        // ファイル名
        $this->path = $this->file['name'];

        $this->name = end(explode('/', $this->path));

        // ファイル名が1バイト以上あるかチェック(0バイトを弾きたい場合)
        if ($this->name === '') {
            $this->setError(self::ERROR_FILE_NAME_IS_INVALID);
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    protected function isValidFileNameCharacter() {
        return preg_match( '#^[０-９一-龠ぁ-んァ-ヶ0-9a-zA-Z-_\#:%\.@()/\?&=~]+$#', $this->name);
    }

    /**
     * @return bool
     */
    protected function isValidFileSize() {

        $this->size = filesize($this->path);

        if($this->size <= 0) {
            $this->setError(self::ERROR_FILE_SIZE);
            return false;
        }

        if ($this->size > self::DEFAULT_MAX_FILE_SIZE) {
            $this->setError(self::ERROR_FILE_SIZE_OVER);
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    protected function isValidUploadFile() {

        if (!is_uploaded_file($this->tmp_name)) {
            $this->setError(self::ERROR_FILE_IS_NOT_UPLOADED);
            return false;
        }
        return true;
    }

    protected function getMsOfficeMimeType() {

        // for office 2007
        if ($this->mime_type == "application/zip" || $this->mime_type == "application/x-zip") {

            $work_dir_path = '/tmp/' . date('YmdHis') . "_" . substr(uniqid(rand(), 1), 1, 5);
            $zip_file_path = $work_dir_path . '/' . 'ooxml_' . date("YmdHis") . '.zip';
            $xml_file_path = $work_dir_path . "/[Content_Types].xml"; //[Content_Type].xmlファイルの保存パス

            mkdir($work_dir_path . '/', 0777);
            //system("chmod 777 $work_dir_path");

            copy($this->path, $zip_file_path);
            shell_exec("unzip $zip_file_path \\\[Content_Types\\\].xml -d $work_dir_path");

            if (preg_match("/.*?<Override PartName=\"\/([^\/]+)\/.*/m", file_get_contents($xml_file_path), $match)) {
                $t = $match[1];
                if (array_key_exists($t, self::$oo_xml_tag_mime_type)) {
                    $this->mime_type = self::$oo_xml_tag_mime_type[$t];
                }
            }

            unlink($xml_file_path);
            unlink($zip_file_path);

            if (is_dir($work_dir_path)) {
                rmdir($work_dir_path);
            }

        } else {

            if (($fh = fopen($this->path, "rb")) !== FALSE) {

                // 0バイト目からの値を判定する
                fseek($fh, 0);
                $hex = strtoupper(bin2hex(fread($fh, 10)));

                // Office系のファイルチェック
                if (preg_match("/^D0CF11E0A1B11AE100.*/", $hex)) {

                    // ここまで到達すればOfficeなのは間違いないので、値をいれておく
                    $this->mime_type = "application/msword";

                    // 512バイト目からの値を判定する
                    fseek($fh, 512);
                    $hex = strtoupper(bin2hex(fread($fh, 10)));

                    // Word
                    if (preg_match("/^(?:ECA5C100).*/", $hex)) {
                        $this->mime_type = "application/msword";
                    } // Excel
                    else if (preg_match("/^(?:FDFFFFFF(?:(?:10|1F|22|23|28|29)02|20000000)|0908100000060500).*/", $hex)) {
                        $this->mime_type = "application/vnd.ms-excel";
                    } // PowerPoint
                    else if (preg_match("/^(?:FDFFFFFF(?:(?:0E|1C|43|10)000000)|006E1EF0|A0461DF0).*/", $hex)) {
                        $this->mime_type = "application/vnd.ms-powerpoint";
                    }
                }
                fclose($fh);
            }
        }
    }

    protected function getMimeType() {

        $this->logger->debug("FileValidator#getMimeType() path: " . $this->path);

        $finfo = new finfo(FILEINFO_MIME_TYPE);

        if ($finfo === false) {
            $this->setError(self::ERROR_FILE_MIME_TYPE_IS_INVALID);
            return false;
        }

        $this->mime_type = $finfo->file($this->path);

        $this->logger->debug("FileValidator#getMimeType() mime_type first: " . $this->mime_type);

        // Office系のチェック
        if (in_array($this->file_type, self::$ms_office_file_type)) {

            // mime_typeが含まれていない場合
            if (!in_array($this->mime_type, self::$allowed_mime_type_dict[$this->extension])) {
                $this->getMsOfficeMimeType();
            }
        }

        $this->logger->debug("FileValidator#getMimeType() mime_type second: : " . $this->mime_type);
    }

    /**
     * @return bool
     */
    protected function isValidMimeType() {

        if ($this->extension == "ico") {
            return true;
        }

        // mimeTypeを取得
        $this->getMimeType();

        // mime_typeが無い場合はエラー
        if (!$this->mime_type) {
            $this->setError(self::ERROR_FILE_MIME_TYPE_IS_INVALID);
            return false;
        }

        // Office系の場合
        if (in_array($this->file_type, self::$ms_office_file_type)) {
            if (!in_array($this->mime_type, self::$allowed_mime_type_dict[$this->extension])) {
                if (!in_array($this->mime_type, self::$allowed_mime_type_dict["office"])) {
                    $this->setError(self::ERROR_FILE_TYPE_IS_NOT_ALLOWED);
                    return false;
                }
            }
            // それ以外の場合
        } else {
            if (!in_array($this->mime_type, self::$allowed_mime_type_dict[$this->extension])) {
                $this->setError(self::ERROR_FILE_TYPE_IS_NOT_ALLOWED);
                return false;
            }
        }

        if (in_array($this->mime_type, self::$allowed_mime_type_dict['jpg']) ||
            in_array($this->mime_type, self::$allowed_mime_type_dict['jpeg']) ||
            in_array($this->mime_type, self::$allowed_mime_type_dict['png']) ||
            in_array($this->mime_type, self::$allowed_mime_type_dict['gif'])
        ) {
            ImageCompositor::adjustImageOrientation($this->path);
        }
        
        return true;
    }

    protected function isValidExtension() {

        $this->extension = substr($this->name, strrpos($this->name, '.') + 1);

        // 拡張子が無い場合はエラー
        if (!$this->extension) {
            $this->setError(self::ERROR_FILE_EXTENSION_IS_INVALID);
            return false;
        }

        $this->extension = strtolower($this->extension);

        //　拡張子が含まれているかどうか？
        if (!in_array($this->extension, self::$file_type_extension_dict[$this->file_type])) {
            $this->setError(self::ERROR_FILE_EXTENSION_IS_INVALID);
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function isValidFile() {

        // ファイルの存在チェック
        if (!isset($this->file)) {
            $this->setError(self::ERROR_FILE_NOT_EXIST);
            return false;
        }

        // ファイルのエラーコードをチェック
        if (!$this->isValidErrorCode()) {
            return false;
        }

        // ファイル名のチェック
        if (!$this->isValidFileName()) {
            return false;
        }

        // 使用可能な文字列かどうかのチェック
        if (!$this->isValidFileNameCharacter()) {
            return false;
        }

        // 拡張子のチェック
        if (!$this->isValidExtension()) {
            return false;
        }

        // ファイルサイズのチェック
        if (!$this->isValidFileSize()) {
            return false;
        }

        // mime_typeのチェック
        if (!$this->isValidMimeType()) {
            return false;
        }

        return true;
    }

    /**
     * S3にアップロードする前にファリルをバリデータする
     * @return bool
     */
    public function validateFacebookEntryImage() {
        // ファイルの存在チェック
        if (!isset($this->file)) {
            $this->setError(self::ERROR_FILE_NOT_EXIST);
            return false;
        }

        // ファイルのエラーコードをチェック
        if (!$this->isValidErrorCode()) {
            return false;
        }

        // ファイル名のチェック
        if (!$this->isValidFileName()) {
            return false;
        }

        // 使用可能な文字列かどうかのチェック
        if (!$this->isValidFileNameCharacter()) {
            return false;
        }

        // ファイルサイズのチェック
        if (!$this->isValidFileSize()) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getFileInfo() {

        return array(
            "name" => $this->name,
            "path" => $this->path,
            "extension" => $this->extension,
            "size" => $this->size,
            "mime_type" => $this->mime_type,
            "file_type" => $this->file_type,
        );
    }

    /**
     * @return mixed
     */
    public function getErrorCode() {
        return $this->error_code;
    }

    /**
     * @return mixed
     */
    public function getErrorMessageKey() {
        return $this->error_message_key;
    }

    /**
     * @return mixed
     */
    public function getErrorMessage() {
        return $this->error_message;
    }
}