<?php
/**
 *  @generated
 */
class Thrift_ExchangeAccessTokenParameter {
  static $_TSPEC;

  public $socialMediaType = null;
  public $snsAccessToken = null;
  public $snsUserId = null;
  public $clientId = null;
  public $scopes = null;
  public $snsRefreshToken = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'socialMediaType',
          'type' => TType::STRING,
          ),
        2 => array(
          'var' => 'snsAccessToken',
          'type' => TType::STRING,
          ),
        3 => array(
          'var' => 'snsUserId',
          'type' => TType::STRING,
          ),
        4 => array(
          'var' => 'clientId',
          'type' => TType::STRING,
          ),
        5 => array(
          'var' => 'scopes',
          'type' => TType::STRING,
          ),
        6 => array(
          'var' => 'snsRefreshToken',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['socialMediaType'])) {
        $this->socialMediaType = $vals['socialMediaType'];
      }
      if (isset($vals['snsAccessToken'])) {
        $this->snsAccessToken = $vals['snsAccessToken'];
      }
      if (isset($vals['snsUserId'])) {
        $this->snsUserId = $vals['snsUserId'];
      }
      if (isset($vals['clientId'])) {
        $this->clientId = $vals['clientId'];
      }
      if (isset($vals['scopes'])) {
        $this->scopes = $vals['scopes'];
      }
      if (isset($vals['snsRefreshToken'])) {
        $this->snsRefreshToken = $vals['snsRefreshToken'];
      }
    }
  }

  public function getName() {
    return 'Thrift_ExchangeAccessTokenParameter';
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
            $xfer += $input->readString($this->socialMediaType);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->snsAccessToken);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->snsUserId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->clientId);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->scopes);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 6:
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
    $xfer += $output->writeStructBegin('Thrift_ExchangeAccessTokenParameter');
    if ($this->socialMediaType !== null) {
      $xfer += $output->writeFieldBegin('socialMediaType', TType::STRING, 1);
      $xfer += $output->writeString($this->socialMediaType);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->snsAccessToken !== null) {
      $xfer += $output->writeFieldBegin('snsAccessToken', TType::STRING, 2);
      $xfer += $output->writeString($this->snsAccessToken);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->snsUserId !== null) {
      $xfer += $output->writeFieldBegin('snsUserId', TType::STRING, 3);
      $xfer += $output->writeString($this->snsUserId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->clientId !== null) {
      $xfer += $output->writeFieldBegin('clientId', TType::STRING, 4);
      $xfer += $output->writeString($this->clientId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->scopes !== null) {
      $xfer += $output->writeFieldBegin('scopes', TType::STRING, 5);
      $xfer += $output->writeString($this->scopes);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->snsRefreshToken !== null) {
      $xfer += $output->writeFieldBegin('snsRefreshToken', TType::STRING, 6);
      $xfer += $output->writeString($this->snsRefreshToken);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
