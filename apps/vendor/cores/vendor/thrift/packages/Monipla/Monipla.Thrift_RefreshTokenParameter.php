<?php
/**
 *  @generated
 */
class Thrift_RefreshTokenParameter {
  static $_TSPEC;

  public $clientId = null;
  public $clientSecret = null;
  public $refreshToken = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'clientId',
          'type' => TType::STRING,
          ),
        2 => array(
          'var' => 'clientSecret',
          'type' => TType::STRING,
          ),
        3 => array(
          'var' => 'refreshToken',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['clientId'])) {
        $this->clientId = $vals['clientId'];
      }
      if (isset($vals['clientSecret'])) {
        $this->clientSecret = $vals['clientSecret'];
      }
      if (isset($vals['refreshToken'])) {
        $this->refreshToken = $vals['refreshToken'];
      }
    }
  }

  public function getName() {
    return 'Thrift_RefreshTokenParameter';
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
            $xfer += $input->readString($this->clientId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->clientSecret);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->refreshToken);
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
    $xfer += $output->writeStructBegin('Thrift_RefreshTokenParameter');
    if ($this->clientId !== null) {
      $xfer += $output->writeFieldBegin('clientId', TType::STRING, 1);
      $xfer += $output->writeString($this->clientId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->clientSecret !== null) {
      $xfer += $output->writeFieldBegin('clientSecret', TType::STRING, 2);
      $xfer += $output->writeString($this->clientSecret);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->refreshToken !== null) {
      $xfer += $output->writeFieldBegin('refreshToken', TType::STRING, 3);
      $xfer += $output->writeString($this->refreshToken);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
