<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class UploadFile extends aafwEntityBase {
    protected $_Relations = array(
        'Users' => array(
            'user_id' => 'id'
        )
    );

    const FILE_LIST_PATTERN = 'W:%dpx<br>H:%dpx';
    const FILE_EDIT_PATTER = '%d × %d - ';

    private static $upload_file_types = array(
        FileValidator::FILE_TYPE_IMAGE => '画像',
        FileValidator::FILE_TYPE_PDF => 'PDF',
        FileValidator::FILE_TYPE_EXCEL => 'Excel',
        FileValidator::FILE_TYPE_OTHER => 'その他',
        FileValidator::FILE_TYPE_CSV => 'CSV',
        FileValidator::FILE_TYPE_JS => 'JS',
        FileValidator::FILE_TYPE_CSS => 'CSS',
        FileValidator::FILE_TYPE_WORD => 'DOC',
        FileValidator::FILE_TYPE_POWER_POINT => 'PPT',
        FileValidator::FILE_TYPE_TEXT => 'TXT',
        FileValidator::FILE_TYPE_VIDEO => 'MP4',
        FileValidator::FILE_TYPE_ARCHIVE => 'ZIP'
    );

    private static $upload_file_preview_kind = array(
        FileValidator::FILE_TYPE_PDF,
        FileValidator::FILE_TYPE_JS,
        FileValidator::FILE_TYPE_CSS,
        FileValidator::FILE_TYPE_ARCHIVE
    );

    private static $upload_file_previews = array(
        FileValidator::FILE_TYPE_PDF => '/img/file/pdf.png',
        FileValidator::FILE_TYPE_JS => '/img/file/js.png',
        FileValidator::FILE_TYPE_CSS => '/img/file/css.png',
        FileValidator::FILE_TYPE_ARCHIVE => '/img/file/zip.png'
    );

    public function getFileType() {
        return self::$upload_file_types[$this->type];
    }

    public function getFilePreview() {
        if ($this->type == FileValidator::FILE_TYPE_IMAGE) {
            return $this->url;
        } elseif (in_array($this->type, self::$upload_file_preview_kind)) {
            return 'http:' . config('Static.Url') . self::$upload_file_previews[$this->type];
        } else {
            return 'http:' . config('Static.Url') . '/img/file/other.png';
        }
    }

    public function getFileSize() {
        $sizeKB = $this->size / 1024;

        if ($sizeKB > 1024) {
            return number_format($sizeKB/1024, 2, ',', '.') . ' MB';
        } else {
            return number_format($sizeKB, 2, ',', '.') . ' KB';
        }
    }

    public function getPhotoSize($photo_format) {
        if ($this->type != FileValidator::FILE_TYPE_IMAGE) {
            return $photo_format == self::FILE_LIST_PATTERN ? '-' : '';
        }

        $extra_data = json_decode($this->extra_data);
        return sprintf($photo_format, $extra_data->default_w, $extra_data->default_h);
    }

    public function getThumbnailPhoto() {
        if ($this->type == FileValidator::FILE_TYPE_IMAGE) {
            $extra_data = json_decode($this->extra_data);
            return $extra_data->thumbnail_url;
        } elseif (in_array($this->type, self::$upload_file_preview_kind)) {
            return 'http:' . config('Static.Url') . self::$upload_file_previews[$this->type];
        } else {
            return 'http:' . config('Static.Url') . '/img/file/other.png';
        }
    }

    public function getPhotoWidth(){
        $extra_data = json_decode($this->extra_data);
        return $extra_data->default_w;
    }

    public function getPhotoHeight(){
        $extra_data = json_decode($this->extra_data);
        return $extra_data->default_h;
    }
}