<?php
class ImageCompositor {
    const DEFAULT_CROP_SIZE = 600;
    const FLG_SCALE_WIDTH = 1;
    const FLG_SCALE_HEIGHT = 2;

    const SUFFIX_SQUARE     = '_s';
    const SUFFIX_MIDDLE     = '_m';     // 520 × ??
    const SUFFIX_REGULAR    = '_r';     // 1000 × 524

    private $_FrameFilePath = null;
    private $_BaseFilePath = null;
    private $_OutTmpFilePath = null;
    private $_isBaseTmpFile = false;
    private $_isFrameTmpFile = false;
    private $_isOutTmpFile = false;
    private $_MarginLeft = 0;
    private $_MarginRight = 0;
    private $_MarginTop = 0;
    private $_MarginBottom = 0;

    private $_X = 0;
    private $_Y = 0;
    private $_W = 0;
    private $_H = 0;

    /**
     *
     */
    public function __construct($base, $frame, $options = null) {
        if (!is_file($base)) {
            if ($data = @file_get_contents($base)) {
                $base = '/tmp/' . uniqid();
                file_put_contents($base, $data);
                $this->_isBaseTmpFile = true;
            } else {
                throw new Exception ('加工対象ファイルのパスが指定されていません');
            }
        }
        if (!is_file($frame)) {
            if ($data = @file_get_contents($frame)) {
                $frame = '/tmp/' . uniqid();
                file_put_contents($frame, $data);
                $this->_isFrameTmpFile = true;
            } else {
                throw new Exception ('フレームファイルのパスが指定されていません');
            }
        }

        $this->_BaseFilePath = $base;
        $this->_FrameFilePath = $frame;
        if (!is_array($options)) $options = array();
        foreach ($options as $key => $value) {
            if (!$value) continue;
            $key = "_$key";
            $this->$key = $value;
        }

    }

    /**
     * 結合を実行する
     * @param ベースイメージ
     * @param フレームイメージ
     * @return リサイズしたベースイメージ
     */
    public function execute($out = null, $options = null) {
        if (!$out) {
            $this->_OutTmpFilePath = $out = '/tmp/' . uniqid();
            $this->_isOutTmpFile = true;
        }
        $imgBase = new Imagick($this->_BaseFilePath);
        $imgFrame = new Imagick($this->_FrameFilePath);

        // if ( $options['justify'] ) $imgBase = $this->justify        ( $imgBase,  $imgFrame );
        // else                       $imgBase = $this->cutCenterImage ( $imgBase,  $imgFrame );

        if ($options['user_custom']) {
            $imgBase = $this->custom($imgBase, $imgFrame);
        } else {
            $this->justify($imgBase, $imgFrame);
        }

        // 合成 & 保存
        $imgBase->compositeImage($imgFrame, $imgFrame->getImageCompose(), 0, 0);
        $imgBase->writeImages($out, TRUE);
        return $out;
    }

    /**
     * 透過範囲に合わせて中心を基準に画像を切り出す
     * @param ベースイメージ
     * @param フレームイメージ
     * @return リサイズしたベースイメージ
     */
    public function cutCenterImage($imgBase = null, $imgFrame = null) {
        if (!$imgBase || !$imgFrame) {
            $imgBase = new Imagick($this->_BaseFilePath);
            $imgFrame = new Imagick($this->_FrameFilePath);
        }
        $w = $imgFrame->getImageWidth();
        $h = $imgFrame->getImageHeight();

        if ($imgBase->getImageWidth() > $imgBase->getImageHeight()) $imgBase->resizeImage(0, $h, imagick::FILTER_BLACKMAN, true);
        else                                                          $imgBase->resizeImage($w, 0, imagick::FILTER_BLACKMAN, true);

        $x = $imgBase->getImageWidth() / 2 - $w / 2;
        $y = $imgBase->getImageHeight() / 2 - $h / 2;
        $imgBase->cropImage($w, $h, $x, $y);

        $m_width = $this->_MarginLeft + $this->_MarginRight;
        $m_height = $this->_MarginTop + $this->_MarginBottom;

        if ($w > $h) $imgBase->resizeImage($w - $m_width, 0, imagick::FILTER_BLACKMAN, true);
        else           $imgBase->resizeImage(0, $h - $m_height, imagick::FILTER_BLACKMAN, true);
        $imgBase->spliceImage($this->_MarginLeft, $this->_MarginTop, 0, 0);
        $imgBase->spliceImage($this->_MarginRight, $this->_MarginBottom, $imgBase->getImageWidth(), $imgBase->getImageHeight());
        return $imgBase;
    }

    /**
     * 透過範囲に合わせてサイズを自動調整する
     * @param ベースイメージ
     * @param フレームイメージ
     * @return リサイズしたベースイメージ
     */
    public function justify($imgBase = null, $imgFrame = null) {
        if (!$imgBase || !$imgFrame) {
            $imgBase = new Imagick($this->_BaseFilePath);
            $imgFrame = new Imagick($this->_FrameFilePath);
        }
        $w = $imgFrame->getImageWidth();
        $h = $imgFrame->getImageHeight();

        $m_width = $this->_MarginLeft + $this->_MarginRight;
        $m_height = $this->_MarginTop + $this->_MarginBottom;

        if ($imgBase->getImageWidth() > $imgBase->getImageHeight()) {
            $imgBase->resizeImage($w - $m_width, 0, imagick::FILTER_BLACKMAN, true);
            if ($h - $m_height > $imgBase->getImageHeight()) {
                $size = ($h - $m_height - $imgBase->getImageHeight()) / 2;
                $imgBase->spliceImage(0, $size, 0, 0);
                $imgBase->spliceImage(0, $size, $imgBase->getImageWidth(), $imgBase->getImageHeight());
            } // 横長だけどアスペクト比的にフレームの枠よりも縦長
            else {
                $imgBase->resizeImage(0, $h - $m_height, imagick::FILTER_BLACKMAN, true);
                $size = ($w - $m_width - $imgBase->getImageWidth()) / 2;
                $imgBase->spliceImage($size, 0, 0, 0);
                $imgBase->spliceImage($size, 0, $imgBase->getImageWidth(), $imgBase->getImageHeight());
            }
        } else {
            $imgBase->resizeImage(0, $h - $m_height, imagick::FILTER_BLACKMAN, true);
            if ($w - $m_width > $imgBase->getImageWidth()) {
                $size = ($w - $m_width - $imgBase->getImageWidth()) / 2;
                $imgBase->spliceImage($size, 0, 0, 0);
                $imgBase->spliceImage($size, 0, $imgBase->getImageWidth(), $imgBase->getImageHeight());
            } // 縦長だけどアスペクト比的にフレームの枠よりも横長
            else {
                $imgBase->resizeImage($w - $m_width, 0, imagick::FILTER_BLACKMAN, true);
                $size = ($h - $m_height - $imgBase->getImageHeight()) / 2;
                $imgBase->spliceImage(0, $size, 0, 0);
                $imgBase->spliceImage(0, $size, $imgBase->getImageWidth(), $imgBase->getImageHeight());
            }
        }
        $imgBase->spliceImage($this->_MarginLeft, $this->_MarginTop, 0, 0);
        $imgBase->spliceImage($this->_MarginRight, $this->_MarginBottom, $imgBase->getImageWidth(), $imgBase->getImageHeight());
        return $imgBase;
    }

    /**
     * ユーザが調整した画像を合成する
     * @param ベースイメージ
     * @param フレームイメージ
     * @return リサイズしたベースイメージ
     */
    public function custom($imgBase = null, $imgFrame = null) {

        $X = $this->_X;
        $Y = $this->_Y;

        $W = $this->_W;
        $H = $this->_H;

        $frame_W = $imgFrame->getImageWidth();
        $frame_H = $imgFrame->getImageHeight();

        $imgBase->resizeImage($W, $H, imagick::FILTER_BLACKMAN, 0, true);
        $base_W = $imgBase->getImageWidth();
        $base_H = $imgBase->getImageHeight();

        $imgBase->setImageBackgroundColor('#FFFFFF');

        if ($X > 0) {
            $imgBase->spliceImage($X, 0, 0, 0);
        } elseif ($X < 0) {
            $imgBase->cropImage(($base_W + $X), 0, (-1 * $X), 0);
        }
        if ($Y > 0) {
            $imgBase->spliceImage(0, $Y, 0, 0);
        } elseif ($Y < 0) {
            $imgBase->cropImage(0, ($base_H + $Y), 0, (-1 * $Y));
        }
        $base_W = $imgBase->getImageWidth();
        $base_H = $imgBase->getImageHeight();

        $remainder_W = ($frame_W - $base_W);
        if ($remainder_W > 0) {
            $imgBase->spliceImage($remainder_W, 0, $base_W, 0);
        } elseif ($remainder_W < 0) {
            $frame_W_cut = ($X < 0) ? ($frame_W - $X) : $frame_W;
            $imgBase->cropImage($frame_W_cut, 0, 0, 0);
        }
        $remainder_H = ($frame_H - $base_H);
        if ($remainder_H > 0) {
            $imgBase->spliceImage(0, $remainder_H, 0, $base_H);
        } elseif ($remainder_H < 0) {
            $frame_H_cut = ($Y < 0) ? ($frame_H - $Y) : $frame_H;
            $imgBase->cropImage(0, $frame_H_cut, 0, 0);
        }

        return $imgBase;
    }

    /**
     * @param $imagePath
     */
    public static function adjustImageOrientation($imagePath) {
        if (!$imagePath) return ;

        $raw = file_get_contents($imagePath);
        $img = new Imagick();
        $img->readImageBlob($raw);
        $orientation = 0;
        if (function_exists("exif_read_data")) {
            $exif = exif_read_data($imagePath);
            $orientation = $exif['Orientation'];
        }

        $width = $exif['COMPUTED']['Width'];
        $height = $exif['COMPUTED']['Height'];
        switch ((int)$orientation) {
            case 0: #未定義
                break;
            case 1: #通常
                break;
            case 2: #左右反転
                $img->flopImage();
                break;
            case 3: #180°回転
                $img->rotateImage(new ImagickPixel(), 180);
                break;
            case 4: #上下反転
                $img->flipImage();
                break;
            case 5: #反時計回りに90°回転 上下反転
                $img->rotateImage(new ImagickPixel(), 270);
                $img->flipImage();
                break;
            case 6: #時計回りに90°回転
                $img->rotateImage(new ImagickPixel(), 90);
                break;
            case 7: #時計回りに90°回転 上下反転
                $img->rotateImage(new ImagickPixel(), 90);
                $img->flipImage();
                break;
            case 8: #反時計回りに90°回転
                $img->rotateImage(new ImagickPixel(), 270);
                break;
        }
        if ($width && $height) {
            $img->thumbnailImage($width, $height, true);
        }
        $raw = $img->getImageBlob();
        $fh = fopen($imagePath, 'w');
        fwrite($fh, $raw);
        fclose($fh);
        unset($img);
    }

    /**
     * @param $imagePath
     * @param int $width
     * @param int $height
     * @return array|bool
     */
    public static function thumbnailImage($imagePath, $width = 150, $height = 150) {
        if (!$imagePath) return false;

        $raw = file_get_contents($imagePath);
        $img = new Imagick();
        $img->readImageBlob($raw);

        $img_info = array(
            'default_w' => $img->getImageWidth(),
            'default_h' => $img->getImageHeight(),
            'thumbnail_w' => $width,
            'thumbnail_h' => $height
        );

        $img->thumbnailImage($width, $height, true);

        $raw = $img->getImageBlob();
        $fh = fopen($imagePath, 'w');
        fwrite($fh, $raw);
        fclose($fh);
        unset($img);

        return $img_info;
    }

    /**
     * @param $image_path
     * @param int $scale_size
     */
    public static function cropSquareImage($image_path, $scale_size = self::DEFAULT_CROP_SIZE) {
        $raw = file_get_contents($image_path);
        $img = new Imagick();
        $img->readImageBlob($raw);

        $width = $img->getImageWidth();
        $height = $img->getImageHeight();

        if ($width >= $height && $height > $scale_size) {
            $img->scaleImage(0, $scale_size);
            $height = $scale_size;
            $width = $img->getImageWidth();
        } elseif ($height > $width && $width > $scale_size) {
            $img->scaleImage($scale_size, 0);
            $width = $scale_size;
            $height = $img->getImageHeight();
        }

        if ($width != $height) {
            $crop_size = $width > $height ? $height : $width;

            if ($width > $height) {
                $crop_coordinate_x = ($width - $crop_size) / 2;
                $crop_coordinate_y = 0;
            } else {
                $crop_coordinate_y = ($height - $crop_size) / 2;
                $crop_coordinate_x = 0;
            }

            $img->cropImage($crop_size, $crop_size, $crop_coordinate_x, $crop_coordinate_y);
            // remove blank space in gif
            $img->setImagePage(0, 0, 0, 0);
        }

        $raw = $img->getImageBlob();
        $fh = fopen($image_path, 'w');
        fwrite($fh, $raw);
        fclose($fh);
        unset($img);
    }

    /**
     * @param $image_path
     * @param int $custom_width
     * @return Exception
     */
    public static function scaleImageWidth($image_path, $custom_width = 0) {
        if ($custom_width == 0) {
            return new Exception('Illegal custom image size');
        }

        $raw = file_get_contents($image_path);
        $img = new Imagick();
        $img->readImageBlob($raw);

        if ($img->getImageWidth() > $custom_width) {
            $img->scaleImage($custom_width, 0);

            $raw = $img->getImageBlob();
            $fh = fopen($image_path, 'w');
            fwrite($fh, $raw);
            fclose($fh);
        }

        unset($img);
    }

    /**
     * 縦横比を維持したまま、指定サイズの画像を作成する (余白付き)
     * @param $image_path
     * @param int $custom_width
     * @param int $custom_height
     * @param int $r
     * @param int $g
     * @param int $b
     * @return リサイズしたベースイメージ
     */
    public static function scaleImageAspectRetained($image_path, $custom_width = 0, $custom_height = 0, $r = 255, $g = 255, $b = 255) {
        if ($custom_width == 0 || $custom_height == 0) {
            return new Exception('Illegal custom image size');
        }

        $raw = file_get_contents($image_path);
        $img = new Imagick();
        $img->readImageBlob($raw);
        $img->setImageBackgroundColor(new ImagickPixel('rgb(' . $r . ', ' . $g . ', ' . $b . ')'));

        if ($img->getImageWidth() > $custom_width && $img->getImageHeight() > $custom_height) {
            $which = ($custom_width / $img->getImageWidth()) < ($custom_height / $img->getImageHeight()) ? self::FLG_SCALE_WIDTH : self::FLG_SCALE_HEIGHT;
        } else if ($img->getImageWidth() > $custom_width) {
            $which = self::FLG_SCALE_WIDTH;
        } else if ($img->getImageHeight() > $custom_height) {
            $which = self::FLG_SCALE_HEIGHT;
        } else if ($img->getImageHeight() !== 0 && $img->getImageWidth() !== 0) {
            $which = ($custom_width / $img->getImageWidth()) < ($custom_height / $img->getImageHeight()) ? self::FLG_SCALE_WIDTH : self::FLG_SCALE_HEIGHT;
        }

        if ($which === self::FLG_SCALE_WIDTH) {
            $img->scaleImage($custom_width, 0);
            $height = ($custom_height - $img->getImageHeight()) / 2;
            $img->spliceImage(0, $height, 0, 0);
            $img->spliceImage(
                0,
                $custom_height - $img->getImageHeight(),
                0,
                $img->getImageHeight()
            );
        } else if ($which === self::FLG_SCALE_HEIGHT) {
            $img->scaleImage(0, $custom_height);
            $width = ($custom_width - $img->getImageWidth()) / 2;
            $img->spliceImage($width, 0, 0, 0);
            $img->spliceImage(
                $custom_width - $img->getImageWidth(),
                0,
                $img->getImageWidth(),
                0
            );
        } else {
            return new Exception('Illegal image');
        }

        $raw = $img->getImageBlob();
        $fh = fopen($image_path, 'w');
        fwrite($fh, $raw);
        fclose($fh);

        unset($img);
    }

    /**
     * @param $img_path
     * @param $clone_type
     * @return mixed
     */
    public static function cloneImage($img_path, $clone_type) {
        $img_path_info = pathinfo($img_path);
        $clone_img['name'] = $img_path_info['dirname'] . '/' . $img_path_info['filename'] . '_' . $clone_type . '.' . $img_path_info['extension'];

        file_put_contents($clone_img['name'], file_get_contents($img_path));
        return $clone_img;
    }

    /**
     * @param $image_path
     * @return array
     */
    public static function getSize($image_path) {
        $raw = file_get_contents($image_path);
        $img = new Imagick();
        $img->readImageBlob($raw);

        return array($img->getImageWidth(), $img->getImageHeight());
    }

    public function __destruct() {
        if ($this->_isBaseTmpFile) unlink($this->_BaseFilePath);
        if ($this->_isFrameTmpFile) unlink($this->_FrameFilePath);
        if ($this->_isOutTmpFile) unlink($this->_OutTmpFilePath);
    }
}
