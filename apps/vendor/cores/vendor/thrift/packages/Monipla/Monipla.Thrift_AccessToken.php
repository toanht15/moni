<?php
/**
 *  @generated
 */
class Thrift_AccessToken {
  static $_TSPEC;

  public $result = null;
  public $accessToken = null;
  public $refreshToken = null;
  public $expiredIn = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'result',
          'type' => TType::STRUCT,
          'class' => 'Thrift_APIResult',
          ),
        2 => array(
          'var' => 'accessToken',
          'type' => TType::STRING,
          ),
        3 => array(
          'var' => 'refreshToken',
          'type' => TType::STRING,
          ),
        4 => array(
          'var' => 'expiredIn',
          'type' => TType::I32,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['result'])) {
        $this->result = $vals['result'];
      }
      if (isset($vals['accessToken'])) {
        $this->accessToken = $vals['accessToken'];
      }
      if (isset($vals['refreshToken'])) {
        $this->refreshToken = $vals['refreshToken'];
      }
      if (isset($vals['expiredIn'])) {
        $this->expiredIn = $vals['expiredIn'];
      }
    }
  }

  public function getName() {
    return 'Thrift_AccessToken';
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
          if ($ftype == TType::STRUCT) {
            $this->result = new Thrift_APIResult();
            $xfer += $this->result->read($input);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->accessToken);
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
        case 4:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->expiredIn);
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
    $xfer += $output->writeStructBegin('Thrift_AccessToken');
    if ($this->result !== null) {
      if (!is_object($this->result)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('result', TType::STRUCT, 1);
      $xfer += $this->result->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->accessToken !== null) {
      $xfer += $output->writeFieldBegin('accessToken', TType::STRING, 2);
      $xfer += $output->writeString($this->accessToken);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->refreshToken !== null) {
      $xfer += $output->writeFieldBegin('refreshToken', TType::STRING, 3);
      $xfer += $output->writeString($this->refreshToken);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->expiredIn !== null) {
      $xfer += $output->writeFieldBegin('expiredIn', TType::I32, 4);
      $xfer += $output->writeI32($this->expiredIn);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
