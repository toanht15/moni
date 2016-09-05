<?php
/**
 *  @generated
 */
class Thrift_SocialAccessToken {
  static $_TSPEC;

  public $accessToken = null;
  public $socialMediaType = null;
  public $snsAccessToken = null;
  public $snsRefreshToken = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'accessToken',
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
      if (isset($vals['accessToken'])) {
        $this->accessToken = $vals['accessToken'];
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
    return 'Thrift_SocialAccessToken';
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
            $xfer += $input->readString($this->accessToken);
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
    $xfer += $output->writeStructBegin('Thrift_SocialAccessToken');
    if ($this->accessToken !== null) {
      $xfer += $output->writeFieldBegin('accessToken', TType::STRING, 1);
      $xfer += $output->writeString($this->accessToken);
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
