<?php
/**
 *  @generated
 */
class Thrift_LoginParameter {
  static $_TSPEC;

  public $clientId = null;
  public $mailAddress = null;
  public $password = null;
  public $scopes = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'clientId',
          'type' => TType::STRING,
          ),
        2 => array(
          'var' => 'mailAddress',
          'type' => TType::STRING,
          ),
        3 => array(
          'var' => 'password',
          'type' => TType::STRING,
          ),
        4 => array(
          'var' => 'scopes',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['clientId'])) {
        $this->clientId = $vals['clientId'];
      }
      if (isset($vals['mailAddress'])) {
        $this->mailAddress = $vals['mailAddress'];
      }
      if (isset($vals['password'])) {
        $this->password = $vals['password'];
      }
      if (isset($vals['scopes'])) {
        $this->scopes = $vals['scopes'];
      }
    }
  }

  public function getName() {
    return 'Thrift_LoginParameter';
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
            $xfer += $input->readString($this->mailAddress);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->password);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->scopes);
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
    $xfer += $output->writeStructBegin('Thrift_LoginParameter');
    if ($this->clientId !== null) {
      $xfer += $output->writeFieldBegin('clientId', TType::STRING, 1);
      $xfer += $output->writeString($this->clientId);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->mailAddress !== null) {
      $xfer += $output->writeFieldBegin('mailAddress', TType::STRING, 2);
      $xfer += $output->writeString($this->mailAddress);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->password !== null) {
      $xfer += $output->writeFieldBegin('password', TType::STRING, 3);
      $xfer += $output->writeString($this->password);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->scopes !== null) {
      $xfer += $output->writeFieldBegin('scopes', TType::STRING, 4);
      $xfer += $output->writeString($this->scopes);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
