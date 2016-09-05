<?php
/**
 *  @generated
 */
class Thrift_UserData {
  static $_TSPEC;

  public $result = null;
  public $id = -1;
  public $name = null;
  public $mailAddress = null;
  public $socialAccounts = null;
  public $enabledPassword = 0;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'result',
          'type' => TType::STRUCT,
          'class' => 'Thrift_APIResult',
          ),
        2 => array(
          'var' => 'id',
          'type' => TType::I64,
          ),
        3 => array(
          'var' => 'name',
          'type' => TType::STRING,
          ),
        4 => array(
          'var' => 'mailAddress',
          'type' => TType::STRING,
          ),
        5 => array(
          'var' => 'socialAccounts',
          'type' => TType::LST,
          'etype' => TType::STRUCT,
          'elem' => array(
            'type' => TType::STRUCT,
            'class' => 'Thrift_SocialAccount',
            ),
          ),
        6 => array(
          'var' => 'enabledPassword',
          'type' => TType::I32,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['result'])) {
        $this->result = $vals['result'];
      }
      if (isset($vals['id'])) {
        $this->id = $vals['id'];
      }
      if (isset($vals['name'])) {
        $this->name = $vals['name'];
      }
      if (isset($vals['mailAddress'])) {
        $this->mailAddress = $vals['mailAddress'];
      }
      if (isset($vals['socialAccounts'])) {
        $this->socialAccounts = $vals['socialAccounts'];
      }
      if (isset($vals['enabledPassword'])) {
        $this->enabledPassword = $vals['enabledPassword'];
      }
    }
  }

  public function getName() {
    return 'Thrift_UserData';
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
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->id);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->name);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->mailAddress);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::LST) {
            $this->socialAccounts = array();
            $_size197 = 0;
            $_etype200 = 0;
            $xfer += $input->readListBegin($_etype200, $_size197);
            for ($_i201 = 0; $_i201 < $_size197; ++$_i201)
            {
              $elem202 = null;
              $elem202 = new Thrift_SocialAccount();
              $xfer += $elem202->read($input);
              $this->socialAccounts []= $elem202;
            }
            $xfer += $input->readListEnd();
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 6:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->enabledPassword);
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
    $xfer += $output->writeStructBegin('Thrift_UserData');
    if ($this->result !== null) {
      if (!is_object($this->result)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('result', TType::STRUCT, 1);
      $xfer += $this->result->write($output);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->id !== null) {
      $xfer += $output->writeFieldBegin('id', TType::I64, 2);
      $xfer += $output->writeI64($this->id);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->name !== null) {
      $xfer += $output->writeFieldBegin('name', TType::STRING, 3);
      $xfer += $output->writeString($this->name);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->mailAddress !== null) {
      $xfer += $output->writeFieldBegin('mailAddress', TType::STRING, 4);
      $xfer += $output->writeString($this->mailAddress);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->socialAccounts !== null) {
      if (!is_array($this->socialAccounts)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('socialAccounts', TType::LST, 5);
      {
        $output->writeListBegin(TType::STRUCT, count($this->socialAccounts));
        {
          foreach ($this->socialAccounts as $iter203)
          {
            $xfer += $iter203->write($output);
          }
        }
        $output->writeListEnd();
      }
      $xfer += $output->writeFieldEnd();
    }
    if ($this->enabledPassword !== null) {
      $xfer += $output->writeFieldBegin('enabledPassword', TType::I32, 6);
      $xfer += $output->writeI32($this->enabledPassword);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


?>
