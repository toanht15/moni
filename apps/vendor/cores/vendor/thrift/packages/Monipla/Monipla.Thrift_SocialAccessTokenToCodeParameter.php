<?php
/**
 *  @generated
 */
class Thrift_SocialAccessTokenToCodeParameter {
  static $_TSPEC;

  public $code = null;
  public $socialMediaType = null;
  public $snsAccessToken = null;
  public $snsRefreshToken = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'code',
          'type' => TType::STRING,
          ),
        2 => array(
          'var' => 'socialMediaType',
          'type' => TType::STRING,
          ),
        3 => array(
          'var' => 'snsAccessToken',
          'type' => TType::STRING,
          ),
        4 => array(
          'var' => 'snsRefreshToken',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['code'])) {
        $this->code = $vals['code'];
      }
      if (isset($vals['socialMediaType'])) {
        $this->socialMediaType = $vals['socialMediaType'];
      }
      if (isset($vals['snsAccessToken'])) {
        $this->snsAccessToken = $vals['snsAccessToken'];
      }
      if (isset($vals['snsRefreshToken'])) {
        $this->snsRefreshToken = $vals['snsRefreshToken'];
      }
    }
  }

  public function getName() {
    return 'Thrift_SocialAccessTokenToCodeParameter';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->code);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->socialMediaType);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->snsAccessToken);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->snsRefreshToken);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('Thrift_SocialAccessTokenToCodeParameter');
    if ($this->code !== null) {
      $xfer += $output->writeFieldBegin('code', TType::STRING, 1);
      $xfer += $output->writeString($this->code);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->socialMediaType !== null) {
      $xfer += $output->writeFieldBegin('socialMediaType', TType::STRING, 2);
      $xfer += $output->writeString($this->socialMediaType);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->snsAccessToken !== null) {
      $xfer += $output->writeFieldBegin('snsAccessToken', TType::STRING, 3);
      $xfer += $output->writeString($this->snsAccessToken);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->snsRefreshToken !== null) {
      $xfer += $output->writeFieldBegin('snsRefreshToken', TType::STRING, 4);
      $xfer += $output->writeString($this->snsRefreshToken);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
